-- ============================================================================
-- Skema Database: Employee Management System (LSP Junior Web Developer)
-- Database: employee_db
-- ============================================================================
CREATE DATABASE IF NOT EXISTS employee_db CHARACTER
SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE employee_db;

-- Hapus tabel lama (urutan terbalik dari relasi Foreign Key) agar bersih saat diimpor ulang
DROP TABLE IF EXISTS employees;

DROP TABLE IF EXISTS departments;

DROP TABLE IF EXISTS users;

-- ----------------------------------------------------------------------------
-- 1. Tabel users (Akun pengguna untuk Login)
-- ----------------------------------------------------------------------------
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Tabel departments (Master data departemen)
-- ----------------------------------------------------------------------------
CREATE TABLE departments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Tabel employees (Data utama pegawai - 9 field input + 3 field sistem + FK)
-- ----------------------------------------------------------------------------
CREATE TABLE employees (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    department_id INT UNSIGNED NOT NULL,
    position VARCHAR(80) NOT NULL,
    hire_date DATE NOT NULL,
    status ENUM ('Aktif', 'Nonaktif') NOT NULL DEFAULT 'Aktif',
    address VARCHAR(255) NULL DEFAULT NULL,
    created_by INT UNSIGNED NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_employees_full_name (full_name),
    INDEX idx_employees_status (status),
    CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_employees_created_by FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE
    SET
        NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Seed Awal: Master Departemen
-- ----------------------------------------------------------------------------
INSERT INTO
    departments (code, name)
VALUES
    ('IT', 'Teknologi Informasi'),
    ('HRD', 'Sumber Daya Manusia'),
    ('FIN', 'Keuangan'),
    ('OPS', 'Operasional'),
    ('MKT', 'Pemasaran');

-- ----------------------------------------------------------------------------
-- Seed Awal: 3 Data Pegawai Contoh (untuk memudahkan uji Dashboard & Daftar)
-- ----------------------------------------------------------------------------
INSERT INTO
    employees (
        employee_code,
        full_name,
        email,
        phone,
        department_id,
        position,
        hire_date,
        status,
        address
    )
VALUES
    (
        'PEG-001',
        'Budi Santoso',
        'budi.santoso@perusahaan.co.id',
        '081234567890',
        1,
        'Web Developer',
        '2024-01-15',
        'Aktif',
        'Jl. Merdeka No. 10, Jakarta'
    ),
    (
        'PEG-002',
        'Siti Rahmawati',
        'siti.rahmawati@perusahaan.co.id',
        '082198765432',
        2,
        'HR Specialist',
        '2023-06-01',
        'Aktif',
        'Jl. Sudirman No. 45, Bandung'
    ),
    (
        'PEG-003',
        'Andi Wijaya',
        'andi.wijaya@perusahaan.co.id',
        '085611223344',
        3,
        'Staf Akuntansi',
        '2022-11-10',
        'Nonaktif',
        NULL
    );