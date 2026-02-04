<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataKuliahModel;
use App\Models\MkPrasyaratModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;

class MataKuliah extends BaseController
{
	public function index()
	{
		$model = new MataKuliahModel();

		$semester = $this->request->getGet('semester');
		$tipe = $this->request->getGet('tipe');
		$search = $this->request->getGet('search');

		$builder = $model->orderBy('semester', 'ASC')->orderBy('kode_mk', 'ASC');

		if ($semester !== null && $semester !== '') {
			$builder->where('semester', $semester);
		}
		if (!empty($tipe)) {
			$builder->where('tipe', $tipe);
		}
		if (!empty($search)) {
			$builder->groupStart()
				->like('kode_mk', $search)
				->orLike('nama_mk', $search)
				->groupEnd();
		}

		return view('admin/mata_kuliah/index', [
			'matakuliah' => $builder->findAll(),
			'filters' => [
				'semester' => $semester ?? '',
				'tipe' => $tipe ?? '',
				'search' => $search ?? '',
			],
		]);
	}

	public function create()
	{
		if (session('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menambah data.');
		}
		$model = new MataKuliahModel();
		$data['daftar_mk'] = $model->findAll();
		return view('admin/mata_kuliah/create', $data);
	}

	public function store()
	{
		if (session('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menambah data.');
		}

		$validation = \Config\Services::validation();
		$validation->setRules([
			'kode_mk' => [
				'label' => 'Kode Mata Kuliah',
				'rules' => 'required|is_unique[mata_kuliah.kode_mk]',
				'errors' => [
					'required' => 'Kode Mata Kuliah wajib diisi.',
					'is_unique' => 'Kode Mata Kuliah sudah ada. Silakan pakai kode lain.'
				]
			],
			'nama_mk' => [
				'label' => 'Nama Mata Kuliah',
				'rules' => 'required',
				'errors' => [
					'required' => 'Nama Mata Kuliah wajib diisi.'
				]
			],
		]);

		if (!$this->validate($validation->getRules())) {
			return redirect()->back()->withInput()->with('error', $validation->getErrors());
		}

		$model = new MataKuliahModel();
		$mkId = $model->insert([
			'kode_mk' => $this->request->getPost('kode_mk'),
			'nama_mk' => $this->request->getPost('nama_mk'),
			'kategori' => $this->request->getPost('kategori'),
			'tipe' => $this->request->getPost('tipe'),
			'semester' => $this->request->getPost('semester'),
			'sks' => $this->request->getPost('sks'),
			'deskripsi_singkat' => $this->request->getPost('deskripsi_singkat'),
		]);

		$prasyarat = $this->request->getPost('prasyarat_mk_id') ?? [];
		if (!empty($prasyarat)) {
			$prasyarat = array_unique(array_filter($prasyarat));
			$mkPrasyaratModel = new MkPrasyaratModel();
			foreach ($prasyarat as $prasyarat_mk_id) {
				if ($prasyarat_mk_id != $mkId) {
					$mkPrasyaratModel->insert([
						'mata_kuliah_id' => $mkId,
						'prasyarat_mk_id' => $prasyarat_mk_id
					]);
				}
			}
		}

		return redirect()->to('/admin/mata-kuliah')->with('success', 'Mata Kuliah berhasil ditambah!');
	}

	public function edit($id)
	{
		if (session('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat mengedit data.');
		}
		$model = new MataKuliahModel();
		$mk = $model->find($id);

		$all_mk = $model->findAll();
		$prasyaratModel = new MkPrasyaratModel();
		$prasyaratRows = $prasyaratModel->where('mata_kuliah_id', $id)->findAll();
		$prasyarat_terpilih = array_column($prasyaratRows, 'prasyarat_mk_id');

		return view('admin/mata_kuliah/edit', [
			'mk' => $mk,
			'all_mk' => $all_mk,
			'prasyarat_terpilih' => $prasyarat_terpilih,
		]);
	}

	public function update($id)
	{
		if (session('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat mengupdate data.');
		}

		$validation = \Config\Services::validation();
		$validation->setRules([
			'kode_mk' => [
				'label' => 'Kode Mata Kuliah',
				'rules' => 'required',
				'errors' => ['required' => 'Kode Mata Kuliah wajib diisi.']
			],
			'nama_mk' => [
				'label' => 'Nama Mata Kuliah',
				'rules' => 'required',
				'errors' => ['required' => 'Nama Mata Kuliah wajib diisi.']
			],
		]);

		if (!$this->validate($validation->getRules())) {
			return redirect()->back()->withInput()->with('error', $validation->getErrors());
		}

		$kode_baru = $this->request->getPost('kode_mk');
		$model = new MataKuliahModel();
		$duplikat = $model->where('kode_mk', $kode_baru)->where('id !=', $id)->first();
		if ($duplikat) {
			return redirect()->back()->withInput()->with('error', ['Kode Mata Kuliah sudah ada. Silakan pakai kode lain.']);
		}

		$model->update($id, [
			'kode_mk' => $kode_baru,
			'nama_mk' => $this->request->getPost('nama_mk'),
			'kategori' => $this->request->getPost('kategori'),
			'tipe' => $this->request->getPost('tipe'),
			'semester' => $this->request->getPost('semester'),
			'sks' => $this->request->getPost('sks'),
			'deskripsi_singkat' => $this->request->getPost('deskripsi_singkat'),
		]);

		$mkPrasyaratModel = new MkPrasyaratModel();
		$mkPrasyaratModel->where('mata_kuliah_id', $id)->delete();
		$prasyarat = $this->request->getPost('prasyarat_mk_id') ?? [];
		if (!empty($prasyarat)) {
			$prasyarat = array_unique(array_filter($prasyarat));
			foreach ($prasyarat as $prasyarat_mk_id) {
				if ($prasyarat_mk_id != $id) {
					$mkPrasyaratModel->insert([
						'mata_kuliah_id' => $id,
						'prasyarat_mk_id' => $prasyarat_mk_id
					]);
				}
			}
		}

		return redirect()->to('/admin/mata-kuliah')->with('success', 'Mata Kuliah berhasil diupdate!');
	}

