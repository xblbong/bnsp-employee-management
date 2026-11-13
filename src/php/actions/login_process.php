<?php

declare(strict_types=1);

/**
 * Action Handler: Memproses pengiriman form Login (POST).
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/sanitizer.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/flash.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../functions/user_functions.php';

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// 1. Verifikasi token CSRF
if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', 'Sesi formulir tidak valid atau kedaluwarsa. Silakan coba lagi.');
    redirect('login.php');
}

// 2. Ambil input (username di-trim, password dibiarkan apa adanya)
$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

// 3. Validasi sisi server
$errors = [];

if ($username === '' || mb_strlen($username) < 3) {
    $errors['username'] = 'Username wajib diisi (minimal 3 karakter).';
}

if ($password === '' || mb_strlen($password) < 6) {
    $errors['password'] = 'Password wajib diisi (minimal 6 karakter).';
}

if (!empty($errors)) {
    setFormErrors($errors);
    setOldInput(['username' => $username]);
    setFlash('error', 'Mohon periksa kembali username dan password Anda.');
    redirect('login.php');
}

// 4. Autentikasi ke database MySQL
$user = authenticateUser($username, $password);

if ($user === null) {
    setOldInput(['username' => $username]);
    setFlash('error', 'Username atau password tidak sesuai.');
    redirect('login.php');
}

// 5. Login berhasil: buat sesi aman dan arahkan ke Dashboard
loginUser($user);
setFlash('success', 'Selamat datang, ' . $user['full_name'] . '.');
redirect('dashboard.php');
