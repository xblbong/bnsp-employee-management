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

## Menjalankan secara lokal dengan Laragon

1. Letakkan folder proyek di direktori `www` Laragon, misalnya `C:\laragon\www\employee-management`.
2. Buka Laragon, lalu jalankan Apache dan MySQL.
3. Pastikan konfigurasi database di `src/php/config/database.php` sesuai dengan MySQL lokal. Nilai bawaan memakai database `employee_db`, user `root`, dan password kosong.
4. Untuk instalasi database baru saja, impor `src/database/employee_db.sql` melalui phpMyAdmin atau HeidiSQL. **File ini menghapus tabel yang sudah ada**, jadi jangan impor ulang pada database yang berisi data penting tanpa backup.
5. Jika database baru belum memiliki akun admin, jalankan `php src/database/seed_admin.php` dari terminal Laragon pada direktori proyek. Seeder menggunakan akun demo dan menjalankan ulang seeder akan mengatur ulang kredensial akun tersebut.
6. Buka `http://localhost/employee-management/`. `index.php` akan mengarahkan ke halaman login.

PHP dijalankan melalui Apache Laragon; `npm start` tidak tersedia dan tidak diperlukan untuk menjalankan aplikasi. `npm` hanya diperlukan jika aset Tailwind perlu dibangun ulang. Jika nama folder proyek berbeda, sesuaikan `BASE_URL` di `src/php/config/app.php` dan path pada `.htaccess`.

## Halaman tidak ditemukan

Apache menggunakan `src/404.php` untuk URL yang tidak ditemukan. Jika nama folder proyek atau `BASE_URL` berubah, sesuaikan path pada `.htaccess` di root proyek.
