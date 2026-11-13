<?php

declare(strict_types=1);

require_once __DIR__ . '/php/config/app.php';
require_once __DIR__ . '/php/helpers/sanitizer.php';
require_once __DIR__ . '/php/helpers/csrf.php';
require_once __DIR__ . '/php/helpers/flash.php';
require_once __DIR__ . '/php/helpers/auth.php';

requireAuth();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
$user = currentUser();

require_once __DIR__ . '/views/partials/header.php';
require_once __DIR__ . '/views/partials/navbar.php';
require_once __DIR__ . '/views/partials/alerts.php';
?>

<div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
    <h1 class="text-2xl font-bold text-slate-900">Kerangka Layout Berhasil Terpasang</h1>
    <p class="text-sm text-slate-600 mt-2">
        Selamat datang, <strong><?= e($user['full_name'] ?? '') ?></strong>.
        Pada Task 5.5 berikutnya, halaman ini akan diisi statistik pegawai dari MySQL dan elemen multimedia SVG.
    </p>
</div>

<?php require_once __DIR__ . '/views/partials/footer.php'; ?>