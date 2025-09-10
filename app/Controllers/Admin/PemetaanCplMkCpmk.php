<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplModel;
use App\Models\CpmkModel;
use App\Models\MataKuliahModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;
use Dompdf\Options;

class PemetaanCplMkCpmk extends BaseController
{
    protected $cplModel;
    protected $cpmkModel;
    protected $mkModel;
    protected $db;

    public function __construct()
    {
        $this->cplModel = new CplModel();
        $this->cpmkModel = new CpmkModel();
        $this->mkModel = new MataKuliahModel();
        $this->db = \Config\Database::connect();
    }

    private function getQueryData()
    {
        return $this->db->table('cpl_cpmk')
            ->select(
                'cpl.id AS cpl_id, cpmk.id AS cpmk_id, cpl.kode_cpl, cpmk.kode_cpmk, GROUP_CONCAT(DISTINCT mk.nama_mk ORDER BY mk.nama_mk SEPARATOR ", ") AS mk_list'
            )
            ->join('cpl', 'cpl.id = cpl_cpmk.cpl_id')
            ->join('cpmk', 'cpmk.id = cpl_cpmk.cpmk_id')
            ->join('cpmk_mk', 'cpmk_mk.cpmk_id = cpmk.id', 'left')
            ->join('mata_kuliah mk', 'mk.id = cpmk_mk.mata_kuliah_id', 'left')
            ->groupBy(['cpl.id', 'cpmk.id'])
            ->orderBy('cpl.kode_cpl', 'asc')
            ->orderBy('cpmk.kode_cpmk', 'asc')
            ->get()->getResultArray();
    }

    public function index()
    {
        $data['title'] = 'Pemetaan CPL - CPMK - MK';
        $data['rows'] = $this->getQueryData();

        return view('admin/pemetaan_cpl_mk_cpmk/index', $data);
    }

