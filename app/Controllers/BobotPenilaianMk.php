<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BobotPenilaianMk extends BaseController
{
    private $teknik_list = [
        'partisipasi'      => 'Partisipasi',
        'observasi'        => 'Observasi',
        'unjuk_kerja'      => 'Unjuk Kerja',
        'case_method'      => 'Project Based',
        'tes_tulis_uts'    => 'UTS',
        'tes_tulis_uas'    => 'UAS',
        'tes_lisan'        => 'Tes Lisan'
    ];

    public function index()
    {
        $grouped = $this->getData();
        return view('bobot_penilaian_mk/index', [
            'grouped' => $grouped,
            'teknik_list' => $this->teknik_list
        ]);
    }

    // Helper untuk ambil data & struktur sesuai view
    private function getData()
    {
        $db = \Config\Database::connect();
        $rows = $db->table('rps_mingguan')
            ->select('rps_mingguan.*, cpl.kode_cpl, mata_kuliah.kode_mk, mata_kuliah.nama_mk, cpmk.kode_cpmk')
            ->join('cpl', 'cpl.id = rps_mingguan.cpl_id')
            ->join('cpmk', 'cpmk.id = rps_mingguan.cpmk_id')
            ->join('rps', 'rps.id = rps_mingguan.rps_id')
            ->join('mata_kuliah', 'mata_kuliah.id = rps.mata_kuliah_id')
            ->orderBy('mata_kuliah.nama_mk, cpl.kode_cpl, cpmk.kode_cpmk')
            ->get()->getResultArray();

        // Grouping MK > CPL > CPMK
        $grouped = [];
        foreach ($rows as $row) {
            $mk = $row['nama_mk'];
            $cpl = $row['kode_cpl'];
            $grouped[$mk][$cpl][] = $row;
        }
        return $grouped;
    }

    //  EXPORT EXCEL 
    public function exportExcel()
    {
        $grouped = $this->getData();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'MK', 'CPL', 'CPMK',
            'Partisipasi', 'Observasi', 'Unjuk Kerja', 'Project Based', 'UTS', 'UAS', 'Tes Lisan', 'Total'
        ];
        $colMap = range('A', 'K');
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($colMap[$i] . '1', $h);
        }
        $sheet->getStyle("A1:K1")->getFont()->setBold(true);

        $rowNum = 2;
        foreach ($grouped as $mk => $cpls) {
            $startRow = $rowNum;
            $rowspan_mk = array_sum(array_map('count', $cpls));
            $total_per_mk = array_fill_keys(array_keys($this->teknik_list), 0);
            $grand_total_per_mk = 0;

            $mk_printed = false;
            foreach ($cpls as $cpl => $cpmks) {
                $cpl_printed = false;
                foreach ($cpmks as $row) {
                    $teknik_array = json_decode($row['teknik_penilaian'] ?? '{}', true) ?: [];
                    $total = 0;
                    // MK
                    if (!$mk_printed) {
                        $sheet->setCellValue('A' . $rowNum, $mk);
                        if ($rowspan_mk > 1)
                            $sheet->mergeCells("A$rowNum:A" . ($rowNum + $rowspan_mk - 1));
                        $mk_printed = true;
                    }
                    // CPL
                    if (!$cpl_printed) {
                        $sheet->setCellValue('B' . $rowNum, $cpl);
                        if (count($cpmks) > 1)
                            $sheet->mergeCells("B$rowNum:B" . ($rowNum + count($cpmks) - 1));
                        $cpl_printed = true;
                    }
                    // CPMK
                    $sheet->setCellValue('C' . $rowNum, $row['kode_cpmk']);

                    // Teknik & Total
                    $col = 'D';
                    foreach ($this->teknik_list as $key => $label) {
                        $bobot = isset($teknik_array[$key]) ? (int)$teknik_array[$key] : 0;
                        $sheet->setCellValue($col . $rowNum, $bobot ? $bobot : 0);
                        if ($bobot) $sheet->getStyle($col.$rowNum)->getFont()->setBold(true);
                        $total_per_mk[$key] += $bobot;
                        $total += $bobot;
                        $col++;
                    }
                    // Total per row
                    $sheet->setCellValue($col . $rowNum, $total);
                    $sheet->getStyle($col.$rowNum)->getFont()->setBold(true);
                    $grand_total_per_mk += $total;

                    $rowNum++;
                }
            }
            // Baris total per MK
            $sheet->setCellValue('A' . $rowNum, 'Total');
            $sheet->mergeCells("A$rowNum:C$rowNum");
            $sheet->getStyle("A$rowNum:C$rowNum")->getFont()->setBold(true);
            $col = 'D';
            foreach ($this->teknik_list as $key => $label) {
                $sheet->setCellValue($col . $rowNum, $total_per_mk[$key]);
                $sheet->getStyle($col . $rowNum)->getFont()->setBold(true);
                $col++;
            }
            $sheet->setCellValue($col . $rowNum, $grand_total_per_mk);
            $sheet->getStyle($col . $rowNum)->getFont()->setBold(true);
            $rowNum++;
        }

        // Border, auto width
        $lastRow = $rowNum - 1;
        $sheet->getStyle("A1:K$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        foreach ($colMap as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'bobot_penilaian_mk.xlsx';
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
        $grouped = $this->getData();

        $html = '<h3 style="text-align:center;">Bobot Penilaian Berdasarkan MK</h3>';
        $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="border-collapse:collapse">';
        $html .= '<thead style="font-weight:bold;background:#f5f7fc">';
        $html .= '<tr>
            <th>MK</th>
            <th>CPL</th>
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

        foreach ($grouped as $mk => $cpls) {
            $rowspan_mk = array_sum(array_map('count', $cpls));
            $total_per_mk = array_fill_keys(array_keys($this->teknik_list), 0);
            $grand_total_per_mk = 0;

            $mk_printed = false;
            foreach ($cpls as $cpl => $cpmks) {
                $cpl_printed = false;
                foreach ($cpmks as $row) {
                    $teknik_array = json_decode($row['teknik_penilaian'] ?? '{}', true) ?: [];
                    $total = 0;
                    $html .= '<tr>';
                    // MK
                    if (!$mk_printed) {
                        $html .= '<td rowspan="'.$rowspan_mk.'" class="align-middle">'.htmlspecialchars($mk).'</td>';
                        $mk_printed = true;
                    }
                    // CPL
                    if (!$cpl_printed) {
                        $html .= '<td rowspan="'.count($cpmks).'" class="align-middle">'.htmlspecialchars($cpl).'</td>';
                        $cpl_printed = true;
                    }
                    // CPMK
                    $html .= '<td class="align-middle">'.htmlspecialchars($row['kode_cpmk']).'</td>';
                    // Teknik & total
                    foreach ($this->teknik_list as $key => $label) {
                        $bobot = isset($teknik_array[$key]) ? (int)$teknik_array[$key] : 0;
                        $html .= '<td class="text-center">'.($bobot ? '<b>'.$bobot.'</b>' : '0').'</td>';
                        $total_per_mk[$key] += $bobot;
                        $total += $bobot;
                    }
                    $html .= '<td class="text-center"><b>'.$total.'</b></td>';
                    $grand_total_per_mk += $total;
                    $html .= '</tr>';
                }
            }
            // Baris total per MK
            $html .= '<tr style="font-weight:bold;text-align:center;"><td colspan="3">Total</td>';
            foreach ($this->teknik_list as $key => $label) {
                $html .= '<td>'.$total_per_mk[$key].'</td>';
            }
            $html .= '<td>'.$grand_total_per_mk.'</td></tr>';
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
            ->setHeader('Content-Disposition', 'attachment; filename="bobot_penilaian_mk.pdf"')
            ->setBody($mpdf->Output('', 'S'));
    }
}
