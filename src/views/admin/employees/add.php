<?php
/**
 *  * Deklarasi variabel yang digunakan dalam tampilan ini untuk membantu IDE dalam memberikan saran kode (code completion) dan pemeriksaan tipe data.
 * @var array<int, array<string, mixed>> $departments
 * @var array<string, string> $errors
 * @var array<string, mixed> $oldInput
 * @var string $selectedStatus
 * @var string $selectedDepartment
 */
?>
<!-- Navigasi Kembali & Judul -->
<div class="mb-6 max-w-4xl">
    <a
        href="<?= e(baseUrl('employees.php')) ?>"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 mb-3 transition-colors">
        <i class="bi bi-arrow-left" aria-hidden="true"></i>
        <span>Kembali ke Daftar Pegawai</span>
    </a>
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Pegawai Baru</h1>
    <p class="text-sm text-slate-500 mt-1">
        Lengkapi formulir di bawah ini untuk mencatat data pegawai baru. Kolom bertanda <span class="text-red-600 font-semibold">*</span> wajib diisi.
    </p>
</div>

<!-- Kartu Form Tambah Pegawai -->
<section class="max-w-4xl bg-white border border-slate-200 rounded-xl shadow-xs p-6 sm:p-8">
    <form
        id="employeeForm"
        action="<?= e(baseUrl('php/actions/employee_store.php')) ?>"
        method="POST"
        novalidate>
        <?= csrfField() ?>

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
                    value="<?= e(oldValue($oldInput, 'employee_code')) ?>"
                    placeholder="Contoh: PEG-004"
                    maxlength="20"
                    class="w-full uppercase h-10 rounded-lg border px-3.5 text-sm font-mono text-slate-900 placeholder:font-sans placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['employee_code']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>">
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
                    value="<?= e(oldValue($oldInput, 'full_name')) ?>"
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
                    value="<?= e(oldValue($oldInput, 'email')) ?>"
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
                    value="<?= e(oldValue($oldInput, 'phone')) ?>"
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
                            <?= e($dept['code'] . ' - ' . $dept['name']) ?>
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
                    value="<?= e(oldValue($oldInput, 'position')) ?>"
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
                    value="<?= e(oldValue($oldInput, 'hire_date')) ?>"
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
                    class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors <?= isset($errors['address']) ? 'border-red-500 bg-red-50/30' : 'border-slate-300 bg-white' ?>"><?= e(oldValue($oldInput, 'address')) ?></textarea>
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
                <span>Simpan Data Pegawai</span>
            </button>
        </div>
    </form>
</section>

