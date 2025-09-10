<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplModel;
use App\Models\MataKuliahModel;
use App\Models\CplMkModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class PetaCPL extends BaseController
{
    private function getMatriksData()
    {
        $cplModel = new CplModel();
        $mkModel = new MataKuliahModel();
        $cplMkModel = new CplMkModel();

        $allCplList = $cplModel->orderBy('kode_cpl', 'ASC')->findAll();
        $mataKuliah = $mkModel->findAll();
        $relasi = $cplMkModel->findAll();
        
        $dataMatriks = [];
        foreach ($allCplList as $cpl) {
            for ($semester = 1; $semester <= 8; $semester++) {
                $dataMatriks[$cpl['id']][$semester] = [];
            }
        }
        foreach ($relasi as $item) {
            $mk = array_filter($mataKuliah, fn($m) => $m['id'] == $item['mata_kuliah_id']);
            $mk = reset($mk);
            if ($mk) {
                $dataMatriks[$item['cpl_id']][$mk['semester']][] = $mk['nama_mk'];
            }
        }
        
        return ['cplList' => $allCplList, 'dataMatriks' => $dataMatriks];
    }

    public function index()
    {
        return view('admin/peta_cpl/index', $this->getMatriksData());
    }
    
    public function exportPdf()
    {
        $data = $this->getMatriksData();
        $allCplList = $data['cplList'];
        $dataMatriks = $data['dataMatriks'];

        $html = '<style>table{border-collapse:collapse;width:100%;font-size:10px}th,td{border:1px solid #000;padding:5px;vertical-align:top}th{background:#f2f2f2;text-align:center}h3{text-align:center;font-weight:bold}</style><h3>Peta Pemenuhan CPL per Semester</h3><table><thead><tr><th>Kode CPL</th>';
        for ($i = 1; $i <= 8; $i++) {
            $html .= "<th>Semester $i</th>";
        }
        $html .= '</tr></thead><tbody>';

        foreach ($allCplList as $cpl) {
            $html .= '<tr><td>' . esc($cpl['kode_cpl']) . '</td>';
            for ($semester = 1; $semester <= 8; $semester++) {
                $isi = isset($dataMatriks[$cpl['id']][$semester]) && count($dataMatriks[$cpl['id']][$semester]) ? esc(implode(', ', $dataMatriks[$cpl['id']][$semester])) : '';
                $html .= '<td>' . $isi . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Peta_CPL_Semester.pdf', ['Attachment' => false]);
        exit;
    }

    public function exportExcel()
    {
        $data = $this->getMatriksData();
        $allCplList = $data['cplList'];
        $dataMatriks = $data['dataMatriks'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $header = ['Kode CPL'];
        for ($i = 1; $i <= 8; $i++) $header[] = "Semester $i";
        $sheet->fromArray($header, NULL, 'A1');

        $lastCol = chr(ord('A') + count($header) - 1);
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D2EFFF']]
        ]);

        $rowNum = 2;
        foreach ($allCplList as $cpl) {
            $sheet->setCellValue('A' . $rowNum, $cpl['kode_cpl']);
            $colIndex = 'B';
            for ($semester = 1; $semester <= 8; $semester++) {
                $isi = isset($dataMatriks[$cpl['id']][$semester]) && count($dataMatriks[$cpl['id']][$semester]) ? implode(', ', $dataMatriks[$cpl['id']][$semester]) : '';
                $sheet->setCellValue($colIndex . $rowNum, $isi);
                $colIndex++;
            }
            $rowNum++;
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastRow = $rowNum - 1;
        $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $filename = 'Peta_CPL_Semester.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}