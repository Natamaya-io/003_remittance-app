(() => {
  'use strict';

  const qs = (selector, root = document) => root.querySelector(selector);
  const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));

  /* Navigation, dropdown, alert, confirmation */
  const sidebarToggle = qs('#sidebarToggle');
  const sidebarOverlay = qs('#sidebarOverlay');
  sidebarToggle?.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
  sidebarOverlay?.addEventListener('click', () => document.body.classList.remove('sidebar-open'));

  qsa('[data-dropdown]').forEach((dropdown) => {
    const button = qs('[data-dropdown-toggle]', dropdown);
    button?.addEventListener('click', (event) => {
      event.stopPropagation();
      qsa('[data-dropdown].open').forEach((other) => {
        if (other !== dropdown) other.classList.remove('open');
      });
      dropdown.classList.toggle('open');
      button.setAttribute('aria-expanded', dropdown.classList.contains('open') ? 'true' : 'false');
    });
  });
  document.addEventListener('click', () => qsa('[data-dropdown].open').forEach((item) => item.classList.remove('open')));

  qsa('[data-alert-close]').forEach((button) => button.addEventListener('click', () => button.closest('.alert')?.remove()));
  window.setTimeout(() => qsa('.alert').forEach((alert) => alert.remove()), 7000);

  /* data-confirm handled globally by custom-dialogs.js */

  /* Global keyboard shortcut '/' or Ctrl+K / Cmd+K to open Search Modal */
  document.addEventListener('keydown', (event) => {
    const isSlash = event.key === '/' && !event.ctrlKey && !event.metaKey && !event.altKey;
    const isCmdK = (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k';

    if (isSlash || isCmdK) {
      const activeEl = document.activeElement;
      if (isSlash && activeEl && ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeEl.tagName)) return;
      if (isSlash && activeEl?.isContentEditable) return;

      event.preventDefault();
      if (typeof window.openSearchModal === 'function') {
        window.openSearchModal();
      } else {
        const searchInput = qs('.custom-search-input');
        if (searchInput) {
          searchInput.focus();
          searchInput.select?.();
        }
      }
    }
  });

  qsa('.password-toggle').forEach((button) => {
    button.addEventListener('click', () => {
      const input = document.getElementById(button.dataset.target || '');
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  });

  /* File drop zone */
  qsa('[data-drop-zone]').forEach((zone) => {
    const input = qs('[data-file-input]', zone);
    const label = qs('[data-file-name]', zone);
    const showName = () => {
      if (input?.files?.[0] && label) label.textContent = input.files[0].name;
    };
    input?.addEventListener('change', showName);
    ['dragenter', 'dragover'].forEach((type) => zone.addEventListener(type, (event) => {
      event.preventDefault();
      zone.classList.add('dragover');
    }));
    ['dragleave', 'drop'].forEach((type) => zone.addEventListener(type, (event) => {
      event.preventDefault();
      zone.classList.remove('dragover');
    }));
    zone.addEventListener('drop', (event) => {
      if (!input || !event.dataTransfer?.files?.length) return;
      input.files = event.dataTransfer.files;
      showName();
    });
  });

  /* Checkbox bulk selection */
  const selectAll = qs('[data-select-all]');
  const rowChecks = qsa('[data-row-check]');
  const selectedCount = qs('[data-selected-count]');
  const updateSelection = () => {
    const checked = rowChecks.filter((item) => item.checked).length;
    if (selectedCount) selectedCount.textContent = String(checked);
    if (selectAll) selectAll.checked = rowChecks.length > 0 && checked === rowChecks.length;
  };
  selectAll?.addEventListener('change', () => {
    rowChecks.forEach((item) => { item.checked = selectAll.checked; });
    updateSelection();
  });
  rowChecks.forEach((item) => item.addEventListener('change', updateSelection));

  /* Smart tables */
  const normalize = (value) => String(value ?? '').toLocaleLowerCase('id-ID').replace(/\s+/g, ' ').trim();
  qsa('table[data-smart-table]').forEach((table, index) => {
    if (table.dataset.smartReady === '1') return;
    const body = table.tBodies[0];
    if (!body) return;
    const rows = Array.from(body.rows).filter((row) => !row.querySelector('.empty-state'));
    if (!rows.length) return;
    table.dataset.smartReady = '1';

    const wrapper = table.closest('.table-responsive') || table.parentElement;
    const toolbar = document.createElement('div');
    toolbar.className = 'smart-table-toolbar';
    const search = document.createElement('input');
    search.type = 'search';
    search.className = 'smart-table-search';
    search.placeholder = 'Cari data pada tabel... (Ctrl + F)';
    const column = document.createElement('select');
    column.className = 'form-select compact';
    column.innerHTML = '<option value="-1">Semua kolom</option>';
    qsa('thead th', table).forEach((header, headerIndex) => {
      if (header.classList.contains('select-column')) return;
      const option = document.createElement('option');
      option.value = String(headerIndex);
      option.textContent = header.textContent.trim() || `Kolom ${headerIndex + 1}`;
      column.appendChild(option);
    });
    const count = document.createElement('span');
    count.className = 'smart-table-count';
    const clear = document.createElement('button');
    clear.type = 'button';
    clear.className = 'btn btn-light btn-sm';
    clear.textContent = 'Bersihkan';
    toolbar.append(search, column, count, clear);
    wrapper?.parentElement?.insertBefore(toolbar, wrapper);

    const apply = () => {
      const query = normalize(search.value);
      const columnIndex = Number(column.value);
      let visible = 0;
      rows.forEach((row) => {
        const text = columnIndex >= 0 && row.cells[columnIndex] ? row.cells[columnIndex].textContent : row.textContent;
        const show = query === '' || normalize(text).includes(query);
        row.hidden = !show;
        if (show) visible += 1;
      });
      count.textContent = `${visible} dari ${rows.length} data`;
    };
    search.addEventListener('input', apply);
    column.addEventListener('change', apply);
    clear.addEventListener('click', () => { search.value = ''; apply(); search.focus(); });
    apply();

    if (index === 0) {
      document.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'f') {
          const active = document.activeElement;
          if (active instanceof HTMLInputElement || active instanceof HTMLTextAreaElement || active instanceof HTMLSelectElement) return;
          event.preventDefault();
          search.focus();
          search.select();
        }
      });
    }
  });

  /* Canvas utilities */
  const toNumber = (value) => {
    const number = Number(value);
    return Number.isFinite(number) ? number : 0;
  };

  const prepareCanvas = (canvas, minimumHeight = 120, minimumWidth = 160) => {
    const rect = canvas.getBoundingClientRect();
    const ratio = Math.max(1, window.devicePixelRatio || 1);
    const width = Math.max(minimumWidth, Math.floor(rect.width));
    const height = Math.max(minimumHeight, Math.floor(rect.height));
    canvas.width = Math.floor(width * ratio);
    canvas.height = Math.floor(height * ratio);
    const context = canvas.getContext('2d');
    if (!context) return null;
    context.setTransform(ratio, 0, 0, ratio, 0, 0);
    context.clearRect(0, 0, width, height);
    return { context, width, height };
  };

  const smoothPath = (context, points) => {
    if (!points.length) return;
    context.moveTo(points[0].x, points[0].y);
    for (let index = 1; index < points.length; index += 1) {
      const previous = points[index - 1];
      const current = points[index];
      const middleX = (previous.x + current.x) / 2;
      context.bezierCurveTo(middleX, previous.y, middleX, current.y, current.x, current.y);
    }
  };

  const drawEmpty = (canvas, message, minimumHeight) => {
    const data = prepareCanvas(canvas, minimumHeight);
    if (!data) return;
    const { context, width, height } = data;
    context.fillStyle = '#8492a4';
    context.font = '8px Segoe UI, Arial, sans-serif';
    context.textAlign = 'center';
    context.textBaseline = 'middle';
    context.fillText(message, width / 2, height / 2);
  };

  /* Dashboard charts */
  const dashboardRoot = qs('#dashboardModernV14');
  if (dashboardRoot) {
    const payload = window.dashboardModernData || {};
    const trendRows = Array.isArray(payload.trend) ? payload.trend : [];
    const statusRows = Array.isArray(payload.statuses) ? payload.statuses : [];
    const normalizedTrend = trendRows.map((row) => ({
      label: String(row.date ?? row.label ?? ''),
      value: Math.max(0, toNumber(row.total ?? row.value ?? 0)),
    }));

    const drawMini = () => {
      const canvas = qs('#dashboardMiniChart');
      if (!canvas) return;
      const data = prepareCanvas(canvas, 38, 130);
      if (!data) return;
      const { context, width, height } = data;
      const values = normalizedTrend.length ? normalizedTrend.map((item) => item.value) : [0, 0, 0, 0, 0, 0, 0];
      const minimum = Math.min(...values);
      const maximum = Math.max(...values);
      const range = maximum - minimum;
      const padding = 3;
      const points = values.map((value, index) => ({
        x: padding + ((width - padding * 2) * index / Math.max(1, values.length - 1)),
        y: range === 0
          ? height * 0.58
          : padding + (height - padding * 2) * (1 - ((value - minimum) / range)),
      }));

      const gradient = context.createLinearGradient(0, 0, 0, height);
      gradient.addColorStop(0, 'rgba(255,255,255,.28)');
      gradient.addColorStop(1, 'rgba(255,255,255,0)');
      context.beginPath();
      context.moveTo(points[0].x, height - padding);
      smoothPath(context, points);
      context.lineTo(points[points.length - 1].x, height - padding);
      context.closePath();
      context.fillStyle = gradient;
      context.fill();

      context.beginPath();
      smoothPath(context, points);
      context.strokeStyle = '#fff';
      context.lineWidth = 1.8;
      context.lineCap = 'round';
      context.lineJoin = 'round';
      context.stroke();
    };

    const drawTrend = () => {
      const canvas = qs('#dashboardTrendChart');
      if (!canvas) return;
      if (!normalizedTrend.length) {
        drawEmpty(canvas, 'Belum ada data tren transaksi.', 150);
        return;
      }
      const data = prepareCanvas(canvas, 150, 240);
      if (!data) return;
      const { context, width, height } = data;
      const padding = { top: 13, right: 12, bottom: 20, left: 28 };
      const graphWidth = width - padding.left - padding.right;
      const graphHeight = height - padding.top - padding.bottom;
      const maximum = Math.max(1, ...normalizedTrend.map((row) => row.value));

      for (let step = 0; step <= 4; step += 1) {
        const ratio = step / 4;
        const y = padding.top + graphHeight - graphHeight * ratio;
        context.beginPath();
        context.moveTo(padding.left, y);
        context.lineTo(width - padding.right, y);
        context.strokeStyle = '#e7edf4';
        context.lineWidth = 1;
        context.stroke();
      }

      const points = normalizedTrend.map((row, index) => ({
        x: normalizedTrend.length === 1
          ? padding.left + graphWidth / 2
          : padding.left + graphWidth * index / (normalizedTrend.length - 1),
        y: padding.top + graphHeight - graphHeight * row.value / maximum,
      }));
      const gradient = context.createLinearGradient(0, padding.top, 0, height - padding.bottom);
      gradient.addColorStop(0, 'rgba(37,99,235,.22)');
      gradient.addColorStop(1, 'rgba(37,99,235,.01)');
      context.beginPath();
      context.moveTo(points[0].x, height - padding.bottom);
      smoothPath(context, points);
      context.lineTo(points[points.length - 1].x, height - padding.bottom);
      context.closePath();
      context.fillStyle = gradient;
      context.fill();
      context.beginPath();
      smoothPath(context, points);
      context.strokeStyle = '#2563eb';
      context.lineWidth = 2.1;
      context.lineCap = 'round';
      context.lineJoin = 'round';
      context.stroke();
      points.forEach((point) => {
        context.beginPath();
        context.arc(point.x, point.y, 2.2, 0, Math.PI * 2);
        context.fillStyle = '#fff';
        context.fill();
        context.strokeStyle = '#2563eb';
        context.lineWidth = 1.5;
        context.stroke();
      });
    };

    const drawStatus = () => {
      const canvas = qs('#dashboardStatusChart');
      if (!canvas) return;
      const rows = statusRows
        .map((row) => ({ label: String(row.label || row.status || 'LAINNYA'), value: Math.max(0, toNumber(row.total)) }))
        .filter((row) => row.value > 0);
      if (!rows.length) {
        drawEmpty(canvas, 'Belum ada status.', 120);
        return;
      }
      const data = prepareCanvas(canvas, 120, 120);
      if (!data) return;
      const { context, width, height } = data;
      const colors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
      const total = rows.reduce((sum, row) => sum + row.value, 0);
      const radius = Math.min(width, height) * 0.36;
      const innerRadius = radius * 0.68;
      const centerX = width / 2;
      const centerY = height / 2;
      let start = -Math.PI / 2;
      rows.forEach((row, index) => {
        const angle = row.value / total * Math.PI * 2;
        context.beginPath();
        context.arc(centerX, centerY, radius, start, start + angle);
        context.arc(centerX, centerY, innerRadius, start + angle, start, true);
        context.closePath();
        context.fillStyle = colors[index % colors.length];
        context.fill();
        start += angle;
      });
      context.fillStyle = '#0f172a';
      context.font = '800 17px Segoe UI, Arial, sans-serif';
      context.textAlign = 'center';
      context.textBaseline = 'middle';
      context.fillText(String(total), centerX, centerY - 4);
      context.fillStyle = '#8492a4';
      context.font = '6px Segoe UI, Arial, sans-serif';
      context.fillText('TOTAL', centerX, centerY + 9);
    };

    const renderDashboard = () => {
      drawMini();
      drawTrend();
      drawStatus();
    };
    renderDashboard();
    let dashboardResizeTimer;
    window.addEventListener('resize', () => {
      window.clearTimeout(dashboardResizeTimer);
      dashboardResizeTimer = window.setTimeout(renderDashboard, 140);
    });
  }

  /* Modern FX board */
  const fxBoard = qs('#fxModernBoard');
  if (fxBoard) {
    const searchInput = qs('#fxModernSearch', fxBoard);
    const countLabel = qs('#fxModernCount', fxBoard);
    const emptyState = qs('#fxModernEmpty', fxBoard);
    const refreshButton = qs('#fxModernRefresh', fxBoard);
    const statusLabel = qs('#fxModernStatus', fxBoard);
    const providerLabel = qs('#fxModernProvider', fxBoard);
    const updatedLabel = qs('#fxModernUpdated', fxBoard);
    const endpoint = String(fxBoard.dataset.endpoint || '');
    const spread = Math.max(0, toNumber(fxBoard.dataset.spread || 0.25));
    const idr = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const idrWhole = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

    const drawFxChart = (canvas) => {
      let values = [];
      try {
        values = JSON.parse(canvas.dataset.values || '[]');
      } catch (_error) {
        values = [];
      }
      values = Array.isArray(values) ? values.map(toNumber).filter((value) => value > 0) : [];
      const data = prepareCanvas(canvas, 38, 90);
      if (!data) return;
      const { context, width, height } = data;
      const direction = canvas.dataset.direction || 'flat';
      const lineColor = direction === 'up' ? '#10b981' : (direction === 'down' ? '#ef4444' : '#2563eb');
      const fillColor = direction === 'up' ? 'rgba(16,185,129,.18)' : (direction === 'down' ? 'rgba(239,68,68,.16)' : 'rgba(37,99,235,.17)');

      if (values.length < 2) {
        context.beginPath();
        context.moveTo(4, height * 0.58);
        context.lineTo(width - 4, height * 0.58);
        context.strokeStyle = lineColor;
        context.lineWidth = 1.8;
        context.lineCap = 'round';
        context.stroke();
        return;
      }

      const minimum = Math.min(...values);
      const maximum = Math.max(...values);
      const range = Math.max(maximum - minimum, Math.abs(maximum) * 0.000001, 0.000001);
      const padding = 4;
      const points = values.map((value, index) => ({
        x: padding + (width - padding * 2) * index / (values.length - 1),
        y: padding + (height - padding * 2) * (1 - (value - minimum) / range),
      }));
      const gradient = context.createLinearGradient(0, 0, 0, height);
      gradient.addColorStop(0, fillColor);
      gradient.addColorStop(1, 'rgba(255,255,255,0)');
      context.beginPath();
      context.moveTo(points[0].x, height - padding);
      smoothPath(context, points);
      context.lineTo(points[points.length - 1].x, height - padding);
      context.closePath();
      context.fillStyle = gradient;
      context.fill();
      context.beginPath();
      smoothPath(context, points);
      context.strokeStyle = lineColor;
      context.lineWidth = 1.9;
      context.lineCap = 'round';
      context.lineJoin = 'round';
      context.stroke();
    };

    const renderFxCharts = () => qsa('.fx-modern-chart', fxBoard).forEach(drawFxChart);

    const filterFxCards = () => {
      const keyword = normalize(searchInput?.value || '');
      let visible = 0;
      qsa('.fx-modern-card', fxBoard).forEach((card) => {
        const show = keyword === '' || normalize(card.dataset.search || '').includes(keyword);
        card.hidden = !show;
        if (show) visible += 1;
      });
      if (countLabel) countLabel.textContent = String(visible);
      if (emptyState) emptyState.hidden = visible > 0;
    };
    searchInput?.addEventListener('input', filterFxCards);

    const extractTrend = (row, mid) => {
      const raw = Array.isArray(row.trend) ? row.trend : [];
      const values = raw.map((point) => toNumber(point?.value ?? point)).filter((value) => value > 0);
      return values.length ? values : [mid];
    };

    const updateFxCard = (card, row) => {
      const code = String(row.quote_currency || '').toUpperCase();
      let mid = toNumber(row.display_rate);
      if (mid <= 0) {
        const raw = toNumber(row.rate);
        mid = raw > 0 ? 1 / raw : 0;
      }
      if (!code || mid <= 0) return;

      const change = toNumber(row.change_percent);
      let direction = ['up', 'down', 'flat'].includes(row.direction) ? row.direction : 'flat';
      if (direction === 'flat' && change > 0) direction = 'up';
      if (direction === 'flat' && change < 0) direction = 'down';
      const symbol = direction === 'up' ? '↗' : (direction === 'down' ? '↘' : '→');
      const trend = extractTrend(row, mid);
      const minimum = Math.min(...trend);
      const maximum = Math.max(...trend);
      const position = maximum > minimum ? Math.max(0, Math.min(100, (mid - minimum) / (maximum - minimum) * 100)) : 50;

      const midLabel = qs('[data-role="mid"]', card);
      const buyLabel = qs('[data-role="buy"]', card);
      const sellLabel = qs('[data-role="sell"]', card);
      const changeLabel = qs('[data-role="change"]', card);
      const positionLabel = qs('[data-role="position-label"]', card);
      const minimumLabel = qs('[data-role="minimum"]', card);
      const maximumLabel = qs('[data-role="maximum"]', card);
      const fill = qs('[data-role="range-fill"]', card);
      const marker = qs('[data-role="range-marker"]', card);
      const canvas = qs('.fx-modern-chart', card);

      if (midLabel) midLabel.textContent = idr.format(mid);
      if (buyLabel) buyLabel.textContent = idr.format(mid * (1 - spread / 100));
      if (sellLabel) sellLabel.textContent = idr.format(mid * (1 + spread / 100));
      if (changeLabel) {
        changeLabel.className = `fx-modern-change ${direction}`;
        changeLabel.textContent = `${symbol} ${Math.abs(change).toLocaleString('id-ID', { minimumFractionDigits: 3, maximumFractionDigits: 3 })}%`;
      }
      if (positionLabel) positionLabel.textContent = `${Math.round(position)}%`;
      if (minimumLabel) minimumLabel.textContent = idrWhole.format(minimum);
      if (maximumLabel) maximumLabel.textContent = idrWhole.format(maximum);
      if (fill) fill.style.width = `${position}%`;
      if (marker) marker.style.left = `${position}%`;
      if (canvas) {
        canvas.dataset.direction = direction;
        canvas.dataset.values = JSON.stringify(trend);
        drawFxChart(canvas);
      }
    };

    let fxLoading = false;
    const refreshFx = async () => {
      if (fxLoading || endpoint === '') return;
      fxLoading = true;
      refreshButton?.classList.add('loading');
      if (statusLabel) statusLabel.textContent = 'Memperbarui...';
      try {
        const separator = endpoint.includes('?') ? '&' : '?';
        const response = await fetch(`${endpoint}${separator}_=${Date.now()}`, {
          cache: 'no-store',
          headers: { Accept: 'application/json' },
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const payload = await response.json();
        const rates = Array.isArray(payload.rates) ? payload.rates : [];
        rates.forEach((row) => {
          const code = String(row.quote_currency || '').toUpperCase();
          const card = code ? qs(`.fx-modern-card[data-code="${CSS.escape(code)}"]`, fxBoard) : null;
          if (card) updateFxCard(card, row);
        });
        if (providerLabel) providerLabel.textContent = String(payload.provider || 'Cache');
        if (updatedLabel) updatedLabel.textContent = new Date().toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' });
        if (statusLabel) statusLabel.textContent = payload.cached ? 'Cache aktif' : 'Data terbaru';
      } catch (error) {
        console.error('Gagal memperbarui kurs:', error);
        if (statusLabel) statusLabel.textContent = 'Koneksi tertunda';
      } finally {
        fxLoading = false;
        refreshButton?.classList.remove('loading');
      }
    };

    refreshButton?.addEventListener('click', refreshFx);
    window.setInterval(refreshFx, 60000);
    renderFxCharts();
    filterFxCards();

    let fxResizeTimer;
    window.addEventListener('resize', () => {
      window.clearTimeout(fxResizeTimer);
      fxResizeTimer = window.setTimeout(renderFxCharts, 140);
    });
  }
})();
(() => {
    'use strict';

    const select = (
        selector,
        parent = document
    ) => parent.querySelector(selector);

    const selectAll = (
        selector,
        parent = document
    ) => Array.from(
        parent.querySelectorAll(selector)
    );

    const toNumber = (value) => {
        const number = Number(value);

        return Number.isFinite(number)
            ? number
            : 0;
    };

    const normalize = (value) => {
        return String(value || '')
            .trim()
            .toLowerCase();
    };


    /*
    |--------------------------------------------------------------------------
    | Persiapan canvas
    |--------------------------------------------------------------------------
    */

    const prepareCanvas = (
        canvas,
        minimumHeight = 38,
        minimumWidth = 90
    ) => {
        const rectangle =
            canvas.getBoundingClientRect();

        const ratio = Math.max(
            1,
            window.devicePixelRatio || 1
        );

        const width = Math.max(
            minimumWidth,
            Math.floor(rectangle.width)
        );

        const height = Math.max(
            minimumHeight,
            Math.floor(rectangle.height)
        );

        canvas.width = Math.floor(
            width * ratio
        );

        canvas.height = Math.floor(
            height * ratio
        );

        const context =
            canvas.getContext('2d');

        if (!context) {
            return null;
        }

        context.setTransform(
            ratio,
            0,
            0,
            ratio,
            0,
            0
        );

        context.clearRect(
            0,
            0,
            width,
            height
        );

        return {
            context,
            width,
            height,
        };
    };


    /*
    |--------------------------------------------------------------------------
    | Garis grafik melengkung
    |--------------------------------------------------------------------------
    */

    const smoothPath = (
        context,
        points
    ) => {
        if (points.length === 0) {
            return;
        }

        context.moveTo(
            points[0].x,
            points[0].y
        );

        for (
            let index = 1;
            index < points.length;
            index += 1
        ) {
            const previous =
                points[index - 1];

            const current =
                points[index];

            const centerX =
                (
                    previous.x
                    + current.x
                ) / 2;

            context.bezierCurveTo(
                centerX,
                previous.y,
                centerX,
                current.y,
                current.x,
                current.y
            );
        }
    };


    /*
    |--------------------------------------------------------------------------
    | Modern FX board
    |--------------------------------------------------------------------------
    */

    const initializeFxBoard = (fxBoard) => {
        if (
            fxBoard.dataset.fxInitialized
            === 'true'
        ) {
            return;
        }

        fxBoard.dataset.fxInitialized =
            'true';


        const searchInput = select(
            '[data-fx-search]',
            fxBoard
        );

        const countLabel = select(
            '[data-fx-count]',
            fxBoard
        );

        const emptyState = select(
            '[data-fx-empty]',
            fxBoard
        );

        const refreshButton = select(
            '[data-fx-refresh]',
            fxBoard
        );

        const statusLabel = select(
            '[data-fx-status]',
            fxBoard
        );

        const providerLabel = select(
            '[data-fx-provider]',
            fxBoard
        );

        const updatedLabel = select(
            '[data-fx-updated]',
            fxBoard
        );

        const endpoint = String(
            fxBoard.dataset.endpoint || ''
        );

        const spread = Math.max(
            0,
            toNumber(
                fxBoard.dataset.spread
                || 0.25
            )
        );


        const idr = new Intl.NumberFormat(
            'id-ID',
            {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }
        );

        const idrWhole =
            new Intl.NumberFormat(
                'id-ID',
                {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0,
                }
            );


        const drawFxChart = (canvas) => {
            let values = [];

            try {
                values = JSON.parse(
                    canvas.dataset.values
                    || '[]'
                );
            } catch {
                values = [];
            }

            values = Array.isArray(values)
                ? values
                    .map(toNumber)
                    .filter(
                        (value) => value > 0
                    )
                : [];


            const compact =
                fxBoard.classList.contains(
                    'fx-modern-board--login'
                );

            const canvasData =
                prepareCanvas(
                    canvas,
                    compact ? 30 : 38,
                    compact ? 70 : 90
                );

            if (!canvasData) {
                return;
            }

            const {
                context,
                width,
                height,
            } = canvasData;


            const direction =
                canvas.dataset.direction
                || 'flat';

            const lineColor =
                direction === 'up'
                    ? '#10b981'
                    : (
                        direction === 'down'
                            ? '#ef4444'
                            : '#2563eb'
                    );

            const fillColor =
                direction === 'up'
                    ? 'rgba(16,185,129,.18)'
                    : (
                        direction === 'down'
                            ? 'rgba(239,68,68,.16)'
                            : 'rgba(37,99,235,.17)'
                    );


            if (values.length < 2) {
                context.beginPath();

                context.moveTo(
                    4,
                    height * 0.58
                );

                context.lineTo(
                    width - 4,
                    height * 0.58
                );

                context.strokeStyle =
                    lineColor;

                context.lineWidth =
                    compact ? 1.5 : 1.8;

                context.lineCap =
                    'round';

                context.stroke();

                return;
            }


            const minimum =
                Math.min(...values);

            const maximum =
                Math.max(...values);

            const range = Math.max(
                maximum - minimum,
                Math.abs(maximum) * 0.000001,
                0.000001
            );

            const padding =
                compact ? 3 : 4;


            const points = values.map(
                (
                    value,
                    index
                ) => ({
                    x:
                        padding
                        + (
                            (
                                width
                                - padding * 2
                            )
                            * index
                            / (
                                values.length
                                - 1
                            )
                        ),

                    y:
                        padding
                        + (
                            height
                            - padding * 2
                        )
                        * (
                            1
                            - (
                                value
                                - minimum
                            ) / range
                        ),
                })
            );


            const gradient =
                context.createLinearGradient(
                    0,
                    0,
                    0,
                    height
                );

            gradient.addColorStop(
                0,
                fillColor
            );

            gradient.addColorStop(
                1,
                'rgba(255,255,255,0)'
            );


            context.beginPath();

            context.moveTo(
                points[0].x,
                height - padding
            );

            smoothPath(
                context,
                points
            );

            context.lineTo(
                points[
                    points.length - 1
                ].x,
                height - padding
            );

            context.closePath();

            context.fillStyle =
                gradient;

            context.fill();


            context.beginPath();

            smoothPath(
                context,
                points
            );

            context.strokeStyle =
                lineColor;

            context.lineWidth =
                compact ? 1.6 : 1.9;

            context.lineCap =
                'round';

            context.lineJoin =
                'round';

            context.stroke();
        };


        const renderFxCharts = () => {
            selectAll(
                '.fx-modern-chart',
                fxBoard
            ).forEach(drawFxChart);
        };


        /*
        |--------------------------------------------------------------------------
        | Pencarian mata uang
        |--------------------------------------------------------------------------
        */

        const filterFxCards = () => {
            const keyword = normalize(
                searchInput?.value || ''
            );

            let visible = 0;

            selectAll(
                '.fx-modern-card',
                fxBoard
            ).forEach(
                (card) => {
                    const searchable =
                        normalize(
                            card.dataset.search
                            || ''
                        );

                    const show =
                        keyword === ''
                        || searchable.includes(
                            keyword
                        );

                    card.hidden = !show;

                    if (show) {
                        visible += 1;
                    }
                }
            );

            if (countLabel) {
                countLabel.textContent =
                    String(visible);
            }

            if (emptyState) {
                emptyState.hidden =
                    visible > 0;
            }
        };

        searchInput?.addEventListener(
            'input',
            filterFxCards
        );


        /*
        |--------------------------------------------------------------------------
        | Pembaruan isi kartu
        |--------------------------------------------------------------------------
        */

        const extractTrend = (
            row,
            mid
        ) => {
            const raw = Array.isArray(
                row.trend
            )
                ? row.trend
                : [];

            const values = raw
                .map(
                    (point) => toNumber(
                        point?.value
                        ?? point
                    )
                )
                .filter(
                    (value) => value > 0
                );

            return values.length > 0
                ? values
                : [mid];
        };


        const updateFxCard = (
            card,
            row
        ) => {
            const code = String(
                row.quote_currency
                || ''
            ).toUpperCase();

            let mid = toNumber(
                row.display_rate
            );

            if (mid <= 0) {
                const rawRate = toNumber(
                    row.rate
                );

                mid = rawRate > 0
                    ? 1 / rawRate
                    : 0;
            }

            if (
                code === ''
                || mid <= 0
            ) {
                return;
            }


            const change = toNumber(
                row.change_percent
            );

            let direction =
                [
                    'up',
                    'down',
                    'flat',
                ].includes(row.direction)
                    ? row.direction
                    : 'flat';

            if (
                direction === 'flat'
                && change > 0
            ) {
                direction = 'up';
            }

            if (
                direction === 'flat'
                && change < 0
            ) {
                direction = 'down';
            }


            const symbol =
                direction === 'up'
                    ? '↗'
                    : (
                        direction === 'down'
                            ? '↘'
                            : '→'
                    );


            const trend = extractTrend(
                row,
                mid
            );

            const minimum =
                Math.min(...trend);

            const maximum =
                Math.max(...trend);

            const position =
                maximum > minimum
                    ? Math.max(
                        0,
                        Math.min(
                            100,
                            (
                                mid
                                - minimum
                            )
                            / (
                                maximum
                                - minimum
                            )
                            * 100
                        )
                    )
                    : 50;


            const midLabel = select(
                '[data-role="mid"]',
                card
            );

            const buyLabel = select(
                '[data-role="buy"]',
                card
            );

            const sellLabel = select(
                '[data-role="sell"]',
                card
            );

            const changeLabel = select(
                '[data-role="change"]',
                card
            );

            const positionLabel = select(
                '[data-role="position-label"]',
                card
            );

            const minimumLabel = select(
                '[data-role="minimum"]',
                card
            );

            const maximumLabel = select(
                '[data-role="maximum"]',
                card
            );

            const fill = select(
                '[data-role="range-fill"]',
                card
            );

            const marker = select(
                '[data-role="range-marker"]',
                card
            );

            const canvas = select(
                '.fx-modern-chart',
                card
            );


            if (midLabel) {
                midLabel.textContent =
                    idr.format(mid);
            }

            if (buyLabel) {
                buyLabel.textContent =
                    idr.format(
                        mid
                        * (
                            1
                            - spread / 100
                        )
                    );
            }

            if (sellLabel) {
                sellLabel.textContent =
                    idr.format(
                        mid
                        * (
                            1
                            + spread / 100
                        )
                    );
            }

            if (changeLabel) {
                changeLabel.className =
                    `fx-modern-change ${direction}`;

                changeLabel.textContent =
                    `${symbol} ${
                        Math.abs(change)
                            .toLocaleString(
                                'id-ID',
                                {
                                    minimumFractionDigits: 3,
                                    maximumFractionDigits: 3,
                                }
                            )
                    }%`;
            }

            if (positionLabel) {
                positionLabel.textContent =
                    `${Math.round(position)}%`;
            }

            if (minimumLabel) {
                minimumLabel.textContent =
                    idrWhole.format(minimum);
            }

            if (maximumLabel) {
                maximumLabel.textContent =
                    idrWhole.format(maximum);
            }

            if (fill) {
                fill.style.width =
                    `${position}%`;
            }

            if (marker) {
                marker.style.left =
                    `${position}%`;
            }

            if (canvas) {
                canvas.dataset.direction =
                    direction;

                canvas.dataset.values =
                    JSON.stringify(trend);

                drawFxChart(canvas);
            }
        };


        /*
        |--------------------------------------------------------------------------
        | Fetch data kurs
        |--------------------------------------------------------------------------
        */

        let fxLoading = false;

        const refreshFx = async () => {
            if (
                fxLoading
                || endpoint === ''
            ) {
                return;
            }

            fxLoading = true;

            refreshButton?.classList.add(
                'loading'
            );

            if (statusLabel) {
                statusLabel.textContent =
                    'Memperbarui...';
            }


            try {
                const separator =
                    endpoint.includes('?')
                        ? '&'
                        : '?';

                const response = await fetch(
                    `${endpoint}${separator}_=${Date.now()}`,
                    {
                        cache: 'no-store',

                        headers: {
                            Accept:
                                'application/json',
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        `HTTP ${response.status}`
                    );
                }

                const payload =
                    await response.json();

                const source =
                    payload.data
                    && typeof payload.data
                        === 'object'
                        ? payload.data
                        : payload;

                const rates = Array.isArray(
                    source.rates
                )
                    ? source.rates
                    : [];


                const cards = selectAll(
                    '.fx-modern-card',
                    fxBoard
                );


                rates.forEach(
                    (row) => {
                        const code = String(
                            row.quote_currency
                            || ''
                        ).toUpperCase();

                        const card = cards.find(
                            (candidate) => {
                                return String(
                                    candidate.dataset.code
                                    || ''
                                ).toUpperCase()
                                    === code;
                            }
                        );

                        if (card) {
                            updateFxCard(
                                card,
                                row
                            );
                        }
                    }
                );


                if (providerLabel) {
                    providerLabel.textContent =
                        String(
                            source.provider
                            || payload.provider
                            || 'Cache'
                        );
                }

                if (updatedLabel) {
                    updatedLabel.textContent =
                        new Date()
                            .toLocaleString(
                                'id-ID',
                                {
                                    dateStyle:
                                        'short',

                                    timeStyle:
                                        'short',
                                }
                            );
                }

                if (statusLabel) {
                    statusLabel.textContent =
                        source.cached
                        || payload.cached
                            ? 'Cache aktif'
                            : 'Data terbaru';
                }
            } catch (error) {
                console.error(
                    'Gagal memperbarui kurs:',
                    error
                );

                if (statusLabel) {
                    statusLabel.textContent =
                        'Koneksi tertunda';
                }
            } finally {
                fxLoading = false;

                refreshButton?.classList.remove(
                    'loading'
                );
            }
        };


        refreshButton?.addEventListener(
            'click',
            refreshFx
        );

        window.setInterval(
            refreshFx,
            60000
        );


        renderFxCharts();
        filterFxCards();


        let fxResizeTimer = null;

        window.addEventListener(
            'resize',
            () => {
                window.clearTimeout(
                    fxResizeTimer
                );

                fxResizeTimer =
                    window.setTimeout(
                        renderFxCharts,
                        140
                    );
            }
        );
    };


    selectAll(
        '[data-fx-board]'
    ).forEach(initializeFxBoard);


    /*
    |--------------------------------------------------------------------------
    | Tombol tampil/sembunyikan password
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        (event) => {
            const button =
                event.target.closest(
                    '[data-password-toggle]'
                );

            if (!button) {
                return;
            }

            const targetId =
                button.dataset.target;

            if (!targetId) {
                return;
            }

            const input =
                document.getElementById(
                    targetId
                );

            if (!input) {
                return;
            }

            const showPassword =
                input.type === 'password';

            input.type = showPassword
                ? 'text'
                : 'password';

            button.setAttribute(
                'aria-pressed',
                showPassword
                    ? 'true'
                    : 'false'
            );

            button.setAttribute(
                'aria-label',
                showPassword
                    ? 'Sembunyikan password'
                    : 'Tampilkan password'
            );

            const icon =
                button.querySelector('i');

            if (icon) {
                icon.className =
                    showPassword
                        ? 'bi bi-eye-slash'
                        : 'bi bi-eye';
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Interaksi halaman login
    |--------------------------------------------------------------------------
    */

    const loginScene = select(
        '[data-login-scene]'
    );

    if (!loginScene) {
        return;
    }


    const visual = select(
        '[data-login-visual]',
        loginScene
    );

    const formCard = select(
        '[data-login-card]',
        loginScene
    );

    const loginForm = select(
        '[data-login-form]',
        loginScene
    );

    const submitButton = select(
        '[data-login-submit]',
        loginScene
    );

    const reduceMotion =
        window.matchMedia(
            '(prefers-reduced-motion: reduce)'
        ).matches;


    /*
    |--------------------------------------------------------------------------
    | Parallax siluet panel kiri
    |--------------------------------------------------------------------------
    */

    if (
        visual
        && !reduceMotion
    ) {
        const parallaxItems = selectAll(
            '[data-parallax]',
            visual
        );

        visual.addEventListener(
            'pointermove',
            (event) => {
                const rectangle =
                    visual.getBoundingClientRect();

                const normalizedX =
                    Math.max(
                        0,
                        Math.min(
                            1,
                            (
                                event.clientX
                                - rectangle.left
                            ) / rectangle.width
                        )
                    );

                const normalizedY =
                    Math.max(
                        0,
                        Math.min(
                            1,
                            (
                                event.clientY
                                - rectangle.top
                            ) / rectangle.height
                        )
                    );


                visual.style.setProperty(
                    '--login-x',
                    `${normalizedX * 100}%`
                );

                visual.style.setProperty(
                    '--login-y',
                    `${normalizedY * 100}%`
                );


                const deltaX =
                    (
                        normalizedX
                        - 0.5
                    ) * 24;

                const deltaY =
                    (
                        normalizedY
                        - 0.5
                    ) * 18;


                parallaxItems.forEach(
                    (item) => {
                        const factor =
                            toNumber(
                                item.dataset.parallax
                                || 0.2
                            );

                        item.style.transform =
                            `translate3d(
                                ${deltaX * factor}px,
                                ${deltaY * factor}px,
                                0
                            )`;
                    }
                );
            }
        );


        visual.addEventListener(
            'pointerleave',
            () => {
                visual.style.setProperty(
                    '--login-x',
                    '50%'
                );

                visual.style.setProperty(
                    '--login-y',
                    '35%'
                );

                parallaxItems.forEach(
                    (item) => {
                        item.style.transform =
                            'translate3d(0, 0, 0)';
                    }
                );
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Efek tilt kartu login
    |--------------------------------------------------------------------------
    */

    if (
        formCard
        && !reduceMotion
    ) {
        formCard.addEventListener(
            'pointermove',
            (event) => {
                const rectangle =
                    formCard.getBoundingClientRect();

                const x =
                    (
                        event.clientX
                        - rectangle.left
                    ) / rectangle.width
                    - 0.5;

                const y =
                    (
                        event.clientY
                        - rectangle.top
                    ) / rectangle.height
                    - 0.5;


                formCard.style.transform =
                    `perspective(900px)
                     rotateX(${-y * 2.8}deg)
                     rotateY(${x * 3.2}deg)
                     translateY(-2px)`;


                formCard.style.boxShadow =
                    `${-x * 10}px
                     ${22 - y * 8}px
                     70px
                     rgba(21,48,78,.17),
                     inset 0 1px
                     rgba(255,255,255,.9)`;
            }
        );


        formCard.addEventListener(
            'pointerleave',
            () => {
                formCard.style.transform =
                    '';

                formCard.style.boxShadow =
                    '';
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Status loading login
    |--------------------------------------------------------------------------
    */

    loginForm?.addEventListener(
        'submit',
        () => {
            if (!submitButton) {
                return;
            }

            submitButton.disabled = true;

            submitButton.classList.add(
                'is-loading'
            );

            submitButton.innerHTML = `
                <span>Memproses akses...</span>
                <i class="bi bi-arrow-clockwise"></i>
            `;
        }
    );
})();