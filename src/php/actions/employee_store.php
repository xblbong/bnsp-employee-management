<?php

declare(strict_types=1);

/**
 * Action Handler: Memproses penyimpanan data pegawai baru (POST).
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/sanitizer.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/flash.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../functions/department_functions.php';
require_once __DIR__ . '/../functions/employee_functions.php';
require_once __DIR__ . '/../helpers/validation.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('employee_add.php');
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', 'Token keamanan formulir tidak valid atau kedaluwarsa. Silakan ulangi.');
    redirect('employee_add.php');
}

$input = sanitizeEmployeeInput($_POST);
$errors = validateEmployeeInput($input);

if (!empty($errors)) {
    setFormErrors($errors);
    setOldInput($input);
    setFlash('error', 'Gagal menyimpan data. Mohon periksa kolom yang ditandai merah.');
    redirect('employee_add.php');
}

try {
    $user = currentUser();
    $createdBy = isset($user['id']) ? (int) $user['id'] : null;

    createEmployee($input, $createdBy);

    setFlash('success', 'Data pegawai "' . $input['full_name'] . '" berhasil ditambahkan.');
    redirect('employees.php');
} catch (PDOException $e) {
    error_log('Create employee error: ' . $e->getMessage());
    setOldInput($input);
    setFlash('error', 'Terjadi kesalahan saat menyimpan data ke database.');
    redirect('employee_add.php');
}
