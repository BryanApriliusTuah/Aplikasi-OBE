<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GradeConfigModel;

class Settings extends BaseController
{
    /**
     * Display grade configuration settings
     */
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/dashboard')->with('error', 'Anda tidak memiliki hak akses ke menu ini.');
        }

        $model = new GradeConfigModel();
        $data['grades'] = $model->getAllGradesForDisplay();
        $data['title'] = 'Pengaturan Sistem Penilaian';

        return view('admin/settings/index', $data);
    }

    /**
     * Show create grade form
     */
    public function create()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/settings')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $data['title'] = 'Tambah Konfigurasi Nilai';
        return view('admin/settings/create', $data);
    }

    /**
     * Store new grade configuration
     */
    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/settings')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new GradeConfigModel();

        $data = [
            'grade_letter'  => $this->request->getPost('grade_letter'),
            'min_score'     => $this->request->getPost('min_score'),
            'max_score'     => $this->request->getPost('max_score'),
            'grade_point'   => $this->request->getPost('grade_point'),
            'description'   => $this->request->getPost('description'),
            'is_passing'    => $this->request->getPost('is_passing') ? 1 : 0,
            'order_number'  => $this->request->getPost('order_number'),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Validate min_score < max_score
        if ($data['min_score'] >= $data['max_score']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nilai minimum harus lebih kecil dari nilai maksimum.');
        }

        // Validate no overlapping ranges
        if (!$model->validateGradeRanges($data)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Range nilai bertabrakan dengan konfigurasi yang sudah ada.');
        }

        if ($model->save($data)) {
            return redirect()->to('/admin/settings')->with('success', 'Konfigurasi nilai berhasil ditambahkan.');
        } else {
            $errors = $model->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Gagal menyimpan data.';
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Show edit grade form
     */
    public function edit($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/settings')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new GradeConfigModel();
        $gradeData = $model->find($id);

        if (empty($gradeData)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Konfigurasi nilai tidak ditemukan.');
        }

        $data['grade'] = $gradeData;
        $data['title'] = 'Edit Konfigurasi Nilai';

        return view('admin/settings/edit', $data);
    }

    /**
     * Update grade configuration
     */
    public function update($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/settings')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new GradeConfigModel();

        $data = [
            'grade_letter'  => $this->request->getPost('grade_letter'),
            'min_score'     => $this->request->getPost('min_score'),
            'max_score'     => $this->request->getPost('max_score'),
            'grade_point'   => $this->request->getPost('grade_point'),
            'description'   => $this->request->getPost('description'),
            'is_passing'    => $this->request->getPost('is_passing') ? 1 : 0,
            'order_number'  => $this->request->getPost('order_number'),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Validate min_score < max_score
        if ($data['min_score'] >= $data['max_score']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nilai minimum harus lebih kecil dari nilai maksimum.');
        }

        // Validate no overlapping ranges (excluding current record)
        if (!$model->validateGradeRanges($data, $id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Range nilai bertabrakan dengan konfigurasi yang sudah ada.');
        }

        if ($model->update($id, $data)) {
            return redirect()->to('/admin/settings')->with('success', 'Konfigurasi nilai berhasil diperbarui.');
        } else {
            $errors = $model->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Gagal memperbarui data.';
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Delete grade configuration
     */
    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/settings')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new GradeConfigModel();

        // Check if grade is being used (optional - you can add this check later)
        // For now, we'll allow deletion

        if ($model->delete($id)) {
            return redirect()->to('/admin/settings')->with('success', 'Konfigurasi nilai berhasil dihapus.');
        } else {
            return redirect()->to('/admin/settings')->with('error', 'Gagal menghapus konfigurasi nilai.');
        }
    }

    /**
     * Toggle grade active status
     */
    public function toggle($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/settings')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new GradeConfigModel();
        $grade = $model->find($id);

        if ($grade) {
            $newStatus = $grade['is_active'] ? 0 : 1;
            $model->update($id, ['is_active' => $newStatus]);

            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->to('/admin/settings')->with('success', "Konfigurasi nilai berhasil {$statusText}.");
        }

        return redirect()->to('/admin/settings')->with('error', 'Konfigurasi nilai tidak ditemukan.');
    }

    /**
     * Reset to default grade configuration
     */
    public function resetToDefault()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/settings')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new GradeConfigModel();

        // Delete all existing grades
        $db = \Config\Database::connect();
        $db->table('grade_config')->truncate();

        // Insert default grades
        $defaultGrades = [
            [
                'grade_letter' => 'A',
                'min_score' => 80.01,
                'max_score' => 100.00,
                'grade_point' => 4.00,
                'description' => 'Istimewa',
                'is_passing' => 1,
                'order_number' => 1,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'AB',
                'min_score' => 70.01,
                'max_score' => 80.00,
                'grade_point' => 3.50,
                'description' => 'Baik Sekali',
                'is_passing' => 1,
                'order_number' => 2,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'B',
                'min_score' => 65.01,
                'max_score' => 70.00,
                'grade_point' => 3.00,
                'description' => 'Baik',
                'is_passing' => 1,
                'order_number' => 3,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'BC',
                'min_score' => 60.01,
                'max_score' => 65.00,
                'grade_point' => 2.50,
                'description' => 'Cukup Baik',
                'is_passing' => 1,
                'order_number' => 4,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'C',
                'min_score' => 50.01,
                'max_score' => 60.00,
                'grade_point' => 2.00,
                'description' => 'Cukup',
                'is_passing' => 1,
                'order_number' => 5,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'D',
                'min_score' => 40.01,
                'max_score' => 50.00,
                'grade_point' => 1.00,
                'description' => 'Kurang',
                'is_passing' => 0,
                'order_number' => 6,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'E',
                'min_score' => 0.00,
                'max_score' => 40.00,
                'grade_point' => 0.00,
                'description' => 'Sangat Kurang',
                'is_passing' => 0,
                'order_number' => 7,
                'is_active' => 1,
            ],
        ];

        $builder = $db->table('grade_config');
        $builder->insertBatch($defaultGrades);

        return redirect()->to('/admin/settings')->with('success', 'Konfigurasi nilai berhasil direset ke default.');
    }
}
