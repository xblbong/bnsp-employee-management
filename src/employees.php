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

$pageTitle = 'Daftar Pegawai';
$activePage = 'employees';

$search = trim((string) ($_GET['q'] ?? ''));
$statusFilter = trim((string) ($_GET['status'] ?? ''));
if (!in_array($statusFilter, ['Aktif', 'Nonaktif'], true)) {
    $statusFilter = '';
}

$employees = getAllEmployees($search, $statusFilter);
$isFiltering = ($search !== '' || $statusFilter !== '');

require_once __DIR__ . '/views/partials/header.php';
require_once __DIR__ . '/views/partials/navbar.php';
require_once __DIR__ . '/views/partials/alerts.php';
?>

<!-- Header Halaman & Tombol Tambah -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Daftar Pegawai</h1>
        <p class="text-sm text-slate-500 mt-1">
            Kelola seluruh data pegawai yang tercatat di sistem.
        </p>
    </div>

    <a
        href="<?= e(baseUrl('employee_add.php')) ?>"
        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shrink-0">
        <i class="bi bi-person-plus" aria-hidden="true"></i>
        <span>Tambah Pegawai</span>
    </a>
</div>

<!-- Kartu Tabel Daftar Pegawai -->
<section class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">

    <!-- Toolbar Pencarian & Filter -->
    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50">
        <form id="employeeFilterForm" action="<?= e(baseUrl('employees.php')) ?>" method="GET" class="flex flex-col md:flex-row md:items-center gap-3">
            <!-- Input Pencarian -->
            <div class="relative flex-1">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm" aria-hidden="true"></i>
                <input
                    type="text"
                    id="searchInput"
                    name="q"
                    value="<?= e($search) ?>"
                    placeholder="Cari NIP, nama, jabatan, atau departemen..."
                    class="w-full h-10 rounded-lg border border-slate-300 bg-white pl-10 pr-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
            </div>

            <!-- Filter Status -->
            <div class="flex items-center gap-2.5">
                <select
                    id="statusFilter"
                    name="status"
                    aria-label="Filter status kepegawaian"
                    class="h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    <option value="">Semua Status</option>
                    <option value="Aktif" <?= $statusFilter === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Nonaktif" <?= $statusFilter === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>

                <button
                    type="submit"
                    class="h-10 inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                    <i class="bi bi-funnel" aria-hidden="true"></i>
                    <span>Filter</span>
                </button>

                <?php if ($isFiltering): ?>
                    <a
                        href="<?= e(baseUrl('employees.php')) ?>"
                        id="resetFilterLink"
                        class="h-10 inline-flex items-center justify-center rounded-lg px-3 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
            <span id="employeeCountText">
                Menampilkan <strong class="font-semibold text-slate-700"><?= count($employees) ?></strong> data pegawai
            </span>
            <span id="searchLoadingIndicator" class="hidden text-blue-600 font-medium">
                Memuat data...
            </span>
        </div>
    </div>

    <!-- Isi Tabel atau Empty State -->
    <div id="employeeTableWrapper" class="<?= empty($employees) ? 'hidden' : '' ?>">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600">
                        <th class="px-5 py-3.5 whitespace-nowrap">NIP</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Nama Pegawai</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Departemen</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Jabatan</th>
                        <th class="px-5 py-3.5 whitespace-nowrap">Status</th>
                        <th class="px-5 py-3.5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="employeeTableBody" class="divide-y divide-slate-200 text-sm">
                    <?php foreach ($employees as $employee): ?>
                        <?php
                        $empId = (int) $employee['id'];
                        $isAktif = ($employee['status'] ?? '') === 'Aktif';
                        $badgeClass = $isAktif
                            ? 'bg-green-100 text-green-800'
                            : 'bg-slate-100 text-slate-600';
                        ?>
                        <tr data-employee-row="<?= $empId ?>" class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs text-slate-600 whitespace-nowrap">
                                <?= e($employee['employee_code']) ?>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="font-medium text-slate-900">
                                    <?= e($employee['full_name']) ?>
                                </div>
                                <div class="text-xs text-slate-500">
                                    <?= e($employee['email']) ?>
                                </div>
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
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a
                                        href="<?= e(baseUrl('employee_detail.php?id=' . $empId)) ?>"
                                        title="Lihat Detail"
                                        aria-label="Lihat detail <?= e($employee['full_name']) ?>"
                                        class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                        <span>Detail</span>
                                    </a>

                                    <a
                                        href="<?= e(baseUrl('employee_edit.php?id=' . $empId)) ?>"
                                        title="Edit Pegawai"
                                        aria-label="Edit <?= e($employee['full_name']) ?>"
                                        class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                        <span>Edit</span>
                                    </a>

                                    <button
                                        type="button"
                                        data-delete-btn
                                        data-id="<?= $empId ?>"
                                        data-name="<?= e($employee['full_name']) ?>"
                                        data-code="<?= e($employee['employee_code']) ?>"
                                        title="Hapus Pegawai"
                                        aria-label="Hapus <?= e($employee['full_name']) ?>"
                                        class="inline-flex items-center gap-1 rounded-md border border-red-200 bg-white px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors cursor-pointer">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State -->
    <div id="employeeEmptyState" class="p-10 text-center <?= empty($employees) ? '' : 'hidden' ?>">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
            <i class="bi bi-people text-2xl" aria-hidden="true"></i>
        </div>
        <?php if ($isFiltering): ?>
            <h2 class="text-base font-semibold text-slate-900">Data tidak ditemukan</h2>
            <p class="text-sm text-slate-500 mt-1">
                Tidak ada pegawai yang cocok dengan kata kunci atau filter status tersebut.
            </p>
            <div class="mt-4">
                <a
                    href="<?= e(baseUrl('employees.php')) ?>"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <span>Reset Pencarian</span>
                </a>
            </div>
        <?php else: ?>
            <h2 class="text-base font-semibold text-slate-900">Belum ada data pegawai</h2>
            <p class="text-sm text-slate-500 mt-1">
                Tambahkan pegawai pertama untuk mulai mencatat data kepegawaian.
            </p>
            <div class="mt-4">
                <a
                    href="<?= e(baseUrl('employee_add.php')) ?>"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="bi bi-person-plus" aria-hidden="true"></i>
                    <span>Tambah Pegawai Pertama</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Konfirmasi Hapus (akan dihubungkan ke Axios AJAX pada langkah Delete/AJAX) -->
<div
    id="deleteConfirmModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="deleteModalTitle">
    <div class="w-full max-w-md rounded-xl bg-white border border-slate-200 shadow-lg p-6">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <i class="bi bi-exclamation-triangle text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <h2 id="deleteModalTitle" class="text-base font-bold text-slate-900">
                    Konfirmasi Penghapusan
                </h2>
                <p class="text-sm text-slate-600 mt-1">
                    Apakah Anda yakin ingin menghapus data pegawai
                    <strong id="deleteTargetName" class="text-slate-900">-</strong>
                    (<span id="deleteTargetCode" class="font-mono text-xs">-</span>)?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
        </div>

        <input type="hidden" id="deleteCsrfToken" value="<?= e(generateCsrfToken()) ?>">

        <div class="mt-6 flex items-center justify-end gap-2.5">
            <button
                type="button"
                id="cancelDeleteBtn"
                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
                Batal
            </button>
            <button
                type="button"
                id="confirmDeleteBtn"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                <span>Ya, Hapus Pegawai</span>
            </button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/views/partials/footer.php'; ?>