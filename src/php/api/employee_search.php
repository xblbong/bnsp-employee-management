<?php

declare(strict_types=1);

/**
 * API Endpoint JSON (AJAX): Mencari dan menyaring daftar pegawai.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/sanitizer.php';
require_once __DIR__ . '/../helpers/flash.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../functions/employee_functions.php';

// Memastikan pengguna telah login dan memiliki hak akses yang sesuai
requireAuthJson();

//jika metode permintaan bukan GET, kembalikan respons 405 Method Not Allowed
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metode permintaan tidak diizinkan.',
    ]);
    exit;
}

$search = trim((string) ($_GET['q'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));

//jika status tidak valid, setel ke string kosong untuk mengabaikan filter status
if (!in_array($status, ['Aktif', 'Nonaktif'], true)) {
    $status = '';
}

//coba panggil getAllEmployees() untuk mendapatkan daftar pegawai berdasarkan parameter pencarian dan status
try {
    $employees = getAllEmployees($search, $status);

    echo json_encode([
        'success'   => true,
        'count'     => count($employees),
        'employees' => $employees,
    ]);
} catch (PDOException $e) {
    error_log('AJAX search employee error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal memuat data pegawai.',
    ]);
}
