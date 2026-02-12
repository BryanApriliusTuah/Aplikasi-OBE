<?php
namespace App\Controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BobotPenilaianCpl extends BaseController
{
    private $teknik_label = [
        'partisipasi'   => 'Partisipasi',
        'observasi'     => 'Observasi',
        'unjuk_kerja'   => 'Unjuk Kerja',
        'proyek'        => 'Project Based',
        'tes_tulis_uts' => 'UTS',
        'tes_tulis_uas' => 'UAS',
        'tes_lisan'     => 'Tes Lisan',
    ];

    public function index()
    {
        $db = \Config\Database::connect();
        $data = $this->getData($db);
        return view('bobot_penilaian_cpl/index', [
            'penilaian' => $data
        ]);
    }

    private function getData($db = null)
    {
        if ($db === null) $db = \Config\Database::connect();
        $rows = $db->table('rps_mingguan')
            ->select('rps_mingguan.*, cpl.kode_cpl, mata_kuliah.kode_mk, mata_kuliah.nama_mk, cpmk.kode_cpmk')
            ->join('cpl', 'cpl.id = rps_mingguan.cpl_id')
            ->join('cpmk', 'cpmk.id = rps_mingguan.cpmk_id')
            ->join('rps', 'rps.id = rps_mingguan.rps_id')
            ->join('mata_kuliah', 'mata_kuliah.id = rps.mata_kuliah_id')
            ->orderBy('cpl.kode_cpl, mata_kuliah.kode_mk, cpmk.kode_cpmk')
            ->get()->getResultArray();

        $result = [];
        foreach ($rows as $r) {
            $teknik = json_decode($r['teknik_penilaian'] ?? '{}', true) ?: [];
            $row = [
                'kode_cpl'   => $r['kode_cpl'],
                'kode_mk'    => $r['kode_mk'],
                'nama_mk'    => $r['nama_mk'],
                'kode_cpmk'  => $r['kode_cpmk'],
            ];
            $total = 0;
            foreach ($this->teknik_label as $k => $lbl) {
                $val = isset($teknik[$k]) ? (int)$teknik[$k] : 0;
                $row[$k] = $val;
                $total += $val;
            }
            $row['total'] = $total;
            $result[] = $row;
        }
        return $result;
    }

    // ======================== EXPORT EXCEL 
    public function exportExcel()
    {
        $data = $this->getData();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'CPL', 'MK', 'Nama MK', 'CPMK',
            'Partisipasi', 'Observasi', 'Unjuk Kerja', 'Project Based',
            'UTS', 'UAS', 'Tes Lisan', 'Total'
        ];
        $colMap = range('A', 'L');
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($colMap[$i] . '1', $h);
        }

        // Data
        $rowNum = 2;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $rowNum, $row['kode_cpl']);
            $sheet->setCellValue('B' . $rowNum, $row['kode_mk']);
            $sheet->setCellValue('C' . $rowNum, $row['nama_mk']);
            $sheet->setCellValue('D' . $rowNum, $row['kode_cpmk']);
            $sheet->setCellValue('E' . $rowNum, $row['partisipasi'] ?: 0);
            $sheet->setCellValue('F' . $rowNum, $row['observasi'] ?: 0);
            $sheet->setCellValue('G' . $rowNum, $row['unjuk_kerja'] ?: 0);
            $sheet->setCellValue('H' . $rowNum, $row['proyek'] ?: 0);
            $sheet->setCellValue('I' . $rowNum, $row['tes_tulis_uts'] ?: 0);
            $sheet->setCellValue('J' . $rowNum, $row['tes_tulis_uas'] ?: 0);
            $sheet->setCellValue('K' . $rowNum, $row['tes_lisan'] ?: 0);
            $sheet->setCellValue('L' . $rowNum, $row['total']);
            // Total bold
            $sheet->getStyle('L'.$rowNum)->getFont()->setBold(true);
            $rowNum++;
        }

        // Styling: border, bold header, auto width
        $lastRow = $rowNum - 1;
        $range = "A1:L$lastRow";
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle("A1:L1")->getFont()->setBold(true);
        foreach ($colMap as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // CPL bold
        $sheet->getStyle("A2:A$lastRow")->getFont()->setBold(true);

        // Output
        $filename = 'bobot_penilaian_cpl.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    //  EXPORT PDF 
    public function exportPdf()
    {
        $data = $this->getData();

        $html = '<h3 style="text-align:center;">Bobot Penilaian Berdasarkan CPL</h3>';
        $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="border-collapse:collapse">';
        $html .= '<thead style="font-weight:bold;background:#f5f7fc">';
        $html .= '<tr>
            <th>CPL</th>
            <th>MK</th>
            <th>Nama MK</th>
            <th>CPMK</th>
            <th>Partisipasi</th>
            <th>Observasi</th>
            <th>Unjuk Kerja</th>
            <th>Project Based</th>
            <th>UTS</th>
            <th>UAS</th>
            <th>Tes Lisan</th>
            <th>Total</th>
        </tr></thead><tbody>';

        foreach ($data as $row) {
            $html .= '<tr>';
            $html .= '<td><b>' . $row['kode_cpl'] . '</b></td>';
            $html .= '<td>' . $row['kode_mk'] . '</td>';
            $html .= '<td>' . $row['nama_mk'] . '</td>';
            $html .= '<td>' . $row['kode_cpmk'] . '</td>';
            $html .= '<td><b>' . $row['partisipasi'] . '</b></td>';
            $html .= '<td>' . $row['observasi'] . '</td>';
            $html .= '<td>' . $row['unjuk_kerja'] . '</td>';
            $html .= '<td>' . $row['proyek'] . '</td>';
            $html .= '<td>' . $row['tes_tulis_uts'] . '</td>';
            $html .= '<td>' . $row['tes_tulis_uas'] . '</td>';
            $html .= '<td>' . $row['tes_lisan'] . '</td>';
            $html .= '<td><b>' . $row['total'] . '</b></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        $mpdf->WriteHTML($html);

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="bobot_penilaian_cpl.pdf"')
            ->setBody($mpdf->Output('', 'S'));
    }
}
