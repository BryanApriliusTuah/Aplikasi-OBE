<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CpmkModel;
use App\Models\NilaiCpmkMahasiswaModel;
use App\Models\MataKuliahModel;
use App\Models\MengajarModel;

class CapaianCpmk extends BaseController
{
    protected $cpmkModel;
    protected $nilaiCpmkModel;
    protected $jadwalMengajarModel;
    protected $mataKuliahModel;

    public function __construct()
    {
        $this->cpmkModel = new CpmkModel();
        $this->nilaiCpmkModel = new NilaiCpmkMahasiswaModel();
        $this->jadwalMengajarModel = new MengajarModel();
        $this->mataKuliahModel = new MataKuliahModel();
    }

    public function index()
    {
        // Check user role
        if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Capaian CPMK',
            'mataKuliah' => $this->mataKuliahModel->findAll(),
            'tahunAkademik' => $this->getTahunAkademik()
        ];

        return view('admin/capaian_cpmk/index', $data);
    }

    public function getChartData()
    {
        $mataKuliahId = $this->request->getGet('mata_kuliah_id');
        $tahunAkademik = $this->request->getGet('tahun_akademik');
        $kelas = $this->request->getGet('kelas');

        if (!$mataKuliahId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mata kuliah harus dipilih'
            ]);
        }

        // Get jadwal mengajar based on filters
        $jadwalBuilder = $this->jadwalMengajarModel
            ->where('mata_kuliah_id', $mataKuliahId);

        if ($tahunAkademik) {
            $jadwalBuilder->where('tahun_akademik', $tahunAkademik);
        }

        if ($kelas) {
            $jadwalBuilder->where('kelas', $kelas);
        }

        $jadwal = $jadwalBuilder->first();

        if (!$jadwal) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jadwal mengajar tidak ditemukan'
            ]);
        }

        // Get all CPMK for this mata kuliah
        $db = \Config\Database::connect();
        $builder = $db->table('cpmk_mk');
        $cpmkList = $builder
            ->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
            ->join('cpmk', 'cpmk.id = cpmk_mk.cpmk_id')
            ->where('cpmk_mk.mata_kuliah_id', $mataKuliahId)
            ->orderBy('cpmk.kode_cpmk', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($cpmkList)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada CPMK untuk mata kuliah ini'
            ]);
        }

        // Calculate average for each CPMK
        $chartData = [
            'labels' => [],
            'data' => [],
            'details' => []
        ];

        foreach ($cpmkList as $cpmk) {
            $avgBuilder = $db->table('nilai_cpmk_mahasiswa');
            $result = $avgBuilder
                ->select('AVG(nilai_cpmk) as rata_rata, COUNT(*) as jumlah_mahasiswa')
                ->where('cpmk_id', $cpmk['id'])
                ->where('jadwal_mengajar_id', $jadwal['id'])
                ->get()
                ->getRowArray();

            $average = $result['rata_rata'] ? round($result['rata_rata'], 2) : 0;
            $jumlahMhs = $result['jumlah_mahasiswa'] ?? 0;

            $chartData['labels'][] = $cpmk['kode_cpmk'];
            $chartData['data'][] = $average;
            $chartData['details'][] = [
                'cpmk_id' => $cpmk['id'],
                'kode_cpmk' => $cpmk['kode_cpmk'],
                'deskripsi' => $cpmk['deskripsi'],
                'rata_rata' => $average,
                'jumlah_mahasiswa' => $jumlahMhs
            ];
        }

        // Get mata kuliah info
        $mataKuliah = $this->mataKuliahModel->find($mataKuliahId);

        return $this->response->setJSON([
            'success' => true,
            'chartData' => $chartData,
            'mataKuliah' => $mataKuliah,
            'jadwal' => $jadwal
        ]);
    }

    public function getDetailData()
    {
        $mataKuliahId = $this->request->getGet('mata_kuliah_id');
        $tahunAkademik = $this->request->getGet('tahun_akademik');
        $kelas = $this->request->getGet('kelas');
        $cpmkId = $this->request->getGet('cpmk_id');

        if (!$mataKuliahId || !$cpmkId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        // Get jadwal mengajar
        $jadwalBuilder = $this->jadwalMengajarModel
            ->where('mata_kuliah_id', $mataKuliahId);

        if ($tahunAkademik) {
            $jadwalBuilder->where('tahun_akademik', $tahunAkademik);
        }

        if ($kelas) {
            $jadwalBuilder->where('kelas', $kelas);
        }

        $jadwal = $jadwalBuilder->first();

        if (!$jadwal) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ]);
        }

        // Get detail nilai mahasiswa
        $db = \Config\Database::connect();
        $builder = $db->table('nilai_cpmk_mahasiswa');
        $nilaiDetail = $builder
            ->select('mahasiswa.nim, mahasiswa.nama_lengkap, nilai_cpmk_mahasiswa.nilai_cpmk')
            ->join('mahasiswa', 'mahasiswa.id = nilai_cpmk_mahasiswa.mahasiswa_id')
            ->where('nilai_cpmk_mahasiswa.cpmk_id', $cpmkId)
            ->where('nilai_cpmk_mahasiswa.jadwal_mengajar_id', $jadwal['id'])
            ->orderBy('mahasiswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Get CPMK info
        $cpmk = $this->cpmkModel->find($cpmkId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $nilaiDetail,
            'cpmk' => $cpmk
        ]);
    }

    private function getTahunAkademik()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('jadwal_mengajar');
        $result = $builder
            ->select('tahun_akademik')
            ->distinct()
            ->orderBy('tahun_akademik', 'DESC')
            ->get()
            ->getResultArray();

        return array_column($result, 'tahun_akademik');
    }

    public function getKelasByMataKuliah()
    {
        $mataKuliahId = $this->request->getGet('mata_kuliah_id');
        $tahunAkademik = $this->request->getGet('tahun_akademik');

        if (!$mataKuliahId) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('jadwal_mengajar');
        $builder->select('kelas')
            ->where('mata_kuliah_id', $mataKuliahId);

        if ($tahunAkademik) {
            $builder->where('tahun_akademik', $tahunAkademik);
        }

        $result = $builder
            ->distinct()
            ->orderBy('kelas', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(array_column($result, 'kelas'));
    }
}