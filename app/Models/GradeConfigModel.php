<?php

namespace App\Models;

use CodeIgniter\Model;

class GradeConfigModel extends Model
{
    protected $table            = 'grade_config';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = [
        'grade_letter',
        'min_score',
        'max_score',
        'grade_point',
        'description',
        'is_passing',
        'order_number',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'grade_letter' => 'required|max_length[10]',
        'min_score'    => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'max_score'    => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'grade_point'  => 'permit_empty|decimal|greater_than_equal_to[0]|less_than_equal_to[4]',
        'description'  => 'permit_empty|max_length[100]',
        'is_passing'   => 'required|in_list[0,1]',
        'order_number' => 'required|integer',
        'is_active'    => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'grade_letter' => [
            'required' => 'Huruf mutu harus diisi',
            'max_length' => 'Huruf mutu maksimal 10 karakter'
        ],
        'min_score' => [
            'required' => 'Nilai minimum harus diisi',
            'decimal' => 'Nilai minimum harus berupa angka',
            'greater_than_equal_to' => 'Nilai minimum harus >= 0',
            'less_than_equal_to' => 'Nilai minimum harus <= 100'
        ],
        'max_score' => [
            'required' => 'Nilai maksimum harus diisi',
            'decimal' => 'Nilai maksimum harus berupa angka',
            'greater_than_equal_to' => 'Nilai maksimum harus >= 0',
            'less_than_equal_to' => 'Nilai maksimum harus <= 100'
        ],
    ];

    /**
     * Get all active grades ordered by order_number
     *
     * @return array
     */
    public function getActiveGrades(): array
    {
        return $this->where('is_active', 1)
                    ->orderBy('order_number', 'ASC')
                    ->findAll();
    }

    /**
     * Convert numeric score to grade letter based on active grade configuration
     *
     * @param float $score The numeric score (0-100)
     * @return array|null Grade configuration array or null if no match
     */
    public function getGradeByScore(float $score): ?array
    {
        $grades = $this->getActiveGrades();

        foreach ($grades as $grade) {
            if ($score > $grade['min_score'] && $score <= $grade['max_score']) {
                return $grade;
            }
        }

        // If no match found, return the lowest grade
        if (!empty($grades)) {
            return end($grades);
        }

        return null;
    }

    /**
     * Get grade letter for a given score
     *
     * @param float $score
     * @return string Grade letter (e.g., 'A', 'AB', 'B', etc.)
     */
    public function getGradeLetter(float $score): string
    {
        $grade = $this->getGradeByScore($score);
        return $grade ? $grade['grade_letter'] : 'E';
    }

    /**
     * Check if a score is passing
     *
     * @param float $score
     * @return bool
     */
    public function isPassing(float $score): bool
    {
        $grade = $this->getGradeByScore($score);
        return $grade ? (bool)$grade['is_passing'] : false;
    }

    /**
     * Get grade point for a given score
     *
     * @param float $score
     * @return float
     */
    public function getGradePoint(float $score): float
    {
        $grade = $this->getGradeByScore($score);
        return $grade ? (float)$grade['grade_point'] : 0.0;
    }

    /**
     * Validate that grade ranges don't overlap
     *
     * @param array $data Grade data to validate
     * @param int|null $exclude_id ID to exclude from validation (for updates)
     * @return bool
     */
    public function validateGradeRanges(array $data, ?int $exclude_id = null): bool
    {
        $query = $this->where('is_active', 1);

        if ($exclude_id) {
            $query->where('id !=', $exclude_id);
        }

        $grades = $query->findAll();

        foreach ($grades as $grade) {
            // Check if ranges overlap
            if (
                ($data['min_score'] < $grade['max_score'] && $data['max_score'] > $grade['min_score']) ||
                ($grade['min_score'] < $data['max_score'] && $grade['max_score'] > $data['min_score'])
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all grades with details for display
     *
     * @return array
     */
    public function getAllGradesForDisplay(): array
    {
        return $this->orderBy('order_number', 'ASC')->findAll();
    }
}
