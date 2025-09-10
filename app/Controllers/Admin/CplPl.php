<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplPlModel;
use App\Models\CplModel;
use App\Models\ProfilLulusanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class CplPl extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new CplPlModel();
    }

    private function getMatriksData()
    {
        $cplModel = new CplModel();
        $plModel = new ProfilLulusanModel();
        $cplPlModel = $this->model;

        $cplList = $cplModel->orderBy('kode_cpl', 'ASC')->findAll();
        $plList = $plModel->orderBy('kode_pl', 'ASC')->findAll();
        $mapping = $cplPlModel->findAll();

        $matriks = [];
        foreach ($mapping as $row) {
            $matriks[$row['cpl_id']][$row['pl_id']] = $row['id'];
        }
        
        return ['cplList' => $cplList, 'plList' => $plList, 'matriks' => $matriks];
    }

    public function index()
    {
        return view('admin/cpl_pl/index', $this->getMatriksData());
    }

    public function create()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-pl')->with('error', 'Akses ditolak!');
        }

        $cplModel = new CplModel();
        $plModel = new ProfilLulusanModel();

        $data['cpl'] = $cplModel->orderBy('kode_cpl', 'ASC')->findAll();
        $data['pl'] = $plModel->orderBy('kode_pl', 'ASC')->findAll();

        return view('admin/cpl_pl/create', $data);
    }

    public function store()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-pl')->with('error', 'Akses ditolak!');
        }

        $cplId = $this->request->getPost('cpl_id');
        $plIds = $this->request->getPost('pl_id');

        if (!$cplId || empty($plIds) || !is_array($plIds)) {
            return redirect()->back()->withInput()->with('error', 'CPL dan minimal satu Profil Lulusan wajib dipilih.');
        }

        $model = new CplPlModel();
        $countInsert = 0;
        foreach ($plIds as $plId) {
            $exist = $model->where('cpl_id', $cplId)->where('pl_id', $plId)->first();
            if (!$exist) {
                $model->insert(['cpl_id' => $cplId, 'pl_id'  => $plId]);
                $countInsert++;
            }
        }

        if ($countInsert > 0) {
            return redirect()->to('/admin/cpl-pl')->with('success', 'Pemetaan CPL ke PL berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Tidak ada pemetaan baru yang ditambahkan (data kemungkinan sudah ada).');
        }
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-pl')->with('error', 'Akses ditolak!');
        }

        $this->model->delete($id);
        session()->setFlashdata('success', 'Pemetaan berhasil dihapus!');
        return redirect()->to('/admin/cpl-pl');
    }
        
    public function exportExcel()
    {
        $data = $this->getMatriksData();
        $cplList = $data['cplList'];
        $plList = $data['plList'];
        $matriks = $data['matriks'];
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode CPL');
        $col = 'C';
        foreach ($plList as $pl) {
            $sheet->setCellValue($col . '1', $pl['kode_pl']);
            $col++;
        }

        $rowNum = 2;
        $no = 1;
        foreach ($cplList as $cpl) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $cpl['kode_cpl']);
            $col = 'C';
            foreach ($plList as $pl) {
                $value = isset($matriks[$cpl['id']][$pl['id']]) ? '✔' : '';
                $sheet->setCellValue($col . $rowNum, $value);
                $sheet->getStyle($col . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            $rowNum++;
        }
        
        $lastCol = chr(ord('B') + count($plList));
        $lastRow = $rowNum - 1;
        
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D2EFFF']]
        ]);

        $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Matriks_CPL_PL.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    public function exportPdf()
    {
        $data = $this->getMatriksData();
        $cplList = $data['cplList'];
        $plList = $data['plList'];
        $matriks = $data['matriks'];

        $html = '<style>table{border-collapse:collapse;width:100%;font-size:11px}th,td{border:1px solid #000;padding:5px;text-align:center}th{background:#f2f2f2}h3{text-align:center}</style><h3>Matriks Pemetaan CPL ke Profil Lulusan</h3><table><thead><tr><th>No</th><th>Kode CPL</th>';
        foreach ($plList as $pl) {
            $html .= '<th>' . esc($pl['kode_pl']) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        
        $no = 1;
        foreach ($cplList as $cpl) {
            $html .= '<tr><td>' . $no++ . '</td><td>' . esc($cpl['kode_cpl']) . '</td>';
            foreach ($plList as $pl) {
                $value = isset($matriks[$cpl['id']][$pl['id']]) ? '✔' : '';
                $html .= '<td>' . $value . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Matriks_CPL_PL.pdf', ['Attachment' => false]);
        exit;
    }
}