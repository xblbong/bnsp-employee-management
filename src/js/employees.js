/**
 * Interaksi AJAX (Axios) Halaman Daftar Pegawai (MUK Tahap 1a, 2b, 3b, 4d)
 * 1. Menghapus pegawai secara asinkron dengan Modal Konfirmasi + animasi baris.
 * 2. Pencarian dan filter status pegawai secara dinamis tanpa reload halaman.
 */

document.addEventListener('DOMContentLoaded', function () {
    const baseUrlMeta = document.querySelector('meta[name="base-url"]');
    const baseUrl = baseUrlMeta ? baseUrlMeta.getAttribute('content') : '';

    if (typeof axios === 'undefined') {
        console.error('Axios belum dimuat.');
        return;
    }

    initEmployeeDeleteAjax(baseUrl);
    initEmployeeSearchAjax(baseUrl);
});

/**
 * Menampilkan alert dinamis hasil operasi AJAX ke dalam #ajaxAlertContainer.
 */
function showAjaxAlert(type, message) {
    const container = document.getElementById('ajaxAlertContainer');
    if (!container) {
        return;
    }

    container.innerHTML = '';

    const isSuccess = type === 'success';
    const boxClasses = isSuccess
        ? 'mb-5 flex items-start justify-between gap-3 rounded-lg border px-4 py-3 text-sm border-green-200 bg-green-50 text-green-800'
        : 'mb-5 flex items-start justify-between gap-3 rounded-lg border px-4 py-3 text-sm border-red-200 bg-red-50 text-red-800';
    const iconClasses = isSuccess
        ? 'bi bi-check-circle-fill text-green-600 mt-0.5 shrink-0'
        : 'bi bi-exclamation-circle-fill text-red-600 mt-0.5 shrink-0';

    const alertDiv = document.createElement('div');
    alertDiv.className = boxClasses;
    alertDiv.setAttribute('role', 'alert');

    const leftWrap = document.createElement('div');
    leftWrap.className = 'flex items-start gap-2.5';

    const icon = document.createElement('i');
    icon.className = iconClasses;
    icon.setAttribute('aria-hidden', 'true');

    const textSpan = document.createElement('span');
    textSpan.textContent = message;

    leftWrap.appendChild(icon);
    leftWrap.appendChild(textSpan);

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.setAttribute('data-dismiss-alert', '');
    closeBtn.setAttribute('aria-label', 'Tutup notifikasi');
    closeBtn.className = 'text-current opacity-60 hover:opacity-100 cursor-pointer shrink-0';

    const closeIcon = document.createElement('i');
    closeIcon.className = 'bi bi-x-lg text-xs';
    closeIcon.setAttribute('aria-hidden', 'true');
    closeBtn.appendChild(closeIcon);

    alertDiv.appendChild(leftWrap);
    alertDiv.appendChild(closeBtn);
    container.appendChild(alertDiv);
}

/**
 * Memperbarui teks penghitung jumlah data dan status Empty State.
 */
function updateTableCountAndEmptyState(count, isFiltering, onResetCallback) {
    const countText = document.getElementById('employeeCountText');
    const tableWrapper = document.getElementById('employeeTableWrapper');
    const emptyState = document.getElementById('employeeEmptyState');

    if (countText) {
        countText.innerHTML = '';
        countText.appendChild(document.createTextNode('Menampilkan '));
        const strong = document.createElement('strong');
        strong.className = 'font-semibold text-slate-700';
        strong.textContent = String(count);
        countText.appendChild(strong);
        countText.appendChild(document.createTextNode(' data pegawai'));
    }

    if (!tableWrapper || !emptyState) {
        return;
    }

    if (count > 0) {
        tableWrapper.classList.remove('hidden');
        emptyState.classList.add('hidden');
        return;
    }

    tableWrapper.classList.add('hidden');
    emptyState.classList.remove('hidden');

    const heading = emptyState.querySelector('h2');
    const desc = emptyState.querySelector('p');
    const actionWrap = emptyState.querySelector('div.mt-4');

    if (isFiltering) {
        if (heading) heading.textContent = 'Data tidak ditemukan';
        if (desc) desc.textContent = 'Tidak ada pegawai yang cocok dengan kata kunci atau filter status tersebut.';
        if (actionWrap) {
            actionWrap.innerHTML = '';
            const resetBtn = document.createElement('button');
            resetBtn.type = 'button';
            resetBtn.className = 'inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer';
            resetBtn.textContent = 'Reset Pencarian';
            resetBtn.addEventListener('click', function () {
                if (typeof onResetCallback === 'function') {
                    onResetCallback();
                }
            });
            actionWrap.appendChild(resetBtn);
        }
    } else {
        if (heading) heading.textContent = 'Belum ada data pegawai';
        if (desc) desc.textContent = 'Tambahkan pegawai pertama untuk mulai mencatat data kepegawaian.';
    }
}

