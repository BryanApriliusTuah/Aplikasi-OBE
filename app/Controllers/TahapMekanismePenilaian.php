<?php
namespace App\Controllers;
use App\Models\RpsMingguanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class TahapMekanismePenilaian extends BaseController
{
    private function _getProcessedData()
    {
        $model = new RpsMingguanModel();
        
        $allData = $model
            ->select('rps_mingguan.*, cpl.kode_cpl, mata_kuliah.nama_mk, cpmk.kode_cpmk, sub_cpmk.kode_sub_cpmk')
            ->join('cpl', 'cpl.id = rps_mingguan.cpl_id', 'left')
            ->join('cpmk', 'cpmk.id = rps_mingguan.cpmk_id', 'left')
            ->join('sub_cpmk', 'sub_cpmk.id = rps_mingguan.sub_cpmk_id', 'left')
            ->join('rps', 'rps.id = rps_mingguan.rps_id')
            ->join('mata_kuliah', 'mata_kuliah.id = rps.mata_kuliah_id')
            ->orderBy('mata_kuliah.nama_mk, cpl.kode_cpl, cpmk.kode_cpmk, rps_mingguan.minggu')
            ->findAll();

        $jsonFormatter = function ($jsonString) {
            if (empty($jsonString) || $jsonString == 'null' || $jsonString == '[]') return '-';
            $data = json_decode($jsonString, true);
            if (is_array($data) && !empty($data)) {
                return esc(implode(', ', $data));
            }
            return esc($jsonString);
        };
        
        $teknikFormatter = function ($jsonString) {
            $teknik = json_decode($jsonString, true);
            if (!is_array($teknik) || empty($teknik)) return '-';
            
            $labels = [
                'partisipasi' => 'Partisipasi', 'observasi' => 'Observasi', 'unjuk_kerja' => 'Unjuk Kerja',
                'proyek' => 'Proyek', 'tes_tulis_uts' => 'Tes Tulis (UTS)', 'tes_tulis_uas' => 'Tes Tulis (UAS)', 'tes_lisan' => 'Tes Lisan'
            ];
            $hasil = [];
            foreach ($teknik as $key => $bobot) {
                if ($bobot > 0) {
                    $nama = $labels[$key] ?? ucfirst($key);
                    $hasil[] = esc($nama . ' (' . $bobot . ')');
                }
            }
            return !empty($hasil) ? implode(', ', $hasil) : '-';
        };

        foreach ($allData as &$row) {
            $row['tahap_penilaian'] = $jsonFormatter($row['tahap_penilaian']);
            $row['teknik_penilaian'] = $teknikFormatter($row['teknik_penilaian']);
            $row['instrumen'] = $jsonFormatter($row['instrumen']);
            $row['kriteria_penilaian'] = $jsonFormatter($row['kriteria_penilaian']);
            $row['indikator'] = $jsonFormatter($row['indikator']);
            $row['metode'] = $jsonFormatter($row['metode']);
        }
        
        return $allData;
    }

    public function index()
    {
        $perPage = $this->request->getGet('perPage') ?? 10;
        $page    = $this->request->getGet('page') ?? 1;

        $penilaian = $this->_getProcessedData();

        $total_bobot_per_mk = [];
        foreach ($penilaian as $row) {
            $mk = $row['nama_mk'];
            if (!isset($total_bobot_per_mk[$mk])) $total_bobot_per_mk[$mk] = 0;
            $total_bobot_per_mk[$mk] += (int)($row['bobot'] ?? 0);
        }

        $total      = count($penilaian);
        $totalPages = max(ceil($total / $perPage), 1);
        $offset     = ($page - 1) * $perPage;
        $paginated  = array_slice($penilaian, $offset, $perPage);

        return view('tahap_mekanisme_penilaian/index', [
            'penilaian'          => $paginated,
            'total_bobot_per_mk' => $total_bobot_per_mk,
            'total'              => $total,
            'perPage'            => $perPage,
            'page'               => $page,
            'totalPages'         => $totalPages
        ]);
    }

    public function exportExcel()
    {
        $penilaian = $this->_getProcessedData();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $header = ['No', 'CPL', 'Mata Kuliah', 'CPMK', 'SubCPMK', 'Minggu', 'Indikator', 'Kriteria Penilaian', 'Tahap Penilaian', 'Teknik Penilaian', 'Materi Pembelajaran', 'Instrumen', 'Metode', 'Bobot'];
        $colMap = range('A', 'N');
        foreach ($header as $i => $h) {
            $sheet->setCellValue($colMap[$i].'1', $h);
        }

        $rowNum = 2;
        $no = 1;
        foreach ($penilaian as $item) {
            $sheet->setCellValue('A'.$rowNum, $no++);
            $sheet->setCellValue('B'.$rowNum, $item['kode_cpl']);
            $sheet->setCellValue('C'.$rowNum, $item['nama_mk']);
            $sheet->setCellValue('D'.$rowNum, $item['kode_cpmk']);
            $sheet->setCellValue('E'.$rowNum, $item['kode_sub_cpmk']);
            $sheet->setCellValue('F'.$rowNum, $item['minggu']);
            $sheet->setCellValue('G'.$rowNum, $item['indikator']);
            $sheet->setCellValue('H'.$rowNum, $item['kriteria_penilaian']);
            $sheet->setCellValue('I'.$rowNum, $item['tahap_penilaian']);
            $sheet->setCellValue('J'.$rowNum, $item['teknik_penilaian']);
            $sheet->setCellValue('K'.$rowNum, $item['materi_pembelajaran']);
            $sheet->setCellValue('L'.$rowNum, $item['instrumen']);
            $sheet->setCellValue('M'.$rowNum, $item['metode']);
            $sheet->setCellValue('N'.$rowNum, $item['bobot']);
            $rowNum++;
        }

        $range = "A1:N".($rowNum - 1);
        $styleArray = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];
        $sheet->getStyle($range)->applyFromArray($styleArray);
        $sheet->getStyle("A1:N1")->getFont()->setBold(true);

        foreach ($colMap as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'tahap_mekanisme_penilaian.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $penilaian = $this->_getProcessedData();

        $html = '<style> table, th, td { border: 1px solid black; border-collapse: collapse; padding: 4px; font-size: 10px; } </style>';
        $html .= '<h3 style="text-align:center;">Tahap & Mekanisme Penilaian</h3>';
        $html .= '<table>';
        $html .= '<thead style="font-weight:bold;"><tr><th>No</th><th>CPL</th><th>Mata Kuliah</th><th>CPMK</th><th>SubCPMK</th><th>Minggu</th><th>Indikator</th><th>Kriteria Penilaian</th><th>Tahap Penilaian</th><th>Teknik Penilaian</th><th>Materi Pembelajaran</th><th>Instrumen</th><th>Metode</th><th>Bobot</th></tr></thead><tbody>';
        
        $no = 1;
        foreach ($penilaian as $item) {
            $html .= '<tr>';
            $html .= '<td>'.$no++.'</td>';
            $html .= '<td>'.esc($item['kode_cpl']).'</td>';
            $html .= '<td>'.esc($item['nama_mk']).'</td>';
            $html .= '<td>'.esc($item['kode_cpmk']).'</td>';
            $html .= '<td>'.esc($item['kode_sub_cpmk']).'</td>';
            $html .= '<td>'.esc($item['minggu']).'</td>';
            $html .= '<td>'.esc($item['indikator']).'</td>';
            $html .= '<td>'.esc($item['kriteria_penilaian']).'</td>';
            $html .= '<td>'.esc($item['tahap_penilaian']).'</td>';
            $html .= '<td>'.esc($item['teknik_penilaian']).'</td>';
            $html .= '<td>'.esc($item['materi_pembelajaran']).'</td>';
            $html .= '<td>'.esc($item['instrumen']).'</td>';
            $html .= '<td>'.esc($item['metode']).'</td>';
            $html .= '<td>'.esc($item['bobot']).'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
        $mpdf->WriteHTML($html);

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="tahap_mekanisme_penilaian.pdf"')
            ->setBody($mpdf->Output('', 'S'));
    }
}