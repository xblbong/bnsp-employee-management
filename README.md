# bnsp-employee-management

## Struktur aplikasi

```text
src/
  dashboard.php, employees.php, employee_add.php, employee_detail.php, employee_edit.php
  php/
    actions/       # Pemroses form
    api/           # Endpoint JSON/AJAX
    config/        # Konfigurasi aplikasi dan database
    functions/     # Operasi data dan aturan domain
    helpers/       # Autentikasi, validasi, sanitasi, dan CSRF
  views/
    admin/         # Tampilan halaman administrasi
    errors/        # Tampilan error aplikasi
    layouts/       # Kerangka halaman
    partials/      # Komponen tampilan yang digunakan bersama
  css/
  js/
  assets/
```

File halaman di tingkat `src/` mempertahankan URL yang sudah digunakan dan menyiapkan data; markup halaman administrasi berada di `src/views/admin/`. Komponen umum seperti header dan navigasi berada di `src/views/partials/`.

## Halaman tidak ditemukan

Apache menggunakan `src/404.php` untuk URL yang tidak ditemukan. Jika nama folder proyek atau `BASE_URL` berubah, sesuaikan path pada `.htaccess` di root proyek.
