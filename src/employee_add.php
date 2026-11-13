<?php

declare(strict_types=1);

require_once __DIR__ . '/php/config/app.php';
require_once __DIR__ . '/php/config/database.php';
require_once __DIR__ . '/php/helpers/sanitizer.php';
require_once __DIR__ . '/php/helpers/csrf.php';
require_once __DIR__ . '/php/helpers/flash.php';
require_once __DIR__ . '/php/helpers/auth.php';
require_once __DIR__ . '/php/functions/department_functions.php';

requireAuth();

$pageTitle = 'Tambah Pegawai';
$activePage = 'employee_add';

$departments = getAllDepartments();
$errors = getFormErrors();
$oldInput = getOldInput();
$selectedStatus = oldValue($oldInput, 'status', 'Aktif');
$selectedDepartment = oldValue($oldInput, 'department_id');

$pageScripts = ['js/validation.js'];

$viewFile = __DIR__ . '/views/admin/employees/add.php';
require __DIR__ . '/views/layouts/admin.php';