    public function exportExcel()
    {
        $data = $this->getQueryData();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Kode CPL');
        $sheet->setCellValue('B1', 'Kode CPMK');
        $sheet->setCellValue('C1', 'Mata Kuliah');

        $rowNumber = 2;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $rowNumber, $row['kode_cpl']);
            $sheet->setCellValue('B' . $rowNumber, $row['kode_cpmk']);
            $sheet->setCellValue('C' . $rowNumber, $row['mk_list']);
            $rowNumber++;
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:C' . ($rowNumber - 1))->applyFromArray($styleArray);

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Pemetaan_CPL_MK_CPMK.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function exportPdf()
    {
        $data['rows'] = $this->getQueryData();
        
        $html = '<!DOCTYPE html><html><head><style>body{font-family:sans-serif;font-size:12px;}h3{text-align:center;}.table{width:100%;border-collapse:collapse;}.table th,.table td{border:1px solid #000;padding:8px;}.table thead th{background-color:#f2f2f2;text-align:center;}</style></head><body><h3>Pemetaan CPL - MK - CPMK</h3><table class="table"><thead><tr><th>Kode CPL</th><th>Kode CPMK</th><th>Mata Kuliah</th></tr></thead><tbody>';
        
        if (empty($data['rows'])) {
            $html .= '<tr><td colspan="3" style="text-align:center;">Tidak ada data.</td></tr>';
        } else {
            foreach ($data['rows'] as $row) {
                $html .= '<tr><td>'.esc($row['kode_cpl']).'</td><td>'.esc($row['kode_cpmk']).'</td><td>'.esc($row['mk_list']).'</td></tr>';
            }
        }
        $html .= '</tbody></table></body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Pemetaan_CPL_MK_CPMK.pdf', ['Attachment' => 0]);
        exit();
    }

    public function create()
    {
        $data['cpl_list'] = $this->cplModel->findAll();
        return view('admin/pemetaan_cpl_mk_cpmk/create', $data);
    }

    public function getMataKuliahByCpl($cpl_id)
    {
        $builder = $this->db->table('cpl_mk')
            ->select('mata_kuliah.id, mata_kuliah.nama_mk')
            ->join('mata_kuliah', 'mata_kuliah.id = cpl_mk.mata_kuliah_id')
            ->where('cpl_mk.cpl_id', $cpl_id);

        $result = $builder->get()->getResultArray();
        return $this->response->setJSON($result);
    }

    public function getCpmkByKodeCpl($kode_cpl)
    {
        $nomor = preg_replace('/[^0-9]/', '', $kode_cpl); 

        $result = $this->db->table('cpmk')
            ->select('id, kode_cpmk')
            ->like('kode_cpmk', 'CPMK' . $nomor, 'after')
            ->get()->getResultArray();

        return $this->response->setJSON($result);
    }

    public function store()
    {
        $cpl_id   = $this->request->getPost('cpl_id');
        $cpmk_id  = $this->request->getPost('cpmk_id');
        $mk_ids   = $this->request->getPost('mata_kuliah_id');

        if (!$cpl_id || !$cpmk_id || empty($mk_ids)) {
            return redirect()->back()->with('error', 'Semua field wajib diisi.');
        }

        $existsCplCpmk = $this->db->table('cpl_cpmk')
            ->where('cpl_id', $cpl_id)
            ->where('cpmk_id', $cpmk_id)
            ->get()->getRow();

        if (!$existsCplCpmk) {
            $this->db->table('cpl_cpmk')->insert([
                'cpl_id' => $cpl_id,
                'cpmk_id' => $cpmk_id,
            ]);
        }

        $duplikat = [];
        foreach ($mk_ids as $mk_id) {
            $exists = $this->db->table('cpmk_mk')
                ->where('cpmk_id', $cpmk_id)
                ->where('mata_kuliah_id', $mk_id)
                ->get()->getRow();

            if ($exists) {
                $duplikat[] = $mk_id;
            } else {
                $this->db->table('cpmk_mk')->insert([
                    'cpmk_id' => $cpmk_id,
                    'mata_kuliah_id' => $mk_id,
                ]);
            }
        }

        if (!empty($duplikat)) {
            return redirect()->back()->with('error', 'Beberapa mata kuliah sudah terhubung ke CPMK ini. Duplikat tidak disimpan.');
        }

        return redirect()->to('/admin/pemetaan-cpl-mk-cpmk')->with('success', 'Pemetaan berhasil disimpan.');
    }
    
    public function deleteGroup($cplId, $cpmkId)
    {
        $this->db->transStart();
        $this->db->table('cpmk_mk')->where('cpmk_id', $cpmkId)->delete();
        $this->db->table('cpl_cpmk')->where('cpl_id', $cplId)->where('cpmk_id', $cpmkId)->delete();
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
             return redirect()->to('/admin/pemetaan-cpl-mk-cpmk')->with('error', 'Gagal menghapus data.');
        }

        return redirect()->to('/admin/pemetaan-cpl-mk-cpmk')->with('success', 'Data berhasil dihapus.');
    }

    public function edit($cplId = null, $cpmkId = null)
    {
        if (!$cplId || !$cpmkId) {
            return redirect()->back()->with('error', 'ID tidak valid.');
        }

        $cpl = $this->db->table('cpl')->select('id, kode_cpl')->where('id', $cplId)->get()->getRowArray();
        $cpmk = $this->db->table('cpmk')->select('id, kode_cpmk')->where('id', $cpmkId)->get()->getRowArray();

        if (!$cpl || !$cpmk) {
            return redirect()->back()->with('error', 'Data CPL/CPMK tidak ditemukan.');
        }

        $mkTerkait = $this->db->table('cpmk_mk')
            ->where('cpmk_id', $cpmkId)
            ->get()->getResultArray();
        $selectedMkIds = array_column($mkTerkait, 'mata_kuliah_id');

        $allMk = $this->db->table('mata_kuliah')
            ->join('cpl_mk', 'mata_kuliah.id = cpl_mk.mata_kuliah_id')
            ->where('cpl_mk.cpl_id', $cplId)
            ->orderBy('mata_kuliah.nama_mk', 'asc')
            ->select('mata_kuliah.id, mata_kuliah.nama_mk')
            ->get()->getResultArray();

        return view('admin/pemetaan_cpl_mk_cpmk/edit', [
            'cpl' => $cpl,
            'cpmk' => $cpmk,
            'kode_cpl' => $cpl['kode_cpl'],
            'kode_cpmk' => $cpmk['kode_cpmk'],
            'cpl_id' => $cplId,
            'cpmk_id' => $cpmkId,
            'all_mk' => $allMk,
            'selected_mk_ids' => $selectedMkIds,
        ]);
    }

    public function update($cplId = null, $cpmkId = null)
    {
        $mkIds = $this->request->getPost('mata_kuliah_id'); 
    
        if (!$cplId || !$cpmkId || !is_array($mkIds) || empty($mkIds)) {
            return redirect()->back()->with('error', 'Data tidak lengkap atau tidak ada mata kuliah yang dipilih.');
        }

        $this->db->transStart();
        $this->db->table('cpmk_mk')->where('cpmk_id', $cpmkId)->delete();
        foreach ($mkIds as $mkId) {
            $this->db->table('cpmk_mk')->insert([
                'cpmk_id'        => $cpmkId,
                'mata_kuliah_id' => $mkId,
            ]);
        }
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal mengupdate data.');
        }

        return redirect()->to(base_url('admin/pemetaan-cpl-mk-cpmk'))->with('success', 'Berhasil mengubah pemetaan.');
    }
}