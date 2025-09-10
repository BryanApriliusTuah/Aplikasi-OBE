<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CpmkModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class Cpmk extends BaseController
{
    protected $cpmkModel;
    protected $table = 'cpmk';

    public function __construct()
    {
        $this->cpmkModel = new CpmkModel();
    }

    private function onlyDigits(string $s): string
    {
        return preg_replace('/\D+/', '', $s);
    }

    public function index()
    {
        return view('admin/cpmk/index', [
            'cpmk' => $this->cpmkModel->orderBy('kode_cpmk', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/cpmk/create');
    }

    public function store()
    {
        $noUrut = $this->onlyDigits((string)$this->request->getPost('no_urut'));
        $desc   = trim((string)$this->request->getPost('deskripsi'));

        if ($noUrut === '' || $desc === '') {
            return redirect()->back()->withInput()->with('error', 'Nomor urut dan Deskripsi wajib diisi.');
        }

        $finalKode = 'CPMK' . $noUrut;

        $exists = $this->cpmkModel->where('kode_cpmk', $finalKode)->countAllResults();
        if ($exists > 0) {
            return redirect()->back()->withInput()->with('error', 'Kode CPMK sudah ada.');
        }

        $this->cpmkModel->insert([
            'kode_cpmk'  => $finalKode,
            'deskripsi'  => $desc,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/cpmk')->with('success', 'CPMK berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = $this->cpmkModel->find($id);
        if (!$row) {
            return redirect()->to('/admin/cpmk')->with('error', 'Data tidak ditemukan.');
        }
        return view('admin/cpmk/edit', ['row' => $row]);
    }

    public function update($id)
    {
        $id  = (int)$id;
        $row = $this->cpmkModel->find($id);
        if (!$row) {
            return redirect()->to('/admin/cpmk')->with('error', 'Data tidak ditemukan.');
        }

        $noUrut = $this->onlyDigits((string)$this->request->getPost('no_urut'));
        $desc   = trim((string)$this->request->getPost('deskripsi'));

        if ($noUrut === '' || $desc === '') {
            return redirect()->back()->withInput()->with('error', 'Nomor urut dan Deskripsi wajib diisi.');
        }

        $finalKode = 'CPMK' . $noUrut;

        $exists = $this->cpmkModel
            ->where('kode_cpmk', $finalKode)
            ->where('id !=', $id)
            ->countAllResults();
        if ($exists > 0) {
            return redirect()->back()->withInput()->with('error', 'Kode CPMK sudah dipakai.');
        }

        $this->cpmkModel->update($id, [
            'kode_cpmk'  => $finalKode,
            'deskripsi'  => $desc,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/cpmk')->with('success', 'CPMK berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->cpmkModel->delete($id);
        return redirect()->to('/admin/cpmk')->with('success', 'CPMK berhasil dihapus.');
    }

    public function exportExcel()
    {
        $data = $this->cpmkModel->orderBy('kode_cpmk', 'ASC')->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode CPMK');
        $sheet->setCellValue('C1', 'Deskripsi');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D2EFFF']]
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        $rowNum = 2;
        $no = 1;
        foreach ($data as $d) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $d['kode_cpmk']);
            $sheet->setCellValue('C' . $rowNum, $d['deskripsi']);
            $rowNum++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastRow = $rowNum - 1;
        $sheet->getStyle('A1:C' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_CPMK_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        $rows = $this->cpmkModel->orderBy('kode_cpmk', 'ASC')->findAll();

        $html = '<style>table { border-collapse: collapse; width: 100%; font-size:11px; } th, td { border: 1px solid #000; padding: 6px; vertical-align: top; } th { background: #f2f2f2; text-align:center; } h3 { margin: 0 0 10px 0; text-align:center; }</style><h3>Daftar CPMK</h3><table><thead><tr><th style="width:40px;">No</th><th style="width:140px;">Kode CPMK</th><th>Deskripsi</th></tr></thead><tbody>';

        $no = 1;
        foreach ($rows as $r) {
            $html .= '<tr><td style="text-align:center;">'.($no++).'</td><td>'.htmlspecialchars($r['kode_cpmk']).'</td><td>'.nl2br(htmlspecialchars($r['deskripsi'])).'</td></tr>';
        }
        $html .= '</tbody></table>';

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); 
        $dompdf->render();
        $dompdf->stream('Data_CPMK_'.date('Ymd_His').'.pdf', ['Attachment' => false]);
        exit;
    }
}