import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;

Alpine.plugin(collapse);

window.deleteConfirmModal = function () {
    return {
        isOpen: false,
        title: 'Hapus Data?',
        message: 'Data akan dihapus secara permanen dan tidak dapat dipulihkan kembali.',
        confirmText: 'Hapus',
        cancelText: 'Batal',
        targetForm: null,
        callback: null,

        open(detail) {
            this.title = detail.title || 'Hapus Data?';
            this.message = detail.message || 'Data akan dihapus secara permanen dan tidak dapat dipulihkan kembali.';
            this.confirmText = detail.confirmText || 'Hapus';
            this.cancelText = detail.cancelText || 'Batal';
            this.targetForm = detail.form || null;
            this.callback = detail.onConfirm || null;
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
            this.targetForm = null;
            this.callback = null;
        },

        confirmAction() {
            const form = this.targetForm;
            const cb = this.callback;
            this.close();
            if (cb && typeof cb === 'function') {
                cb();
            } else if (form) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        }
    };
};

document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!form || !(form instanceof HTMLFormElement)) return;

    if (form.dataset.confirmed === 'true') {
        delete form.dataset.confirmed;
        return;
    }

    if (form.hasAttribute('data-confirm-delete')) {
        e.preventDefault();
        e.stopPropagation();
        const message = form.getAttribute('data-confirm-delete');
        const title = form.getAttribute('data-confirm-title') || 'Hapus Data?';
        window.dispatchEvent(new CustomEvent('open-delete-modal', {
            detail: {
                title: title,
                message: message,
                form: form
            }
        }));
    }
});

Alpine.start();
