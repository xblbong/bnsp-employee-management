<?php

declare(strict_types=1);

/**
 * Skrip Seeder CLI untuk membuat akun admin default dengan password_hash() asli.
 * Hanya boleh dijalankan melalui terminal (CLI), bukan melalui browser.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Akses ditolak: skrip ini hanya untuk dijalankan melalui terminal CLI.');
}

$host = '127.0.0.1';
$port = '3306';
$dbName = 'employee_db';
$dbUser = 'root';
$dbPass = '';

$defaultUsername = 'admin';
$defaultFullName = 'Administrator HR';
$defaultPassword = 'admin123';
$defaultRole = 'admin';

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (username, full_name, password_hash, role)
            VALUES (:username, :full_name, :password_hash, :role)
            ON DUPLICATE KEY UPDATE
                full_name = VALUES(full_name),
                password_hash = VALUES(password_hash),
                role = VALUES(role)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $defaultUsername,
        ':full_name' => $defaultFullName,
        ':password_hash' => $passwordHash,
        ':role' => $defaultRole,
    ]);

    // Verifikasi langsung hash yang tersimpan di database
    $checkStmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = :username');
    $checkStmt->execute([':username' => $defaultUsername]);
    $user = $checkStmt->fetch();

    if ($user && password_verify($defaultPassword, $user['password_hash'])) {
        echo 'ADMIN_SEED_OK' . PHP_EOL;
        echo 'Username : ' . $defaultUsername . PHP_EOL;
        echo 'Password : ' . $defaultPassword . PHP_EOL;
    } else {
        echo 'ADMIN_SEED_FAILED: Verifikasi password gagal.' . PHP_EOL;
        exit(1);
    }
} catch (PDOException $e) {
    echo 'SEED_ERROR: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
