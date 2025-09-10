<?php

namespace App\Controllers;

use App\Models\RpsModel;
use App\Models\MataKuliahModel;
use App\Models\DosenModel;
use App\Models\RpsReferensiModel;
use App\Models\RpsMingguanModel;
use App\Services\RpsPreviewService;

class Rps extends BaseController
{
    // LIST RPS
    public function index()
    {
        $rpsModel = new RpsModel();
        $perPage = $this->request->getGet('perPage') ?? 10;
        $currentPage = $this->request->getGet('page') ?? 1;

        $rpsList = $rpsModel
            ->select('rps.*, mata_kuliah.nama_mk')
            ->join('mata_kuliah', 'mata_kuliah.id = rps.mata_kuliah_id', 'left')
            ->orderBy('rps.id', 'desc')
            ->paginate($perPage, 'default', $currentPage);

        $pager = $rpsModel->pager;
        $db = \Config\Database::connect();

        foreach ($rpsList as &$row) {
            $pengampu = $db->table('rps_pengampu')
                ->join('dosen', 'dosen.id = rps_pengampu.dosen_id')
                ->select('dosen.nama_lengkap, rps_pengampu.peran')
                ->where('rps_pengampu.rps_id', $row['id'])
                ->where('rps_pengampu.peran', 'pengampu')
                ->get()->getResultArray();
            $row['pengampu_list'] = array_column($pengampu, 'nama_lengkap');

            $koordinator = $db->table('rps_pengampu')
                ->join('dosen', 'dosen.id = rps_pengampu.dosen_id')
                ->select('dosen.nama_lengkap')
                ->where('rps_pengampu.rps_id', $row['id'])
                ->where('rps_pengampu.peran', 'koordinator')
                ->get()->getRowArray();
            $row['koordinator_nama'] = $koordinator ? $koordinator['nama_lengkap'] : '';
        }
        unset($row);

        $data['rps'] = $rpsList;
        $data['pager'] = $pager;
        $data['perPage'] = $perPage;
        $data['currentPage'] = $currentPage;

        return view('rps/index', $data);
    }

