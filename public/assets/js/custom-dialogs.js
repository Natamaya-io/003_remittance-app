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

  _isReactive: false,

  _ensureReactive() {
    if (window.Alpine && !this._isReactive) {
      this.confirmState = window.Alpine.reactive(this.confirmState);
      this.alertState = window.Alpine.reactive(this.alertState);
      this._isReactive = true;
    }
  },

  confirm(message, options = {}) {
    this._ensureReactive();
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
    this._ensureReactive();
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

// =========================================================
// REMITGO! GLOBAL TOAST SYSTEM (GEMINI.MD SECTION 16)
// =========================================================
window.RemitToast = {
  toasts: [],
  _isReactive: false,

  _ensureReactive() {
    if (window.Alpine && !this._isReactive) {
      this.toasts = window.Alpine.reactive(this.toasts || []);
      this._isReactive = true;
    }
  },

  add(options) {
    this._ensureReactive();
    if (typeof options === 'string') {
      options = { message: options };
    }
    const id = options.id || ('toast_' + Date.now() + '_' + Math.floor(Math.random() * 1000));
    const duration = options.duration !== undefined ? options.duration : 5000;
    const rawToast = {
      id: id,
      title: options.title || '',
      message: options.message || '',
      type: options.type || 'info', // 'success' | 'danger' | 'warning' | 'info'
      duration: duration,
      remaining: duration,
      visible: true,
      timer: null,
      timerStart: 0
    };
    const toast = (window.Alpine && window.Alpine.reactive) ? window.Alpine.reactive(rawToast) : rawToast;

    this.toasts.push(toast);

    if (duration > 0) {
      this.startTimer(toast);
    }

    return id;
  },

  dismiss(id) {
    const idx = this.toasts.findIndex(t => t.id === id);
    if (idx !== -1) {
      const toast = this.toasts[idx];
      if (toast.timer) {
        clearTimeout(toast.timer);
        toast.timer = null;
      }
      toast.visible = false;
      setTimeout(() => {
        const removeIdx = this.toasts.findIndex(t => t.id === id);
        if (removeIdx !== -1) {
          this.toasts.splice(removeIdx, 1);
        }
      }, 150);
    }
  },

  startTimer(toast) {
    toast.timerStart = Date.now();
    toast.timer = setTimeout(() => {
      this.dismiss(toast.id);
    }, toast.remaining);
  },

  pause(toast) {
    if (toast.duration <= 0 || !toast.timer) return;
    clearTimeout(toast.timer);
    toast.timer = null;
    const elapsed = Date.now() - toast.timerStart;
    toast.remaining = Math.max(0, toast.remaining - elapsed);
  },

  resume(toast) {
    if (toast.duration <= 0 || toast.remaining <= 0 || toast.timer) return;
    this.startTimer(toast);
  }
};

// Global shorthand: window.remitToast(...)
window.remitToast = function (message, type = 'info', options = {}) {
  if (typeof message === 'object') {
    return window.RemitToast.add(message);
  }
  return window.RemitToast.add({ message, type, ...options });
};
window.remitToast.success = (msg, opt = {}) => window.remitToast(msg, 'success', opt);
window.remitToast.error = (msg, opt = {}) => window.remitToast(msg, 'danger', opt);
window.remitToast.danger = (msg, opt = {}) => window.remitToast(msg, 'danger', opt);
window.remitToast.warning = (msg, opt = {}) => window.remitToast(msg, 'warning', opt);
window.remitToast.info = (msg, opt = {}) => window.remitToast(msg, 'info', opt);

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
      if (typeof HTMLFormElement.prototype.submit === 'function') {
        HTMLFormElement.prototype.submit.call(form);
      } else {
        form.submit();
      }
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

  if (window.RemitDialogs && typeof window.RemitDialogs._ensureReactive === 'function') {
    window.RemitDialogs._ensureReactive();
  }

  if (window.RemitToast && typeof window.RemitToast._ensureReactive === 'function') {
    window.RemitToast._ensureReactive();
  }

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

  // RemitGo! Floating Toast Stack Component (GEMINI.MD Section 16)
  window.Alpine.data('remitToastStack', () => ({
    get toasts() {
      return window.RemitToast.toasts;
    },

    dismiss(id) {
      window.RemitToast.dismiss(id);
    },

    pauseTimer(toast) {
      window.RemitToast.pause(toast);
    },

    resumeTimer(toast) {
      window.RemitToast.resume(toast);
    },

    toastClasses(type) {
      switch (type) {
        case 'success':
          return 'toast-success bg-[#ecfdf5] border-[#a7f3d0] text-[#065f46]';
        case 'danger':
        case 'error':
          return 'toast-danger bg-[#fef2f2] border-[#fecaca] text-[#991b1b]';
        case 'warning':
          return 'toast-warning bg-[#fffbeb] border-[#fde68a] text-[#92400e]';
        default:
          return 'toast-info bg-[#eff6ff] border-[#bfdbfe] text-[#1e40af]';
      }
    },

    toastIcon(type) {
      switch (type) {
        case 'success':
          return 'bi-check-circle-fill text-[#059669]';
        case 'danger':
        case 'error':
          return 'bi-x-circle-fill text-[#dc2626]';
        case 'warning':
          return 'bi-exclamation-triangle-fill text-[#d97706]';
        default:
          return 'bi-info-circle-fill text-[#315B8C]';
      }
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

  // RemitGo! Report View Mode Component (Table vs Card view toggle - GEMINI.MD Section 1.2)
  window.Alpine.data('remitReportView', (initialMode = 'table') => ({
    viewMode: initialMode,

    init() {
      try {
        const local = localStorage.getItem('remitgo_report_view_mode');
        if (local && local !== this.viewMode && (local === 'table' || local === 'list')) {
          this.viewMode = local;
          document.cookie = 'remitgo_report_view_mode=' + local + '; path=/; max-age=31536000; SameSite=Lax';
          document.documentElement.classList.toggle('report-view-list', local === 'list');
        }
      } catch (e) {}

      this.$watch('viewMode', (val) => {
        document.documentElement.classList.toggle('report-view-list', val === 'list');
        try {
          localStorage.setItem('remitgo_report_view_mode', val);
          document.cookie = 'remitgo_report_view_mode=' + val + '; path=/; max-age=31536000; SameSite=Lax';
        } catch (e) {}
      });
    }
  }));
}

document.addEventListener('alpine:init', initRemitDialogs);
if (window.Alpine) {
  initRemitDialogs();
}

// Auto-bootstrap backend flash session messages into floating toasts (GEMINI.MD Section 16.5 Poin 3)
function bootstrapBackendFlashes() {
  const flashDataEl = document.getElementById('remit-flashes-data');
  if (!flashDataEl) return;

  try {
    const raw = flashDataEl.textContent || flashDataEl.innerText || '[]';
    const flashes = JSON.parse(raw.trim());
    if (Array.isArray(flashes)) {
      flashes.forEach(flash => {
        const type = flash.type || 'info';
        const message = flash.message || '';
        if (message.trim()) {
          window.RemitToast.add({
            type: type,
            message: message,
            duration: 5000
          });
        }
      });
    }
  } catch (err) {
    console.warn('Failed to parse backend flash messages:', err);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bootstrapBackendFlashes);
} else {
  bootstrapBackendFlashes();
}
