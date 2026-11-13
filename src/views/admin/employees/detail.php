<?php
/** @var array<string, mixed>|null $employee */
?>
<div class="max-w-4xl">
    <!-- Baris Navigasi Atas -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a
            href="<?= e(baseUrl('employees.php')) ?>"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>Kembali ke Daftar Pegawai</span>
        </a>

        <?php if ($employee !== null): ?>
            <a
                href="<?= e(baseUrl('employee_edit.php?id=' . (int) $employee['id'])) ?>"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition-colors">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                <span>Edit Data Pegawai</span>
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
                Data pegawai yang Anda cari tidak tersedia atau mungkin sudah dihapus dari sistem.
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
        $isAktif = ($employee['status'] ?? '') === 'Aktif';
        $badgeClass = $isAktif
            ? 'bg-green-100 text-green-800'
            : 'bg-slate-100 text-slate-600';
        $initials = getNameInitials($employee['full_name']);
        $addressText = trim((string) ($employee['address'] ?? ''));
        ?>

        <!-- Kartu Detail Pegawai -->
        <section class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <!-- Header Profil Pegawai -->
            <div class="p-6 sm:p-8 border-b border-slate-200 bg-slate-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-600 text-white font-bold text-lg flex items-center justify-center shrink-0">
                        <?= e($initials) ?>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">
                                <?= e($employee['full_name']) ?>
                            </h1>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= e($badgeClass) ?>">
                                <?= e($employee['status']) ?>
                            </span>
                        </div>
                        <p class="text-sm text-slate-600 mt-1">
                            <?= e($employee['position']) ?> &middot; <?= e($employee['department_name']) ?>
                        </p>
                    </div>
                </div>

                <div class="sm:text-right">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-400 block">NIP / Kode</span>
                    <span class="font-mono text-sm font-semibold text-slate-800">
                        <?= e($employee['employee_code']) ?>
                    </span>
                </div>
            </div>

            <!-- Grid Informasi Lengkap (2 Kolom) -->
            <div class="p-6 sm:p-8">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">NIP / Kode Pegawai</dt>
                        <dd class="mt-1 text-sm font-mono font-medium text-slate-900">
                            <?= e($employee['employee_code']) ?>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">
                            <?= e($employee['full_name']) ?>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Email</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            <a href="mailto:<?= e($employee['email']) ?>" class="text-blue-600 hover:underline">
                                <?= e($employee['email']) ?>
                            </a>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Telepon</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            <?= e($employee['phone']) ?>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Departemen</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            <?= e($employee['department_code'] . ' - ' . $employee['department_name']) ?>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jabatan</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            <?= e($employee['position']) ?>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal Masuk</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            <?= e(formatIndonesianDate($employee['hire_date'])) ?>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dicatat Oleh</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            <?= e($employee['created_by_name'] ?? 'Data Awal Sistem') ?>
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Alamat Domisili</dt>
                        <dd class="mt-1 text-sm text-slate-900 leading-relaxed">
                            <?= $addressText !== '' ? e($addressText) : '<span class="text-slate-400">- (Tidak diisi)</span>' ?>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Footer Metadata Sistem -->
            <div class="px-6 sm:px-8 py-4 border-t border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
                <span>Dicatat pada: <?= e(formatIndonesianDate($employee['created_at'], true)) ?></span>
                <span>Terakhir diperbarui: <?= e(formatIndonesianDate($employee['updated_at'], true)) ?></span>
            </div>
        </section>
    <?php endif; ?>
</div>