    // TAMBAH RPS
    public function create()
    {
        $mkModel = new MataKuliahModel();
        $dosenModel = new DosenModel();
        $data['mata_kuliah'] = $mkModel->findAll();
        $data['dosen'] = $dosenModel->where('status_keaktifan', 'Aktif')->findAll();
        return view('rps/create', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $rpsModel = new RpsModel();

        $rpsModel->insert([
            'mata_kuliah_id'    => $this->request->getPost('mata_kuliah_id'),
            'semester'          => $this->request->getPost('semester'),
            'tahun_ajaran'      => $this->request->getPost('tahun_ajaran'),
            'tgl_penyusunan'    => $this->request->getPost('tgl_penyusunan'),
            'status'            => $this->request->getPost('status'),
            'catatan'           => $this->request->getPost('catatan'),
        ]);
        $rps_id = $rpsModel->getInsertID();

        $dosen_pengampu_ids = $this->request->getPost('dosen_pengampu_ids');
        $koordinator_id = $this->request->getPost('koordinator_id');

        if ($dosen_pengampu_ids) {
            foreach ($dosen_pengampu_ids as $dosen_id) {
                $db->table('rps_pengampu')->insert(['rps_id' => $rps_id, 'dosen_id' => $dosen_id, 'peran' => 'pengampu']);
            }
        }
        if ($koordinator_id) {
            $db->table('rps_pengampu')->insert(['rps_id' => $rps_id, 'dosen_id' => $koordinator_id, 'peran' => 'koordinator']);
        }

        return redirect()->to('/rps')->with('success', 'RPS berhasil ditambahkan.');
    }

    // EDIT/UPDATE RPS
    public function edit($id)
    {
        $rpsModel = new RpsModel();
        $mkModel = new MataKuliahModel();
        $dosenModel = new DosenModel();
        $db = \Config\Database::connect();

        $data['rps'] = $rpsModel->find($id);
        $data['mata_kuliah'] = $mkModel->findAll();
        $data['dosen'] = $dosenModel->where('status_keaktifan', 'Aktif')->findAll();

        $pengampu = $db->table('rps_pengampu')->where('rps_id', $id)->where('peran', 'pengampu')->get()->getResultArray();
        $data['pengampu_ids'] = array_column($pengampu, 'dosen_id');

        $koordinator = $db->table('rps_pengampu')->where('rps_id', $id)->where('peran', 'koordinator')->get()->getRowArray();
        $data['koordinator_id'] = $koordinator ? $koordinator['dosen_id'] : '';

        return view('rps/edit', $data);
    }

    public function update($id)
    {
        $db = \Config\Database::connect();
        $rpsModel = new RpsModel();

        $rpsModel->update($id, [
            'mata_kuliah_id'    => $this->request->getPost('mata_kuliah_id'),
            'semester'          => $this->request->getPost('semester'),
            'tahun_ajaran'      => $this->request->getPost('tahun_ajaran'),
            'tgl_penyusunan'    => $this->request->getPost('tgl_penyusunan'),
            'status'            => $this->request->getPost('status'),
            'catatan'           => $this->request->getPost('catatan'),
        ]);

        $db->table('rps_pengampu')->where('rps_id', $id)->delete();
        $dosen_pengampu_ids = $this->request->getPost('dosen_pengampu_ids');
        $koordinator_id = $this->request->getPost('koordinator_id');

        if ($dosen_pengampu_ids) {
            foreach ($dosen_pengampu_ids as $dosen_id) {
                $db->table('rps_pengampu')->insert(['rps_id' => $id, 'dosen_id' => $dosen_id, 'peran' => 'pengampu']);
            }
        }
        if ($koordinator_id) {
            $db->table('rps_pengampu')->insert(['rps_id' => $id, 'dosen_id' => $koordinator_id, 'peran' => 'koordinator']);
        }

        return redirect()->to('/rps')->with('success', 'RPS berhasil diupdate.');
    }

    // HAPUS RPS
    public function delete($id)
    {
        $rpsModel = new RpsModel();
        $db = \Config\Database::connect();
        $rps = $rpsModel->find($id);
        if ($rps && $rps['status'] == 'final') {
            return redirect()->back()->with('error', 'RPS status FINAL tidak bisa dihapus.');
        }
        $rpsModel->delete($id);
        $db->table('rps_pengampu')->where('rps_id', $id)->delete();
        return redirect()->to('/rps')->with('success', 'RPS berhasil dihapus.');
    }

    // REFERENSI RPS 
    public function referensi($rps_id)
    {
        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if ($rps && $rps['status'] == 'final') {
            return redirect()->back()->with('error', 'Tidak dapat mengelola referensi, status sudah FINAL.');
        }
        $referensiModel = new RpsReferensiModel();
        $data['referensi'] = $referensiModel->where('rps_id', $rps_id)->findAll();
        $data['rps_id'] = $rps_id;
        return view('rps/referensi/index', $data);
    }
    public function referensi_create($rps_id)
    {
        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if ($rps && $rps['status'] == 'final') {
            return redirect()->back()->with('error', 'Tidak dapat menambah referensi, status sudah FINAL.');
        }
        $data['rps_id'] = $rps_id;
        return view('rps/referensi/create', $data);
    }
    public function referensi_store($rps_id)
    {
        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if ($rps && $rps['status'] == 'final') {
            return redirect()->back()->with('error', 'Tidak dapat menambah referensi, status sudah FINAL.');
        }
        $referensiModel = new RpsReferensiModel();
        $referensiModel->insert($this->request->getPost() + ['rps_id' => $rps_id]);
        return redirect()->to('/rps/referensi/' . $rps_id)->with('success', 'Referensi ditambah.');
    }
    public function referensi_edit($id)
    {
        $referensiModel = new RpsReferensiModel();
        $referensi = $referensiModel->find($id);
        $rps_id = $referensi['rps_id'];
        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if ($rps && $rps['status'] == 'final') {
            return redirect()->back()->with('error', 'Tidak dapat edit referensi, status sudah FINAL.');
        }
        $data['referensi'] = $referensi;
        return view('rps/referensi/edit', $data);
    }
    public function referensi_update($id)
    {
        $referensiModel = new RpsReferensiModel();
        $referensi = $referensiModel->find($id);
        $rps_id = $referensi['rps_id'];
        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if ($rps && $rps['status'] == 'final') {
            return redirect()->back()->with('error', 'Tidak dapat edit referensi, status sudah FINAL.');
        }
        $referensiModel->update($id, $this->request->getPost());
        return redirect()->to('/rps/referensi/' . $rps_id)->with('success', 'Referensi diupdate.');
    }
    public function referensi_delete($id)
    {
        $referensiModel = new RpsReferensiModel();
        $referensi = $referensiModel->find($id);
        $rps_id = $referensi['rps_id'];
        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if ($rps && $rps['status'] == 'final') {
            return redirect()->back()->with('error', 'Tidak dapat hapus referensi, status sudah FINAL.');
        }
        $referensiModel->delete($id);
        return redirect()->to('/rps/referensi/' . $rps_id)->with('success', 'Referensi dihapus.');
    }

    // RENCANA MINGGUAN 
 public function mingguan($rps_id)
    {
        $mingguanModel = new RpsMingguanModel();
        $db = \Config\Database::connect();
        
        $mingguan = $mingguanModel->select('rps_mingguan.*, cpl.kode_cpl, cpmk.kode_cpmk, sub_cpmk.kode_sub_cpmk')
            ->join('cpl', 'cpl.id = rps_mingguan.cpl_id', 'left')
            ->join('cpmk', 'cpmk.id = rps_mingguan.cpmk_id', 'left')
            ->join('sub_cpmk', 'sub_cpmk.id = rps_mingguan.sub_cpmk_id', 'left')
            ->where('rps_mingguan.rps_id', $rps_id)
            ->orderBy('minggu', 'asc')
            ->findAll();

        $totalBobot = $db->table('rps_mingguan')->where('rps_id', $rps_id)->selectSum('bobot', 'total')->get()->getRow()->total ?? 0;

        return view('rps/mingguan/index', [
            'mingguan' => $mingguan,
            'rps_id'   => $rps_id,
            'totalBobot' => $totalBobot,
        ]);
    }

    public function mingguan_create($rps_id)
    {
        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);

        if (!$rps || ($rps && $rps['status'] == 'final')) {
            return redirect()->back()->with('error', 'Tidak dapat menambah rencana mingguan.');
        }

        $mk_id = $rps['mata_kuliah_id'];
        $db = \Config\Database::connect();

        $cpl = $db->table('cpl')
            ->select('cpl.id, cpl.kode_cpl')
            ->join('cpl_mk', 'cpl.id = cpl_mk.cpl_id')
            ->where('cpl_mk.mata_kuliah_id', $mk_id)
            ->orderBy('cpl.kode_cpl', 'ASC')
            ->get()->getResultArray();

        $totalBobotExist = $db->table('rps_mingguan')
            ->where('rps_id', $rps_id)
            ->selectSum('bobot', 'total')
            ->get()->getRow()->total ?? 0;

        return view('rps/mingguan/create', [
            'cpl'               => $cpl,
            'rps_id'            => $rps_id,
            'mk_id'             => $mk_id,
            'totalBobotExist'   => $totalBobotExist,
            'kriteria_options'  => ['Kehadiran', 'Ketepatan Jawaban Kuis', 'Ketepatan Jawaban Tugas', 'Ketepatan Jawaban UTS', 'Ketepatan Jawaban UAS', 'Hasil Praktik', 'Kualitas Presentasi', 'Hasil Proyek'],
            'instrumen_options' => ['Rubrik', 'Panduan'],
            'metode_options'    => ['Case study', 'Team Base Project', 'Small Group Discussion', 'Role-Play & Simulation', 'Discovery Learning', 'Self-Directed Learning', 'Cooperative Learning', 'Collaborative Learning', 'Contextual Learning', 'Kuliah', 'Responsi', 'Tutorial', 'Seminar atau yang setara', 'Praktikum', 'Praktik Studio', 'Praktik Bengkel', 'Praktik Lapangan', 'Penelitian', 'Pengabdian Kepada Masyarakat'],
        ]);
    }

