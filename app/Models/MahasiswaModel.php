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
}
