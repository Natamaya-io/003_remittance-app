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

// Global Search Focus helper
window.openSearchModal = function () {
  const searchInput = document.querySelector('.custom-search-input') || document.querySelector('input[name="q"]');
  if (searchInput) {
    searchInput.focus();
    searchInput.select?.();
  }
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

  // Inline Search Component untuk Halaman Reports (Real Input, No Modal, No Esc Button)
  window.Alpine.data('remitInlineSearch', (config = {}) => ({
    isOpen: false,
    action: config.action || '/reports/search',
    query: config.query || '',
    extraParams: config.extraParams || {},
    selectedIndex: 0,

    searchTips: [
      { id: 'tip-payer', icon: 'bi-wallet2', prefix: 'payer:', desc: 'Filter berdasarkan nama payer / pengirim', sample: 'payer: ' },
      { id: 'tip-benef', icon: 'bi-person-check', prefix: 'benef:', desc: 'Filter berdasarkan nama penerima (beneficiary)', sample: 'benef: ' },
      { id: 'tip-bank', icon: 'bi-bank', prefix: 'bank:', desc: 'Filter bank tujuan atau nomor rekening', sample: 'bank: ' },
      { id: 'tip-tim', icon: 'bi-people', prefix: 'tim:', desc: 'Filter transaksi berdasarkan tim operasional', sample: 'tim: ' },
      { id: 'tip-date', icon: 'bi-calendar-date', prefix: 'date:', desc: 'Filter tanggal transaksi (YYYY-MM-DD)', sample: 'date: ' },
      { id: 'tip-inv', icon: 'bi-receipt', prefix: 'inv:', desc: 'Filter nomor invoice atau kontrak', sample: 'inv: ' }
    ],

    get filteredTips() {
      const q = this.query.trim().toLowerCase();
      if (!q) return this.searchTips;
      return this.searchTips.filter(tip => 
        tip.prefix.toLowerCase().includes(q) || 
        tip.desc.toLowerCase().includes(q)
      );
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
          subtitle: `"${q}"`
        });
      }
      this.filteredTips.forEach(tip => list.push(tip));
      return list;
    },

    getItemIndex(targetItem) {
      return this.allNavigableItems.findIndex(i => i.id === targetItem.id);
    },

    handleKeydown(e) {
      const items = this.allNavigableItems;
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        this.isOpen = true;
        if (items.length > 0) {
          this.selectedIndex = (this.selectedIndex + 1) % items.length;
          this.scrollActiveIntoView();
        }
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        this.isOpen = true;
        if (items.length > 0) {
          this.selectedIndex = (this.selectedIndex - 1 + items.length) % items.length;
          this.scrollActiveIntoView();
        }
      } else if (e.key === 'Enter') {
        if (this.isOpen && items.length > 0 && items[this.selectedIndex]) {
          const selected = items[this.selectedIndex];
          if (!selected.isSearchAction) {
            e.preventDefault();
            this.selectTip(selected);
            return;
          }
        }
      } else if (e.key === 'Escape') {
        this.isOpen = false;
      }
    },

    scrollActiveIntoView() {
      this.$nextTick(() => {
        const container = this.$refs.suggestionsList;
        const activeEl = container?.querySelector('[data-selected="true"]');
        if (activeEl && container) {
          const containerRect = container.getBoundingClientRect();
          const elRect = activeEl.getBoundingClientRect();
          if (elRect.bottom > containerRect.bottom) {
            activeEl.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
          } else if (elRect.top < containerRect.top) {
            activeEl.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
          }
        }
      });
    },

    selectTip(tip) {
      if (!tip) return;
      this.query = tip.sample;
      this.selectedIndex = 0;
      this.$refs.searchInput?.focus();
    },

    handleSubmit(e) {
      if (!this.query.trim()) {
        e.preventDefault();
        this.$refs.searchInput?.focus();
        return;
      }
      this.isOpen = false;
    },

    executeSearch(q) {
      this.query = q;
      this.isOpen = false;
      this.$refs.searchInput?.form?.submit();
    }
  }));
}

document.addEventListener('alpine:init', initRemitDialogs);
if (window.Alpine) {
  initRemitDialogs();
}
