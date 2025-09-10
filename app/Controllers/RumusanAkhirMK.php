<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RumusanAkhirMK extends BaseController
{
    public function index()
    {
        $rekap = $this->getData();
        return view('rumusan_akhir_mk/index', [
            'rekap' => $rekap
        ]);
    }

    private function getData()
    {
        $db = \Config\Database::connect();

        $data_mingguan = $db->table('rps_mingguan')
            ->select('rps_mingguan.*, rps.mata_kuliah_id, mata_kuliah.nama_mk, cpl.kode_cpl, cpmk.kode_cpmk')
            ->join('rps', 'rps.id = rps_mingguan.rps_id')
            ->join('mata_kuliah', 'mata_kuliah.id = rps.mata_kuliah_id')
            ->join('cpl', 'cpl.id = rps_mingguan.cpl_id')
            ->join('cpmk', 'cpmk.id = rps_mingguan.cpmk_id')
            ->orderBy('mata_kuliah.nama_mk, cpl.kode_cpl, cpmk.kode_cpmk')
            ->get()->getResultArray();

        $rekap = [];
        foreach ($data_mingguan as $row) {
            $mkid = $row['mata_kuliah_id'];
            $rekap[$mkid]['nama_mk'] = $row['nama_mk'];
            $rekap[$mkid]['detail'][] = [
                'kode_cpl' => $row['kode_cpl'],
                'kode_cpmk' => $row['kode_cpmk'],
                'bobot' => (int)$row['bobot']
            ];
            if (!isset($rekap[$mkid]['total'])) $rekap[$mkid]['total'] = 0;
            $rekap[$mkid]['total'] += (int)$row['bobot'];
        }
        return $rekap;
    }

    //  EXPORT EXCEL 
    public function exportExcel()
    {
        $rekap = $this->getData();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['No', 'Mata Kuliah', 'Kode CPL', 'Kode CPMK', 'Bobot'];
        $cols = ['A', 'B', 'C', 'D', 'E'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '1', $h);
        }
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        $rowNum = 2;
        $no = 1;
        foreach ($rekap as $mk) {
            $rowspan = count($mk['detail']);
            $firstRow = $rowNum;
            $total_bobot = 0;
            foreach ($mk['detail'] as $d) {
                $sheet->setCellValue('C'.$rowNum, $d['kode_cpl']);
                $sheet->setCellValue('D'.$rowNum, $d['kode_cpmk']);
                $sheet->setCellValue('E'.$rowNum, $d['bobot']);
                $total_bobot += $d['bobot'];
                $rowNum++;
            }
            // Set No & Mata Kuliah (merge)
            $sheet->setCellValue('A'.$firstRow, $no++);
            $sheet->setCellValue('B'.$firstRow, $mk['nama_mk']);
            if ($rowspan > 1) {
                $sheet->mergeCells("A$firstRow:A".($firstRow+$rowspan-1));
                $sheet->mergeCells("B$firstRow:B".($firstRow+$rowspan-1));
            }
            // Baris total per MK (seperti di sistem)
            $sheet->setCellValue('A'.$rowNum, 'Total');
            $sheet->mergeCells("A$rowNum:D$rowNum");
            $sheet->getStyle("A$rowNum:D$rowNum")->getFont()->setBold(true);
            $sheet->setCellValue('E'.$rowNum, $total_bobot);
            $sheet->getStyle('E'.$rowNum)->getFont()->setBold(true);
            $rowNum++;
        }

        // Border & auto width
        $lastRow = $rowNum - 1;
        $sheet->getStyle("A1:E$lastRow")->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        foreach ($cols as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

        // Output
        $filename = 'rumusan_akhir_mk.xlsx';
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
        $rekap = $this->getData();

        $html = '<h3 style="text-align:center;">Rumusan Akhir Berdasarkan MK</h3>';
        $html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%" style="border-collapse:collapse">';
        $html .= '<thead style="font-weight:bold;background:#f5f7fc">';
        $html .= '<tr>
            <th>No</th>
            <th>Mata Kuliah</th>
            <th>Kode CPL</th>
            <th>Kode CPMK</th>
            <th>Bobot</th>
        </tr></thead><tbody>';

        $no = 1;
        foreach ($rekap as $mk) {
            $rowspan = count($mk['detail']);
            $html .= '<tr>';
            $html .= '<td rowspan="'.$rowspan.'" class="align-middle text-center">'.($no++).'</td>';
            $html .= '<td rowspan="'.$rowspan.'" class="align-middle">'.htmlspecialchars($mk['nama_mk']).'</td>';
            $first = true;
            $total_bobot = 0;
            foreach ($mk['detail'] as $d) {
                if (!$first) $html .= '<tr>';
                $html .= '<td class="align-middle">'.htmlspecialchars($d['kode_cpl']).'</td>';
                $html .= '<td class="align-middle">'.htmlspecialchars($d['kode_cpmk']).'</td>';
                $html .= '<td class="align-middle text-center">'.($d['bobot']).'</td>';
                $total_bobot += $d['bobot'];
                $html .= '</tr>';
                $first = false;
            }
            // Total per MK
            $html .= '<tr style="font-weight:bold;text-align:center;"><td colspan="4">Total</td><td>'.$total_bobot.'</td></tr>';
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
            ->setHeader('Content-Disposition', 'attachment; filename="rumusan_akhir_mk.pdf"')
            ->setBody($mpdf->Output('', 'S'));
    }
}
