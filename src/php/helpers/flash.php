<?php

declare(strict_types=1);

/**
 * Helper Flash Message, Error Validasi Form, dan Nilai Input Lama (Pola PRG).
 */

/**
 * Menyimpan pesan notifikasi sementara (success, error, warning, info) di sesi.
 */
function setFlash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

/**
 * Mengambil dan langsung menghapus flash message agar hanya tampil satu kali.
 */
function getFlash(): ?array
{
    /**
     * getFlash() membaca pesan sukses/error lalu langsung memanggil unset(...). 
     * Ini membuat pesan hanya muncul 1 kali setelah redirect (tidak muncul terus-menerus saat di-refresh).
     */
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

/**
 * Menyimpan daftar error validasi per-kolom saat submit form gagal.
 */
function setFormErrors(array $errors): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['form_errors'] = $errors;
}

/**
 * Mengambil dan menghapus daftar error validasi form.
 */
function getFormErrors(): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $errors = $_SESSION['form_errors'] ?? [];
    unset($_SESSION['form_errors']);

    return is_array($errors) ? $errors : [];
}

/**
 * Menyimpan nilai input lama agar pengguna tidak perlu mengetik ulang saat validasi gagal.
 */
function setOldInput(array $data): void
{
    /** 
     * setOldInput() menyimpan ketikan pengguna jika pengisian form ada yang salah validasi, 
     * sehingga mereka tidak harus mengetik ulang semua kolom dari nol.
    */
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['old_input'] = $data;
}

/**
 * Mengambil dan menghapus nilai input lama dari sesi.
 */
function getOldInput(): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $old = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);

    return is_array($old) ? $old : [];
}

/**
 * Mengambil satu nilai dari array input lama dengan nilai cadangan (default).
 */
function oldValue(array $oldInput, string $key, string $default = ''): string
{
    return isset($oldInput[$key]) ? (string) $oldInput[$key] : $default;
}
