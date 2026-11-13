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

$employeeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$employee = ($employeeId !== false && $employeeId !== null && $employeeId > 0)
    ? getEmployeeById($employeeId)
    : null;

if ($employee === null) {
    http_response_code(404);
}

$pageTitle = $employee !== null ? 'Detail Pegawai - ' . $employee['full_name'] : 'Pegawai Tidak Ditemukan';
$activePage = 'employees';

$pageScripts = [];

$viewFile = __DIR__ . '/views/admin/employees/detail.php';
require __DIR__ . '/views/layouts/admin.php';
