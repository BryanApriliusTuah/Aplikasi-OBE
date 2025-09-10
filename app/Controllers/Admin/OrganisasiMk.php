<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataKuliahModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class OrganisasiMk extends BaseController
{
    public function index()
    {
        $mkModel = new MataKuliahModel();
        $all = $mkModel->orderBy('semester ASC, nama_mk ASC')->findAll();

        $data = [];
        $data['matkul'] = [];
        $data['total_sks'] = [];
        $data['jumlah_mk'] = [];

        foreach ($all as $mk) {
            $smt = $mk['semester'];
            $kat = $mk['kategori'];
            $data['matkul'][$smt][$kat][] = $mk;
            $data['total_sks'][$smt] = ($data['total_sks'][$smt] ?? 0) + $mk['sks'];
            $data['jumlah_mk'][$smt] = ($data['jumlah_mk'][$smt] ?? 0) + 1;
        }

        return view('admin/organisasi_mk/index', $data);
    }

    private function getExportData()
    {
        $mkModel = new MataKuliahModel();
        $all = $mkModel->orderBy('semester ASC, nama_mk ASC')->findAll();
        $bySemester = [];

        foreach ($all as $mk) {
            $smt = $mk['semester'];
            $kat = $mk['kategori'];
            if (!isset($bySemester[$smt])) {
                $bySemester[$smt] = [
                    'wajib_teori' => [],
                    'wajib_praktikum' => [],
                    'pilihan' => [],
                    'mkwk' => [],
                    'total_sks' => 0,
                    'jumlah_mk' => 0
                ];
            }
            $bySemester[$smt][$kat][] = $mk['nama_mk'];
            $bySemester[$smt]['total_sks'] += $mk['sks'];
            $bySemester[$smt]['jumlah_mk'] += 1;
        }
        ksort($bySemester);
        return $bySemester;
    }

    public function exportExcel()
    {
        $bySemester = $this->getExportData();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Semester');
        $sheet->setCellValue('C1', 'MK Wajib (Teori)');
        $sheet->setCellValue('D1', 'MK Wajib (Praktikum)');
        $sheet->setCellValue('E1', 'MK Pilihan');
        $sheet->setCellValue('F1', 'MKWK');
        $sheet->setCellValue('G1', 'Total SKS');
        $sheet->setCellValue('H1', 'Jumlah MK');
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        $no = 1;
        foreach ($bySemester as $semester => $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $semester);
            $sheet->setCellValue('C' . $row, implode("\n", $data['wajib_teori']));
            $sheet->setCellValue('D' . $row, implode("\n", $data['wajib_praktikum']));
            $sheet->setCellValue('E' . $row, implode("\n", $data['pilihan']));
            $sheet->setCellValue('F' . $row, implode("\n", $data['mkwk']));
            $sheet->getStyle('C' . $row . ':F' . $row)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('G' . $row, $data['total_sks']);
            $sheet->setCellValue('H' . $row, $data['jumlah_mk']);
            $row++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $lastRow = $row - 1;
        $sheet->getStyle('A1:H' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $filename = 'Organisasi_Mata_Kuliah.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $bySemester = $this->getExportData();
        $html = '<style>table{border-collapse:collapse;width:100%;font-size:10px}th,td{border:1px solid #000;padding:5px;vertical-align:top}th{background:#f2f2f2;text-align:center}h3{text-align:center}</style><h3>Organisasi Mata Kuliah</h3><table><thead><tr><th rowspan="2">Smt</th><th rowspan="2">SKS</th><th rowspan="2">Jml MK</th><th colspan="2">MK Wajib</th><th rowspan="2">MK Pilihan</th><th rowspan="2">MKWK</th></tr><tr><th>Teori</th><th>Praktikum</th></tr></thead><tbody>';

        foreach ($bySemester as $semester => $data) {
            $html .= '<tr><td style="text-align:center;">' . $semester . '</td><td style="text-align:center;">' . $data['total_sks'] . '</td><td style="text-align:center;">' . $data['jumlah_mk'] . '</td><td>' . nl2br(implode("\n", $data['wajib_teori'])) . '</td><td>' . nl2br(implode("\n", $data['wajib_praktikum'])) . '</td><td>' . nl2br(implode("\n", $data['pilihan'])) . '</td><td>' . nl2br(implode("\n", $data['mkwk'])) . '</td></tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Organisasi_Mata_Kuliah.pdf', ['Attachment' => false]);
        exit;
    }
}