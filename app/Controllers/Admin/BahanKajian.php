<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BahanKajianModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class BahanKajian extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new BahanKajianModel();
    }

    private function digits2(string $s): string
    {
        $d = preg_replace('/\D+/', '', $s);
        return substr($d, 0, 2);
    }

    public function index()
    {
        return view('admin/bahan_kajian/index', [
            'bahan_kajian' => $this->model->orderBy('kode_bk', 'ASC')->findAll()
        ]);
    }

    public function create()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bahan-kajian')->with('error', 'Akses ditolak!');
        }
        return view('admin/bahan_kajian/create', [
            'validation' => \Config\Services::validation()
        ]);
    }

    public function store()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bahan-kajian')->with('error', 'Akses ditolak!');
        }

        $validation = \Config\Services::validation();

        $noUrut = $this->digits2((string)$this->request->getPost('no_urut'));
        $namaBk = trim((string)$this->request->getPost('nama_bk'));

        if ($noUrut === '' || $namaBk === '') {
            if ($noUrut === '') $validation->setError('kode_bk', 'Nomor urut wajib diisi.');
            if ($namaBk === '') $validation->setError('nama_bk', 'Nama Bahan Kajian wajib diisi.');
            return view('admin/bahan_kajian/create', ['validation' => $validation]);
        }

        if (!preg_match('/^\d{2}$/', $noUrut)) {
            $validation->setError('kode_bk', 'Nomor urut harus 2 digit angka, contoh: 01, 02, 10.');
            return view('admin/bahan_kajian/create', ['validation' => $validation]);
        }

        $kode_bk = 'BK' . $noUrut;

        if ($this->model->where('kode_bk', $kode_bk)->first()) {
            $validation->setError('kode_bk', 'Kode BK sudah ada. Silakan pakai kode lain.');
            return view('admin/bahan_kajian/create', ['validation' => $validation]);
        }

        $this->model->insert([
            'kode_bk' => $kode_bk,
            'nama_bk' => $namaBk,
        ]);

        return redirect()->to('/admin/bahan-kajian')->with('success', 'Bahan Kajian berhasil ditambah!');
    }

    public function edit($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bahan-kajian')->with('error', 'Akses ditolak!');
        }
        $data['bk'] = $this->model->find((int)$id);
        return view('admin/bahan_kajian/edit', $data + [
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bahan-kajian')->with('error', 'Akses ditolak!');
        }

        $validation = \Config\Services::validation();

        $id     = (int)$id;
        $exists = $this->model->find($id);
        if (!$exists) {
            return redirect()->to('/admin/bahan-kajian')->with('error', 'Data tidak ditemukan.');
        }

        $noUrut = $this->digits2((string)$this->request->getPost('no_urut'));
        $namaBk = trim((string)$this->request->getPost('nama_bk'));

        if ($noUrut === '' || $namaBk === '') {
            if ($noUrut === '') $validation->setError('kode_bk', 'Nomor urut wajib diisi.');
            if ($namaBk === '') $validation->setError('nama_bk', 'Nama Bahan Kajian wajib diisi.');
            return view('admin/bahan_kajian/edit', ['bk' => $exists, 'validation' => $validation]);
        }

        if (!preg_match('/^\d{2}$/', $noUrut)) {
            $validation->setError('kode_bk', 'Nomor urut harus 2 digit angka, contoh: 01, 02, 10.');
            return view('admin/bahan_kajian/edit', ['bk' => $exists, 'validation' => $validation]);
        }

        $kode_bk = 'BK' . $noUrut;

        $dup = $this->model->where('kode_bk', $kode_bk)->where('id !=', $id)->first();
        if ($dup) {
            $validation->setError('kode_bk', 'Kode BK sudah ada. Silakan pakai kode lain.');
            return view('admin/bahan_kajian/edit', ['bk' => $exists, 'validation' => $validation]);
        }

        $this->model->update($id, [
            'kode_bk' => $kode_bk,
            'nama_bk' => $namaBk,
        ]);

        return redirect()->to('/admin/bahan-kajian')->with('success', 'Bahan Kajian berhasil diupdate!');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/bahan-kajian')->with('error', 'Akses ditolak!');
        }
        $this->model->delete((int)$id);
        session()->setFlashdata('success', 'Data berhasil dihapus!');
        return redirect()->to('/admin/bahan-kajian');
    }

    public function exportExcel()
    {
        $bahan_kajian = $this->model->orderBy('kode_bk', 'ASC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode BK');
        $sheet->setCellValue('C1', 'Nama Bahan Kajian');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D2EFFF']
            ]
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        $row = 2;
        $no  = 1;
        foreach ($bahan_kajian as $bk) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $bk['kode_bk']);
            $sheet->setCellValue('C' . $row, $bk['nama_bk']);
            $row++;
        }
        
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastRow = $row - 1;
        $sheet->getStyle('A1:C' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $filename = 'Bahan_Kajian.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $bahan_kajian = $this->model->orderBy('kode_bk', 'ASC')->findAll();

        $html = '<style>table { border-collapse: collapse; width: 100%; font-size:12px;} th, td { border: 1px solid #000; padding: 6px; vertical-align: top; } th { background: #f2f2f2; text-align:center; } h3 { margin: 0 0 10px 0; text-align:center; }</style><h3>Daftar Bahan Kajian</h3><table><thead><tr><th style="width:50px;">No</th><th>Kode BK</th><th>Nama Bahan Kajian</th></tr></thead><tbody>';
        $no = 1;
        foreach ($bahan_kajian as $bk) {
            $html .= '<tr><td style="text-align:center;">' . $no++ . '</td><td>' . htmlspecialchars($bk['kode_bk']) . '</td><td>' . htmlspecialchars($bk['nama_bk']) . '</td></tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Bahan_Kajian.pdf', ['Attachment' => false]);
        exit;
    }
}