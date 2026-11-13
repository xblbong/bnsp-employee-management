<?php

declare(strict_types=1);

/**
 * Helper Manajemen Sesi & Otorisasi Halaman / API.
 */

/**
 * Memulai sesi PHP dengan pengaturan cookie yang lebih aman.
 */
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    if (PHP_SAPI !== 'cli') {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true, //Cookie sesi tidak bisa dicuri oleh JavaScript browser.
            'samesite' => 'Lax',
        ]);
    }

    session_start();
}

/**
 * Mengecek apakah pengguna sedang login.
 */
function isLoggedIn(): bool
{
    startSecureSession();
    return !empty($_SESSION['user']['id']);
}

/**
 * Mengembalikan data pengguna yang sedang login, atau null jika belum login.
 */
function currentUser(): ?array
{
    startSecureSession();
    return $_SESSION['user'] ?? null;
}

/**
 * Menyimpan sesi pengguna setelah verifikasi login berhasil.
 */
function loginUser(array $user): void
{
    startSecureSession();

    // Regenerasi ID sesi untuk mencegah serangan session fixation
    if (PHP_SAPI !== 'cli') {
        session_regenerate_id(true);
        /**
         * Saat login berhasil, PHP membuat ID sesi baru agar ID sesi lama tidak bisa dibajak (session fixation).
         * Pemisahan requireAuth() (redirect ke login.php untuk halaman biasa) dan 
         * requireAuthJson() (respons 401 JSON untuk permintaan AJAX Axios).
         */
    }

    $_SESSION['user'] = [
        'id'        => (int) $user['id'],
        'username'  => (string) $user['username'],
        'full_name' => (string) $user['full_name'],
        'role'      => (string) $user['role'],
    ];
}

/**
 * Menghapus sesi dan cookie login pengguna saat logout.
 */
function logoutUser(): void
{
    startSecureSession();
    $_SESSION = [];

    if (PHP_SAPI !== 'cli' && ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Melindungi halaman HTML: jika belum login, alihkan ke halaman login.
 */
function requireAuth(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        redirect('login.php');
    }
}

/**
 * Melindungi endpoint API AJAX: jika belum login, kembalikan HTTP 401 JSON.
 */
function requireAuthJson(): void
{
    if (!isLoggedIn()) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Sesi login telah berakhir. Silakan login kembali.',
        ]);
        exit;
    }
}

/**
 * Jika pengguna sudah login dan membuka halaman login, arahkan langsung ke dashboard.
 */
function redirectIfAuth(): void
{
    if (isLoggedIn()) {
        redirect('dashboard.php');
    }
}
