<?php

namespace App\Models;

use CodeIgniter\Model;

class PenyebabTemplateCplModel extends Model
{
	protected $table = 'penyebab_templates_cpl';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $returnType = 'array';
	protected $useSoftDeletes = false;
	protected $protectFields = true;
	protected $allowedFields = [
		'option_key',
		'option_label',
		'template_tercapai',
		'template_tidak_tercapai',
		'is_active',
	];

	protected $useTimestamps = true;
	protected $dateFormat = 'datetime';
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';

	protected $validationRules = [
		'option_key'   => 'required|string|max_length[50]',
		'option_label' => 'required|string|max_length[255]',
	];

	public function getActiveTemplates()
	{
		return $this->where('is_active', 1)->findAll();
	}

	public function getByKey($optionKey)
	{
		return $this->where('option_key', $optionKey)->first();
	}

	public function getTemplatesAsArray()
	{
		$templates = $this->getActiveTemplates();
		$result = [];
		foreach ($templates as $template) {
			$result[$template['option_key']] = $template;
		}
		return $result;
	}

	public function updateByKey($optionKey, $data)
	{
		$template = $this->getByKey($optionKey);
		if ($template) {
			return $this->update($template['id'], $data);
		}
		return false;
	}
}
