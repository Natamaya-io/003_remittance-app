(() => {
  'use strict';

  const qs = (selector, root = document) => root.querySelector(selector);
  const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));
  const num = (value) => Number(value) || 0;
  const rupiahCompact = new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
  });

  const initializePayerAccountPicker = () => {
    const root = qs('[data-payer-account-picker]');
    if (!root) return;

    const select = qs('[data-payer-account-select]', root);
    const company = qs('select[name="payer_company_id"]');
    if (!select || !company) return;

    const fields = {
      account: qs('[data-payer-field="account"]'),
      bank: qs('[data-payer-field="bank"]'),
      email: qs('[data-payer-field="email"]'),
      address: qs('[data-payer-field="address"]'),
      city: qs('[data-payer-field="city"]'),
      country: qs('[data-payer-field="country"]'),
    };

    const options = Array.from(select.options).slice(1);
    const filter = (autoSelect = false) => {
      const companyId = String(company.value || '');
      let visible = 0;
      let defaultOption = null;

      options.forEach((option) => {
        const match = companyId !== '' && option.dataset.companyId === companyId;
        option.hidden = !match;
        option.disabled = !match;
        if (match) {
          visible += 1;
          if (option.dataset.default === '1' && !defaultOption) defaultOption = option;
        }
      });

      const selected = select.options[select.selectedIndex];
      if (selected && selected.value && selected.disabled) select.value = '';
      if (autoSelect && !select.value && defaultOption) {
        select.value = defaultOption.value;
        apply();
      }

      root.classList.toggle('has-options', visible > 0);
      const hint = qs('[data-payer-account-count]', root);
      if (hint) hint.textContent = visible > 0 ? `${visible} rekening tersedia` : 'Belum ada rekening untuk PT ini';
    };

    const apply = () => {
      const option = select.options[select.selectedIndex];
      if (!option || !option.value) return;
      Object.entries(fields).forEach(([key, field]) => {
        if (!field) return;
        field.value = option.dataset[key] || '';
        field.dispatchEvent(new Event('input', { bubbles: true }));
      });
      root.classList.add('selected');
    };

    company.addEventListener('change', () => filter(true));
    select.addEventListener('change', () => {
      root.classList.toggle('selected', Boolean(select.value));
      apply();
    });
    filter(false);
  };

  const initializeCopyButtons = () => {
    qsa('[data-copy-value]').forEach((button) => {
      button.addEventListener('click', async () => {
        const value = String(button.dataset.copyValue || '');
        if (!value) return;
        try {
          await navigator.clipboard.writeText(value);
          const original = button.innerHTML;
          button.innerHTML = '<i class="bi bi-check2"></i>';
          button.classList.add('copied');
          window.setTimeout(() => {
            button.innerHTML = original;
            button.classList.remove('copied');
          }, 1400);
        } catch {
          const area = document.createElement('textarea');
          area.value = value;
          area.style.position = 'fixed';
          area.style.opacity = '0';
          document.body.appendChild(area);
          area.select();
          document.execCommand('copy');
          area.remove();
        }
      });
    });
  };

  const valuesFromCanvas = (canvas) => {
    try {
      const parsed = JSON.parse(canvas?.dataset.values || '[]');
      return Array.isArray(parsed) ? parsed.map(num).filter((value) => value > 0) : [];
    } catch {
      return [];
    }
  };

  const updateFxRange = (rangeRoot) => {
    const card = rangeRoot.closest('.fx-market-card,.login-fx-item');
    const canvas = qs('.fx-sparkline,.login-fx-sparkline', card || document);
    const values = valuesFromCanvas(canvas);
    const currentText = qs('.fx-card-value strong,.login-fx-rate', card || document)?.textContent || '';
    const current = num(currentText.replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(',', '.'));
    if (!values.length && current <= 0) return;

    const all = values.length ? values : [current];
    const min = Math.min(...all);
    const max = Math.max(...all);
    const effectiveCurrent = current > 0 ? current : all[all.length - 1];
    const position = max > min ? Math.max(0, Math.min(100, ((effectiveCurrent - min) / (max - min)) * 100)) : 50;

    const fill = qs('[data-fx-range-fill]', rangeRoot);
    const marker = qs('[data-fx-range-marker]', rangeRoot);
    const minLabel = qs('[data-fx-range-min]', rangeRoot);
    const maxLabel = qs('[data-fx-range-max]', rangeRoot);
    const currentLabel = qs('[data-fx-range-current]', rangeRoot);
    if (fill) fill.style.width = `${position}%`;
    if (marker) marker.style.left = `${position}%`;
    if (minLabel) minLabel.textContent = rupiahCompact.format(min);
    if (maxLabel) maxLabel.textContent = rupiahCompact.format(max);
    if (currentLabel) currentLabel.textContent = `${Math.round(position)}%`;
  };

  const initializeFxRanges = () => {
    qsa('[data-fx-range]').forEach(updateFxRange);
    qsa('.fx-sparkline,.login-fx-sparkline').forEach((canvas) => {
      const observer = new MutationObserver(() => {
        const range = qs('[data-fx-range]', canvas.closest('.fx-market-card,.login-fx-item'));
        if (range) updateFxRange(range);
      });
      observer.observe(canvas, { attributes: true, attributeFilter: ['data-values'] });
    });
  };

  const initializeFormProgress = () => {
    qsa('form[method="post"]').forEach((form) => {
      form.addEventListener('submit', () => {
        const submit = qs('button[type="submit"]', form);
        if (!submit || submit.dataset.noLoading === '1') return;
        submit.disabled = true;
        submit.dataset.originalHtml = submit.innerHTML;
        submit.innerHTML = '<span class="button-spinner"></span><span>Memproses...</span>';
        form.classList.add('is-submitting');
      });
    });
  };

  const initializeKeyboardNavigation = () => {
    document.addEventListener('keydown', (event) => {
      if (!(event.ctrlKey || event.metaKey)) return;
      const key = event.key.toLowerCase();
      const routes = {
        '1': document.querySelector('a[href$="/transactions"]'),
        '2': document.querySelector('a[href$="/reports"]'),
        '3': document.querySelector('a[href$="/complaints"]'),
        '4': document.querySelector('a[href$="/analysis"]'),
      };
      if (routes[key]) {
        event.preventDefault();
        routes[key].click();
      }
    });
  };

  initializePayerAccountPicker();
  initializeCopyButtons();
  initializeFxRanges();
  initializeFormProgress();
  initializeKeyboardNavigation();
})();
