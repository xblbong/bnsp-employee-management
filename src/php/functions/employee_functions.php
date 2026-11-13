<?php

declare(strict_types=1);

/**
 * Modul Fungsi Logika & Akses Data Pegawai (Tabel employees).
 * Seluruh interaksi database menggunakan PHP PDO prepared statements.
 */

/**
 * Mengambil ringkasan jumlah pegawai (Total, Aktif, dan Nonaktif) untuk Dashboard.
 */
function getEmployeeSummaryStats(): array
{
    $pdo = getDbConnection();

    $sql = "SELECT
                COUNT(*) AS total,
                COALESCE(SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END), 0) AS active,
                COALESCE(SUM(CASE WHEN status = 'Nonaktif' THEN 1 ELSE 0 END), 0) AS inactive
            FROM employees";

    $stmt = $pdo->query($sql);
    $row = $stmt->fetch();

    return [
        'total' => (int) ($row['total'] ?? 0),
        'active' => (int) ($row['active'] ?? 0),
        'inactive' => (int) ($row['inactive'] ?? 0),
    ];
}

/**
 * Mengambil daftar sejumlah pegawai terbaru beserta nama departemennya.
 */
function getRecentEmployees(int $limit = 5): array
{
    $pdo = getDbConnection();

    $sql = 'SELECT e.id, e.employee_code, e.full_name, e.position, e.status,
                   d.name AS department_name
            FROM employees e
            INNER JOIN departments d ON e.department_id = d.id
            ORDER BY e.created_at DESC, e.id DESC
            LIMIT :limit';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * Mengambil seluruh daftar pegawai dengan dukungan pencarian kata kunci dan filter status.
 */
function getAllEmployees(string $search = '', string $status = ''): array
{
    $pdo = getDbConnection();

    $sql = 'SELECT e.id, e.employee_code, e.full_name, e.email, e.phone,
                   e.position, e.hire_date, e.status,
                   d.name AS department_name, d.code AS department_code
            FROM employees e
            INNER JOIN departments d ON e.department_id = d.id
            WHERE 1 = 1';

    $params = [];

    $trimmedSearch = trim($search);
    if ($trimmedSearch !== '') {
        $sql .= ' AND (e.employee_code LIKE :q_code
                       OR e.full_name LIKE :q_name
                       OR e.position LIKE :q_position
                       OR d.name LIKE :q_dept)';
        $likePattern = '%' . $trimmedSearch . '%';
        $params[':q_code'] = $likePattern;
        $params[':q_name'] = $likePattern;
        $params[':q_position'] = $likePattern;
        $params[':q_dept'] = $likePattern;
    }

    if (in_array($status, ['Aktif', 'Nonaktif'], true)) {
        $sql .= ' AND e.status = :status';
        $params[':status'] = $status;
    }

    $sql .= ' ORDER BY e.created_at DESC, e.id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}