/**
 * Menginisialisasi Modal Konfirmasi & Penghapusan Pegawai via Axios POST.
 */
function initEmployeeDeleteAjax(baseUrl) {
    const modal = document.getElementById('deleteConfirmModal');
    const targetNameEl = document.getElementById('deleteTargetName');
    const targetCodeEl = document.getElementById('deleteTargetCode');
    const csrfInput = document.getElementById('deleteCsrfToken');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const tableBody = document.getElementById('employeeTableBody');

    if (!modal || !confirmBtn || !cancelBtn || !tableBody) {
        return;
    }

    let selectedEmployeeId = null;
    let isDeleting = false;

    function openModal(id, name, code) {
        selectedEmployeeId = id;
        if (targetNameEl) targetNameEl.textContent = name;
        if (targetCodeEl) targetCodeEl.textContent = code;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        cancelBtn.focus();
    }

    function closeModal() {
        if (isDeleting) {
            return;
        }
        selectedEmployeeId = null;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Event delegation agar tombol hapus di baris hasil AJAX search juga berfungsi
    tableBody.addEventListener('click', function (event) {
        const deleteBtn = event.target.closest('[data-delete-btn]');
        if (!deleteBtn) {
            return;
        }

        const id = Number(deleteBtn.getAttribute('data-id'));
        const name = deleteBtn.getAttribute('data-name') || '-';
        const code = deleteBtn.getAttribute('data-code') || '-';
        openModal(id, name, code);
    });

    cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    confirmBtn.addEventListener('click', async function () {
        if (!selectedEmployeeId || isDeleting) {
            return;
        }

        isDeleting = true;
        confirmBtn.disabled = true;
        cancelBtn.disabled = true;
        const confirmLabel = confirmBtn.querySelector('span');
        if (confirmLabel) {
            confirmLabel.textContent = 'Menghapus...';
        }

        const deletedId = selectedEmployeeId;
        const csrfToken = csrfInput ? csrfInput.value : '';

        try {
            const response = await axios.post(baseUrl + '/php/api/employee_delete.php', {
                id: deletedId,
                csrf_token: csrfToken
            });

            isDeleting = false;
            closeModal();

            if (response.data && response.data.success) {
                const row = tableBody.querySelector('tr[data-employee-row="' + deletedId + '"]');
                if (row) {
                    // Animasi CSS memudar sebelum baris dihapus dari DOM
                    row.classList.add('row-removing');
                    setTimeout(function () {
                        row.remove();
                        const remainingCount = tableBody.querySelectorAll('tr[data-employee-row]').length;
                        const searchVal = (document.getElementById('searchInput')?.value || '').trim();
                        const statusVal = document.getElementById('statusFilter')?.value || '';
                        const isFiltering = searchVal !== '' || statusVal !== '';

                        updateTableCountAndEmptyState(remainingCount, isFiltering, function () {
                            const searchInput = document.getElementById('searchInput');
                            const statusFilter = document.getElementById('statusFilter');
                            if (searchInput) searchInput.value = '';
                            if (statusFilter) statusFilter.value = '';
                            statusFilter?.dispatchEvent(new Event('change'));
                        });
                    }, 280);
                }

                showAjaxAlert('success', response.data.message || 'Data pegawai berhasil dihapus.');
            } else {
                showAjaxAlert('error', (response.data && response.data.message) || 'Gagal menghapus data pegawai.');
            }
        } catch (error) {
            isDeleting = false;
            closeModal();
            const serverMsg = error.response && error.response.data && error.response.data.message;
            showAjaxAlert('error', serverMsg || 'Terjadi gangguan jaringan saat menghapus data pegawai.');
        } finally {
            confirmBtn.disabled = false;
            cancelBtn.disabled = false;
            if (confirmLabel) {
                confirmLabel.textContent = 'Ya, Hapus Pegawai';
            }
        }
    });
}

/**
 * Menginisialisasi Live Search & Filter Status via Axios GET.
 */
function initEmployeeSearchAjax(baseUrl) {
    const filterForm = document.getElementById('employeeFilterForm');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('employeeTableBody');
    const loadingIndicator = document.getElementById('searchLoadingIndicator');

    if (!filterForm || !searchInput || !statusFilter || !tableBody) {
        return;
    }

    let debounceTimer = null;

    function createActionLink(href, title, ariaLabel, extraClasses, iconClass, labelText) {
        const link = document.createElement('a');
        link.href = href;
        link.title = title;
        link.setAttribute('aria-label', ariaLabel);
        link.className = extraClasses;

        const icon = document.createElement('i');
        icon.className = 'bi ' + iconClass;
        icon.setAttribute('aria-hidden', 'true');

        const span = document.createElement('span');
        span.textContent = labelText;

        link.appendChild(icon);
        link.appendChild(document.createTextNode(' '));
        link.appendChild(span);
        return link;
    }

    function renderRows(employees) {
        tableBody.innerHTML = '';

        employees.forEach(function (emp) {
            const empId = Number(emp.id);
            const isAktif = emp.status === 'Aktif';

            const tr = document.createElement('tr');
            tr.setAttribute('data-employee-row', String(empId));
            tr.className = 'hover:bg-slate-50/80 transition-colors';

            // Kolom 1: NIP
            const tdCode = document.createElement('td');
            tdCode.className = 'px-5 py-3.5 font-mono text-xs text-slate-600 whitespace-nowrap';
            tdCode.textContent = emp.employee_code;
            tr.appendChild(tdCode);

            // Kolom 2: Nama + Email
            const tdName = document.createElement('td');
            tdName.className = 'px-5 py-3.5 whitespace-nowrap';
            const divName = document.createElement('div');
            divName.className = 'font-medium text-slate-900';
            divName.textContent = emp.full_name;
            const divEmail = document.createElement('div');
            divEmail.className = 'text-xs text-slate-500';
            divEmail.textContent = emp.email;
            tdName.appendChild(divName);
            tdName.appendChild(divEmail);
            tr.appendChild(tdName);

            // Kolom 3: Departemen
            const tdDept = document.createElement('td');
            tdDept.className = 'px-5 py-3.5 text-slate-600 whitespace-nowrap';
            tdDept.textContent = emp.department_name;
            tr.appendChild(tdDept);

            // Kolom 4: Jabatan
            const tdPos = document.createElement('td');
            tdPos.className = 'px-5 py-3.5 text-slate-600 whitespace-nowrap';
            tdPos.textContent = emp.position;
            tr.appendChild(tdPos);

            // Kolom 5: Status Badge
            const tdStatus = document.createElement('td');
            tdStatus.className = 'px-5 py-3.5 whitespace-nowrap';
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ' +
                (isAktif ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600');
            badge.textContent = emp.status;
            tdStatus.appendChild(badge);
            tr.appendChild(tdStatus);

            // Kolom 6: Aksi
            const tdAction = document.createElement('td');
            tdAction.className = 'px-5 py-3.5 text-right whitespace-nowrap';
            const actionWrap = document.createElement('div');
            actionWrap.className = 'inline-flex items-center justify-end gap-1.5';

            actionWrap.appendChild(createActionLink(
                baseUrl + '/employee_detail.php?id=' + empId,
                'Lihat Detail',
                'Lihat detail ' + emp.full_name,
                'inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors',
                'bi-eye',
                'Detail'
            ));

            actionWrap.appendChild(createActionLink(
                baseUrl + '/employee_edit.php?id=' + empId,
                'Edit Pegawai',
                'Edit ' + emp.full_name,
                'inline-flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-colors',
                'bi-pencil-square',
                'Edit'
            ));

            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.setAttribute('data-delete-btn', '');
            delBtn.setAttribute('data-id', String(empId));
            delBtn.setAttribute('data-name', emp.full_name);
            delBtn.setAttribute('data-code', emp.employee_code);
            delBtn.title = 'Hapus Pegawai';
            delBtn.setAttribute('aria-label', 'Hapus ' + emp.full_name);
            delBtn.className = 'inline-flex items-center gap-1 rounded-md border border-red-200 bg-white px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors cursor-pointer';

            const delIcon = document.createElement('i');
            delIcon.className = 'bi bi-trash';
            delIcon.setAttribute('aria-hidden', 'true');
            const delSpan = document.createElement('span');
            delSpan.textContent = 'Hapus';

            delBtn.appendChild(delIcon);
            delBtn.appendChild(document.createTextNode(' '));
            delBtn.appendChild(delSpan);
            actionWrap.appendChild(delBtn);

            tdAction.appendChild(actionWrap);
            tr.appendChild(tdAction);
            tableBody.appendChild(tr);
        });
    }

    async function fetchEmployees() {
        const q = searchInput.value.trim();
        const status = statusFilter.value;
        const isFiltering = q !== '' || status !== '';

        if (loadingIndicator) {
            loadingIndicator.classList.remove('hidden');
        }

        try {
            const response = await axios.get(baseUrl + '/php/api/employee_search.php', {
                params: { q: q, status: status }
            });

            if (response.data && response.data.success) {
                const employees = response.data.employees || [];
                renderRows(employees);
                updateTableCountAndEmptyState(employees.length, isFiltering, function () {
                    searchInput.value = '';
                    statusFilter.value = '';
                    fetchEmployees();
                });
            }
        } catch (error) {
            const serverMsg = error.response && error.response.data && error.response.data.message;
            showAjaxAlert('error', serverMsg || 'Gagal memuat ulang daftar pegawai.');
        } finally {
            if (loadingIndicator) {
                loadingIndicator.classList.add('hidden');
            }
        }
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchEmployees, 300);
    });

    statusFilter.addEventListener('change', fetchEmployees);

    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        clearTimeout(debounceTimer);
        fetchEmployees();
    });
}