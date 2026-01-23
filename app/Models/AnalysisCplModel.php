<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalysisCplModel extends Model
{
	protected $table = 'analisis_cpl';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $returnType = 'array';
	protected $useSoftDeletes = false;
	protected $protectFields = true;
	protected $allowedFields = [
		'program_studi',
		'tahun_akademik',
		'angkatan',
		'mode',
		'analisis_summary',
		'auto_options',
		'bukti_dokumentasi_file',
		'notulensi_rapat_file'
	];

	// Dates
	protected $useTimestamps = true;
	protected $dateFormat = 'datetime';
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';

	// Validation
	protected $validationRules = [
		'program_studi' => 'required|string|max_length[100]',
		'tahun_akademik' => 'required|string|max_length[20]',
		'angkatan' => 'required|string|max_length[10]',
		'mode' => 'required|in_list[auto,manual]',
	];

	protected $validationMessages = [
		'program_studi' => [
			'required' => 'Program studi harus dipilih',
		],
		'tahun_akademik' => [
			'required' => 'Tahun akademik harus diisi',
		],
		'angkatan' => [
			'required' => 'Angkatan harus diisi',
		],
		'mode' => [
			'required' => 'Mode analisis harus dipilih',
			'in_list' => 'Mode analisis tidak valid',
		],
	];

	/**
	 * Get or create analysis record for a specific program studi, tahun akademik, and angkatan
	 */
	public function getAnalysis($programStudi, $tahunAkademik, $angkatan)
	{
		return $this->where('program_studi', $programStudi)
			->where('tahun_akademik', $tahunAkademik)
			->where('angkatan', $angkatan)
			->first();
	}

	/**
	 * Save or update analysis
	 */
	public function saveAnalysis($data)
	{
		$existing = $this->getAnalysis(
			$data['program_studi'],
			$data['tahun_akademik'],
			$data['angkatan']
		);

		if ($existing) {
			return $this->update($existing['id'], $data);
		}

		return $this->insert($data);
	}
}
