<?php

declare(strict_types=1);

/**
 * Modul Fungsi Akses Data Departemen (Tabel departments).
 */

/**
 * Mengambil seluruh daftar departemen diurutkan berdasarkan nama.
 */
function getAllDepartments(): array
{
    $pdo = getDbConnection();

    $sql = 'SELECT id, code, name FROM departments ORDER BY name ASC';
    $stmt = $pdo->query($sql);

    return $stmt->fetchAll();
}

/**
 * Mengecek apakah ID departemen benar-benar terdaftar di database.
 */
function departmentExists(int $departmentId): bool
{
    $pdo = getDbConnection();

    $sql = 'SELECT 1 FROM departments WHERE id = :id LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $departmentId]);

    return $stmt->fetchColumn() !== false;
}
