<?php

declare(strict_types=1); // untuk pemeriksaan tipe data di PHP 8. Ini adalah best practice modern agar bug akibat tipe data tertukar cepat ketahuan.

/**
 * Konfigurasi dan Koneksi Terpusat Database menggunakan PHP PDO.
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'employee_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Mengembalikan objek koneksi PDO (satu instance per siklus request).
 */
function getDbConnection(): PDO
{
    /**
     * Variabel static di dalam fungsi membuat nilai $pdo diingat selama satu kali pemuatan halaman. 
     * Jadi kalau halaman memanggil getDbConnection() sebanyak 3 kali, PHP tidak perlu membuat 3 koneksi baru ke MySQL
     * cukup memakai ulang koneksi pertama (lebih cepat dan hemat memori).
     */
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Jika ada kesalahan query SQL, PDO langsung melempar exception agar bisa ditangkap oleh try-catch.
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //Hasil query otomatis berbentuk array asosiatif (mis. $row['full_name']), lebih rapi dan hemat memori dibanding array ganda.
        PDO::ATTR_EMULATE_PREPARES   => false, //Memaksa MySQL sendiri yang menjalankan prepared statements secara asli (native), sehingga aplikasi terlindung maksimal dari serangan SQL Injection.
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Catat detail error di log server, jangan tampilkan detail teknis ke user
        /**
         * Menampilkan pesan error asli database ke layar browser berbahaya karena bisa membocorkan nama database, host, atau struktur tabel kepada orang luar. 
         * Dengan error_log(), detail error disimpan diam-diam di log server Laragon, 
         * sedangkan pengguna hanya melihat pesan aman.
         */
        error_log('Database connection error: ' . $e->getMessage());
        http_response_code(500);
        exit('Terjadi gangguan koneksi ke database. Silakan hubungi administrator.');
    }
}
