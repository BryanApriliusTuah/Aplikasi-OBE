<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplModel;
use App\Models\MahasiswaModel;
use App\Models\NilaiCpmkMahasiswaModel;

class CapaianCpl extends BaseController
{
    protected $cplModel;
    protected $mahasiswaModel;
    protected $nilaiCpmkModel;

    public function __construct()
    {
        $this->cplModel = new CplModel();
        $this->mahasiswaModel = new MahasiswaModel();
        $this->nilaiCpmkModel = new NilaiCpmkMahasiswaModel();
    }

    public function index()
    {
        // Check user role
        if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Capaian CPL',
            'programStudi' => ['Teknik Informatika', 'Sistem Informasi', 'Teknik Komputer'],
            'tahunAngkatan' => $this->getTahunAngkatan(),
            'mahasiswa' => [] // Will be loaded via AJAX
        ];

        return view('admin/capaian_cpl/index', $data);
    }

    public function getChartData()
    {
        $mahasiswaId = $this->request->getGet('mahasiswa_id');
        $programStudi = $this->request->getGet('program_studi');
        $tahunAngkatan = $this->request->getGet('tahun_angkatan');

        if (!$mahasiswaId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mahasiswa harus dipilih'
            ]);
        }

        // Get mahasiswa info
        $mahasiswa = $this->mahasiswaModel->find($mahasiswaId);
        if (!$mahasiswa) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ]);
        }

        // Get all CPL
        $db = \Config\Database::connect();
        $cplList = $db->table('cpl')
            ->orderBy('kode_cpl', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($cplList)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data CPL'
            ]);
        }

        // Calculate CPL achievement for the student
        $chartData = [
            'labels' => [],
            'data' => [],
            'details' => []
        ];

        foreach ($cplList as $cpl) {
            // Get all CPMK linked to this CPL
            $cpmkLinked = $db->table('cpl_cpmk')
                ->select('cpmk_id')
                ->where('cpl_id', $cpl['id'])
                ->get()
                ->getResultArray();

            if (empty($cpmkLinked)) {
                // No CPMK linked to this CPL, skip or set to 0
                $chartData['labels'][] = $cpl['kode_cpl'];
                $chartData['data'][] = 0;
                $chartData['details'][] = [
                    'cpl_id' => $cpl['id'],
                    'kode_cpl' => $cpl['kode_cpl'],
                    'deskripsi' => $cpl['deskripsi'],
                    'jenis_cpl' => $cpl['jenis_cpl'],
                    'rata_rata' => 0,
                    'jumlah_cpmk' => 0,
                    'jumlah_mk' => 0
                ];
                continue;
            }

            $cpmkIds = array_column($cpmkLinked, 'cpmk_id');

            // Get average nilai_cpmk for this student across all CPMK linked to this CPL
            $nilaiBuilder = $db->table('nilai_cpmk_mahasiswa');
            $result = $nilaiBuilder
                ->select('AVG(nilai_cpmk) as rata_rata, COUNT(DISTINCT cpmk_id) as jumlah_cpmk, COUNT(DISTINCT jadwal_mengajar_id) as jumlah_mk')
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereIn('cpmk_id', $cpmkIds)
                ->get()
                ->getRowArray();

            $average = $result['rata_rata'] ? round($result['rata_rata'], 2) : 0;
            $jumlahCpmk = $result['jumlah_cpmk'] ?? 0;
            $jumlahMk = $result['jumlah_mk'] ?? 0;

            $chartData['labels'][] = $cpl['kode_cpl'];
            $chartData['data'][] = $average;
            $chartData['details'][] = [
                'cpl_id' => $cpl['id'],
                'kode_cpl' => $cpl['kode_cpl'],
                'deskripsi' => $cpl['deskripsi'],
                'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
                'rata_rata' => $average,
                'jumlah_cpmk' => $jumlahCpmk,
                'jumlah_mk' => $jumlahMk
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'chartData' => $chartData,
            'mahasiswa' => $mahasiswa
        ]);
    }

    public function getDetailData()
    {
        $mahasiswaId = $this->request->getGet('mahasiswa_id');
        $cplId = $this->request->getGet('cpl_id');

        if (!$mahasiswaId || !$cplId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $db = \Config\Database::connect();

        // Get CPL info
        $cpl = $this->cplModel->find($cplId);
        if (!$cpl) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'CPL tidak ditemukan'
            ]);
        }

        // Get all CPMK linked to this CPL
        $cpmkLinked = $db->table('cpl_cpmk')
            ->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
            ->join('cpmk', 'cpmk.id = cpl_cpmk.cpmk_id')
            ->where('cpl_cpmk.cpl_id', $cplId)
            ->orderBy('cpmk.kode_cpmk', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($cpmkLinked)) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [],
                'cpl' => $cpl,
                'message' => 'Tidak ada CPMK yang terkait dengan CPL ini'
            ]);
        }

        // Get nilai for each CPMK
        $detailData = [];
        foreach ($cpmkLinked as $cpmk) {
            // Get all nilai for this CPMK and mahasiswa
            $nilaiList = $db->table('nilai_cpmk_mahasiswa')
                ->select('nilai_cpmk_mahasiswa.nilai_cpmk, mata_kuliah.kode_mk, mata_kuliah.nama_mk, jadwal_mengajar.tahun_akademik, jadwal_mengajar.kelas')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = nilai_cpmk_mahasiswa.jadwal_mengajar_id')
                ->join('mata_kuliah', 'mata_kuliah.id = jadwal_mengajar.mata_kuliah_id')
                ->where('nilai_cpmk_mahasiswa.mahasiswa_id', $mahasiswaId)
                ->where('nilai_cpmk_mahasiswa.cpmk_id', $cpmk['id'])
                ->get()
                ->getResultArray();

            // Calculate average for this CPMK
            $totalNilai = 0;
            $countNilai = count($nilaiList);
            foreach ($nilaiList as $nilai) {
                $totalNilai += $nilai['nilai_cpmk'];
            }
            $rataCpmk = $countNilai > 0 ? round($totalNilai / $countNilai, 2) : 0;

            $detailData[] = [
                'kode_cpmk' => $cpmk['kode_cpmk'],
                'deskripsi_cpmk' => $cpmk['deskripsi'],
                'rata_rata' => $rataCpmk,
                'jumlah_nilai' => $countNilai,
                'detail_mk' => $nilaiList
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $detailData,
            'cpl' => $cpl
        ]);
    }

    public function getMahasiswaByFilter()
    {
        $programStudi = $this->request->getGet('program_studi');
        $tahunAngkatan = $this->request->getGet('tahun_angkatan');

        $builder = $this->mahasiswaModel
            ->select('id, nim, nama_lengkap, program_studi, tahun_angkatan')
            ->where('status_mahasiswa', 'Aktif');

        if ($programStudi) {
            $builder->where('program_studi', $programStudi);
        }

        if ($tahunAngkatan) {
            $builder->where('tahun_angkatan', $tahunAngkatan);
        }

        $mahasiswa = $builder
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        return $this->response->setJSON($mahasiswa);
    }

    public function getComparativeData()
    {
        $programStudi = $this->request->getGet('program_studi');
        $tahunAngkatan = $this->request->getGet('tahun_angkatan');

        if (!$programStudi || !$tahunAngkatan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program studi dan tahun angkatan harus dipilih'
            ]);
        }

        $db = \Config\Database::connect();

        // Get all mahasiswa in this filter
        $mahasiswaList = $this->mahasiswaModel
            ->where('program_studi', $programStudi)
            ->where('tahun_angkatan', $tahunAngkatan)
            ->where('status_mahasiswa', 'Aktif')
            ->findAll();

        if (empty($mahasiswaList)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada mahasiswa aktif'
            ]);
        }

        $mahasiswaIds = array_column($mahasiswaList, 'id');

        // Get all CPL
        $cplList = $db->table('cpl')
            ->orderBy('kode_cpl', 'ASC')
            ->get()
            ->getResultArray();

        $chartData = [
            'labels' => [],
            'data' => [],
            'details' => []
        ];

        foreach ($cplList as $cpl) {
            // Get all CPMK linked to this CPL
            $cpmkLinked = $db->table('cpl_cpmk')
                ->select('cpmk_id')
                ->where('cpl_id', $cpl['id'])
                ->get()
                ->getResultArray();

            if (empty($cpmkLinked)) {
                $chartData['labels'][] = $cpl['kode_cpl'];
                $chartData['data'][] = 0;
                $chartData['details'][] = [
                    'kode_cpl' => $cpl['kode_cpl'],
                    'deskripsi' => $cpl['deskripsi'],
                    'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
                    'rata_rata' => 0,
                    'jumlah_mahasiswa' => 0
                ];
                continue;
            }

            $cpmkIds = array_column($cpmkLinked, 'cpmk_id');

            // Get average for all students
            $result = $db->table('nilai_cpmk_mahasiswa')
                ->select('AVG(nilai_cpmk) as rata_rata, COUNT(DISTINCT mahasiswa_id) as jumlah_mahasiswa')
                ->whereIn('mahasiswa_id', $mahasiswaIds)
                ->whereIn('cpmk_id', $cpmkIds)
                ->get()
                ->getRowArray();

            $average = $result['rata_rata'] ? round($result['rata_rata'], 2) : 0;
            $jumlahMhs = $result['jumlah_mahasiswa'] ?? 0;

            $chartData['labels'][] = $cpl['kode_cpl'];
            $chartData['data'][] = $average;
            $chartData['details'][] = [
                'kode_cpl' => $cpl['kode_cpl'],
                'deskripsi' => $cpl['deskripsi'],
                'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
                'rata_rata' => $average,
                'jumlah_mahasiswa' => $jumlahMhs
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'chartData' => $chartData,
            'programStudi' => $programStudi,
            'tahunAngkatan' => $tahunAngkatan,
            'totalMahasiswa' => count($mahasiswaList)
        ]);
    }

    private function getTahunAngkatan()
    {
        $db = \Config\Database::connect();
        $result = $db->table('mahasiswa')
            ->select('tahun_angkatan')
            ->distinct()
            ->where('status_mahasiswa', 'Aktif')
            ->orderBy('tahun_angkatan', 'DESC')
            ->get()
            ->getResultArray();

        return array_column($result, 'tahun_angkatan');
    }

    private function getJenisCplLabel($jenis)
    {
        $labels = [
            'P' => 'Pengetahuan',
            'KK' => 'Keterampilan Khusus',
            'S' => 'Sikap',
            'KU' => 'Keterampilan Umum'
        ];

        return $labels[$jenis] ?? $jenis;
    }
}