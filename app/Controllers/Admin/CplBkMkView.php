<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplModel;
use App\Models\BahanKajianModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class CplBkMkView extends BaseController
{
    private function getMatriksData()
    {
        $db = \Config\Database::connect();
        $cplModel = new CplModel();
        $bkModel = new BahanKajianModel();
        
        $cpl = $cplModel->orderBy('kode_cpl', 'ASC')->findAll();
        $bk = $bkModel->orderBy('kode_bk', 'ASC')->findAll();
        
        $bkIds = array_column($bk, 'id');
        $cplIds = array_column($cpl, 'id');
        $mapping = [];

        if (!empty($bkIds) && !empty($cplIds)) {
            $sql = "SELECT cpl_bk.bk_id, cpl_bk.cpl_id, mk.nama_mk FROM cpl_bk JOIN bk_mk ON bk_mk.bahan_kajian_id = cpl_bk.bk_id JOIN mata_kuliah mk ON mk.id = bk_mk.mata_kuliah_id JOIN cpl_mk ON cpl_mk.cpl_id = cpl_bk.cpl_id AND cpl_mk.mata_kuliah_id = bk_mk.mata_kuliah_id WHERE cpl_bk.bk_id IN (" . implode(',', $bkIds) . ") AND cpl_bk.cpl_id IN (" . implode(',', $cplIds) . ") ORDER BY cpl_bk.bk_id, cpl_bk.cpl_id, mk.nama_mk";
            $rows = $db->query($sql)->getResultArray();

            foreach ($rows as $row) {
                $mapping[$row['bk_id']][$row['cpl_id']][] = $row['nama_mk'];
            }
        }
        
        return ['cpl' => $cpl, 'bk' => $bk, 'mapping' => $mapping];
    }

    public function index()
    {
        return view('admin/cpl_bk_mk/index', $this->getMatriksData());
    }

    public function exportExcel()
    {
        $data = $this->getMatriksData();
        $cpl = $data['cpl'];
        $bk = $data['bk'];
        $mapping = $data['mapping'];
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Bahan Kajian');
        $col = 'C';
        foreach ($cpl as $cp) {
            $sheet->setCellValue($col . '1', $cp['kode_cpl']);
            $col++;
        }

        $rowNum = 2;
        $no = 1;
        foreach ($bk as $bahan) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $bahan['nama_bk']);
            $col = 'C';
            foreach ($cpl as $cp) {
                $listMk = $mapping[$bahan['id']][$cp['id']] ?? [];
                $isi = implode("\n", $listMk);
                $sheet->setCellValue($col . $rowNum, $isi);
                $sheet->getStyle($col . $rowNum)->getAlignment()->setWrapText(true);
                $col++;
            }
            $rowNum++;
        }

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cpl) + 2);
        $lastRow = $rowNum - 1;
        
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ]);
        
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Matriks_CPL_BK_MK.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $data = $this->getMatriksData();
        $cpl = $data['cpl'];
        $bk = $data['bk'];
        $mapping = $data['mapping'];

        $html = '<style>table{border-collapse:collapse;width:100%;font-size:9px}th,td{border:1px solid #000;padding:4px;vertical-align:top}th{background:#f2f2f2;text-align:center}h3{text-align:center}</style><h3>Pemetaan CPL–BK–MK</h3><table><thead><tr><th style="width:20px;">No</th><th>Bahan Kajian</th>';
        foreach ($cpl as $cp) {
            $html .= '<th>' . esc($cp['kode_cpl']) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        
        $no = 1;
        foreach ($bk as $bahan) {
            $html .= '<tr><td style="text-align:center;">' . $no++ . '</td><td>' . esc($bahan['nama_bk']) . '</td>';
            foreach ($cpl as $cp) {
                $listMk = $mapping[$bahan['id']][$cp['id']] ?? [];
                $isi = implode('<br>', array_map('esc', $listMk));
                $html .= '<td>' . $isi . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Matriks_CPL_BK_MK.pdf', ['Attachment' => false]);
        exit;
    }
}