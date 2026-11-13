<?php

declare(strict_types=1);

/**
 * Helper Validasi Input Pegawai (Server-Side) sesuai Spesifikasi Phase 1.
 */

/**
 * Membersihkan spasi berlebih di awal/akhir setiap input form pegawai.
 */
function sanitizeEmployeeInput(array $postData): array
{
    return [
        'employee_code' => trim((string) ($postData['employee_code'] ?? '')),
        'full_name'     => trim((string) ($postData['full_name'] ?? '')),
        'email'         => trim((string) ($postData['email'] ?? '')),
        'phone'         => trim((string) ($postData['phone'] ?? '')),
        'department_id' => trim((string) ($postData['department_id'] ?? '')),
        'position'      => trim((string) ($postData['position'] ?? '')),
        'hire_date'     => trim((string) ($postData['hire_date'] ?? '')),
        'status'        => trim((string) ($postData['status'] ?? '')),
        'address'       => trim((string) ($postData['address'] ?? '')),
    ];
}

/**
 * Memvalidasi seluruh field pegawai dan mengembalikan array error per kolom.
 * Jika validasi lolos semua, mengembalikan array kosong [].
 */
function validateEmployeeInput(array $data, ?int $excludeEmployeeId = null): array
{
    $errors = [];

    // 1. NIP / Kode Pegawai
    if ($data['employee_code'] === '' || !preg_match('/^[A-Za-z0-9-]{4,20}$/', $data['employee_code'])) {
        $errors['employee_code'] = 'NIP wajib diisi (4 - 20 karakter, hanya huruf, angka, atau "-").';
    } elseif (isEmployeeCodeExists($data['employee_code'], $excludeEmployeeId)) {
        $errors['employee_code'] = 'NIP sudah terdaftar pada pegawai lain.';
    }

    // 2. Nama Lengkap
    $nameLength = mb_strlen($data['full_name']);
    if ($data['full_name'] === '' || $nameLength < 3 || $nameLength > 100 || !preg_match('/\p{L}/u', $data['full_name'])) {
        $errors['full_name'] = 'Nama lengkap wajib diisi (3 - 100 karakter dan mengandung huruf).';
    }

    // 3. Email
    if (
        $data['email'] === ''
        || mb_strlen($data['email']) > 100
        || filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false
    ) {
        $errors['email'] = 'Masukkan alamat email yang valid (maksimal 100 karakter).';
    } elseif (isEmployeeEmailExists($data['email'], $excludeEmployeeId)) {
        $errors['email'] = 'Email sudah digunakan oleh pegawai lain.';
    }

    // 4. Nomor Telepon
    if ($data['phone'] === '' || !preg_match('/^\+?[0-9]{10,15}$/', $data['phone'])) {
        $errors['phone'] = 'Nomor telepon wajib diisi (10 - 15 digit angka, boleh diawali "+").';
    }

    // 5. Departemen
    if ($data['department_id'] === '' || !ctype_digit($data['department_id']) || (int) $data['department_id'] <= 0) {
        $errors['department_id'] = 'Silakan pilih departemen pegawai.';
    } elseif (!departmentExists((int) $data['department_id'])) {
        $errors['department_id'] = 'Departemen yang dipilih tidak valid.';
    }

    // 6. Jabatan
    $positionLength = mb_strlen($data['position']);
    if ($data['position'] === '' || $positionLength < 2 || $positionLength > 80) {
        $errors['position'] = 'Jabatan wajib diisi (2 - 80 karakter).';
    }

    // 7. Tanggal Masuk
    if ($data['hire_date'] === '') {
        $errors['hire_date'] = 'Tanggal masuk wajib diisi.';
    } else {
        $dateObj = DateTime::createFromFormat('Y-m-d', $data['hire_date']);
        $isValidDate = $dateObj !== false && $dateObj->format('Y-m-d') === $data['hire_date'];
        if (!$isValidDate) {
            $errors['hire_date'] = 'Format tanggal masuk tidak valid.';
        }
    }

    // 8. Status Kepegawaian
    if (!in_array($data['status'], ['Aktif', 'Nonaktif'], true)) {
        $errors['status'] = 'Silakan pilih status kepegawaian yang sah.';
    }

    // 9. Alamat (Opsional)
    if ($data['address'] !== '' && mb_strlen($data['address']) > 255) {
        $errors['address'] = 'Alamat maksimal 255 karakter.';
    }

    return $errors;
}
