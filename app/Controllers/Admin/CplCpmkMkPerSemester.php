<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplModel;
use App\Models\CpmkModel;
use App\Models\MataKuliahModel;
use App\Models\CpmkMkModel;
use App\Models\CplCpmkModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class CplCpmkMkPerSemester extends BaseController
{
    private function getMatriksData()
    {
        $cplModel     = new CplModel();
        $cpmkModel    = new CpmkModel();
        $mkModel      = new MataKuliahModel();
        $cpmkMkModel  = new CpmkMkModel();
        $cplCpmkModel = new CplCpmkModel();
        
        $cplList = $cplModel->orderBy('kode_cpl', 'ASC')->findAll();

        $data = [];
        foreach ($cplList as $cpl) {
            $cplCpmkRelations = $cplCpmkModel->where('cpl_id', $cpl['id'])->findAll();
            if (empty($cplCpmkRelations)) {
                continue;
            }

            $cpmkIds = array_column($cplCpmkRelations, 'cpmk_id');
            $cpmks = [];
            if (!empty($cpmkIds)) {
                $cpmks = $cpmkModel->whereIn('id', $cpmkIds)->orderBy('kode_cpmk', 'ASC')->findAll();
            }

            foreach ($cpmks as &$cpmk) {
                $cpmk['semester'] = [];
                $relasi = $cpmkMkModel->where('cpmk_id', $cpmk['id'])->findAll();
                foreach ($relasi as $rel) {
                    $mk = $mkModel->find($rel['mata_kuliah_id']);
                    if ($mk) {
                        $cpmk['semester'][$mk['semester']][] = $mk['nama_mk'];
                    }
                }
            }
            $data[] = [
                'cpl'  => $cpl,
                'cpmk' => $cpmks
            ];
        }
        return $data;
    }

    public function index()
    {
        return view('admin/cpl_cpmk_mk_per_semester/index', [
            'data' => $this->getMatriksData(),
        ]);
    }

    public function exportExcel()
    {
        $data = $this->getMatriksData();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Kode CPL');
        $sheet->setCellValue('B1', 'Kode CPMK');
        $colIndex = 'C';
        for ($i = 1; $i <= 8; $i++) {
            $sheet->setCellValue($colIndex . '1', 'Semester ' . $i);
            $colIndex++;
        }
        
        $lastHeaderCol = chr(ord($colIndex) - 1);
        $row = 2;
        foreach ($data as $item) {
            $startMergeRow = $row;
            foreach ($item['cpmk'] as $cpmk) {
                $sheet->setCellValue('A' . $row, $item['cpl']['kode_cpl']);
                $sheet->setCellValue('B' . $row, $cpmk['kode_cpmk']);
                $colIndex = 'C';
                for ($i = 1; $i <= 8; $i++) {
                    $isi = isset($cpmk['semester'][$i]) ? implode("\n", $cpmk['semester'][$i]) : '';
                    $sheet->setCellValue($colIndex . $row, $isi);
                    $sheet->getStyle($colIndex . $row)->getAlignment()->setWrapText(true);
                    $colIndex++;
                }
                $row++;
            }
            if ($row > $startMergeRow + 1) {
                $sheet->mergeCells("A{$startMergeRow}:A" . ($row - 1));
            }
        }

        $lastRow = $row - 1;
        $sheet->getStyle("A1:{$lastHeaderCol}1")->applyFromArray([
            'font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle("A1:{$lastHeaderCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ]);

        foreach (range('A', $lastHeaderCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Pemenuhan_CPL_CPMK_MK_per_Semester.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $data = $this->getMatriksData();
        $html = '<style>table{border-collapse:collapse;width:100%;font-size:9px}th,td{border:1px solid #000;padding:4px;vertical-align:middle}th{background:#f2f2f2;text-align:center}h3{text-align:center}</style><h3>Pemenuhan CPL & CPMK oleh Mata Kuliah</h3><table><thead><tr><th>Kode CPL</th><th>Kode CPMK</th>';
        for ($i = 1; $i <= 8; $i++) {
            $html .= "<th>Semester {$i}</th>";
        }
        $html .= '</tr></thead><tbody>';
        
        foreach ($data as $item) {
            $cpmkCount = count($item['cpmk']);
            $isFirst = true;
            if ($cpmkCount > 0) {
                foreach ($item['cpmk'] as $cpmk) {
                    $html .= '<tr>';
                    if ($isFirst) {
                        $html .= '<td rowspan="' . $cpmkCount . '" style="text-align:center;">' . esc($item['cpl']['kode_cpl']) . '</td>';
                        $isFirst = false;
                    }
                    $html .= '<td>' . esc($cpmk['kode_cpmk']) . '</td>';
                    for ($i = 1; $i <= 8; $i++) {
                        $isi = isset($cpmk['semester'][$i]) ? implode('<br>', array_map('esc', $cpmk['semester'][$i])) : '';
                        $html .= '<td>' . $isi . '</td>';
                    }
                    $html .= '</tr>';
                }
            }
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Pemenuhan_CPL_CPMK_MK_per_Semester.pdf', ['Attachment' => false]);
        exit;
    }
}