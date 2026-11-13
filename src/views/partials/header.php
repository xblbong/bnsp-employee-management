<?php

declare(strict_types=1);

$documentTitle = isset($pageTitle) && $pageTitle !== ''
    ? $pageTitle . ' - ' . APP_NAME
    : APP_NAME;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= e(BASE_URL) ?>">
    <title><?= e($documentTitle) ?></title>
    <link rel="stylesheet" href="<?= e(baseUrl('css/tailwind.css')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('css/style.css')) ?>">
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen flex bg-slate-50">