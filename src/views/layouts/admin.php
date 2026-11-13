<?php

declare(strict_types=1);

if (!isset($viewFile) || !is_string($viewFile) || !is_file($viewFile)) {
    http_response_code(404);

    require_once __DIR__ . '/../../php/config/app.php';
    require_once __DIR__ . '/../../php/helpers/sanitizer.php';

    $pageTitle = 'Halaman Tidak Ditemukan';
    require __DIR__ . '/../errors/404.php';
    return;
}

require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/alerts.php';
require $viewFile;
require __DIR__ . '/../partials/footer.php';
