document.addEventListener('DOMContentLoaded', initEmployeeDeletion);

function initEmployeeDeletion() {
    const modal = document.getElementById('deleteConfirmModal');
    if (!modal) {
        return;
    }

    const confirmButton = document.getElementById('confirmDeleteBtn');
    const cancelButton = document.getElementById('cancelDeleteBtn');
    const csrfInput = document.getElementById('deleteCsrfToken');
    const targetName = document.getElementById('deleteTargetName');
    const targetCode = document.getElementById('deleteTargetCode');
    const alertContainer = document.getElementById('ajaxAlertContainer');
    const tableWrapper = document.getElementById('employeeTableWrapper');
    const emptyState = document.getElementById('employeeEmptyState');
    const countText = document.querySelector('#employeeCountText strong');
    const confirmLabel = confirmButton && confirmButton.querySelector('span');

    if (
        !confirmButton || !cancelButton || !csrfInput || !targetName || !targetCode ||
        !alertContainer || !tableWrapper || !emptyState || !countText || !confirmLabel
    ) {
        throw new Error('Komponen penghapusan pegawai tidak lengkap.');
    }

    if (!window.axios || !modal.dataset.endpoint) {
        throw new Error('Axios atau endpoint penghapusan pegawai tidak tersedia.');
    }

    let activeButton = null;
    let isDeleting = false;

    function showAlert(message, type) {
        const isSuccess = type === 'success';
        const alert = document.createElement('div');
        const iconClass = isSuccess
            ? 'bi-check-circle-fill text-green-600'
            : 'bi-exclamation-circle-fill text-red-600';
        const boxClass = isSuccess
            ? 'border-green-200 bg-green-50 text-green-800'
            : 'border-red-200 bg-red-50 text-red-800';

        alert.className = `mb-5 flex items-start justify-between gap-3 rounded-lg border px-4 py-3 text-sm ${boxClass}`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            <div class="flex items-start gap-2.5">
                <i class="bi ${iconClass} mt-0.5 shrink-0" aria-hidden="true"></i>
                <span></span>
            </div>
            <button type="button" data-dismiss-alert class="text-current opacity-60 hover:opacity-100 cursor-pointer shrink-0" aria-label="Tutup notifikasi">
                <i class="bi bi-x-lg text-xs" aria-hidden="true"></i>
            </button>
        `;
        alert.querySelector('span').textContent = message;
        alertContainer.replaceChildren(alert);
    }

    function closeModal(restoreFocus) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');

        if (restoreFocus && activeButton && activeButton.isConnected) {
            activeButton.focus();
        }

        activeButton = null;
    }

    document.addEventListener('click', function (event) {
        const deleteButton = event.target.closest('[data-delete-btn]');
        if (!deleteButton || isDeleting) {
            return;
        }

        activeButton = deleteButton;
        targetName.textContent = deleteButton.dataset.name || 'pegawai';
        targetCode.textContent = deleteButton.dataset.code || '-';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        confirmButton.focus();
    });

    cancelButton.addEventListener('click', function () {
        if (!isDeleting) {
            closeModal(true);
        }
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal && !isDeleting) {
            closeModal(true);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden') && !isDeleting) {
            closeModal(true);
        }
    });

    confirmButton.addEventListener('click', async function () {
        if (!activeButton || isDeleting) {
            return;
        }

        isDeleting = true;
        confirmButton.disabled = true;
        confirmLabel.textContent = 'Menghapus...';

        const employeeId = activeButton.dataset.id;
        const row = Array.from(document.querySelectorAll('[data-employee-row]'))
            .find(function (item) {
                return item.dataset.employeeRow === employeeId;
            });

        try {
            const response = await window.axios.post(modal.dataset.endpoint, {
                id: employeeId,
                csrf_token: csrfInput.value,
            });
            const result = response.data;

            if (!result || result.success !== true || String(result.deleted_id) !== employeeId) {
                throw new Error(result && result.message ? result.message : 'Data pegawai gagal dihapus.');
            }

            closeModal(false);

            if (row) {
                row.classList.add('row-removing');
                await new Promise(function (resolve) {
                    setTimeout(resolve, 300);
                });
                row.remove();

                const remainingCount = Math.max(0, Number(countText.textContent) - 1);
                countText.textContent = String(remainingCount);

                if (remainingCount === 0) {
                    tableWrapper.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                }
            }

            showAlert(result.message, 'success');
        } catch (error) {
            const responseMessage = error.response && error.response.data && error.response.data.message;
            showAlert(responseMessage || error.message || 'Terjadi kesalahan saat menghapus data pegawai.', 'error');
        } finally {
            isDeleting = false;
            confirmButton.disabled = false;
            confirmLabel.textContent = 'Ya, Hapus Pegawai';
        }
    });
}
