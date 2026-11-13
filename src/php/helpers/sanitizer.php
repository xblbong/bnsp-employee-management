<?php

declare(strict_types=1);

/**
 * Helper Sanitasi Output (Anti-XSS) & Utilitas URL/Redirect.
 */

/**
 * Mengamankan teks sebelum dicetak ke HTML untuk mencegah serangan XSS.
 */
function e(?string $value): string //fungsi e() adalah singkatan dari escape, yang artinya mengamankan teks sebelum dicetak ke HTML. Ini mencegah serangan XSS (Cross-Site Scripting) dengan mengubah karakter khusus menjadi entitas HTML.
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Membangun URL lengkap berdasarkan konstanta BASE_URL.
 */
function baseUrl(string $path = ''): string
{
    if ($path === '') {
        return BASE_URL;
    }

    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Mengarahkan browser ke halaman lain di dalam aplikasi lalu menghentikan skrip.
 */
function redirect(string $path): never
{
    header('Location: ' . baseUrl($path));
    exit;
}
