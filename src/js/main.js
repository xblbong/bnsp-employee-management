/**
 * Interaksi UI Umum (MUK Tahap 3b - File JavaScript Terpisah)
 * Mengelola drawer sidebar versi mobile dan penutupan alert notifikasi.
 */

document.addEventListener('DOMContentLoaded', function () {
    initMobileSidebar();
    initDismissibleAlerts();
});

/**
 * Mengatur buka/tutup menu sidebar pada layar kecil (mobile/tablet).
 */
function initMobileSidebar() {
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const openBtn = document.getElementById('sidebarOpenBtn');
    const closeBtn = document.getElementById('sidebarCloseBtn');

    if (!sidebar || !backdrop || !openBtn) {
        return;
    }

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
    }

    openBtn.addEventListener('click', openSidebar);

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    backdrop.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !backdrop.classList.contains('hidden')) {
            closeSidebar();
        }
    });
}

/**
 * Menangani tombol tutup (x) pada kotak alert dengan animasi halus.
 */
function initDismissibleAlerts() {
    document.addEventListener('click', function (event) {
        const closeBtn = event.target.closest('[data-dismiss-alert]');
        if (!closeBtn) {
            return;
        }

        const alertBox = closeBtn.closest('[role="alert"]');
        if (!alertBox) {
            return;
        }

        alertBox.classList.add('alert-dismissing');
        setTimeout(function () {
            alertBox.remove();
        }, 200);
    });
} 