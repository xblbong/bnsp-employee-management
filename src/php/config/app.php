<?php

declare(strict_types=1); // untuk pemeriksaan tipe data di PHP 8. Ini adalah best practice modern agar bug akibat tipe data tertukar cepat ketahuan.

/**
 * Konfigurasi Dasar Aplikasi
 * Menyimpan identitas aplikasi, zona waktu, dan alamat dasar (BASE_URL).
 */

date_default_timezone_set('Asia/Jakarta');

define('APP_NAME', 'Sistem Pencatatan Data Pegawai');

/**
 * Menyimpan awalan URL proyek kita (/employee-management/src). 
 * Jadi nanti saat membuat link menu atau melakukan redirect, 
 * kita tidak mudah salah hitung path ../ karena cukup memanggil BASE_URL.
 */
define('BASE_URL', '/employee-management/src');
