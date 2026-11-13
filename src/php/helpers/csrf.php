<?php

declare(strict_types=1);

/**
 * Helper Perlindungan CSRF (Cross-Site Request Forgery).
 */

/**
 * Menghasilkan atau mengambil token CSRF aktif di dalam sesi.
 */
function generateCsrfToken(): string
{
    /**
     * CSRF adalah serangan di mana situs luar diam-diam memaksa browser Anda mengirim form ke aplikasi kita. 
     * Dengan menaruh token acak rahasia (random_bytes(32)) di sesi dan di setiap form/AJAX, 
     * server bisa memastikannya memakai hash_equals() bahwa kiriman benar-benar berasal dari form aplikasi kita sendiri.
     */
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Mencetak tag input tersembunyi berisi token CSRF untuk disisipkan di dalam form HTML.
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Memeriksa kecocokan token CSRF yang dikirim oleh form/AJAX dengan yang ada di sesi.
 */
function verifyCsrfToken(?string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}
