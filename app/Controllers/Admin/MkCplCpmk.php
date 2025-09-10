<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataKuliahModel;
use App\Models\CplModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MkCplCpmk extends BaseController
{
    public function index()
    {
        $mkModel = new MataKuliahModel();
        $cplModel = new CplModel();

        $cpl = $cplModel->orderBy('id', 'asc')->findAll();

        $db = \Config\Database::connect();
        $builder = $db->table('cpmk_mk');
        $builder->select('cpmk_mk.mata_kuliah_id, cpl_cpmk.cpl_id, cpmk.kode_cpmk');
        $builder->join('cpl_cpmk', 'cpl_cpmk.cpmk_id = cpmk_mk.cpmk_id');
        $builder->join('cpmk', 'cpmk.id = cpmk_mk.cpmk_id');
        $allRelations = $builder->get()->getResultArray();

        $mapping = [];
        foreach ($allRelations as $rel) {
            $mapping[$rel['mata_kuliah_id']][$rel['cpl_id']][] = $rel['kode_cpmk'];
        }
        
        // Mengambil SEMUA mata kuliah, bukan per halaman
        $mataKuliah = $mkModel->orderBy('id', 'asc')->findAll();
        
        $data = [];
        foreach ($mataKuliah as $mk) {
            $row = [
                'nama_mk' => $mk['nama_mk'],
                'cpl' => []
            ];
            foreach ($cpl as $cp) {
                $cpmkCodes = $mapping[$mk['id']][$cp['id']] ?? [];
                $row['cpl'][$cp['kode_cpl']] = $cpmkCodes;
            }
            $data[] = $row;
        }

        return view('admin/mk_cpl_cpmk/index', [
            'data'      => $data,
            'cpl'       => $cpl,
        ]);
    }

    public function export_excel()
{
    $mkModel = new \App\Models\MataKuliahModel();
    $cplModel = new \App\Models\CplModel();

    $cplHeaders = $cplModel->orderBy('id', 'asc')->findAll();
    $mataKuliah = $mkModel->orderBy('id', 'asc')->findAll();

    $db = \Config\Database::connect();
    $builder = $db->table('cpmk_mk');
    $builder->select('cpmk_mk.mata_kuliah_id, cpl_cpmk.cpl_id, cpmk.kode_cpmk');
    $builder->join('cpl_cpmk', 'cpl_cpmk.cpmk_id = cpmk_mk.cpmk_id');
    $builder->join('cpmk', 'cpmk.id = cpmk_mk.cpmk_id');
    $allRelations = $builder->get()->getResultArray();

    $mapping = [];
    foreach ($allRelations as $rel) {
        $mapping[$rel['mata_kuliah_id']][$rel['cpl_id']][] = $rel['kode_cpmk'];
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Nama Mata Kuliah');
    $cplColMap = [];
    foreach ($cplHeaders as $i => $cp) {
        $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 2);
        $sheet->setCellValue($col . '1', $cp['kode_cpl']);
        $cplColMap[$cp['id']] = $col;
    }

    $row = 2;
    foreach ($mataKuliah as $mk) {
        $sheet->setCellValue('A' . $row, $mk['nama_mk']);
        foreach ($cplHeaders as $cp) {
            $col = $cplColMap[$cp['id']];
            $cpmkCodes = $mapping[$mk['id']][$cp['id']] ?? [];
            $isi = implode("\n", $cpmkCodes);
            $sheet->setCellValue($col . $row, $isi);
            if ($isi) {
                $sheet->getStyle($col . $row)->getAlignment()->setWrapText(true);
            }
        }
        $row++;
    }

    // --- STYLING EXCEL ---
    $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($cplHeaders) + 1);
    $lastRow = $row - 1;
    $cellRange = "A1:{$lastCol}{$lastRow}";
    $headerRange = "A1:{$lastCol}1";

    $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle($headerRange)->getFont()->setBold(true);

    $sheet->getColumnDimension('A')->setAutoSize(true);
    foreach ($cplColMap as $col) {
        $sheet->getColumnDimension($col)->setWidth(15);
    }

    $filename = 'Pemetaan_MK_CPL_CPMK.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
}