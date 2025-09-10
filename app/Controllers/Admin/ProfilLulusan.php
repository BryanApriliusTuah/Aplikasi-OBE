<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProfilLulusanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class ProfilLulusan extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProfilLulusanModel();
    }

    private function digits2(string $s): string
    {
        $d = preg_replace('/\D+/', '', $s);
        return substr($d, 0, 2);
    }

    public function index()
    {
        return view('admin/profil_lulusan/index', [
            'profil_lulusan' => $this->model->orderBy('kode_pl', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        if (session('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menambah data.');
        }
        return view('admin/profil_lulusan/create', [
            'validation' => \Config\Services::validation()
        ]);
    }

    public function store()
    {
        if (session('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menambah data.');
        }

        $validation = \Config\Services::validation();

        $noUrut    = $this->digits2((string)$this->request->getPost('no_urut'));
        $deskripsi = trim((string)$this->request->getPost('deskripsi'));

        if ($noUrut === '' || $deskripsi === '') {
            if ($noUrut === '')    $validation->setError('kode_pl', 'Nomor urut wajib diisi.');
            if ($deskripsi === '') $validation->setError('deskripsi', 'Deskripsi wajib diisi.');
            return view('admin/profil_lulusan/create', ['validation' => $validation]);
        }

        if (!preg_match('/^\d{2}$/', $noUrut)) {
            $validation->setError('kode_pl', 'Nomor urut harus 2 digit angka, contoh: 01, 02, 10.');
            return view('admin/profil_lulusan/create', ['validation' => $validation]);
        }

        $kode_pl = 'PL' . $noUrut;

        if ($this->model->where('kode_pl', $kode_pl)->first()) {
            $validation->setError('kode_pl', 'Kode PL sudah ada.');
            return view('admin/profil_lulusan/create', ['validation' => $validation]);
        }

        $this->model->insert([
            'kode_pl'   => $kode_pl,
            'deskripsi' => $deskripsi,
        ]);

        return redirect()->to('/admin/profil-lulusan')->with('success', 'Profil Lulusan berhasil ditambah!');
    }

    public function edit($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat mengedit data.');
        }

        $profil_lulusan = $this->model->find((int)$id);
        if (!$profil_lulusan) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Profil lulusan tidak ditemukan.');
        }

        return view('admin/profil_lulusan/edit', [
            'profil_lulusan' => $profil_lulusan,
            'validation'     => \Config\Services::validation(),
        ]);
    }

    public function update($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat mengupdate data.');
        }

        $id       = (int)$id;
        $existing   = $this->model->find($id);
        if (!$existing) {
            return redirect()->to('/admin/profil-lulusan')->with('error', 'Data tidak ditemukan.');
        }

        $validation = \Config\Services::validation();

        $noUrut    = $this->digits2((string)$this->request->getPost('no_urut'));
        $deskripsi = trim((string)$this->request->getPost('deskripsi'));

        if ($noUrut === '' || $deskripsi === '') {
            if ($noUrut === '')    $validation->setError('kode_pl', 'Nomor urut wajib diisi.');
            if ($deskripsi === '') $validation->setError('deskripsi', 'Deskripsi wajib diisi.');
            return view('admin/profil_lulusan/edit', [
                'profil_lulusan' => $existing,
                'validation'     => $validation
            ]);
        }

        if (!preg_match('/^\d{2}$/', $noUrut)) {
            $validation->setError('kode_pl', 'Nomor urut harus 2 digit angka, contoh: 01, 02, 10.');
            return view('admin/profil_lulusan/edit', [
                'profil_lulusan' => $existing,
                'validation'     => $validation
            ]);
        }

        $kode_pl = 'PL' . $noUrut;

        $dup = $this->model->where('kode_pl', $kode_pl)->where('id !=', $id)->first();
        if ($dup) {
            $validation->setError('kode_pl', 'Kode PL sudah dipakai.');
            return view('admin/profil_lulusan/edit', [
                'profil_lulusan' => $existing,
                'validation'     => $validation
            ]);
        }

        $this->model->update($id, [
            'kode_pl'   => $kode_pl,
            'deskripsi' => $deskripsi,
        ]);

        return redirect()->to('/admin/profil-lulusan')->with('success', 'Profil lulusan berhasil diupdate!');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menghapus data.');
        }

        $this->model->delete((int)$id);
        return redirect()->to('/admin/profil-lulusan')->with('success', 'Data profil lulusan berhasil dihapus.');
    }

    public function exportExcel()
    {
        $profil_lulusan = $this->model->orderBy('kode_pl', 'ASC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode PL');
        $sheet->setCellValue('C1', 'Deskripsi');

        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D2EFFF']
            ]
        ]);

        $row = 2; $no = 1;
        foreach ($profil_lulusan as $pl) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $pl['kode_pl']);
            $sheet->setCellValue('C' . $row, $pl['deskripsi']);
            $row++;
        }

        $lastRow = $row - 1;
        $sheet->getStyle('A1:C' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
        
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Profil_Lulusan_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $profil_lulusan = $this->model->orderBy('kode_pl', 'ASC')->findAll();

        $html = '<style>table { border-collapse: collapse; width: 100%; font-size:12px; } th, td { border: 1px solid #000; padding: 6px; vertical-align: top; } th { background: #f2f2f2; } h3 { margin: 0 0 10px 0; text-align:center; }</style><h3>Daftar Profil Lulusan</h3><table><thead><tr><th style="width:40px;">No</th><th style="width:120px;">Kode PL</th><th>Deskripsi</th></tr></thead><tbody>';

        $no = 1;
        foreach ($profil_lulusan as $pl) {
            $html .= '<tr><td>' . ($no++) . '</td><td>' . htmlspecialchars($pl['kode_pl']) . '</td><td>' . nl2br(htmlspecialchars($pl['deskripsi'])) . '</td></tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Profil_Lulusan_' . date('Ymd_His') . '.pdf', ['Attachment' => false]);
        exit;
    }
}