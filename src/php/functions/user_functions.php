<?php

declare(strict_types=1);

/**
 * Fungsi Logika & Akses Data Pengguna (Tabel users).
 */

/**
 * Mencari satu data pengguna berdasarkan username menggunakan PDO prepared statement.
 */
function findUserByUsername(string $username): ?array
{
    $pdo = getDbConnection();

    $sql = 'SELECT id, username, full_name, password_hash, role
            FROM users
            WHERE username = :username
            LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    return $user !== false ? $user : null;
}

/**
 * Memverifikasi kredensial login (username dan password).
 * Mengembalikan array data user jika cocok, atau null jika gagal.
 */
function authenticateUser(string $username, string $password): ?array
{
    $user = findUserByUsername($username);

    if ($user === null) {
        return null;
    }

    /**
     * Saat user mengetik password, authenticateUser() mencocokkannya dengan hash bcrypt di database memakai password_verify(). 
     * Jika gagal (baik karena username tidak ada maupun password salah), 
     * pesan error yang tampil sengaja disamakan ("Username atau password tidak sesuai")
     * agar penyerang tidak bisa menebak username mana yang terdaftar.
     */
    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }

    return $user;
}
