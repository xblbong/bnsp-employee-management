<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle . ' - ' . APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(baseUrl('css/tailwind.css')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(baseUrl('css/style.css')) ?>">
</head>

<body>
    <main class="min-h-screen flex items-center justify-center p-6">
        <section class="w-full max-w-lg rounded-xl text-center sm:p-10">
            <div class="mx-auto mb-5 inline-flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                <i class="bi bi-compass text-2xl" aria-hidden="true"></i>
            </div>
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Error 404</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Halaman Tidak Ditemukan</h1>
            <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                Alamat yang Anda buka tidak tersedia atau sudah dipindahkan.
            </p>
            <a
                href="<?= e(baseUrl('login.php')) ?>"
                class="mt-6 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                <i class="bi bi-house" aria-hidden="true"></i>
                <span>Kembali ke Aplikasi</span>
            </a>
        </section>
    </main>
</body>

</html>
