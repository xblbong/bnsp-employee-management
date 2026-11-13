<?php

declare(strict_types=1);

$activeMenu = $activePage ?? '';
$loggedInUser = currentUser();

$navItems = [
    [
        'key'   => 'dashboard',
        'label' => 'Dashboard',
        'href'  => baseUrl('dashboard.php'),
        'icon'  => 'bi-grid',
    ],
    [
        'key'   => 'employees',
        'label' => 'Daftar Pegawai',
        'href'  => baseUrl('employees.php'),
        'icon'  => 'bi-people',
    ],
    [
        'key'   => 'employee_add',
        'label' => 'Tambah Pegawai',
        'href'  => baseUrl('employee_add.php'),
        'icon'  => 'bi-person-plus',
    ],
];
?>

<!-- Backdrop Gelap untuk Drawer Mobile -->
<div id="sidebarBackdrop" class="fixed inset-0 z-30 bg-slate-900/40 hidden lg:hidden" aria-hidden="true"></div>

<!-- Sidebar Navigasi Utama -->
<aside
    id="appSidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col transform -translate-x-full transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto"
    aria-label="Navigasi Utama">
    <!-- Brand / Logo Aplikasi -->
    <div class="h-16 px-5 border-b border-slate-200 flex items-center justify-between">
        <a href="<?= e(baseUrl('dashboard.php')) ?>" class="flex items-center gap-2.5 text-slate-900 font-bold text-base">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white">
                <i class="bi bi-people-fill text-base" aria-hidden="true"></i>
            </span>
            <span class="tracking-tight">Data Pegawai</span>
        </a>

        <button
            type="button"
            id="sidebarCloseBtn"
            class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 cursor-pointer"
            aria-label="Tutup menu">
            <i class="bi bi-x-lg text-lg" aria-hidden="true"></i>
        </button>
    </div>

    <!-- Daftar Menu -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <?php foreach ($navItems as $item): ?>
            <?php
            $isActive = $activeMenu === $item['key'];
            $linkClass = $isActive
                ? 'bg-blue-50 text-blue-700 font-semibold'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium';
            $iconColor = $isActive ? 'text-blue-600' : 'text-slate-400';
            ?>
            <a
                href="<?= e($item['href']) ?>"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors <?= e($linkClass) ?>"
                <?= $isActive ? 'aria-current="page"' : '' ?>>
                <i class="bi <?= e($item['icon']) ?> text-base <?= e($iconColor) ?>" aria-hidden="true"></i>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Info Pengguna & Tombol Logout -->
    <div class="p-4 border-t border-slate-200 bg-slate-50/60">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-semibold text-sm shrink-0">
                <i class="bi bi-person-fill" aria-hidden="true"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-900 truncate">
                    <?= e($loggedInUser['full_name'] ?? 'Admin') ?>
                </p>
                <p class="text-xs text-slate-500 truncate">
                    <?= e($loggedInUser['username'] ?? '') ?>
                </p>
            </div>
        </div>

        <form action="<?= e(baseUrl('logout.php')) ?>" method="POST">
            <?= csrfField() ?>
            <button
                type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition-colors cursor-pointer">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Kolom Konten Utama -->
<div class="flex-1 flex flex-col min-w-0">
    <!-- Top Header Khusus Mobile/Tablet -->
    <header class="lg:hidden sticky top-0 z-20 h-14 bg-white border-b border-slate-200 px-4 flex items-center justify-between">
        <button
            type="button"
            id="sidebarOpenBtn"
            class="p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 cursor-pointer"
            aria-label="Buka menu navigasi">
            <i class="bi bi-list text-xl" aria-hidden="true"></i>
        </button>

        <span class="text-sm font-bold text-slate-900">Sistem Data Pegawai</span>

        <span class="w-8" aria-hidden="true"></span>
    </header>

    <!-- Area Konten Halaman (dibuka di sini, ditutup di footer.php) -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-6xl w-full mx-auto animate-fade-in-up"></main>
</div>