    public function mingguan_store($rps_id)
    {
        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if (!$rps || ($rps && $rps['status'] == 'final')) {
            return redirect()->back()->with('error', 'Tidak dapat menyimpan rencana mingguan.');
        }

        $mingguanModel = new RpsMingguanModel();
        $cpl_id = $this->request->getPost('cpl_id');
        $cpmk_id = $this->request->getPost('cpmk_id');
        $sub_cpmk_id = $this->request->getPost('sub_cpmk_id');
        
        $duplikat = $mingguanModel->where('rps_id', $rps_id)
            ->where('cpl_id', $cpl_id)->where('cpmk_id', $cpmk_id)->where('sub_cpmk_id', $sub_cpmk_id)
            ->first();
        if ($duplikat) {
            return redirect()->back()->withInput()->with('error', "Kombinasi CPL–CPMK–SubCPMK ini sudah ada di Minggu ke-{$duplikat['minggu']}!");
        }

        $teknik_bobot_assoc = [];
        $teknik = $this->request->getPost('teknik_penilaian');
        $bobot_teknik = $this->request->getPost('bobot_teknik');
        if (is_array($teknik)) {
            foreach ($teknik as $k) {
                $teknik_bobot_assoc[$k] = (isset($bobot_teknik[$k]) && $bobot_teknik[$k] !== '') ? intval($bobot_teknik[$k]) : 0;
            }
        }

        $processCheckbox = function ($fieldName) {
            $values = $this->request->getPost($fieldName) ?? [];
            $otherValue = $this->request->getPost($fieldName . '_lainnya');
            if (!empty($otherValue) && in_array('Lainnya', $values)) {
                $values = array_diff($values, ['Lainnya']);
                $values[] = trim($otherValue);
            }
            return json_encode(array_values($values));
        };

        $mingguanModel->insert([
            'rps_id'              => $rps_id,
            'minggu'              => $this->request->getPost('minggu'),
            'cpl_id'              => $cpl_id,
            'cpmk_id'             => $cpmk_id,
            'sub_cpmk_id'         => $sub_cpmk_id,
            'indikator'           => json_encode(array_values(array_filter($this->request->getPost('indikator') ?? [], 'trim'))),
            'kriteria_penilaian'  => $processCheckbox('kriteria_penilaian'),
            'tahap_penilaian'     => json_encode($this->request->getPost('tahap_penilaian') ?? []),
            'teknik_penilaian'    => json_encode($teknik_bobot_assoc),
            'materi_pembelajaran' => $this->request->getPost('materi_pembelajaran'),
            'instrumen'           => $processCheckbox('instrumen'),
            'metode'              => $processCheckbox('metode'),
            'bobot'               => array_sum($teknik_bobot_assoc),
        ]);

        return redirect()->to('/rps/mingguan/' . $rps_id)->with('success', 'Rencana Mingguan berhasil ditambah.');
    }

public function mingguan_edit($id)
{
    $mingguanModel = new RpsMingguanModel();
    $rencana_mingguan = $mingguanModel->find($id);

    if (!$rencana_mingguan) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Data Rencana Mingguan tidak ditemukan: ' . $id);
    }

