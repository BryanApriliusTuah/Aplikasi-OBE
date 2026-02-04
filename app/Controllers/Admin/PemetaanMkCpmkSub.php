<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{SubCpmkMkModel, SubCpmkModel, CpmkMkModel};
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class PemetaanMkCpmkSub extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function getQueryBuilder()
    {
        $subCpmkMkModel = new SubCpmkMkModel();

        return $subCpmkMkModel->select('
                sub_cpmk.id, 
                cpl.kode_cpl, 
                cpmk.kode_cpmk, 
                sub_cpmk.kode_sub_cpmk, 
                sub_cpmk.deskripsi, 
                mk.nama_mk as mata_kuliah
            ')
            ->join('sub_cpmk', 'sub_cpmk.id = sub_cpmk_mk.sub_cpmk_id')
            ->join('mata_kuliah mk', 'mk.id = sub_cpmk_mk.mata_kuliah_id')
            ->join('cpmk', 'cpmk.id = sub_cpmk.cpmk_id')
            ->join('cpl_cpmk', 'cpl_cpmk.cpmk_id = cpmk.id')
            ->join('cpl', 'cpl.id = cpl_cpmk.cpl_id')
            ->orderBy('cpl.kode_cpl', 'ASC')
            ->orderBy('cpmk.kode_cpmk', 'ASC')
            ->orderBy('sub_cpmk.kode_sub_cpmk', 'ASC')
            ->orderBy('mk.nama_mk', 'ASC');
    }

    public function index()
    {
        $rows = $this->getQueryBuilder()->findAll();

        $search = $this->request->getGet('search');
        $filters = ['search' => $search ?? ''];

        if (!empty($search)) {
            $searchLower = strtolower($search);
            $rows = array_values(array_filter($rows, function ($r) use ($searchLower) {
                return str_contains(strtolower($r['kode_cpl']), $searchLower)
                    || str_contains(strtolower($r['kode_cpmk']), $searchLower)
                    || str_contains(strtolower($r['kode_sub_cpmk'] ?? ''), $searchLower)
                    || str_contains(strtolower($r['mata_kuliah'] ?? ''), $searchLower)
                    || str_contains(strtolower($r['deskripsi'] ?? ''), $searchLower);
            }));
        }

        return view('admin/pemetaan_mk_cpmk_subcpmk/index', [
            'title'   => 'Pemetaan MK – CPMK – SubCPMK',
            'rows'    => $rows,
            'filters' => $filters
        ]);
    }

    public function create()
    {
        $cpl = $this->db->table('cpl')->orderBy('kode_cpl')->get()->getResultArray();
        $cpmk = $this->db->table('cpmk')
            ->select('cpmk.*, cpl.kode_cpl')
            ->join('cpl_cpmk', 'cpl_cpmk.cpmk_id = cpmk.id')
            ->join('cpl', 'cpl.id = cpl_cpmk.cpl_id')
            ->orderBy('kode_cpmk')
            ->get()->getResultArray();
        $mk = $this->db->table('mata_kuliah')->orderBy('kode_mk')->get()->getResultArray();

        return view('admin/pemetaan_mk_cpmk_subcpmk/create', compact('cpl', 'cpmk', 'mk'));
    }

    public function getCpmkByCpl($kodeCpl)
    {
        return $this->response->setJSON(
            $this->db->table('cpmk')->like('kode_cpmk', $kodeCpl)->get()->getResult()
        );
    }

    public function getMkByCpmk($cpmkId)
    {
        return $this->response->setJSON(
            $this->db->table('cpmk_mk')
                ->select('mata_kuliah.id, mata_kuliah.kode_mk, mata_kuliah.nama_mk')
                ->join('mata_kuliah', 'mata_kuliah.id = cpmk_mk.mata_kuliah_id')
                ->where('cpmk_mk.cpmk_id', $cpmkId)
                ->get()->getResult()
        );
    }

    public function getNextSuffix($cpmkId = null)
    {
        if (!$cpmkId) {
            return $this->response->setJSON(['error' => 'CPMK ID tidak valid.'])->setStatusCode(400);
        }

        $cpmk = $this->db->table('cpmk')->where('id', $cpmkId)->get()->getRow();
        if (!$cpmk) {
            return $this->response->setJSON(['next_suffix' => 1]);
        }
        $cpmkCodeNumber = str_replace('CPMK', '', $cpmk->kode_cpmk);
        $prefix = 'SubCPMK' . $cpmkCodeNumber;

        $subCpmks = $this->db->table('sub_cpmk')
            ->select('kode_sub_cpmk')
            ->where('cpmk_id', $cpmkId)
            ->like('kode_sub_cpmk', $prefix, 'after')
            ->get()
            ->getResultArray();

        if (empty($subCpmks)) {
            return $this->response->setJSON(['next_suffix' => 1]);
        }

        $existingSuffixes = [];
        foreach ($subCpmks as $sub) {
            $suffixStr = str_replace($prefix, '', $sub['kode_sub_cpmk']);
            if (is_numeric($suffixStr)) {
                $existingSuffixes[] = (int)$suffixStr;
            }
        }

        if (empty($existingSuffixes)) {
            return $this->response->setJSON(['next_suffix' => 1]);
        }
        
        sort($existingSuffixes);
        $nextSuffix = 1;
        foreach ($existingSuffixes as $suffix) {
            if ($nextSuffix == $suffix) {
                $nextSuffix++;
            } else {
                break;
            }
        }

        return $this->response->setJSON(['next_suffix' => $nextSuffix]);
    }

    public function store()
    {
        $cpmkId     = $this->request->getPost('cpmk_id');
        $kodeSuffix = trim($this->request->getPost('kode_suffix'));
        $deskripsi  = trim($this->request->getPost('deskripsi'));
        $mkIds      = $this->request->getPost('mata_kuliah_id');

        if (empty($cpmkId) || empty($kodeSuffix) || empty($deskripsi)) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        if (empty($mkIds) || !is_array($mkIds)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu mata kuliah.');
        }

        $cpmk = $this->db->table('cpmk')->where('id', $cpmkId)->get()->getRowArray();
        if (!$cpmk) return redirect()->back()->withInput()->with('error', 'CPMK tidak ditemukan.');

        $kodeSubCpmk = 'SubCPMK' . str_replace('CPMK', '', $cpmk['kode_cpmk']) . $kodeSuffix;

        $exist = $this->db->table('sub_cpmk')
            ->where(['kode_sub_cpmk' => $kodeSubCpmk, 'cpmk_id' => $cpmkId])
            ->get()->getRow();

        if ($exist) return redirect()->back()->withInput()->with('error', 'Kode SubCPMK ini sudah digunakan.');

        $this->db->table('sub_cpmk')->insert([
            'cpmk_id'        => $cpmkId,
            'kode_sub_cpmk'  => $kodeSubCpmk,
            'deskripsi'      => $deskripsi,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        $subCpmkId = $this->db->insertID();
        foreach ($mkIds as $mkId) {
            $this->db->table('sub_cpmk_mk')->insert([
                'sub_cpmk_id'    => $subCpmkId,
                'mata_kuliah_id' => $mkId,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('/admin/pemetaan-mk-cpmk-sub')->with('success', 'SubCPMK berhasil disimpan.');
    }

    public function edit($id)
    {
        $subcpmk = (new SubCpmkModel())
            ->select('sub_cpmk.*, cpmk.id AS cpmk_id, cpmk.kode_cpmk')
            ->join('cpmk', 'cpmk.id = sub_cpmk.cpmk_id')
            ->where('sub_cpmk.id', $id)->first();

        if (!$subcpmk) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        $mkTerkait = (new CpmkMkModel())
            ->select('mata_kuliah.*')
            ->join('mata_kuliah', 'mata_kuliah.id = cpmk_mk.mata_kuliah_id')
            ->where('cpmk_mk.cpmk_id', $subcpmk['cpmk_id'])
            ->findAll();

        $mkTerpilih = (new SubCpmkMkModel())
            ->where('sub_cpmk_id', $id)
            ->findColumn('mata_kuliah_id');

        return view('admin/pemetaan_mk_cpmk_subcpmk/edit', [
            'subcpmk'     => $subcpmk,
            'mkTerkait'   => $mkTerkait,
            'mkTerpilih'  => $mkTerpilih,
            'title'       => 'Edit SubCPMK'
        ]);
    }

    public function update($id)
    {
        $deskripsi = trim($this->request->getPost('deskripsi'));
        $mkIds     = $this->request->getPost('mata_kuliah_id') ?? [];

        if (empty($deskripsi)) {
            return redirect()->back()->withInput()->with('error', 'Deskripsi tidak boleh kosong.');
        }

        if (count($mkIds) < 1) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu mata kuliah.');
        }

        (new SubCpmkModel())->update($id, [ 'deskripsi' => $deskripsi ]);

        $subCpmkMkModel = new SubCpmkMkModel();
        $subCpmkMkModel->where('sub_cpmk_id', $id)->delete();

        foreach ($mkIds as $mkId) {
            $subCpmkMkModel->insert([
                'sub_cpmk_id'    => $id,
                'mata_kuliah_id' => $mkId,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to(base_url('admin/pemetaan-mk-cpmk-sub'))->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        if (!$this->db->table('sub_cpmk')->where('id', $id)->get()->getRow()) {
            return redirect()->to(base_url('admin/pemetaan-mk-cpmk-sub'))->with('error', 'Data SubCPMK tidak ditemukan.');
        }

        $this->db->transStart();
        $this->db->table('rps_mingguan')->where('sub_cpmk_id', $id)->delete();
        $this->db->table('sub_cpmk_mk')->where('sub_cpmk_id', $id)->delete();
        $this->db->table('sub_cpmk')->where('id', $id)->delete();
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            session()->setFlashdata('error', 'Gagal menghapus data.');
        } else {
            session()->setFlashdata('success', 'Data SubCPMK dan semua data terkait berhasil dihapus.');
        }
        
        return redirect()->to(base_url('admin/pemetaan-mk-cpmk-sub'));
    }

    public function exportExcel()
    {
        $data = $this->getQueryBuilder()->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode CPL');
        $sheet->setCellValue('C1', 'Kode CPMK');
        $sheet->setCellValue('D1', 'Nama Mata Kuliah');
        $sheet->setCellValue('E1', 'Kode SubCPMK');
        $sheet->setCellValue('F1', 'Deskripsi');

        $rowNumber = 2;
        foreach ($data as $index => $row) {
            $sheet->setCellValue('A' . $rowNumber, $index + 1);
            $sheet->setCellValue('B' . $rowNumber, $row['kode_cpl']);
            $sheet->setCellValue('C' . $rowNumber, $row['kode_cpmk']);
            $sheet->setCellValue('D' . $rowNumber, $row['mata_kuliah']);
            $sheet->setCellValue('E' . $rowNumber, $row['kode_sub_cpmk']);
            $sheet->setCellValue('F' . $rowNumber, $row['deskripsi']);
            $rowNumber++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Pemetaan_MK_CPMK_SubCPMK.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function exportPdf()
    {
        $rows = $this->getQueryBuilder()->findAll();
        
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Pemetaan MK CPMK SubCPMK</title><style>body { font-family: sans-serif; font-size: 10px; } .table { width: 100%; border-collapse: collapse; } .table th, .table td { border: 1px solid #000; padding: 5px; text-align: left; } .table thead th { background-color: #f2f2f2; text-align: center; } .text-center { text-align: center; }</style></head><body><h3 class="text-center">Pemetaan MK – CPMK – SubCPMK</h3><table class="table"><thead><tr><th>No</th><th>Kode CPL</th><th>Kode CPMK</th><th>Nama Mata Kuliah</th><th>Kode SubCPMK</th><th>Deskripsi</th></tr></thead><tbody>';
        
        if (empty($rows)) {
            $html .= '<tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>';
        } else {
            $no = 1;
            foreach ($rows as $row) {
                $html .= '<tr><td class="text-center">' . $no++ . '</td><td>' . esc($row['kode_cpl']) . '</td><td>' . esc($row['kode_cpmk']) . '</td><td>' . esc($row['mata_kuliah']) . '</td><td>' . esc($row['kode_sub_cpmk']) . '</td><td>' . esc($row['deskripsi']) . '</td></tr>';
            }
        }

        $html .= '</tbody></table></body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Pemetaan_MK_CPMK_SubCPMK.pdf', ['Attachment' => 0]);
        exit();
    }
}