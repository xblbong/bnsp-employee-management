<?php

declare(strict_types=1);

require_once __DIR__ . '/php/config/app.php';
require_once __DIR__ . '/php/config/database.php';
require_once __DIR__ . '/php/helpers/sanitizer.php';
require_once __DIR__ . '/php/helpers/csrf.php';
require_once __DIR__ . '/php/helpers/flash.php';
require_once __DIR__ . '/php/helpers/auth.php';
require_once __DIR__ . '/php/functions/department_functions.php';
require_once __DIR__ . '/php/functions/employee_functions.php';

requireAuth();

$employeeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$employee = ($employeeId !== false && $employeeId !== null && $employeeId > 0)
    ? getEmployeeById($employeeId)
    : null;

if ($employee === null) {
    http_response_code(404);
}

$pageTitle = $employee !== null ? 'Edit Pegawai - ' . $employee['full_name'] : 'Pegawai Tidak Ditemukan';
$activePage = 'employees';

$departments = $employee !== null ? getAllDepartments() : [];
$errors = getFormErrors();
$oldInput = getOldInput();
$hasOldInput = !empty($oldInput);

$getFieldValue = static function (string $key, string $default = '') use ($hasOldInput, $oldInput, $employee): string {
    if ($hasOldInput) {
        return oldValue($oldInput, $key, $default);
    }

    return isset($employee[$key]) ? (string) $employee[$key] : $default;
};

$pageScripts = ['js/validation.js'];

$viewFile = __DIR__ . '/views/admin/employees/edit.php';
require __DIR__ . '/views/layouts/admin.php';
