/**
 * RemitGo! Custom Datepicker Component (Alpine.js)
 * Standalone, lightweight, non-native calendar popup.
 * Pure TailwindCSS & DM Sans / Roboto. Zero external dependencies.
 */
function initCustomDatePicker() {
  if (!window.Alpine) return;
  window.Alpine.data('customDatePicker', (config = {}) => ({
    name: config.name || 'date',
    value: config.value || '',
    mode: config.mode || 'date', // 'date' | 'month'
    placeholder: config.placeholder || (config.mode === 'month' ? 'Pilih bulan' : 'Pilih tanggal'),
    required: Boolean(config.required),
    open: false,

    viewYear: new Date().getFullYear(),
    viewMonth: new Date().getMonth(), // 0-11

    monthNames: [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ],
    monthNamesShort: [
      'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
      'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ],
    dayNames: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],

    init() {
      if (this.value) {
        const parts = this.value.split('-');
        if (parts.length >= 2) {
          const y = parseInt(parts[0], 10);
          const m = parseInt(parts[1], 10) - 1;
          if (!isNaN(y) && !isNaN(m)) {
            this.viewYear = y;
            this.viewMonth = m;
          }
        }
      }
    },

    get displayLabel() {
      if (!this.value) return '';
      const parts = this.value.split('-');
      if (this.mode === 'month' && parts.length >= 2) {
        const m = parseInt(parts[1], 10) - 1;
        return (this.monthNames[m] || parts[1]) + ' ' + parts[0];
      }
      if (parts.length === 3) {
        const y = parts[0];
        const m = parseInt(parts[1], 10) - 1;
        const d = parseInt(parts[2], 10);
        return d + ' ' + (this.monthNamesShort[m] || parts[1]) + ' ' + y;
      }
      return this.value;
    },

    get currentMonthName() {
      return this.monthNames[this.viewMonth] || '';
    },

    prevMonth() {
      if (this.viewMonth === 0) {
        this.viewMonth = 11;
        this.viewYear--;
      } else {
        this.viewMonth--;
      }
    },

    nextMonth() {
      if (this.viewMonth === 11) {
        this.viewMonth = 0;
        this.viewYear++;
      } else {
        this.viewMonth++;
      }
    },

    prevYear() {
      this.viewYear--;
    },

    nextYear() {
      this.viewYear++;
    },

    get daysInMonth() {
      const days = [];
      const firstDayOfMonth = new Date(this.viewYear, this.viewMonth, 1);
      const totalDays = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();

      // Monday is 0, Sunday is 6
      let startDay = firstDayOfMonth.getDay() - 1;
      if (startDay === -1) startDay = 6;

      // Previous month filler
      const prevTotalDays = new Date(this.viewYear, this.viewMonth, 0).getDate();
      for (let i = startDay - 1; i >= 0; i--) {
        days.push({
          day: prevTotalDays - i,
          month: this.viewMonth - 1,
          year: this.viewMonth === 0 ? this.viewYear - 1 : this.viewYear,
          isCurrentMonth: false,
          dateStr: ''
        });
      }

      // Current month days
      const today = new Date();
      const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

      for (let d = 1; d <= totalDays; d++) {
        const dateStr = this.viewYear + '-' + String(this.viewMonth + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
        days.push({
          day: d,
          month: this.viewMonth,
          year: this.viewYear,
          isCurrentMonth: true,
          dateStr,
          isSelected: this.value === dateStr,
          isToday: todayStr === dateStr
        });
      }

      // Next month filler
      const remaining = (7 - (days.length % 7)) % 7;
      for (let i = 1; i <= remaining; i++) {
        days.push({
          day: i,
          month: this.viewMonth + 1,
          year: this.viewMonth === 11 ? this.viewYear + 1 : this.viewYear,
          isCurrentMonth: false,
          dateStr: ''
        });
      }

      return days;
    },

    selectDate(dateStr) {
      if (!dateStr) return;
      this.value = dateStr;
      this.open = false;
      this.$dispatch('change', { value: this.value });
      this.$nextTick(() => {
        const input = this.$el.querySelector('input[type="hidden"]');
        if (input) {
          input.value = this.value;
          input.dispatchEvent(new Event('input', { bubbles: true }));
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    },

    selectMonth(monthIndex) {
      const monthStr = this.viewYear + '-' + String(monthIndex + 1).padStart(2, '0');
      this.value = monthStr;
      this.viewMonth = monthIndex;
      this.open = false;
      this.$dispatch('change', { value: this.value });
      this.$nextTick(() => {
        const input = this.$el.querySelector('input[type="hidden"]');
        if (input) {
          input.value = this.value;
          input.dispatchEvent(new Event('input', { bubbles: true }));
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    },

    setToday() {
      const today = new Date();
      if (this.mode === 'month') {
        this.selectMonth(today.getMonth());
      } else {
        const dateStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
        this.viewYear = today.getFullYear();
        this.viewMonth = today.getMonth();
        this.selectDate(dateStr);
      }
    },

    clear() {
      this.value = '';
      this.open = false;
      this.$dispatch('change', { value: '' });
      this.$nextTick(() => {
        const input = this.$el.querySelector('input[type="hidden"]');
        if (input) {
          input.value = '';
          input.dispatchEvent(new Event('input', { bubbles: true }));
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    }
  }));
}

document.addEventListener('alpine:init', initCustomDatePicker);
if (window.Alpine) {
  initCustomDatePicker();
}
