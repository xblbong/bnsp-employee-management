<?php

declare(strict_types=1);

/**
 * Action Handler: Memproses pembaruan data pegawai (POST).
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
    redirect('employees.php');
}

$employeeId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($employeeId === false || $employeeId === null || $employeeId <= 0) {
    setFlash('error', 'ID pegawai tidak valid.');
    redirect('employees.php');
}

$existingEmployee = getEmployeeById($employeeId);
if ($existingEmployee === null) {
    setFlash('error', 'Data pegawai tidak ditemukan atau sudah dihapus.');
    redirect('employees.php');
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', 'Token keamanan formulir tidak valid atau kedaluwarsa. Silakan ulangi.');
    redirect('employee_edit.php?id=' . $employeeId);
}

$input = sanitizeEmployeeInput($_POST);
$errors = validateEmployeeInput($input, $employeeId);

if (!empty($errors)) {
    setFormErrors($errors);
    setOldInput($input);
    setFlash('error', 'Gagal memperbarui data. Mohon periksa kolom yang ditandai merah.');
    redirect('employee_edit.php?id=' . $employeeId);
}

try {
    updateEmployee($employeeId, $input);

    setFlash('success', 'Data pegawai "' . $input['full_name'] . '" berhasil diperbarui.');
    redirect('employees.php');
} catch (PDOException $e) {
    error_log('Update employee error: ' . $e->getMessage());
    setOldInput($input);
    setFlash('error', 'Terjadi kesalahan saat memperbarui data di database.');
    redirect('employee_edit.php?id=' . $employeeId);
}
