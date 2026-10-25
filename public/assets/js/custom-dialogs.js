/**
 * RemitGo! Custom Dialog & Alert System (Alpine.js)
 * Eliminates browser-native alert() and confirm() dialogs.
 * Provides custom Asphalt Gamified modals and flash toasts.
 */

window.RemitDialogs = {
  confirmState: {
    open: false,
    title: 'Konfirmasi Tindakan',
    message: '',
    confirmText: 'Lanjutkan',
    cancelText: 'Batal',
    isDanger: true,
    resolve: null
  },

  alertState: {
    open: false,
    title: 'Pemberitahuan',
    message: '',
    type: 'info', // 'info' | 'success' | 'warning' | 'danger'
    buttonText: 'Mengerti'
  },

  confirm(message, options = {}) {
    return new Promise((resolve) => {
      this.confirmState.message = message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
      this.confirmState.title = options.title || 'Konfirmasi Tindakan';
      this.confirmState.confirmText = options.confirmText || 'Lanjutkan';
      this.confirmState.cancelText = options.cancelText || 'Batal';
      this.confirmState.isDanger = options.isDanger !== false;
      this.confirmState.resolve = resolve;
      this.confirmState.open = true;
    });
  },

  alert(message, options = {}) {
    this.alertState.message = message || '';
    this.alertState.title = options.title || 'Pemberitahuan';
    this.alertState.type = options.type || 'info';
    this.alertState.buttonText = options.buttonText || 'Mengerti';
    this.alertState.open = true;
  }
};

// Global shorthand
window.remitConfirm = (msg, opt) => window.RemitDialogs.confirm(msg, opt);
window.remitAlert = (msg, opt) => window.RemitDialogs.alert(msg, opt);

// Replace native window.alert with custom alert
const _originalAlert = window.alert;
window.alert = function (msg) {
  window.RemitDialogs.alert(String(msg), { title: 'Pemberitahuan Sistem' });
};

// Listen for data-confirm forms globally
document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    const confirmMsg = form.dataset.confirm;
    if (!confirmMsg) return;

    if (form._remitConfirmed) {
      delete form._remitConfirmed;
      return;
    }

    event.preventDefault();
    event.stopImmediatePropagation();

    const ok = await window.RemitDialogs.confirm(confirmMsg, {
      title: 'Konfirmasi Tindakan',
      confirmText: 'Ya, Lanjutkan',
      cancelText: 'Batal',
      isDanger: true
    });

    if (ok) {
      form._remitConfirmed = true;
      form.submit();
    }
  }, true);
});

// Global Search Modal helper
window.openSearchModal = function (config) {
  window.dispatchEvent(new CustomEvent('open-search-modal', { detail: config || {} }));
};

// Alpine data bindings
function initRemitDialogs() {
  if (!window.Alpine) return;

  window.Alpine.data('remitGlobalDialogs', () => ({
    get confirm() {
      return window.RemitDialogs.confirmState;
    },
    get alert() {
      return window.RemitDialogs.alertState;
    },

    onConfirm() {
      const cb = this.confirm.resolve;
      this.confirm.open = false;
      if (typeof cb === 'function') cb(true);
    },

    onCancel() {
      const cb = this.confirm.resolve;
      this.confirm.open = false;
      if (typeof cb === 'function') cb(false);
    },

    closeAlert() {
      this.alert.open = false;
    }
  }));

  window.Alpine.data('remitSearchModal', () => ({
    isOpen: false,
    title: 'Pencarian Transaksi',
    subtitle: 'Ketik kata kunci lalu tekan Enter untuk mencari.',
    placeholder: 'Cari tim, payer, beneficiary, bank, invoice, SWIFT...',
    action: '/reports/search',
    query: '',
    extraParams: {},

    openModal(config = {}) {
      if (typeof config === 'object' && config !== null) {
        if (config.title) this.title = config.title;
        if (config.subtitle) this.subtitle = config.subtitle;
        if (config.placeholder) this.placeholder = config.placeholder;
        if (config.action) this.action = config.action;
        if (config.query !== undefined) this.query = config.query;
        if (config.extraParams) this.extraParams = config.extraParams;
      }
      this.isOpen = true;
      this.$nextTick(() => {
        const input = this.$refs.modalSearchInput;
        if (input) {
          input.focus();
          input.select?.();
        }
      });
    },

    closeModal() {
      this.isOpen = false;
    },

    submitSearch(e) {
      if (!this.query.trim()) {
        e.preventDefault();
        this.$refs.modalSearchInput?.focus();
      }
    }
  }));
}

document.addEventListener('alpine:init', initRemitDialogs);
if (window.Alpine) {
  initRemitDialogs();
}
