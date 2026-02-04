<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplMkModel;
use App\Models\CplModel;
use App\Models\MataKuliahModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class CplMk extends BaseController
{
    private function getMatriksData()
    {
        $mkModel    = new MataKuliahModel();
        $cplModel   = new CplModel();
        $cplmkModel = new CplMkModel();

        $mataKuliah = $mkModel->orderBy('kode_mk', 'ASC')->findAll();
        $cpl = $cplModel->orderBy('kode_cpl', 'ASC')->findAll();

        $pemetaan = [];
        $allMapping = $cplmkModel->findAll();
        foreach ($allMapping as $map) {
            $pemetaan[$map['mata_kuliah_id']][$map['cpl_id']] = true;
        }

        return ['mataKuliah' => $mataKuliah, 'cpl' => $cpl, 'pemetaan' => $pemetaan];
    }

    public function index()
    {
        $data = $this->getMatriksData();

        $search = $this->request->getGet('search');
        $data['filters'] = ['search' => $search ?? ''];

        if (!empty($search)) {
            $searchLower = strtolower($search);
            $data['mataKuliah'] = array_values(array_filter($data['mataKuliah'], function ($mk) use ($searchLower) {
                return str_contains(strtolower($mk['kode_mk']), $searchLower)
                    || str_contains(strtolower($mk['nama_mk']), $searchLower);
            }));
        }

        return view('admin/cpl_mk/index', $data);
    }

    public function create()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-mk')->with('error', 'Akses ditolak!');
        }

        $mkModel  = new MataKuliahModel();
        $cplModel = new CplModel();

        return view('admin/cpl_mk/create', [
            'mk'  => $mkModel->orderBy('kode_mk', 'ASC')->findAll(),
            'cpl' => $cplModel->orderBy('kode_cpl', 'ASC')->findAll()
        ]);
    }

    public function store()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-mk')->with('error', 'Akses ditolak!');
        }

        $mkIds = $this->request->getPost('mata_kuliah_id');
        $cplIds = $this->request->getPost('cpl_id');

        if (!$mkIds || !is_array($mkIds) || count($mkIds) == 0 || !$cplIds || count($cplIds) == 0) {
            return redirect()->back()->withInput()->with('error', 'Minimal satu Mata Kuliah dan satu CPL wajib dipilih!');
        }

        $model = new CplMkModel();
        $inserted = 0;
        foreach ($mkIds as $mkId) {
            foreach ($cplIds as $cplId) {
                $exist = $model->where('mata_kuliah_id', $mkId)
                               ->where('cpl_id', $cplId)
                               ->first();
                if (!$exist) {
                    $model->insert([
                        'mata_kuliah_id' => $mkId,
                        'cpl_id'         => $cplId
                    ]);
                    $inserted++;
                }
            }
        }

        if ($inserted > 0) {
            return redirect()->to('/admin/cpl-mk')->with('success', 'Pemetaan CPL ke MK berhasil disimpan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Tidak ada pemetaan baru yang ditambahkan!');
        }
    }

    public function delete($mkId, $cplId)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl-mk')->with('error', 'Akses ditolak!');
        }
        
        $model = new CplMkModel();
        $deleted = $model->where('mata_kuliah_id', $mkId)->where('cpl_id', $cplId)->delete();

        if ($deleted) {
            session()->setFlashdata('success', 'Pemetaan berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data!');
        }
        return redirect()->to('/admin/cpl-mk');
    }
    
    public function exportExcel()
    {
        $data = $this->getMatriksData();
        $mkList = $data['mataKuliah'];
        $cplList = $data['cpl'];
        $matriks = $data['pemetaan'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode MK');
        $sheet->setCellValue('C1', 'Nama Mata Kuliah');
        $col = 'D';
        foreach ($cplList as $cpl) {
            $sheet->setCellValue($col . '1', $cpl['kode_cpl']);
            $col++;
        }

        $rowNum = 2;
        $no = 1;
        foreach ($mkList as $mk) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $mk['kode_mk']);
            $sheet->setCellValue('C' . $rowNum, $mk['nama_mk']);
            $col = 'D';
            foreach ($cplList as $cpl) {
                $value = isset($matriks[$mk['id']][$cpl['id']]) ? '✔' : '';
                $sheet->setCellValue($col . $rowNum, $value);
                $sheet->getStyle($col . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            $rowNum++;
        }

        $lastCol = chr(ord('C') + count($cplList));
        $lastRow = $rowNum - 1;
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true], 'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Matriks_CPL_MK.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    public function exportPdf()
    {
        $data = $this->getMatriksData();
        $mkList = $data['mataKuliah'];
        $cplList = $data['cpl'];
        $matriks = $data['pemetaan'];

        $html = '<style>table{border-collapse:collapse;width:100%;font-size:10px}th,td{border:1px solid #000;padding:5px;text-align:center}th{background:#f2f2f2}</style><h3 style="text-align:center;">Matriks CPL ke Mata Kuliah</h3><table><thead><tr><th>No</th><th>Kode MK</th><th>Nama MK</th>';
        foreach ($cplList as $cpl) {
            $html .= '<th>' . esc($cpl['kode_cpl']) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        
        $no = 1;
        foreach ($mkList as $mk) {
            $html .= '<tr><td>' . $no++ . '</td><td style="text-align:left;white-space:nowrap;">' . esc($mk['kode_mk']) . '</td><td style="text-align:left;white-space:normal;">' . esc($mk['nama_mk']) . '</td>';
            foreach ($cplList as $cpl) {
                $value = isset($matriks[$mk['id']][$cpl['id']]) ? '✔' : '';
                $html .= '<td>' . $value . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Matriks_CPL_MK.pdf', ['Attachment' => false]);
        exit;
    }
}