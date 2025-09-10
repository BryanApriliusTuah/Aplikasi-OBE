<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;

class TeknikPenilaianCpmk extends BaseController
{
    public function index()
    {
        $penilaianData = $this->_getProcessedData();

        return view('teknik_penilaian_cpmk/index', [
            'penilaian'  => $penilaianData,
            'perPage'    => count($penilaianData),
            'page'       => 1,
            'totalPages' => 1,
        ]);
    }

    private function _getProcessedData()
    {
        $db = \Config\Database::connect();

        $allData = $db->table('rps_mingguan')
            ->select('cpl.kode_cpl, mata_kuliah.nama_mk, cpmk.kode_cpmk, rps_mingguan.teknik_penilaian')
            ->join('rps', 'rps.id = rps_mingguan.rps_id')
            ->join('mata_kuliah', 'mata_kuliah.id = rps.mata_kuliah_id')
            ->join('cpl', 'cpl.id = rps_mingguan.cpl_id')
            ->join('cpmk', 'cpmk.id = rps_mingguan.cpmk_id')
            ->orderBy('cpl.kode_cpl', 'ASC')
            ->orderBy('mata_kuliah.nama_mk', 'ASC')
            ->orderBy('cpmk.kode_cpmk', 'ASC')
            ->get()->getResultArray();

        $penilaian = [];
        foreach ($allData as $row) {
            $key = $row['kode_cpl'] . '|' . $row['nama_mk'] . '|' . $row['kode_cpmk'];
            
            if (!isset($penilaian[$key])) {
                $penilaian[$key] = [
                    'kode_cpl'  => $row['kode_cpl'],
                    'nama_mk'   => $row['nama_mk'],
                    'kode_cpmk' => $row['kode_cpmk'],
                    'teknik'    => [],
                ];
            }
            
            $teknik = json_decode($row['teknik_penilaian'] ?? '[]', true);
            if (is_array($teknik)) {
                foreach ($teknik as $kode_teknik => $bobot) {
                    if (isset($penilaian[$key]['teknik'][$kode_teknik])) {
                        $penilaian[$key]['teknik'][$kode_teknik] += (int)$bobot;
                    } else {
                        $penilaian[$key]['teknik'][$kode_teknik] = (int)$bobot;
                    }
                }
            }
        }
        
        return array_values($penilaian);
    }

    public function exportExcel()
    {
        $penilaian = $this->_getProcessedData();
        $kolom_teknik = ['partisipasi', 'observasi', 'unjuk_kerja', 'proyek', 'tes_tulis_uts', 'tes_tulis_uas', 'tes_lisan'];
        $kolom_label = [
            'partisipasi'   => 'Partisipasi',
            'observasi'     => 'Observasi',
            'unjuk_kerja'   => 'Unjuk Kerja',
            'proyek'        => 'Case Method/Project Based',
            'tes_tulis_uts' => 'UTS',
            'tes_tulis_uas' => 'UAS',
            'tes_lisan'     => 'Lisan',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CPL')->setCellValue('B1', 'Mata Kuliah')->setCellValue('C1', 'CPMK');
        
        $col = 'D';
        foreach ($kolom_teknik as $tk) {
            $sheet->setCellValue($col . '1', $kolom_label[$tk] ?? ucfirst($tk));
            $col++;
        }

        $rowNum = 2;
        foreach ($penilaian as $item) {
            $sheet->setCellValue('A' . $rowNum, $item['kode_cpl'])
                  ->setCellValue('B' . $rowNum, $item['nama_mk'])
                  ->setCellValue('C' . $rowNum, $item['kode_cpmk']);
            $col = 'D';
            foreach ($kolom_teknik as $tk) {
                $sheet->setCellValue($col . $rowNum, $item['teknik'][$tk] ?? '');
                $col++;
            }
            $rowNum++;
        }

        $lastCol = chr(ord('C') + count($kolom_teknik));
        $range = "A1:" . $lastCol . ($rowNum - 1);
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle("A1:" . $lastCol . "1")->getFont()->setBold(true);

        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        $filename = 'teknik_penilaian_cpmk.xlsx';
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
        $kolom_teknik = ['partisipasi', 'observasi', 'unjuk_kerja', 'proyek', 'tes_tulis_uts', 'tes_tulis_uas', 'tes_lisan'];
        $kolom_label = [
            'partisipasi'   => 'Partisipasi',
            'observasi'     => 'Observasi',
            'unjuk_kerja'   => 'Unjuk Kerja',
            'proyek'        => 'Case Method/Project Based',
            'tes_tulis_uts' => 'UTS',
            'tes_tulis_uas' => 'UAS',
            'tes_lisan'     => 'Lisan',
        ];

        $html = '<style> table, th, td { border: 1px solid black; border-collapse: collapse; padding: 5px; } th { background-color: #f2f2f2; } </style>';
        $html .= '<h2 style="text-align:center;">Teknik Penilaian CPMK</h2>';
        $html .= '<table width="100%"><thead><tr><th>CPL</th><th>Mata Kuliah</th><th>CPMK</th>';
        foreach ($kolom_teknik as $tk) {
            $html .= '<th>' . ($kolom_label[$tk] ?? ucfirst($tk)) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($penilaian as $item) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['kode_cpl']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['nama_mk']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['kode_cpmk']) . '</td>';
            foreach ($kolom_teknik as $tk) {
                $html .= '<td>' . htmlspecialchars($item['teknik'][$tk] ?? '') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);
        $mpdf->WriteHTML($html);
        
        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="teknik_penilaian_cpmk.pdf"')
            ->setBody($mpdf->Output('', 'S'));
    }
}