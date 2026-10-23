(() => {
  'use strict';

  const qs = (selector, root = document) => root.querySelector(selector);
  const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));
  const number = (value) => Number(value) || 0;
  const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const percent = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 3, maximumFractionDigits: 3 });

  const prepareCanvas = (canvas) => {
    const width = Math.max(90, Math.floor(canvas.getBoundingClientRect().width || canvas.parentElement?.clientWidth || 160));
    const height = Math.max(30, Number(canvas.getAttribute('height')) || 48);
    const ratio = Math.max(1, window.devicePixelRatio || 1);
    canvas.width = Math.floor(width * ratio);
    canvas.height = Math.floor(height * ratio);
    canvas.style.height = `${height}px`;
    const ctx = canvas.getContext('2d');
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    ctx.clearRect(0, 0, width, height);
    return { ctx, width, height };
  };

  const drawSparkline = (canvas, values, direction = 'flat') => {
    const clean = values.map(number).filter((value) => value > 0);
    const { ctx, width, height } = prepareCanvas(canvas);
    if (clean.length < 2) {
      ctx.strokeStyle = 'rgba(210,232,248,.35)';
      ctx.setLineDash([4, 4]);
      ctx.beginPath();
      ctx.moveTo(3, height / 2);
      ctx.lineTo(width - 3, height / 2);
      ctx.stroke();
      return;
    }
    const min = Math.min(...clean);
    const max = Math.max(...clean);
    const range = Math.max(max - min, Math.abs(max) * 0.001, 1e-9);
    const pad = 4;
    const points = clean.map((value, index) => ({
      x: pad + ((width - pad * 2) * index / (clean.length - 1)),
      y: pad + (height - pad * 2) * (1 - ((value - min) / range)),
    }));
    const stroke = direction === 'up' ? '#69e4a8' : (direction === 'down' ? '#ff8490' : '#7acbff');
    const fill = direction === 'up' ? 'rgba(65,221,143,.18)' : (direction === 'down' ? 'rgba(255,100,118,.16)' : 'rgba(84,180,246,.17)');
    const gradient = ctx.createLinearGradient(0, 0, 0, height);
    gradient.addColorStop(0, fill);
    gradient.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.beginPath();
    ctx.moveTo(points[0].x, height - pad);
    points.forEach((point) => ctx.lineTo(point.x, point.y));
    ctx.lineTo(points[points.length - 1].x, height - pad);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();
    ctx.beginPath();
    points.forEach((point, index) => index === 0 ? ctx.moveTo(point.x, point.y) : ctx.lineTo(point.x, point.y));
    ctx.strokeStyle = stroke;
    ctx.lineWidth = 2;
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    ctx.stroke();
    const end = points[points.length - 1];
    ctx.beginPath();
    ctx.arc(end.x, end.y, 2.8, 0, Math.PI * 2);
    ctx.fillStyle = stroke;
    ctx.fill();
  };

  const canvasValues = (canvas) => {
    try { return JSON.parse(canvas.dataset.values || '[]'); } catch { return []; }
  };

  const renderAllSparklines = () => {
    qsa('.fx-sparkline,.login-fx-sparkline').forEach((canvas) => {
      const change = canvas.closest('article')?.querySelector('.fx-change');
      const direction = change?.classList.contains('up') ? 'up' : (change?.classList.contains('down') ? 'down' : 'flat');
      drawSparkline(canvas, canvasValues(canvas), direction);
    });
  };

  const normalizedRate = (row, base = 'IDR') => {
    const raw = number(row.rate);
    return number(row.display_rate) || (raw > 0 ? (base === 'IDR' ? 1 / raw : raw) : 0);
  };

  const rateTrend = (row) => Array.isArray(row.trend) ? row.trend.map((point) => number(point.value)).filter((value) => value > 0) : [];

  const updateDashboardRate = (card, row, spread) => {
    const mid = normalizedRate(row, 'IDR');
    if (mid <= 0) return;
    const direction = ['up', 'down'].includes(row.direction) ? row.direction : 'flat';
    const change = number(row.change_percent);
    const value = qs('.fx-card-value strong', card);
    if (value) value.textContent = rupiah.format(mid);
    const buy = qs('.fx-buy-sell>div:first-child strong', card);
    const sell = qs('.fx-buy-sell>div:last-child strong', card);
    if (buy) buy.textContent = rupiah.format(mid * (1 - spread / 100));
    if (sell) sell.textContent = rupiah.format(mid * (1 + spread / 100));
    const badge = qs('.fx-change', card);
    if (badge) {
      badge.className = `fx-change ${direction}`;
      badge.innerHTML = `<i>${direction === 'up' ? '↗' : (direction === 'down' ? '↘' : '→')}</i><b>${percent.format(Math.abs(change))}%</b>`;
    }
    const canvas = qs('.fx-sparkline', card);
    if (canvas) {
      canvas.dataset.values = JSON.stringify(rateTrend(row));
      drawSparkline(canvas, rateTrend(row), direction);
    }
  };

  const updateLoginRate = (card, row) => {
    const mid = normalizedRate(row, 'IDR');
    if (mid <= 0) return;
    const direction = ['up', 'down'].includes(row.direction) ? row.direction : 'flat';
    const change = number(row.change_percent);
    const value = qs('.login-fx-rate', card);
    if (value) value.textContent = rupiah.format(mid);
    const badge = qs('.fx-change', card);
    if (badge) {
      badge.className = `fx-change ${direction}`;
      badge.textContent = `${direction === 'up' ? '↗' : (direction === 'down' ? '↘' : '→')} ${percent.format(Math.abs(change))}%`;
    }
    const canvas = qs('.login-fx-sparkline', card);
    if (canvas) {
      canvas.dataset.values = JSON.stringify(rateTrend(row));
      drawSparkline(canvas, rateTrend(row), direction);
    }
  };

  const initializeFx = () => {
    const board = qs('#fxMarketBoard');
    const login = qs('[data-fx-login]');
    if (!board && !login) return;
    const endpoint = board?.dataset.endpoint || login?.dataset.endpoint;
    const spread = number(board?.dataset.spread || 0.25);
    const refreshButton = qs('#fxRefreshButton');
    let loading = false;

    const fetchRates = async () => {
      if (loading || !endpoint) return;
      loading = true;
      refreshButton?.classList.add('loading');
      try {
        const response = await fetch(`${endpoint}${endpoint.includes('?') ? '&' : '?'}_=${Date.now()}`, { headers: { Accept: 'application/json' }, cache: 'no-store' });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const payload = await response.json();
        const rates = Array.isArray(payload.rates) ? payload.rates : [];
        rates.forEach((row) => {
          const code = String(row.quote_currency || '').toUpperCase();
          if (!code) return;
          const boardCard = qs(`.fx-market-card[data-code="${CSS.escape(code)}"]`);
          const loginCard = qs(`.login-fx-item[data-code="${CSS.escape(code)}"]`);
          if (boardCard) updateDashboardRate(boardCard, row, spread);
          if (loginCard) updateLoginRate(loginCard, row);
        });
        const provider = String(payload.provider || 'cache');
        const providerLabel = qs('#fxProviderLabel');
        const loginProvider = qs('#loginFxProvider');
        if (providerLabel) providerLabel.textContent = provider;
        if (loginProvider) loginProvider.textContent = provider;
        const date = rates[0]?.rate_date || rates[0]?.fetched_at || payload.generated_at;
        const updated = date ? new Date(String(date).replace(' ', 'T')) : new Date();
        const last = qs('#fxLastUpdated');
        if (last && !Number.isNaN(updated.getTime())) last.textContent = updated.toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' });
        const live = qs('#fxLiveText');
        if (live) live.textContent = payload.cached ? 'Cache aktif' : 'Data diperbarui';
        const loginStatus = qs('#loginFxStatus');
        if (loginStatus) loginStatus.textContent = payload.cached ? 'Cache aktif' : 'Baru diperbarui';
      } catch (error) {
        const live = qs('#fxLiveText');
        if (live) live.textContent = 'Koneksi tertunda';
        const loginStatus = qs('#loginFxStatus');
        if (loginStatus) loginStatus.textContent = 'Cache lokal';
      } finally {
        loading = false;
        refreshButton?.classList.remove('loading');
      }
    };

    refreshButton?.addEventListener('click', fetchRates);
    const pollSeconds = Math.max(30, number(board?.dataset.pollSeconds || 60));
    window.setInterval(fetchRates, pollSeconds * 1000);

    const search = qs('#fxSearchInput');
    search?.addEventListener('input', () => {
      const query = String(search.value || '').toLowerCase().trim();
      let visible = 0;
      qsa('.fx-market-card').forEach((card) => {
        const show = query === '' || String(card.dataset.search || '').includes(query);
        card.hidden = !show;
        if (show) visible += 1;
      });
      const count = qs('#fxVisibleCount');
      if (count) count.textContent = String(visible);
      const empty = qs('#fxEmptyState');
      if (empty) empty.hidden = visible > 0;
    });
  };

  const initializeReportComplaintSource = () => {
    const select = qs('[data-report-row-select]');
    if (!select) return;
    const preview = qs('[data-report-source-preview]');
    const companySelect = qs('select[name="company_id"]');
    const subject = qs('input[name="subject"]');
    const description = qs('textarea[name="description"]');
    const fields = {
      company: qs('[data-source-company]'), beneficiary: qs('[data-source-beneficiary]'),
      amount: qs('[data-source-amount]'), status: qs('[data-source-status]'),
    };
    const apply = () => {
      const option = select.options[select.selectedIndex];
      const hasValue = Boolean(option?.value);
      if (preview) preview.hidden = !hasValue;
      if (!hasValue) return;
      Object.entries(fields).forEach(([key, element]) => { if (element) element.textContent = option.dataset[key] || '-'; });
      if (companySelect && option.dataset.companyId) companySelect.value = option.dataset.companyId;
      if (subject && subject.value.trim() === '') subject.value = `Complain report: ${option.dataset.beneficiary || 'transaksi'}`;
      if (description && description.value.trim() === '') {
        description.value = [
          `Sumber report: ${option.dataset.batch || '-'}`,
          `Bill To: ${option.dataset.company || '-'}`,
          `Beneficiary: ${option.dataset.beneficiary || '-'}`,
          `Nominal: ${option.dataset.amount || '-'}`,
          `SWIFT: ${option.dataset.swift || '-'}`,
          `Status: ${option.dataset.status || '-'}`,
        ].join('\n');
      }
    };
    select.addEventListener('change', apply);
    apply();
  };

  const initializeAnalysisSource = () => {
    qsa('input[name="data_source"]').forEach((input) => input.addEventListener('change', () => {
      const label = qs('[data-analysis-source-label]');
      if (label) label.textContent = input.checked && input.value === 'reports' ? 'Report Harian' : 'Transaksi';
    }));
  };

  renderAllSparklines();
  initializeFx();
  initializeReportComplaintSource();
  initializeAnalysisSource();
  let resizeTimer;
  window.addEventListener('resize', () => {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(renderAllSparklines, 150);
  });
})();
