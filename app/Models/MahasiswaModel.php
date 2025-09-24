<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'mahasiswa';

	/**
	 * The primary key of the table.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * Indicates if the model should use an auto-incrementing primary key.
	 *
	 * @var bool
	 */
	protected $useAutoIncrement = true;

	/**
	 * The return type of the results.
	 *
	 * @var string
	 */
	protected $returnType = 'array';

	/**
	 * An array of field names that are allowed to be saved to the database.
	 *
	 * @var array
	 */
	protected $allowedFields = [
		'user_id',
		'nim',
		'nama_lengkap',
		'program_studi',
		'tahun_angkatan',
		'status_mahasiswa'
	];

	/**
	 * Indicates if the model should use the created_at and updated_at fields.
	 *
	 * @var bool
	 */
	protected $useTimestamps = true;

	/**
	 * The name of the field that stores the creation date.
	 *
	 * @var string
	 */
	protected $createdField = 'created_at';

	/**
	 * The name of the field that stores the last update date.
	 *
	 * @var string
	 */
	protected $updatedField = 'updated_at';

	public function getStudentsForScoring(string $program_studi, int $semester): array
	{
		// This is a heuristic to determine the likely year of the students
		$currentYear = date('Y');
		$academicYear = (int)ceil($semester / 2);
		$targetYear = $currentYear - $academicYear + 1;

		return $this->where('program_studi', $program_studi)
			// ->where('tahun_angkatan', $targetYear) // Optional: uncomment to filter by year
			->where('status_mahasiswa', 'Aktif')
			->orderBy('nim', 'ASC')
			->findAll();
	}
}
