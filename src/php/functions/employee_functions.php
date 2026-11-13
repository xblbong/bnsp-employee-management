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
        'total'    => (int) ($row['total'] ?? 0),
        'active'   => (int) ($row['active'] ?? 0),
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

/**
 * Mengecek apakah NIP / Kode Pegawai sudah dipakai pegawai lain.
 */
function isEmployeeCodeExists(string $employeeCode, ?int $excludeId = null): bool
{
    $pdo = getDbConnection();

    $sql = 'SELECT 1 FROM employees WHERE employee_code = :code';
    $params = [':code' => $employeeCode];

    if ($excludeId !== null) {
        $sql .= ' AND id != :exclude_id';
        $params[':exclude_id'] = $excludeId;
    }

    $sql .= ' LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn() !== false;
}

/**
 * Mengecek apakah alamat Email sudah dipakai pegawai lain.
 */
function isEmployeeEmailExists(string $email, ?int $excludeId = null): bool
{
    $pdo = getDbConnection();

    $sql = 'SELECT 1 FROM employees WHERE email = :email';
    $params = [':email' => $email];

    if ($excludeId !== null) {
        $sql .= ' AND id != :exclude_id';
        $params[':exclude_id'] = $excludeId;
    }

    $sql .= ' LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn() !== false;
}

/**
 * Menyimpan data pegawai baru ke database dan mengembalikan ID baris barunya.
 */
function createEmployee(array $data, ?int $createdBy = null): int
{
    $pdo = getDbConnection();

    $sql = 'INSERT INTO employees (
                employee_code,
                full_name,
                email,
                phone,
                department_id,
                position,
                hire_date,
                status,
                address,
                created_by
            ) VALUES (
                :employee_code,
                :full_name,
                :email,
                :phone,
                :department_id,
                :position,
                :hire_date,
                :status,
                :address,
                :created_by
            )';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':employee_code' => $data['employee_code'],
        ':full_name'     => $data['full_name'],
        ':email'         => $data['email'],
        ':phone'         => $data['phone'],
        ':department_id' => (int) $data['department_id'],
        ':position'      => $data['position'],
        ':hire_date'     => $data['hire_date'],
        ':status'        => $data['status'],
        ':address'       => $data['address'] !== '' ? $data['address'] : null,
        ':created_by'    => $createdBy,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Mengambil detail lengkap satu pegawai berdasarkan ID (dipakai di Detail & Edit).
 */
function getEmployeeById(int $id): ?array
{
    $pdo = getDbConnection();

    $sql = 'SELECT e.id, e.employee_code, e.full_name, e.email, e.phone,
                   e.department_id, e.position, e.hire_date, e.status,
                   e.address, e.created_by, e.created_at, e.updated_at,
                   d.name AS department_name, d.code AS department_code,
                   u.full_name AS created_by_name
            FROM employees e
            INNER JOIN departments d ON e.department_id = d.id
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.id = :id
            LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    return $row !== false ? $row : null;
}

/**
 * Mengubah format tanggal database menjadi format Indonesia yang mudah dibaca.
 */
function formatIndonesianDate(?string $dateString, bool $withTime = false): string
{
    if ($dateString === null || trim($dateString) === '') {
        return '-';
    }

    try {
        $dt = new DateTime($dateString);
    } catch (Exception $e) {
        return $dateString;
    }

    $months = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $day = $dt->format('d');
    $month = $months[(int) $dt->format('n')] ?? $dt->format('m');
    $year = $dt->format('Y');

    $formatted = $day . ' ' . $month . ' ' . $year;

    if ($withTime) {
        $formatted .= ', ' . $dt->format('H:i') . ' WIB';
    }

    return $formatted;
}

/**
 * Mengambil maksimal 2 huruf inisial dari nama lengkap untuk avatar profil.
 */
function getNameInitials(string $fullName): string
{
    $words = preg_split('/\s+/', trim($fullName)) ?: [];
    if (empty($words) || $words[0] === '') {
        return 'PG';
    }

    $first = mb_strtoupper(mb_substr($words[0], 0, 1));
    $second = isset($words[1]) ? mb_strtoupper(mb_substr($words[1], 0, 1)) : '';

    return $first . $second;
}
