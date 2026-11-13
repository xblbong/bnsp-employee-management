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

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

$user = currentUser();
$stats = getEmployeeSummaryStats();
$recentEmployees = getRecentEmployees(5);

$pageScripts = [];

$viewFile = __DIR__ . '/views/admin/dashboard.php';
require __DIR__ . '/views/layouts/admin.php';
