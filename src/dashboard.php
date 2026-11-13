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

require_once __DIR__ . '/views/partials/header.php';
require_once __DIR__ . '/views/partials/navbar.php';
require_once __DIR__ . '/views/partials/alerts.php';
?>

<!-- Header Halaman -->
<div class="mb-6">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
    <p class="text-sm text-slate-500 mt-1">
        Selamat datang, <span class="font-medium text-slate-700"><?= e($user['full_name'] ?? 'Admin') ?></span>.
        Berikut ringkasan pencatatan data pegawai saat ini.
    </p>
</div>

<!-- Banner Selamat Datang & Elemen Multimedia (MUK Tahap 1c) -->
<section class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs mb-6">
    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-3 text-center md:text-left">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-100">
                <i class="bi bi-building" aria-hidden="true"></i>
                <span>Portal Kepegawaian</span>
            </span>
            <h2 class="text-lg sm:text-xl font-bold text-slate-900">
                Kelola Data Pegawai Secara Terstruktur
            </h2>
            <p class="text-sm text-slate-600 max-w-xl">
                Tambahkan pegawai baru, perbarui jabatan atau status kerja, serta tinjau detail profil pegawai dalam satu sistem terpusat.
            </p>
            <div class="pt-1 flex flex-wrap items-center justify-center md:justify-start gap-3">
                <a
                    href="<?= e(baseUrl('employee_add.php')) ?>"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="bi bi-person-plus" aria-hidden="true"></i>
                    <span>Tambah Pegawai</span>
                </a>
                <a
                    href="<?= e(baseUrl('employees.php')) ?>"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="bi bi-people" aria-hidden="true"></i>
                    <span>Lihat Daftar Pegawai</span>
                </a>
            </div>
        </div>

        <div class="shrink-0">
            <img
                src="<?= e(baseUrl('assets/images/dashboard-hero.svg')) ?>"
                alt="Ilustrasi Manajemen Data Pegawai"
                class="w-48 sm:w-56 h-auto select-none">
        </div>
    </div>
</section>

<!-- 3 Kartu Ringkasan Statistik -->
<section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6" aria-label="Ringkasan Statistik Pegawai">
    <!-- Total Pegawai -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pegawai</p>
            <p class="text-3xl font-bold text-slate-900 mt-1"><?= e((string) $stats['total']) ?></p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
            <i class="bi bi-people text-xl" aria-hidden="true"></i>
        </div>
    </div>

    <!-- Pegawai Aktif -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pegawai Aktif</p>
            <p class="text-3xl font-bold text-slate-900 mt-1"><?= e((string) $stats['active']) ?></p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 border border-green-100 flex items-center justify-center">
            <i class="bi bi-person-check text-xl" aria-hidden="true"></i>
        </div>
    </div>

    <!-- Pegawai Nonaktif -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pegawai Nonaktif</p>
            <p class="text-3xl font-bold text-slate-900 mt-1"><?= e((string) $stats['inactive']) ?></p>
        </div>
        <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center">
            <i class="bi bi-person-dash text-xl" aria-hidden="true"></i>
        </div>
    </div>
</section>

<!-- Tabel Ringkas Pegawai Terbaru -->
<section class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-4">
        <h2 class="text-base font-semibold text-slate-900">Pegawai Terbaru</h2>
        <a href="<?= e(baseUrl('employees.php')) ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
            <span>Lihat Semua</span>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </a>
    </div>

    <?php if (empty($recentEmployees)): ?>
        <div class="p-8 text-center">
            <div class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-slate-100 text-slate-400 mb-3">
                <i class="bi bi-people text-xl" aria-hidden="true"></i>
            </div>
            <p class="text-sm font-medium text-slate-700">Belum ada data pegawai</p>
            <p class="text-xs text-slate-500 mt-1">Mulai dengan menambahkan data pegawai pertama Anda.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600">
                        <th class="px-5 py-3">NIP</th>
                        <th class="px-5 py-3">Nama Lengkap</th>
                        <th class="px-5 py-3">Departemen</th>
                        <th class="px-5 py-3">Jabatan</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    <?php foreach ($recentEmployees as $employee): ?>
                        <?php
                        $isAktif = ($employee['status'] ?? '') === 'Aktif';
                        $badgeClass = $isAktif
                            ? 'bg-green-100 text-green-800'
                            : 'bg-slate-100 text-slate-600';
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs text-slate-600 whitespace-nowrap">
                                <?= e($employee['employee_code']) ?>
                            </td>
                            <td class="px-5 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                <a href="<?= e(baseUrl('employee_detail.php?id=' . (int) $employee['id'])) ?>" class="hover:text-blue-600 transition-colors">
                                    <?= e($employee['full_name']) ?>
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap">
                                <?= e($employee['department_name']) ?>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap">
                                <?= e($employee['position']) ?>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= e($badgeClass) ?>">
                                    <?= e($employee['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/views/partials/footer.php'; ?>