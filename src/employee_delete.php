<?php

declare(strict_types=1);

/**
 * API Endpoint JSON (AJAX): Menghapus data pegawai berdasarkan ID.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/sanitizer.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/flash.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../functions/employee_functions.php';

requireAuthJson();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metode permintaan tidak diizinkan.',
    ]);
    exit;
}

// Axios mengirim payload dalam format JSON di request body
$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody ?: '', true);
if (!is_array($payload)) {
    $payload = $_POST;
}

if (!verifyCsrfToken(isset($payload['csrf_token']) ? (string) $payload['csrf_token'] : null)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Token keamanan tidak valid atau kedaluwarsa. Silakan muat ulang halaman.',
    ]);
    exit;
}

$employeeId = filter_var($payload['id'] ?? null, FILTER_VALIDATE_INT);
if ($employeeId === false || $employeeId === null || $employeeId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID pegawai tidak valid.',
    ]);
    exit;
}

$employee = getEmployeeById($employeeId);
if ($employee === null) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Data pegawai tidak ditemukan atau sudah dihapus sebelumnya.',
    ]);
    exit;
}

try {
    $deleted = deleteEmployee($employeeId);

    if (!$deleted) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Data pegawai gagal dihapus karena tidak ditemukan.',
        ]);
        exit;
    }

    echo json_encode([
        'success'    => true,
        'message'    => 'Data pegawai "' . $employee['full_name'] . '" berhasil dihapus.',
        'deleted_id' => $employeeId,
    ]);
} catch (PDOException $e) {
    error_log('AJAX delete employee error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan server saat menghapus data pegawai.',
    ]);
}
