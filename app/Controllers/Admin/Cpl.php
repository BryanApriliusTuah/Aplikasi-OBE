<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class Cpl extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new CplModel();
    }

    private function digits2(string $s): string
    {
        $d = preg_replace('/\D+/', '', $s);
        return substr($d, 0, 2);
    }

    public function index()
    {
        return view('admin/cpl/index', [
            'cpl' => $this->model->orderBy('kode_cpl', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl')->with('error', 'Akses ditolak!');
        }
        return view('admin/cpl/create', [
            'validation' => \Config\Services::validation()
        ]);
    }

    public function store()
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl')->with('error', 'Akses ditolak!');
        }

        $validation = \Config\Services::validation();

        $noUrut    = $this->digits2((string)$this->request->getPost('no_urut'));
        $deskripsi = trim((string)$this->request->getPost('deskripsi'));
        $jenisCpl  = trim((string)$this->request->getPost('jenis_cpl'));

        if ($noUrut === '' || $deskripsi === '' || $jenisCpl === '') {
            if ($noUrut === '')    $validation->setError('kode_cpl', 'Nomor urut wajib diisi.');
            if ($deskripsi === '') $validation->setError('deskripsi', 'Deskripsi wajib diisi.');
            if ($jenisCpl === '')  $validation->setError('jenis_cpl', 'Jenis CPL wajib dipilih.');
            return view('admin/cpl/create', ['validation' => $validation]);
        }

        if (!preg_match('/^\d{2}$/', $noUrut)) {
            $validation->setError('kode_cpl', 'Nomor urut harus 2 digit angka, contoh: 01, 02, 10.');
            return view('admin/cpl/create', ['validation' => $validation]);
        }

        $kodeCpl = 'CPL' . $noUrut;

        if ($this->model->where('kode_cpl', $kodeCpl)->first()) {
            $validation->setError('kode_cpl', 'Kode CPL sudah ada. Silakan pakai kode lain.');
            return view('admin/cpl/create', ['validation' => $validation]);
        }

        $this->model->insert([
            'kode_cpl'  => $kodeCpl,
            'deskripsi' => $deskripsi,
            'jenis_cpl' => $jenisCpl,
        ]);

        return redirect()->to('/admin/cpl')->with('success', 'CPL berhasil ditambah!');
    }

    public function edit($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl')->with('error', 'Akses ditolak!');
        }
        $data['cpl'] = $this->model->find((int)$id);
        return view('admin/cpl/edit', $data + [
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl')->with('error', 'Akses ditolak!');
        }

        $validation = \Config\Services::validation();

        $existing  = $this->model->find((int)$id);
        if (!$existing) {
            return redirect()->to('/admin/cpl')->with('error', 'Data tidak ditemukan.');
        }

        $noUrut    = $this->digits2((string)$this->request->getPost('no_urut'));
        $deskripsi = trim((string)$this->request->getPost('deskripsi'));
        $jenisCpl  = trim((string)$this->request->getPost('jenis_cpl'));

        if ($noUrut === '' || $deskripsi === '' || $jenisCpl === '') {
            if ($noUrut === '')    $validation->setError('kode_cpl', 'Nomor urut wajib diisi.');
            if ($deskripsi === '') $validation->setError('deskripsi', 'Deskripsi wajib diisi.');
            if ($jenisCpl === '')  $validation->setError('jenis_cpl', 'Jenis CPL wajib dipilih.');
            return view('admin/cpl/edit', [
                'cpl'        => $existing,
                'validation' => $validation
            ]);
        }

        if (!preg_match('/^\d{2}$/', $noUrut)) {
            $validation->setError('kode_cpl', 'Nomor urut harus 2 digit angka, contoh: 01, 02, 10.');
            return view('admin/cpl/edit', [
                'cpl'        => $existing,
                'validation' => $validation
            ]);
        }

        $kodeCpl = 'CPL' . $noUrut;

        $dup = $this->model->where('kode_cpl', $kodeCpl)->where('id !=', (int)$id)->first();
        if ($dup) {
            $validation->setError('kode_cpl', 'Kode CPL sudah ada. Silakan pakai kode lain.');
            return view('admin/cpl/edit', [
                'cpl'        => $existing,
                'validation' => $validation
            ]);
        }

        $this->model->update((int)$id, [
            'kode_cpl'  => $kodeCpl,
            'deskripsi' => $deskripsi,
            'jenis_cpl' => $jenisCpl
        ]);

        return redirect()->to('/admin/cpl')->with('success', 'CPL berhasil diupdate!');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->to('/admin/cpl')->with('error', 'Akses ditolak!');
        }
        if ($this->model->delete((int)$id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Data gagal dihapus!');
        }
        return redirect()->to('/admin/cpl');
    }

    public function exportExcel()
    {
        $cpl = $this->model->orderBy('kode_cpl', 'ASC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode CPL');
        $sheet->setCellValue('C1', 'Deskripsi');
        $sheet->setCellValue('D1', 'Jenis CPL');

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
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        $row = 2;
        $no = 1;
        foreach ($cpl as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['kode_cpl']);
            $sheet->setCellValue('C' . $row, $item['deskripsi']);
            $sheet->setCellValue('D' . $row, $item['jenis_cpl']);
            $row++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastRow = $row - 1;
        $sheet->getStyle('A1:D' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $filename = 'CPL.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $cpl = $this->model->orderBy('kode_cpl', 'ASC')->findAll();

        $html = '<style>table { border-collapse: collapse; width: 100%; font-size:12px;} th, td { border: 1px solid #000; padding: 6px; vertical-align: top; } th { background: #f2f2f2; text-align:center; } h3 { margin: 0 0 10px 0; text-align:center; }</style><h3>Daftar CPL</h3><table><thead><tr><th>No</th><th>Kode CPL</th><th>Deskripsi</th><th>Jenis CPL</th></tr></thead><tbody>';
        $no = 1;
        foreach ($cpl as $item) {
            $html .= '<tr><td style="text-align:center;">' . $no++ . '</td><td>' . htmlspecialchars($item['kode_cpl']) . '</td><td>' . nl2br(htmlspecialchars($item['deskripsi'])) . '</td><td>' . htmlspecialchars($item['jenis_cpl']) . '</td></tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('CPL.pdf', ['Attachment' => false]);
        exit;
    }
}