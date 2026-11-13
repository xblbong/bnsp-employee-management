/**
 * Validasi Form Client-Side (MUK Tahap 1b & 3b)
 * Menangani validasi untuk form Login (#loginForm) dan Tambah/Edit Pegawai (#employeeForm).
 */

document.addEventListener('DOMContentLoaded', function () {
    initLoginValidation();
    initEmployeeFormValidation();
});

/**
 * Menampilkan pesan error pada satu field input dan menandai border merah.
 */
function setFieldError(field, message) {
    if (!field) {
        return;
    }

    field.classList.remove('border-slate-300', 'bg-white');
    field.classList.add('border-red-500', 'bg-red-50/30');
    field.setAttribute('aria-invalid', 'true');

    const errorEl = document.querySelector('[data-error-for="' + field.name + '"]');
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    }
}

/**
 * Menghapus tanda error pada satu field input.
 */
function clearFieldError(field) {
    if (!field) {
        return;
    }

    field.classList.remove('border-red-500', 'bg-red-50/30');
    field.classList.add('border-slate-300', 'bg-white');
    field.removeAttribute('aria-invalid');

    const errorEl = document.querySelector('[data-error-for="' + field.name + '"]');
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
    }
}

/**
 * Mengubah tombol submit menjadi status memuat agar tidak terjadi klik ganda.
 */
function setSubmitLoading(form, loadingText) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (!submitBtn) {
        return;
    }

    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
    const textSpan = submitBtn.querySelector('span');
    if (textSpan) {
        textSpan.textContent = loadingText;
    }
}

/**
 * Memeriksa apakah string tanggal YYYY-MM-DD merupakan tanggal kalender yang valid.
 */
function isValidCalendarDate(dateString) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return false;
    }

    const parts = dateString.split('-');
    const year = Number(parts[0]);
    const month = Number(parts[1]);
    const day = Number(parts[2]);

    const dateObj = new Date(year, month - 1, day);
    return (
        dateObj.getFullYear() === year &&
        dateObj.getMonth() === month - 1 &&
        dateObj.getDate() === day
    );
}

/**
 * Inisialisasi validasi untuk Halaman Login (#loginForm).
 */
function initLoginValidation() {
    const form = document.getElementById('loginForm');
    if (!form) {
        return;
    }

    const usernameField = form.querySelector('[name="username"]');
    const passwordField = form.querySelector('[name="password"]');

    function validateUsername() {
        const val = usernameField.value.trim();
        if (val.length < 3) {
            setFieldError(usernameField, 'Username wajib diisi (minimal 3 karakter).');
            return false;
        }
        clearFieldError(usernameField);
        return true;
    }

    function validatePassword() {
        const val = passwordField.value;
        if (val.length < 6) {
            setFieldError(passwordField, 'Password wajib diisi (minimal 6 karakter).');
            return false;
        }
        clearFieldError(passwordField);
        return true;
    }

    // Hapus error secara langsung saat pengguna memperbaiki ketikannya
    usernameField.addEventListener('input', function () {
        if (usernameField.getAttribute('aria-invalid') === 'true') {
            validateUsername();
        }
    });

    passwordField.addEventListener('input', function () {
        if (passwordField.getAttribute('aria-invalid') === 'true') {
            validatePassword();
        }
    });

    form.addEventListener('submit', function (event) {
        const isUsernameValid = validateUsername();
        const isPasswordValid = validatePassword();

        if (!isUsernameValid || !isPasswordValid) {
            event.preventDefault();
            const firstInvalid = !isUsernameValid ? usernameField : passwordField;
            firstInvalid.focus();
            return;
        }

        setSubmitLoading(form, 'Memproses...');
    });
}

/**
 * Inisialisasi validasi untuk Form Tambah & Edit Pegawai (#employeeForm).
 */
function initEmployeeFormValidation() {
    const form = document.getElementById('employeeForm');
    if (!form) {
        return;
    }

    const rules = {
        employee_code: function (field) {
            const val = field.value.trim();
            if (!/^[A-Za-z0-9-]{4,20}$/.test(val)) {
                return 'NIP wajib diisi (4–20 karakter, hanya huruf, angka, atau "-").';
            }
            return '';
        },
        full_name: function (field) {
            const val = field.value.trim();
            if (val.length < 3 || val.length > 100 || !/\p{L}/u.test(val)) {
                return 'Nama lengkap wajib diisi (3–100 karakter dan mengandung huruf).';
            }
            return '';
        },
        email: function (field) {
            const val = field.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (val === '' || val.length > 100 || !emailRegex.test(val)) {
                return 'Masukkan alamat email yang valid (maksimal 100 karakter).';
            }
            return '';
        },
        phone: function (field) {
            const val = field.value.trim();
            if (!/^\+?[0-9]{10,15}$/.test(val)) {
                return 'Nomor telepon wajib diisi (10–15 digit angka, boleh diawali "+").';
            }
            return '';
        },
        department_id: function (field) {
            if (field.value.trim() === '') {
                return 'Silakan pilih departemen pegawai.';
            }
            return '';
        },
        position: function (field) {
            const val = field.value.trim();
            if (val.length < 2 || val.length > 80) {
                return 'Jabatan wajib diisi (2–80 karakter).';
            }
            return '';
        },
        hire_date: function (field) {
            const val = field.value.trim();
            if (!isValidCalendarDate(val)) {
                return 'Tanggal masuk wajib diisi dengan tanggal yang valid.';
            }
            return '';
        },
        status: function (field) {
            if (field.value !== 'Aktif' && field.value !== 'Nonaktif') {
                return 'Silakan pilih status kepegawaian yang sah.';
            }
            return '';
        },
        address: function (field) {
            const val = field.value.trim();
            if (val.length > 255) {
                return 'Alamat maksimal 255 karakter.';
            }
            return '';
        }
    };

    const fieldNames = Object.keys(rules);

    function validateField(fieldName) {
        const field = form.querySelector('[name="' + fieldName + '"]');
        if (!field) {
            return true;
        }

        const errorMessage = rules[fieldName](field);
        if (errorMessage !== '') {
            setFieldError(field, errorMessage);
            return false;
        }

        clearFieldError(field);
        return true;
    }

    // Dengarkan perbaikan input secara langsung jika sebelumnya sudah muncul error
    fieldNames.forEach(function (fieldName) {
        const field = form.querySelector('[name="' + fieldName + '"]');
        if (!field) {
            return;
        }

        const eventName = field.tagName === 'SELECT' || field.type === 'date' ? 'change' : 'input';
        field.addEventListener(eventName, function () {
            if (field.getAttribute('aria-invalid') === 'true') {
                validateField(fieldName);
            }
        });
    });

    form.addEventListener('submit', function (event) {
        let firstInvalidField = null;
        let allValid = true;

        fieldNames.forEach(function (fieldName) {
            const isValid = validateField(fieldName);
            if (!isValid) {
                allValid = false;
                if (firstInvalidField === null) {
                    firstInvalidField = form.querySelector('[name="' + fieldName + '"]');
                }
            }
        });

        if (!allValid) {
            event.preventDefault();
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
            return;
        }

        setSubmitLoading(form, 'Menyimpan...');
    });
}