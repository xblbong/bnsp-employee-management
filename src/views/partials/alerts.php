<?php

declare(strict_types=1);

$flashMessage = getFlash();
?>

<!-- Wadah Alert Dinamis untuk AJAX (dipakai pada Daftar Pegawai) -->
<div id="ajaxAlertContainer" class="space-y-2"></div>

<?php if ($flashMessage !== null): ?>
    <?php
    $type = $flashMessage['type'] ?? 'info';
    $styles = match ($type) {
        'success' => [
            'box'  => 'border-green-200 bg-green-50 text-green-800',
            'icon' => 'bi-check-circle-fill text-green-600',
        ],
        'error' => [
            'box'  => 'border-red-200 bg-red-50 text-red-800',
            'icon' => 'bi-exclamation-circle-fill text-red-600',
        ],
        default => [
            'box'  => 'border-blue-200 bg-blue-50 text-blue-800',
            'icon' => 'bi-info-circle-fill text-blue-600',
        ],
    };
    ?>
    <div
        class="mb-5 flex items-start justify-between gap-3 rounded-lg border px-4 py-3 text-sm <?= e($styles['box']) ?>"
        role="alert">
        <div class="flex items-start gap-2.5">
            <i class="bi <?= e($styles['icon']) ?> mt-0.5 shrink-0" aria-hidden="true"></i>
            <span><?= e($flashMessage['message']) ?></span>
        </div>
        <button
            type="button"
            data-dismiss-alert
            class="text-current opacity-60 hover:opacity-100 cursor-pointer shrink-0"
            aria-label="Tutup notifikasi">
            <i class="bi bi-x-lg text-xs" aria-hidden="true"></i>
        </button>
    </div>
<?php endif; ?>