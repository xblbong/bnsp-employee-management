<?php

declare(strict_types=1);

/**
 * Memproses keluar sesi pengguna (Logout) dengan metode POST + verifikasi CSRF.
 */

require_once __DIR__ . '/php/config/app.php';
require_once __DIR__ . '/php/helpers/sanitizer.php';
require_once __DIR__ . '/php/helpers/csrf.php';
require_once __DIR__ . '/php/helpers/flash.php';
require_once __DIR__ . '/php/helpers/auth.php';

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    isLoggedIn() ? redirect('dashboard.php') : redirect('login.php');
}

//jika verifikasi token CSRF gagal, maka set flash error dan redirect ke dashboard.php
if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', 'Permintaan logout tidak valid.');
    redirect('dashboard.php');
}

logoutUser();
redirect('login.php?logged_out=1');
