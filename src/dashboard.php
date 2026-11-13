<?php

declare(strict_types=1);

require_once __DIR__ . '/php/config/app.php';
require_once __DIR__ . '/php/helpers/sanitizer.php';
require_once __DIR__ . '/php/helpers/csrf.php';
require_once __DIR__ . '/php/helpers/flash.php';
require_once __DIR__ . '/php/helpers/auth.php';

requireAuth();

$user = currentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sementara — <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(baseUrl('css/tailwind.css')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>">
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 p-6">

    <div class="max-w-2xl mx-auto bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
        <?php if ($flash !== null): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <h1 class="text-xl font-bold text-slate-900">Dashboard (Uji Sesi Login)</h1>
        <p class="text-sm text-slate-600 mt-2">
            Anda berhasil masuk sebagai <strong><?= e($user['full_name'] ?? '') ?></strong>
            (username: <code><?= e($user['username'] ?? '') ?></code>, role: <code><?= e($user['role'] ?? '') ?></code>).
        </p>

        <form action="<?= e(baseUrl('logout.php')) ?>" method="POST" class="mt-6">
            <?= csrfField() ?>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 cursor-pointer">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</body>

</html>