    $rpsModel = new RpsModel();
    $rps = $rpsModel->find($rencana_mingguan['rps_id']);
    if (!$rps || ($rps && $rps['status'] == 'final')) {
        return redirect()->back()->with('error', 'Tidak dapat mengedit rencana mingguan.');
    }

    $mk_id = $rps['mata_kuliah_id'];
    $db = \Config\Database::connect();
    
    $cpl = $db->table('cpl')
        ->select('cpl.id, cpl.kode_cpl')
        ->join('cpl_mk', 'cpl.id = cpl_mk.cpl_id')
        ->where('cpl_mk.mata_kuliah_id', $mk_id)
        ->orderBy('cpl.kode_cpl', 'ASC')
        ->get()->getResultArray();

    $cpmk_terpilih = $db->table('cpmk')->where('id', $rencana_mingguan['cpmk_id'])->get()->getRowArray();
    $subcpmk_terpilih = $db->table('sub_cpmk')->where('id', $rencana_mingguan['sub_cpmk_id'])->get()->getRowArray();

    $totalBobotExist = $db->table('rps_mingguan')
        ->where('rps_id', $rencana_mingguan['rps_id'])
        ->selectSum('bobot', 'total')
        ->get()->getRow()->total ?? 0;
 

    $kriteria_options = ['Kehadiran', 'Ketepatan Jawaban Kuis', 'Ketepatan Jawaban Tugas', 'Ketepatan Jawaban UTS', 'Ketepatan Jawaban UAS', 'Hasil Praktik', 'Kualitas Presentasi', 'Hasil Proyek'];
    $kriteria_db = json_decode($rencana_mingguan['kriteria_penilaian'] ?? '[]', true);
    if (!is_array($kriteria_db)) $kriteria_db = [];
    $kriteria_lainnya = implode(' ', array_diff($kriteria_db, $kriteria_options));

