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

$pageTitle = $employee !== null ? 'Edit Pegawai — ' . $employee['full_name'] : 'Pegawai Tidak Ditemukan';
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

require_once __DIR__ . '/views/partials/header.php';
require_once __DIR__ . '/views/partials/navbar.php';
require_once __DIR__ . '/views/partials/alerts.php';
?>

<div class="max-w-4xl">
    <!-- Baris Navigasi Kembali -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a
            href="<?= e(baseUrl('employees.php')) ?>"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>Kembali ke Daftar Pegawai</span>
        </a>

        <?php if ($employee !== null): ?>
            <a
                href="<?= e(baseUrl('employee_detail.php?id=' . (int) $employee['id'])) ?>"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                <i class="bi bi-eye" aria-hidden="true"></i>
                <span>Lihat Detail</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if ($employee === null): ?>
        <!-- Error State: Data Tidak Ditemukan -->
        <section class="bg-white border border-slate-200 rounded-xl shadow-xs p-8 sm:p-10 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-50 text-red-600 mb-4">
                <i class="bi bi-person-x text-2xl" aria-hidden="true"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Data Pegawai Tidak Ditemukan</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                Data pegawai yang ingin Anda edit tidak tersedia atau sudah dihapus.
            </p>
            <div class="mt-6">
                <a
                    href="<?= e(baseUrl('employees.php')) ?>"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Kembali ke Daftar Pegawai</span>
                </a>
            </div>
        </section>
    <?php else: ?>
        <?php
        $selectedDepartment = $getFieldValue('department_id');
        $selectedStatus = $getFieldValue('status', 'Aktif');
        ?>

        <!-- Header Judul Edit -->
        <div class="mb-6">
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Edit Data Pegawai
                </h1>
                <span class="inline-flex items-center rounded-md bg-slate-100 border border-slate-200 px-2.5 py-0.5 font-mono text-xs font-semibold text-slate-700">
                    <?= e($employee['employee_code']) ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Perbarui informasi untuk <strong class="text-slate-700"><?= e($employee['full_name']) ?></strong>. Kolom bertanda <span class="text-red-600 font-semibold">*</span> wajib diisi.
            </p>
        </div>

        <!-- Kartu Form Edit Pegawai -->
        <section class="bg-white border border-slate-200 rounded-xl shadow-xs p-6 sm:p-8">
            <form
                id="employeeForm"
                action="<?= e(baseUrl('php/actions/employee_update.php')) ?>"
                method="POST"
                novalidate>
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= e((string) $employee['id']) ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- NIP / Kode Pegawai -->
                    <div>
                        <label for="employee_code" class="block text-sm font-medium text-slate-700 mb-1.5">
                            NIP / Kode Pegawai <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="employee_code"
                            name="employee_code"
                            value="<?= e($getFieldValue('employee_code')) ?>"
                            placeholder="Contoh: PEG-004"
                            maxlength="20"
                            class="w-full h-10 rounded-lg border px-3.5 text-sm font-mono text-slate-900 placeholder:font-sans placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['employee_code']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['employee_code']) ? '' : 'hidden' ?>" data-error-for="employee_code">
                            <?= e($errors['employee_code'] ?? '') ?>
                        </p>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Nama Lengkap <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="<?= e($getFieldValue('full_name')) ?>"
                            placeholder="Masukkan nama lengkap"
                            maxlength="100"
                            class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['full_name']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['full_name']) ? '' : 'hidden' ?>" data-error-for="full_name">
                            <?= e($errors['full_name'] ?? '') ?>
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Email <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= e($getFieldValue('email')) ?>"
                            placeholder="nama@perusahaan.co.id"
                            maxlength="100"
                            class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['email']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['email']) ? '' : 'hidden' ?>" data-error-for="email">
                            <?= e($errors['email'] ?? '') ?>
                        </p>
                    </div>

                    <!-- Nomor Telepon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Nomor Telepon <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?= e($getFieldValue('phone')) ?>"
                            placeholder="081234567890"
                            maxlength="15"
                            class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['phone']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['phone']) ? '' : 'hidden' ?>" data-error-for="phone">
                            <?= e($errors['phone'] ?? '') ?>
                        </p>
                    </div>

                    <!-- Departemen -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Departemen <span class="text-red-600">*</span>
                        </label>
                        <select
                            id="department_id"
                            name="department_id"
                            class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['department_id']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                            <option value="">-- Pilih Departemen --</option>
                            <?php foreach ($departments as $dept): ?>
                                <?php $deptId = (string) $dept['id']; ?>
                                <option value="<?= e($deptId) ?>" <?= $selectedDepartment === $deptId ? 'selected' : '' ?>>
                                    <?= e($dept['code'] . ' — ' . $dept['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['department_id']) ? '' : 'hidden' ?>" data-error-for="department_id">
                            <?= e($errors['department_id'] ?? '') ?>
                        </p>
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Jabatan <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="text"
                            id="position"
                            name="position"
                            value="<?= e($getFieldValue('position')) ?>"
                            placeholder="Contoh: Software Engineer"
                            maxlength="80"
                            class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['position']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['position']) ? '' : 'hidden' ?>" data-error-for="position">
                            <?= e($errors['position'] ?? '') ?>
                        </p>
                    </div>

                    <!-- Tanggal Masuk -->
                    <div>
                        <label for="hire_date" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Tanggal Masuk <span class="text-red-600">*</span>
                        </label>
                        <input
                            type="date"
                            id="hire_date"
                            name="hire_date"
                            value="<?= e($getFieldValue('hire_date')) ?>"
                            class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['hire_date']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['hire_date']) ? '' : 'hidden' ?>" data-error-for="hire_date">
                            <?= e($errors['hire_date'] ?? '') ?>
                        </p>
                    </div>

                    <!-- Status Kepegawaian -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Status Kepegawaian <span class="text-red-600">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full h-10 rounded-lg border px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['status']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
                            <option value="Aktif" <?= $selectedStatus === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?= $selectedStatus === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['status']) ? '' : 'hidden' ?>" data-error-for="status">
                            <?= e($errors['status'] ?? '') ?>
                        </p>
                    </div>

                    <!-- Alamat (Opsional) -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Alamat <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                        </label>
                        <textarea
                            id="address"
                            name="address"
                            rows="3"
                            maxlength="255"
                            placeholder="Alamat domisili pegawai (maksimal 255 karakter)"
                            class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['address']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>"><?= e($getFieldValue('address')) ?></textarea>
                        <p class="mt-1 text-xs text-red-600 <?= isset($errors['address']) ? '' : 'hidden' ?>" data-error-for="address">
                            <?= e($errors['address'] ?? '') ?>
                        </p>
                    </div>
                </div>

                <!-- Tombol Aksi Form -->
                <div class="mt-6 pt-5 border-t border-slate-200 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                    <a
                        href="<?= e(baseUrl('employees.php')) ?>"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors cursor-pointer">
                        <i class="bi bi-check-lg" aria-hidden="true"></i>
                        <span>Perbarui Data</span>
                    </button>
                </div>
            </form>
        </section>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/views/partials/footer.php'; ?>