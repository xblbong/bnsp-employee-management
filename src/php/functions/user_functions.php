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

    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }

    return $user;
}
