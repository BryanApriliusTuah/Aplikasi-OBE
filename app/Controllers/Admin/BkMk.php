<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BkMkModel;
use App\Models\BahanKajianModel;
use App\Models\MataKuliahModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class BkMk extends BaseController
{
    public function index()
    {
        $bkModel   = new BahanKajianModel();
        $bkmkModel = new BkMkModel();

        $bkList = $bkModel->orderBy('kode_bk', 'ASC')->findAll();

        $data = [];
        foreach ($bkList as $bk) {
            $mks = $bkmkModel
                ->select('mk.nama_mk')
                ->join('mata_kuliah mk', 'mk.id = bk_mk.mata_kuliah_id')
                ->where('bk_mk.bahan_kajian_id', $bk['id'])
                ->orderBy('mk.nama_mk', 'ASC')
                ->findAll();
            
            if (!empty($mks)) {
                $mkNames = array_column($mks, 'nama_mk');
                $data[] = [
                    'bk_id'   => $bk['id'],
                    'kode_bk' => $bk['kode_bk'],
                    'nama_bk' => $bk['nama_bk'],
                    'mk_list' => $mkNames,
                ];
            }
        }

        return view('admin/bk_mk/index', ['data' => $data]);
    }

    public function create()
    {
        if (session('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak!');
        }
        $bkModel = new BahanKajianModel();
        $mkModel = new MataKuliahModel();

        return view('admin/bk_mk/create', [
            'bk' => $bkModel->orderBy('kode_bk', 'ASC')->findAll(),
            'mk' => $mkModel->orderBy('kode_mk', 'ASC')->findAll(),
            'selectedBkId' => old('bahan_kajian_id'),
            'selectedMkId' => old('mata_kuliah_id') ?? [],
        ]);
    }

    public function store()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bkmk')->with('error', 'Akses ditolak!');
        }

        $bkId = $this->request->getPost('bahan_kajian_id');
        $mkIds = $this->request->getPost('mata_kuliah_id');

        if (!$bkId || !$mkIds || count($mkIds) == 0) {
            return redirect()->back()->withInput()->with('error', 'Bahan Kajian dan minimal satu Mata Kuliah wajib dipilih!');
        }

        $model = new BkMkModel();
        $countInserted = 0;
        foreach ($mkIds as $mkId) {
            $exist = $model->where('bahan_kajian_id', $bkId)
                           ->where('mata_kuliah_id', $mkId)
                           ->first();
            if (!$exist) {
                $model->insert([
                    'bahan_kajian_id' => $bkId,
                    'mata_kuliah_id'  => $mkId
                ]);
                $countInserted++;
            }
        }

        if ($countInserted > 0) {
            return redirect()->to('/admin/bkmk')->with('success', 'Pemetaan BK ke MK berhasil disimpan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Pemetaan sudah ada semua!');
        }
    }

    public function edit($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bkmk')->with('error', 'Akses ditolak!');
        }

        $bkModel = new BahanKajianModel();
        $mkModel = new MataKuliahModel();
        $bkmkModel = new BkMkModel();

        $bk = $bkModel->find($id);
        if (!$bk) {
            return redirect()->to('/admin/bkmk')->with('error', 'Data BK tidak ditemukan.');
        }

        $mapped = $bkmkModel->where('bahan_kajian_id', $id)->findAll();
        $selectedMkId = array_column($mapped, 'mata_kuliah_id');

        return view('admin/bk_mk/edit', [
            'bk' => $bk,
            'mk' => $mkModel->orderBy('kode_mk', 'ASC')->findAll(),
            'selectedMkId' => $selectedMkId,
        ]);
    }

    public function update($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bkmk')->with('error', 'Akses ditolak!');
        }

        $mkIds = $this->request->getPost('mata_kuliah_id');
        if (!$mkIds || count($mkIds) == 0) {
            return redirect()->back()->withInput()->with('error', 'Minimal satu Mata Kuliah wajib dipilih!');
        }

        $model = new BkMkModel();
        $model->where('bahan_kajian_id', $id)->delete();

        foreach ($mkIds as $mkId) {
            $model->insert([
                'bahan_kajian_id' => $id,
                'mata_kuliah_id'  => $mkId,
            ]);
        }

        return redirect()->to('/admin/bkmk')->with('success', 'Pemetaan BK ke MK berhasil diupdate!');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bkmk')->with('error', 'Akses ditolak!');
        }

        $model = new BkMkModel();
        $model->where('bahan_kajian_id', $id)->delete();

        return redirect()->to('/admin/bkmk')->with('success', 'Semua pemetaan untuk BK ini berhasil dihapus.');
    }

    private function getMatriksData()
    {
        $bkModel   = new BahanKajianModel();
        $mkModel   = new MataKuliahModel();
        $bkmkModel = new BkMkModel();

        $bkList = $bkModel->orderBy('kode_bk', 'ASC')->findAll();
        $mkList = $mkModel->orderBy('kode_mk', 'ASC')->findAll();

        $allMapping = $bkmkModel->findAll();
        $matrix = [];
        foreach ($allMapping as $row) {
            $matrix[$row['mata_kuliah_id']][$row['bahan_kajian_id']] = true;
        }

        return ['bkList' => $bkList, 'mkList' => $mkList, 'matrix' => $matrix];
    }
    
    public function matriks()
    {
        return view('admin/bk_mk/matriks', $this->getMatriksData());
    }
    
    public function exportExcel()
    {
        $data = $this->getMatriksData();
        $bkList = $data['bkList'];
        $mkList = $data['mkList'];
        $matrix = $data['matrix'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode MK');
        $sheet->setCellValue('C1', 'Nama MK');
        $bkCols = [];
        foreach ($bkList as $i => $bk) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 4);
            $sheet->setCellValue($col . '1', $bk['kode_bk']);
            $bkCols[$bk['id']] = $col;
        }

        $row = 2;
        $no = 1;
        foreach ($mkList as $mk) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $mk['kode_mk']);
            $sheet->setCellValue('C' . $row, $mk['nama_mk']);
            foreach ($bkList as $bk) {
                $col = $bkCols[$bk['id']];
                $isi = !empty($matrix[$mk['id']][$bk['id']]) ? '✔' : '';
                $sheet->setCellValue($col . $row, $isi);
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            $row++;
        }

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($bkList) + 3);
        $lastRow = $row - 1;
        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D2EFFF']],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ]);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Matriks_BK_MK.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $data = $this->getMatriksData();
        $bkList = $data['bkList'];
        $mkList = $data['mkList'];
        $matrix = $data['matrix'];

        $html = '<style>table{border-collapse:collapse;width:100%;font-size:9px}th,td{border:1px solid #000;padding:4px;text-align:center}th{background:#f2f2f2}</style><h3>Matriks Bahan Kajian ke Mata Kuliah</h3><table><thead><tr><th>No</th><th>Kode MK</th><th>Nama MK</th>';
        foreach($bkList as $bk) {
            $html .= '<th>' . esc($bk['kode_bk']) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        
        $no = 1;
        foreach($mkList as $mk) {
            $html .= '<tr><td>'.$no++.'</td><td style="text-align:left;white-space:nowrap;">'.esc($mk['kode_mk']).'</td><td style="text-align:left;white-space:normal;">'.esc($mk['nama_mk']).'</td>';
            foreach($bkList as $bk) {
                $isi = !empty($matrix[$mk['id']][$bk['id']]) ? '✔' : '';
                $html .= '<td>'.$isi.'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Matriks_BK_MK.pdf', ['Attachment' => false]);
        exit;
    }
}