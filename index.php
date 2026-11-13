<?php

declare(strict_types=1);

/**
 * Gerbang Awal Proyek (Root Entry Point)
 * Mengarahkan pengunjung langsung ke aplikasi di dalam folder src/.
 */

require_once __DIR__ . '/src/php/config/app.php';
require_once __DIR__ . '/src/php/helpers/sanitizer.php';
require_once __DIR__ . '/src/php/helpers/flash.php';
require_once __DIR__ . '/src/php/helpers/auth.php';

startSecureSession();

if (isLoggedIn()) {
    redirect('dashboard.php');
}

redirect('login.php');
