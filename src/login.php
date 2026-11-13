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
$isLoggedOut = isset($_GET['logged_out']) && $_GET['logged_out'] === '1'; //bernilai 1 karena di logout.php redirect ke login.php?logged_out=1
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(baseUrl('css/tailwind.css')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('css/style.css')) ?>">
</head>

<body class="login-page text-slate-900 antialiased flex flex-col items-center justify-center p-4">

    <main class="login-wrapper">
        <div class="login-card">

            <!-- Panel Kiri: Visual / Gambar (Tampil di iPad & Laptop) -->
            <div class="login-visual-wrap">
                <div class="login-visual">
                    <img src="<?= e(baseUrl('assets/images/login-bg.jpg')) ?>" alt="" aria-hidden="true" loading="lazy"
                        decoding="async" class="login-visual-image">

                    <!-- Brand / Identitas Atas -->
                    <div class="flex items-center gap-3">
                        <div
                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/15 border border-white/20">
                            <i class="bi bi-people-fill text-xl text-white" aria-hidden="true"></i>
                        </div>
                        <span class="text-sm font-semibold tracking-wide text-white">
                            <?= e(APP_NAME) ?>
                        </span>
                    </div>

                    <!-- Headline Tengah -->
                    <div class="my-auto py-8">
                        <p class="text-xs sm:text-sm font-medium text-blue-100 mb-2">
                            Portal Administrasi Kepegawaian
                        </p>
                        <h2 class="text-2xl lg:text-4xl font-bold tracking-tight text-white leading-tight">
                            Kelola Data Pegawai Lebih Cepat &amp; Terstruktur
                        </h2>
                        <p class="text-sm lg:text-base text-blue-100 mt-4 leading-relaxed max-w-md">
                            Pantau, catat, dan perbarui informasi pegawai dalam satu sistem yang rapi, aman, dan mudah
                            diakses.
                        </p>
                    </div>

                    <!-- Info Ringkas Bawah -->
                    <div
                        class="pt-4 border-t border-white/15 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-blue-100">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="bi bi-shield-check" aria-hidden="true"></i>
                            Akses Terproteksi
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="bi bi-person-badge" aria-hidden="true"></i>
                            Data Terpusat
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="bi bi-lightning-charge" aria-hidden="true"></i>
                            Pencatatan Cepat
                        </span>
                    </div>
                </div>
            </div>

            <!-- Panel Kanan: Form Input Login -->
            <div class="login-form-panel">
                <div class="login-form-inner">

                    <!-- Badge Ikon Khusus Tampilan HP -->
                    <div class="login-mobile-badge text-center mb-4">
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                            <i class="bi bi-people-fill text-2xl" aria-hidden="true"></i>
                        </div>
                    </div>

                    <!-- Header Identitas Aplikasi -->
                    <div class="text-center md:text-left mb-6">
                        <h1 class="text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">
                            Sistem Data Pegawai
                        </h1>
                        <p class="text-sm text-slate-500 mt-1.5">
                            Masuk untuk mengelola pencatatan data pegawai
                        </p>
                    </div>

                    <!-- Notifikasi Berhasil Logout -->
                    <!-- Jika pengguna baru saja logout ($isLoggedOut bernilai benar karena ada ?logged_out=1 di URL) DAN (&&) tidak ada pesan flash lain ($flash === null), maka tampilkan kotak hijau bertuliskan 'Anda telah berhasil keluar dari sistem.' -->
                    <?php if ($isLoggedOut && $flash === null): ?>
                        <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
                            role="alert">
                            <i class="bi bi-check-circle-fill text-green-600 mt-0.5" aria-hidden="true"></i>
                            <span>Anda berhasil keluar dari sistem</span>
                        </div>
                    <?php endif; ?>

                    <!-- Jika ada pesan flash sementara di sesi ($flash tidak kosong / !== null), tampilkan kotak alert. -->
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
                        <div class="mb-5 flex items-start gap-2.5 rounded-lg border px-4 py-3 text-sm <?= e($alertClass) ?>"
                            role="alert">
                            <i class="bi <?= e($iconClass) ?> mt-0.5" aria-hidden="true"></i>
                            <span><?= e($flash['message']) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Form Login -->
                    <form id="loginForm" action="<?= e(baseUrl('php/actions/login_process.php')) ?>" method="POST"
                        class="space-y-4" novalidate>
                        <?= csrfField() ?>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Username <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="username" name="username"
                                value="<?= e(oldValue($oldInput, 'username')) ?>" placeholder="Masukkan username"
                                autocomplete="username"
                                class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['username']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                            <p class="mt-1 text-xs text-red-600 <?= isset($errors['username']) ? '' : 'hidden' ?>"
                                data-error-for="username">
                                <?= e($errors['username'] ?? '') ?>
                            </p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Password <span class="text-red-600">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" placeholder="Masukkan password"
                                    autocomplete="current-password"
                                    class="w-full h-10 rounded-lg border pl-3.5 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['password']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                                <!-- Eye Icon -->
                                    <button type="button" id="togglePassword"
                                        class="absolute inset-y-0 right-0 z-10 flex items-center justify-center w-10 text-slate-500 hover:text-slate-700 cursor-pointer focus:outline-none"
                                        aria-label="Lihat password">
                                        <i class="bi bi-eye-slash" aria-hidden="true"></i>
                                    </button>
                            </div>
                            <p class="mt-1 text-xs text-red-600 <?= isset($errors['password']) ? '' : 'hidden' ?>"
                                data-error-for="password">
                                <?= e($errors['password'] ?? '') ?>
                            </p>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="pt-1">
                            <button type="submit"
                                class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-colors cursor-pointer">
                                <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                                <span>Masuk</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <p class="login-copyright text-center text-xs mt-5">
            &copy; <?= date('Y') ?> <?= e(APP_NAME) ?>
        </p>
    </main>
    <script src="<?= e(baseUrl('js/validation.js')) ?>"></script>
    <script>
        (function () {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput && !toggleBtn.dataset.toggleBound) {
                toggleBtn.dataset.toggleBound = 'true';
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    const icon = toggleBtn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('bi-eye', isPassword);
                        icon.classList.toggle('bi-eye-slash', !isPassword);
                    }

                    toggleBtn.setAttribute(
                        'aria-label',
                        isPassword ? 'Sembunyikan password' : 'Lihat password'
                    );
                });
            }
        })();
    </script>
</body>

</html>