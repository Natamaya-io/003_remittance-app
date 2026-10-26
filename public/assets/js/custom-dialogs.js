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

  window.Alpine.data('remitSearchModal', (initialConfig = {}) => ({
    isOpen: false,
    defaultAction: (initialConfig && initialConfig.defaultAction) || '/reports/search',
    action: (initialConfig && initialConfig.defaultAction) || '/reports/search',
    placeholder: 'Cari transaksi...',
    query: '',
    extraParams: {},
    selectedIndex: 0,

    get extraParamsList() {
      if (!this.extraParams || typeof this.extraParams !== 'object') return [];
      if (Array.isArray(this.extraParams)) return this.extraParams;
      return Object.entries(this.extraParams).map(([name, value]) => ({
        name: String(name),
        value: value === null || value === undefined ? '' : String(value)
      }));
    },

    get allNavigableItems() {
      const list = [];
      const q = this.query.trim();
      if (q) {
        list.push({
          id: 'search-submit-action',
          isSearchAction: true,
          icon: 'bi-search',
          title: 'Cari transaksi',
          subtitle: `"${q}" di seluruh sistem`
        });
      }
      return list;
    },

    handleKeydown(e) {
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
      } else if (e.key === 'Enter') {
        e.preventDefault();
        this.executeSearch(this.query);
      } else if (e.key === 'Escape') {
        e.preventDefault();
        this.closeModal();
      }
    },

    selectItem(item) {
      this.executeSearch(this.query);
    },

    executeSearch(q) {
      const query = (q || this.query || '').trim();
      if (!query) {
        this.$refs.modalSearchInput?.focus();
        return;
      }
      const form = this.$refs.searchForm;
      if (form) {
        form.submit();
      } else {
        const urlObj = new URL(this.action, window.location.origin);
        urlObj.searchParams.set('q', query);
        this.extraParamsList.forEach(p => {
          if (p.name && p.value) urlObj.searchParams.set(p.name, p.value);
        });
        window.location.href = urlObj.toString();
      }
    },

    openModal(config = {}) {
      const cfg = typeof config === 'object' && config !== null ? config : {};
      this.placeholder = cfg.placeholder || 'Cari transaksi...';
      this.action = cfg.action || this.defaultAction;
      this.query = cfg.query !== undefined ? String(cfg.query) : '';
      this.extraParams = cfg.extraParams ? { ...cfg.extraParams } : {};
      this.selectedIndex = 0;
      this.isOpen = true;
      this.$nextTick(() => {
        setTimeout(() => {
          const input = this.$refs.modalSearchInput;
          if (input) {
            input.focus();
            input.select?.();
          }
        }, 30);
      });
    },

    closeModal() {
      this.isOpen = false;
    }
  }));
}

document.addEventListener('alpine:init', initRemitDialogs);
if (window.Alpine) {
  initRemitDialogs();
}
