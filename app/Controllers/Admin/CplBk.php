<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplBkModel;
use App\Models\CplModel;
use App\Models\BahanKajianModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class CplBk extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new CplBkModel();
    }
    
    private function getMatriksData()
    {
        $bkModel = new BahanKajianModel();
        $cplModel = new CplModel();

        $cplList = $cplModel->orderBy('kode_cpl', 'ASC')->findAll();
        $bkList = $bkModel->orderBy('kode_bk', 'ASC')->findAll();
        $mapping = $this->model->findAll();

        $matrix = [];
        foreach ($bkList as $bk) {
            $matrix[$bk['id']] = [
                'id' => $bk['id'],
                'kode_bk' => $bk['kode_bk'],
                'nama_bk' => $bk['nama_bk'],
                'cpl' => [],
                'jumlah' => 0
            ];
        }

        foreach ($mapping as $map) {
            if (isset($matrix[$map['bk_id']])) {
                $matrix[$map['bk_id']]['cpl'][$map['cpl_id']] = $map['id'];
                $matrix[$map['bk_id']]['jumlah']++;
            }
        }

        return ['cplList' => $cplList, 'matrix' => $matrix];
    }

    public function index()
    {
        return view('admin/cpl_bk/index', $this->getMatriksData());
    }

    public function create()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-bk')->with('error', 'Akses ditolak!');
        }

        $cplModel = new CplModel();
        $bkModel = new BahanKajianModel();

        $data['cpl'] = $cplModel->orderBy('kode_cpl', 'ASC')->findAll();
        $data['bk'] = $bkModel->orderBy('kode_bk', 'ASC')->findAll();

        return view('admin/cpl_bk/create', $data);
    }

    public function store()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-bk')->with('error', 'Akses ditolak!');
        }

        $cplBkModel = new CplBkModel();
        $cplId = $this->request->getPost('cpl_id');
        $bkIds  = $this->request->getPost('bk_id');

        if (!$cplId || !$bkIds || count($bkIds) == 0) {
            return redirect()->back()->withInput()->with('error', 'CPL dan minimal satu Bahan Kajian wajib dipilih!');
        }

        $countInsert = 0;
        foreach ($bkIds as $bkId) {
            $exist = $cplBkModel->where('cpl_id', $cplId)->where('bk_id', $bkId)->first();
            if (!$exist) {
                $cplBkModel->insert(['cpl_id' => $cplId, 'bk_id'  => $bkId]);
                $countInsert++;
            }
        }

        if ($countInsert > 0) {
            return redirect()->to('/admin/cpl-bk')->with('success', 'Pemetaan CPL ke Bahan Kajian berhasil ditambahkan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Semua pemetaan yang dipilih sudah ada!');
        }
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-bk')->with('error', 'Akses ditolak!');
        }

        $this->model->delete($id);
        return redirect()->to('/admin/cpl-bk')->with('success', 'Pemetaan berhasil dihapus.');
    }

    public function exportPdf()
    {
        $data = $this->getMatriksData();
        $cplList = $data['cplList'];
        $matrix = $data['matrix'];

        $html = '<style>table{border-collapse:collapse;width:100%;font-size:10px}th,td{border:1px solid #000;padding:5px;text-align:center}th{background:#f2f2f2}</style><h3 style="text-align:center;">Matriks Pemetaan CPL ke Bahan Kajian</h3><table><thead><tr><th>No</th><th>Kode BK</th>';
        foreach ($cplList as $cpl) {
            $html .= '<th>' . esc($cpl['kode_cpl']) . '</th>';
        }
        $html .= '<th>Jumlah</th></tr></thead><tbody>';
        
        $no = 1;
        foreach ($matrix as $bk) {
            $html .= '<tr><td>' . $no++ . '</td><td>' . esc($bk['kode_bk']) . '</td>';
            foreach ($cplList as $cpl) {
                $value = isset($bk['cpl'][$cpl['id']]) ? '✔' : '';
                $html .= '<td>' . $value . '</td>';
            }
            $html .= '<td>' . $bk['jumlah'] . '</td></tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Matriks_CPL_BK.pdf', ['Attachment' => false]);
        exit;
    }

    public function exportExcel()
    {
        $data = $this->getMatriksData();
        $cplList = $data['cplList'];
        $matrix = $data['matrix'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode BK');
        $col = 'C';
        foreach ($cplList as $cpl) {
            $sheet->setCellValue($col . '1', $cpl['kode_cpl']);
            $col++;
        }
        $sheet->setCellValue($col . '1', 'Jumlah');

        $rowNum = 2;
        $no = 1;
        foreach ($matrix as $bk) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $bk['kode_bk']);
            $col = 'C';
            foreach ($cplList as $cpl) {
                $value = isset($bk['cpl'][$cpl['id']]) ? '✔' : '';
                $sheet->setCellValue($col . $rowNum, $value);
                $col++;
            }
            $sheet->setCellValue($col . $rowNum, $bk['jumlah']);
            $rowNum++;
        }

        $lastCol = chr(ord('B') + count($cplList) + 1);
        $lastRow = $rowNum - 1;
        
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ]);
        
        $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ]);
        
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Matriks_CPL_BK.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}