    $instrumen_options = ['Rubrik', 'Panduan'];
    $instrumen_db = json_decode($rencana_mingguan['instrumen'] ?? '[]', true);
    if (!is_array($instrumen_db)) $instrumen_db = [];
    $instrumen_lainnya = implode(' ', array_diff($instrumen_db, $instrumen_options));
    
    $metode_options = ['Case study', 'Team Base Project', 'Small Group Discussion', 'Role-Play & Simulation', 'Discovery Learning', 'Self-Directed Learning', 'Cooperative Learning', 'Collaborative Learning', 'Contextual Learning', 'Kuliah', 'Responsi', 'Tutorial', 'Seminar atau yang setara', 'Praktikum', 'Praktik Studio', 'Praktik Bengkel', 'Praktik Lapangan', 'Penelitian', 'Pengabdian Kepada Masyarakat'];
    $metode_db = json_decode($rencana_mingguan['metode'] ?? '[]', true);
    if (!is_array($metode_db)) $metode_db = [];
    $metode_lainnya = implode(' ', array_diff($metode_db, $metode_options));

    return view('rps/mingguan/edit', [
        'rencana_mingguan'  => $rencana_mingguan,
        'cpl'               => $cpl,
        'cpmk_terpilih'     => $cpmk_terpilih,
        'subcpmk_terpilih'  => $subcpmk_terpilih,
        'rps_id'            => $rencana_mingguan['rps_id'],
        'mk_id'             => $mk_id,
        'totalBobotExist'   => $totalBobotExist,
        'kriteria_options'  => $kriteria_options,
        'instrumen_options' => $instrumen_options,
        'metode_options'    => $metode_options,
        'kriteria_lainnya'  => $kriteria_lainnya,
        'instrumen_lainnya' => $instrumen_lainnya,
        'metode_lainnya'    => $metode_lainnya,
    ]);
}
    public function mingguan_update($id)
    {
        $mingguanModel = new RpsMingguanModel();
        $rencana_mingguan = $mingguanModel->find($id);
        $rps_id = $rencana_mingguan['rps_id'];

        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if (!$rps || ($rps && $rps['status'] == 'final')) {
            return redirect()->back()->with('error', 'Tidak dapat menyimpan rencana mingguan.');
        }

        $cpl_id = $this->request->getPost('cpl_id');
        $cpmk_id = $this->request->getPost('cpmk_id');
        $sub_cpmk_id = $this->request->getPost('sub_cpmk_id');
        
        $duplikat = $mingguanModel->where('rps_id', $rps_id)->where('id !=', $id)
            ->where('cpl_id', $cpl_id)->where('cpmk_id', $cpmk_id)->where('sub_cpmk_id', $sub_cpmk_id)
            ->first();
        if ($duplikat) {
            return redirect()->back()->withInput()->with('error', "Kombinasi CPL–CPMK–SubCPMK ini sudah ada di Minggu ke-{$duplikat['minggu']}!");
        }
        
        $teknik_bobot_assoc = [];
        $teknik = $this->request->getPost('teknik_penilaian');
        $bobot_teknik = $this->request->getPost('bobot_teknik');
        if (is_array($teknik)) {
            foreach ($teknik as $k) {
                $teknik_bobot_assoc[$k] = (isset($bobot_teknik[$k]) && $bobot_teknik[$k] !== '') ? intval($bobot_teknik[$k]) : 0;
            }
        }
        
        $processCheckbox = function ($fieldName) {
            $values = $this->request->getPost($fieldName) ?? [];
            $otherValue = $this->request->getPost($fieldName . '_lainnya');
            if (!empty($otherValue) && in_array('Lainnya', $values)) {
                $values = array_diff($values, ['Lainnya']);
                $values[] = trim($otherValue);
            }
            return json_encode(array_values($values));
        };

        $mingguanModel->update($id, [
            'minggu'              => $this->request->getPost('minggu'),
            'cpl_id'              => $cpl_id,
            'cpmk_id'             => $cpmk_id,
            'sub_cpmk_id'         => $sub_cpmk_id,
            'indikator'           => json_encode(array_values(array_filter($this->request->getPost('indikator') ?? [], 'trim'))),
            'kriteria_penilaian'  => $processCheckbox('kriteria_penilaian'),
            'tahap_penilaian'     => json_encode($this->request->getPost('tahap_penilaian') ?? []),
            'teknik_penilaian'    => json_encode($teknik_bobot_assoc),
            'materi_pembelajaran' => $this->request->getPost('materi_pembelajaran'),
            'instrumen'           => $processCheckbox('instrumen'),
            'metode'              => $processCheckbox('metode'),
            'bobot'               => array_sum($teknik_bobot_assoc),
        ]);

        return redirect()->to('/rps/mingguan/' . $rps_id)->with('success', 'Rencana Mingguan berhasil diupdate.');
    }

    public function mingguan_delete($id)
    {
        $mingguanModel = new RpsMingguanModel();
        $mingguan = $mingguanModel->find($id);
        $rps_id = $mingguan['rps_id'];

        $rpsModel = new RpsModel();
        $rps = $rpsModel->find($rps_id);
        if ($rps && $rps['status'] == 'final') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus rencana mingguan.');
        }
        
        $mingguanModel->delete($id);
        return redirect()->to('/rps/mingguan/' . $rps_id)->with('success', 'Rencana Mingguan berhasil dihapus.');
    }

    public function get_cpmk($mk_id, $cpl_id)
{
    $db = \Config\Database::connect();
    
    $cpmk = $db->table('cpmk')
        ->select('cpmk.id, cpmk.kode_cpmk')
        ->join('cpl_cpmk', 'cpl_cpmk.cpmk_id = cpmk.id')
        ->join('cpmk_mk', 'cpmk_mk.cpmk_id = cpmk.id')
        ->where('cpl_cpmk.cpl_id', $cpl_id)
        ->where('cpmk_mk.mata_kuliah_id', $mk_id)
        ->orderBy('cpmk.kode_cpmk', 'ASC')
        ->distinct()
        ->get()->getResultArray();
        
    return $this->response->setJSON($cpmk);
}

    public function get_subcpmk($mk_id, $cpmk_id)
    {
        $db = \Config\Database::connect();

        $subcpmk = $db->table('sub_cpmk')
            ->select('sub_cpmk.id, sub_cpmk.kode_sub_cpmk')
            ->join('sub_cpmk_mk', 'sub_cpmk_mk.sub_cpmk_id = sub_cpmk.id')
            ->where('sub_cpmk.cpmk_id', $cpmk_id)
            ->where('sub_cpmk_mk.mata_kuliah_id', $mk_id)
            ->orderBy('sub_cpmk.kode_sub_cpmk', 'ASC')
            ->distinct()
            ->get()->getResultArray();
            
        return $this->response->setJSON($subcpmk);
    }
        // PREVIEW 
    public function preview($id)
    {
        $data = RpsPreviewService::getData($id);
        return view('rps/preview', $data);
    }
}