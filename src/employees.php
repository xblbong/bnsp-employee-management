<?php

declare(strict_types=1);

require_once __DIR__ . '/php/config/app.php';
require_once __DIR__ . '/php/config/database.php';
require_once __DIR__ . '/php/helpers/sanitizer.php';
require_once __DIR__ . '/php/helpers/csrf.php';
require_once __DIR__ . '/php/helpers/flash.php';
require_once __DIR__ . '/php/helpers/auth.php';
require_once __DIR__ . '/php/functions/employee_functions.php';

requireAuth();

$pageTitle = 'Daftar Pegawai';
$activePage = 'employees';

$search = trim((string) ($_GET['q'] ?? ''));
$statusFilter = trim((string) ($_GET['status'] ?? ''));
if (!in_array($statusFilter, ['Aktif', 'Nonaktif'], true)) {
    $statusFilter = '';
}

$employees = getAllEmployees($search, $statusFilter);
$isFiltering = ($search !== '' || $statusFilter !== '');

$pageScripts = [
    'assets/vendor/axios/axios.min.js',
    'js/employees.js',
];

$viewFile = __DIR__ . '/views/admin/employees/index.php';
require __DIR__ . '/views/layouts/admin.php';
