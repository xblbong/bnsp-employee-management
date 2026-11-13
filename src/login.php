<?php

declare(strict_types=1);

require_once __DIR__ . '/php/config/app.php';
require_once __DIR__ . '/php/helpers/sanitizer.php';
require_once __DIR__ . '/php/helpers/csrf.php';
require_once __DIR__ . '/php/helpers/flash.php';
require_once __DIR__ . '/php/helpers/auth.php';

startSecureSession();
redirectIfAuth();

$flash = getFlash();
$errors = getFormErrors();
$oldInput = getOldInput();
$isLoggedOut = isset($_GET['logged_out']) && $_GET['logged_out'] === '1';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(baseUrl('css/tailwind.css')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>">
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased flex items-center justify-center p-4">

    <main class="w-full max-w-md">
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs p-6 sm:p-8">

            <!-- Header Identitas Aplikasi -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 mb-4">
                    <i class="bi bi-people-fill text-2xl" aria-hidden="true"></i>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Sistem Data Pegawai
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Masuk untuk mengelola pencatatan data pegawai
                </p>
            </div>

            <!-- Notifikasi Berhasil Logout -->
            <?php if ($isLoggedOut && $flash === null): ?>
                <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                    <i class="bi bi-check-circle-fill text-green-600 mt-0.5" aria-hidden="true"></i>
                    <span>Anda telah berhasil keluar dari sistem.</span>
                </div>
            <?php endif; ?>

            <!-- Flash Alert Error / Info -->
            <?php if ($flash !== null): ?>
                <?php
                $isSuccess = $flash['type'] === 'success';
                $alertClass = $isSuccess
                    ? 'border-green-200 bg-green-50 text-green-800'
                    : 'border-red-200 bg-red-50 text-red-800';
                $iconClass = $isSuccess
                    ? 'bi-check-circle-fill text-green-600'
                    : 'bi-exclamation-circle-fill text-red-600';
                ?>
                <div class="mb-5 flex items-start gap-2.5 rounded-lg border px-4 py-3 text-sm <?= e($alertClass) ?>" role="alert">
                    <i class="bi <?= e($iconClass) ?> mt-0.5" aria-hidden="true"></i>
                    <span><?= e($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form id="loginForm" action="<?= e(baseUrl('php/actions/login_process.php')) ?>" method="POST" class="space-y-4">
                <?= csrfField() ?>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Username <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= e(oldValue($oldInput, 'username')) ?>"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['username']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                    <p class="mt-1 text-xs text-red-600 <?= isset($errors['username']) ? '' : 'hidden' ?>" data-error-for="username">
                        <?= e($errors['username'] ?? '') ?>
                    </p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Password <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['password']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                    <p class="mt-1 text-xs text-red-600 <?= isset($errors['password']) ? '' : 'hidden' ?>" data-error-for="password">
                        <?= e($errors['password'] ?? '') ?>
                    </p>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-1">
                    <button
                        type="submit"
                        class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-colors cursor-pointer">
                        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                        <span>Masuk</span>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-5">
            &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>
        </p>
    </main>

</body>

</html>