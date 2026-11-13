<?php

declare(strict_types=1);

http_response_code(404);

require_once __DIR__ . '/php/config/app.php';
require_once __DIR__ . '/php/helpers/sanitizer.php';

$pageTitle = 'Halaman Tidak Ditemukan';

require __DIR__ . '/views/errors/404.php';