	public function delete($id)
	{
		if (session('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menghapus data.');
		}
		$model = new MataKuliahModel();
		$model->delete($id);
		return redirect()->to(base_url('admin/mata-kuliah'))->with('success', 'Mata Kuliah berhasil dihapus!');
	}

	public function exportExcel()
	{
		$model = new MataKuliahModel();
		$data = $model->orderBy('semester', 'ASC')->orderBy('kode_mk', 'ASC')->findAll();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Kode MK');
		$sheet->setCellValue('C1', 'Nama Mata Kuliah');
		$sheet->setCellValue('D1', 'Kategori');
		$sheet->setCellValue('E1', 'Tipe');
		$sheet->setCellValue('F1', 'Semester');
		$sheet->setCellValue('G1', 'SKS');
		$sheet->setCellValue('H1', 'Deskripsi Singkat');

		$headerStyle = [
			'font' => ['bold' => true],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D2EFFF']]
		];
		$sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

		$row = 2;
		$no = 1;
		foreach ($data as $mk) {
			$sheet->setCellValue('A' . $row, $no++);
			$sheet->setCellValue('B' . $row, $mk['kode_mk']);
			$sheet->setCellValue('C' . $row, $mk['nama_mk']);
			$sheet->setCellValue('D' . $row, $mk['kategori']);
			$sheet->setCellValue('E' . $row, $mk['tipe']);
			$sheet->setCellValue('F' . $row, $mk['semester']);
			$sheet->setCellValue('G' . $row, $mk['sks']);
			$sheet->setCellValue('H' . $row, $mk['deskripsi_singkat']);
			$row++;
		}

		foreach (range('A', 'H') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		$lastRow = $row - 1;
		$sheet->getStyle('A1:H' . $lastRow)->applyFromArray([
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000'],],],
		]);

		$filename = 'Mata_Kuliah.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	public function syncFromApi()
	{
		if (session('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat melakukan sinkronisasi.');
		}

		$apiUrl = 'https://tik.upr.ac.id/api/siuber/matakuliah';
		$apiKey = 'XT)+KVdVT]Z]1-p8<tIz/H0W5}_z%@KS';

		$client = \Config\Services::curlrequest();

		try {
			$response = $client->request('GET', $apiUrl, [
				'headers' => [
					'x-api-key' => $apiKey,
					'Accept'    => 'application/json',
				],
				'timeout' => 30,
			]);

			$body = json_decode($response->getBody(), true);

			$body = $body['data'] ?? null;

			if (!is_array($body)) {
				return redirect()->back()->with('error', 'Format response API tidak valid.');
			}

			$model = new MataKuliahModel();
			$inserted = 0;
			$updated = 0;

			foreach ($body as $item) {
				if ($item['program_studi_kode'] == 58) {
					log_message('debug', 'API item keys: ' . json_encode(array_keys($item)));
					log_message('debug', 'API item: ' . json_encode($item));
					$kode = $item['matakuliah_kode'] ?? null;
					$nama = $item['matakuliah_nama'] ?? null;
					$semester = $item['matakuliah_semester'] ?? 0;

					if (!$kode || !$nama) {
						continue;
					}

					$existing = $model->where('kode_mk', $kode)->first();

					$data = [
						'kode_mk'  => $kode,
						'nama_mk'  => $nama,
						'semester'  => $semester,
					];

					if ($existing) {
						$model->update($existing['id'], $data);
						$updated++;
					} else {
						$model->insert($data);
						$inserted++;
					}
				} else {
					continue;
				}
			}

			return redirect()->back()->with('success', "Sinkronisasi berhasil! $inserted data baru ditambahkan, $updated data diperbarui.");
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Gagal mengambil data dari API: ' . $e->getMessage());
		}
	}

	public function exportPdf()
	{
		$model = new MataKuliahModel();
		$data = $model->orderBy('semester', 'ASC')->orderBy('kode_mk', 'ASC')->findAll();

		$html = '<style>table { border-collapse: collapse; width: 100%; font-size:10px;} th, td { border: 1px solid #000; padding: 5px; } th { background: #f2f2f2; text-align:center; }</style><h3 style="text-align:center;">Daftar Mata Kuliah</h3><table><thead><tr><th>No</th><th>Kode MK</th><th>Nama Mata Kuliah</th><th>Kategori</th><th>Tipe</th><th>Semester</th><th>SKS</th><th>Deskripsi</th></tr></thead><tbody>';
		$no = 1;
		foreach ($data as $mk) {
			$html .= '<tr><td style="text-align:center;">' . $no++ . '</td><td>' . htmlspecialchars($mk['kode_mk']) . '</td><td>' . htmlspecialchars($mk['nama_mk']) . '</td><td>' . htmlspecialchars($mk['kategori']) . '</td><td>' . htmlspecialchars($mk['tipe']) . '</td><td style="text-align:center;">' . htmlspecialchars($mk['semester']) . '</td><td style="text-align:center;">' . htmlspecialchars($mk['sks']) . '</td><td>' . nl2br(htmlspecialchars($mk['deskripsi_singkat'])) . '</td></tr>';
		}
		$html .= '</tbody></table>';

		$dompdf = new Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'landscape');
		$dompdf->render();
		$dompdf->stream('Mata_Kuliah.pdf', ['Attachment' => false]);
		exit;
	}
}
