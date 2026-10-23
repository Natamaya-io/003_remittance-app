(() => {
  'use strict';

  window.FableFxBoardV22 = true;

  const q = (selector, root = document) =>
    root.querySelector(selector);

  const qa = (selector, root = document) =>
    Array.from(
      root.querySelectorAll(selector)
    );

  const number = (value) =>
    Number.isFinite(Number(value))
      ? Number(value)
      : 0;


  const money = (
    value,
    digits = 2
  ) =>
    new Intl.NumberFormat(
      'id-ID',
      {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: digits,
        maximumFractionDigits: digits,
      }
    ).format(
      number(value)
    );


  const percent = (value) =>
    `${number(value) > 0 ? '+' : ''}${
      number(value).toLocaleString(
        'id-ID',
        {
          minimumFractionDigits: 3,
          maximumFractionDigits: 3,
        }
      )
    }%`;


  const formatDate = (
    value,
    withTime = false
  ) => {

    if (!value) {
      return '-';
    }

    const date =
      new Date(
        String(value)
          .replace(
            ' ',
            'T'
          )
      );

    if (
      Number.isNaN(
        date.getTime()
      )
    ) {
      return String(value);
    }

    return date.toLocaleString(
      'id-ID',
      withTime
        ? {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
          }
        : {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
          }
    );

  };


  const escapeHtml = (value) =>
    String(
      value ?? ''
    )
      .replaceAll(
        '&',
        '&amp;'
      )
      .replaceAll(
        '<',
        '&lt;'
      )
      .replaceAll(
        '>',
        '&gt;'
      )
      .replaceAll(
        '"',
        '&quot;'
      )
      .replaceAll(
        "'",
        '&#039;'
      );


  /*
  |--------------------------------------------------------------------------
  | TOAST
  |--------------------------------------------------------------------------
  */

  const showFxToast = (
    title,
    message = '',
    type = 'success'
  ) => {

    let stack =
      q(
        '.fx-toast-stack'
      );


    if (!stack) {

      stack =
        document.createElement(
          'div'
        );

      stack.className =
        'fx-toast-stack';

      document.body.appendChild(
        stack
      );

    }


    const validType =
      [
        'success',
        'warning',
        'danger',
      ].includes(type)
        ? type
        : 'success';


    const toast =
      document.createElement(
        'div'
      );

    toast.className =
      `fx-toast fx-toast--${validType}`;


    const icon =
      document.createElement(
        'span'
      );

    icon.className =
      'fx-toast__icon';


    icon.textContent =
      validType === 'danger'
        ? '×'
        : (
            validType === 'warning'
              ? '!'
              : '✓'
          );


    const copy =
      document.createElement(
        'div'
      );


    const heading =
      document.createElement(
        'strong'
      );

    heading.textContent =
      String(
        title || 'Informasi'
      );


    copy.appendChild(
      heading
    );


    if (message) {

      const body =
        document.createElement(
          'span'
        );

      body.textContent =
        String(message);

      copy.appendChild(
        body
      );

    }


    toast.append(
      icon,
      copy
    );


    stack.appendChild(
      toast
    );


    window.setTimeout(
      () => {

        toast.classList.add(
          'is-leaving'
        );

        window.setTimeout(
          () => toast.remove(),
          220
        );

      },
      4200
    );

  };


  /*
  |--------------------------------------------------------------------------
  | CURRENCY META
  |--------------------------------------------------------------------------
  */

  const metadata = {

    USD: [
      'us',
      'Dolar Amerika',
    ],

    EUR: [
      'eu',
      'Euro',
    ],

    SGD: [
      'sg',
      'Dolar Singapura',
    ],

    AUD: [
      'au',
      'Dolar Australia',
    ],

    GBP: [
      'gb',
      'Pound Sterling',
    ],

    JPY: [
      'jp',
      'Yen Jepang',
    ],

    CNY: [
      'cn',
      'Yuan Tiongkok',
    ],

    HKD: [
      'hk',
      'Dolar Hong Kong',
    ],

    MYR: [
      'my',
      'Ringgit Malaysia',
    ],

    THB: [
      'th',
      'Baht Thailand',
    ],

    KRW: [
      'kr',
      'Won Korea Selatan',
    ],

    CHF: [
      'ch',
      'Franc Swiss',
    ],

    CAD: [
      'ca',
      'Dolar Kanada',
    ],

    NZD: [
      'nz',
      'Dolar Selandia Baru',
    ],

    SAR: [
      'sa',
      'Riyal Arab Saudi',
    ],

    AED: [
      'ae',
      'Dirham UEA',
    ],

  };


  /*
  |--------------------------------------------------------------------------
  | TREND PARSER
  |--------------------------------------------------------------------------
  */

  const parseTrend = (
    value,
    fallback = 0
  ) => {

    let source =
      value;


    if (
      typeof source ===
      'string'
    ) {

      try {

        source =
          JSON.parse(
            source
          );

      } catch (_error) {

        source =
          [];

      }

    }


    const points =
      Array.isArray(source)
        ? source
            .map(
              (point) => {

                if (
                  point &&
                  typeof point ===
                    'object'
                ) {

                  return {
                    value:
                      number(
                        point.value
                      ),

                    at:
                      String(
                        point.at
                        || point.date
                        || ''
                      ),
                  };

                }

                return {
                  value:
                    number(point),

                  at:
                    '',
                };

              }
            )
            .filter(
              (point) =>
                point.value > 0
            )
        : [];


    return points.length
      ? points
      : [
          {
            value:
              fallback,

            at:
              '',
          },

          {
            value:
              fallback,

            at:
              '',
          },
        ];

  };


  /*
  |--------------------------------------------------------------------------
  | CANVAS
  |--------------------------------------------------------------------------
  */

  const prepareCanvas = (
    canvas,
    minimumWidth = 120,
    minimumHeight = 50
  ) => {

    if (!canvas) {
      return null;
    }


    const rect =
      canvas.getBoundingClientRect();


    const width =
      Math.max(
        minimumWidth,
        Math.floor(
          rect.width
        )
      );


    const height =
      Math.max(
        minimumHeight,
        Math.floor(
          rect.height
        )
      );


    const ratio =
      Math.max(
        1,
        window.devicePixelRatio
        || 1
      );


    canvas.width =
      Math.floor(
        width * ratio
      );


    canvas.height =
      Math.floor(
        height * ratio
      );


    const context =
      canvas.getContext(
        '2d'
      );


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


  const smoothLine = (
    context,
    points
  ) => {

    if (!points.length) {
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
        points[
          index - 1
        ];

      const current =
        points[index];

      const controlX =
        (
          previous.x
          + current.x
        )
        / 2;


      context.bezierCurveTo(
        controlX,
        previous.y,
        controlX,
        current.y,
        current.x,
        current.y
      );

    }

  };


  const palette = (
    direction
  ) => {

    if (
      direction === 'up'
    ) {

      return {
        line:
          '#10b981',

        fill:
          'rgba(16,185,129,.20)',
      };

    }


    if (
      direction === 'down'
    ) {

      return {
        line:
          '#ef4444',

        fill:
          'rgba(239,68,68,.18)',
      };

    }


    return {
      line:
        '#2563eb',

      fill:
        'rgba(37,99,235,.18)',
    };

  };


  /*
  |--------------------------------------------------------------------------
  | INLINE CHART
  |--------------------------------------------------------------------------
  */

  const drawInlineChart = (
    canvas
  ) => {

    const trend =
      parseTrend(
        canvas.dataset.values
        || '[]'
      );


    const values =
      trend.map(
        (point) =>
          point.value
      );


    const prepared =
      prepareCanvas(
        canvas,
        120,
        70
      );


    if (!prepared) {
      return;
    }


    const {
      context,
      width,
      height,
    } = prepared;


    const {
      line,
      fill,
    } =
      palette(
        canvas.dataset.direction
        || 'flat'
      );


    const padding =
      5;


    const minimum =
      Math.min(
        ...values
      );


    const maximum =
      Math.max(
        ...values
      );


    const range =
      Math.max(
        maximum - minimum,
        Math.abs(maximum)
          * .000001,
        .000001
      );


    const points =
      values.map(
        (
          value,
          index
        ) => ({

          x:
            padding
            + (
                width
                - padding * 2
              )
            * index
            / Math.max(
                1,
                values.length - 1
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
                  )
                  / range
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
      fill
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

    smoothLine(
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

    smoothLine(
      context,
      points
    );

    context.strokeStyle =
      line;

    context.lineWidth =
      2.3;

    context.lineCap =
      'round';

    context.lineJoin =
      'round';

    context.stroke();

  };


  /*
  |--------------------------------------------------------------------------
  | DETAIL CHART
  |--------------------------------------------------------------------------
  */

  const drawDetailChart = (
    canvas,
    trend,
    direction
  ) => {

    const prepared =
      prepareCanvas(
        canvas,
        580,
        280
      );


    if (!prepared) {
      return;
    }


    const {
      context,
      width,
      height,
    } = prepared;


    const pointsData =
      parseTrend(
        trend
      ).filter(
        (point) =>
          point.value > 0
      );


    if (
      pointsData.length < 2
    ) {
      return;
    }


    const values =
      pointsData.map(
        (point) =>
          point.value
      );


    const minimum =
      Math.min(
        ...values
      );


    const maximum =
      Math.max(
        ...values
      );


    const range =
      Math.max(
        maximum - minimum,
        Math.abs(maximum)
          * .000001,
        .000001
      );


    const left =
      68;

    const right =
      20;

    const top =
      25;

    const bottom =
      55;


    const chartWidth =
      width
      - left
      - right;


    const chartHeight =
      height
      - top
      - bottom;


    const colors =
      palette(
        direction
      );


    const points =
      pointsData.map(
        (
          point,
          index
        ) => ({

          x:
            left
            + chartWidth
            * index
            / Math.max(
                1,
                pointsData.length - 1
              ),

          y:
            top
            + chartHeight
            * (
                1
                - (
                    point.value
                    - minimum
                  )
                  / range
              ),

          value:
            point.value,

          at:
            point.at,

        })
      );


    context.strokeStyle =
      '#e2e8f0';

    context.lineWidth =
      1;

    context.fillStyle =
      '#64748b';

    context.font =
      '11px Segoe UI, Arial';

    context.textAlign =
      'right';

    context.textBaseline =
      'middle';


    for (
      let step = 0;
      step <= 4;
      step += 1
    ) {

      const y =
        top
        + chartHeight
        * step
        / 4;


      const value =
        maximum
        - range
        * step
        / 4;


      context.beginPath();

      context.moveTo(
        left,
        y
      );

      context.lineTo(
        width - right,
        y
      );

      context.stroke();


      context.fillText(
        new Intl.NumberFormat(
          'id-ID',
          {
            maximumFractionDigits:
              0,
          }
        ).format(value),

        left - 10,

        y
      );

    }


    const gradient =
      context.createLinearGradient(
        0,
        top,
        0,
        height - bottom
      );


    gradient.addColorStop(
      0,
      colors.fill
    );


    gradient.addColorStop(
      1,
      'rgba(255,255,255,0)'
    );


    context.beginPath();

    context.moveTo(
      points[0].x,
      height - bottom
    );

    smoothLine(
      context,
      points
    );

    context.lineTo(
      points[
        points.length - 1
      ].x,

      height - bottom
    );

    context.closePath();

    context.fillStyle =
      gradient;

    context.fill();


    context.beginPath();

    smoothLine(
      context,
      points
    );

    context.strokeStyle =
      colors.line;

    context.lineWidth =
      3.2;

    context.lineCap =
      'round';

    context.lineJoin =
      'round';

    context.stroke();


    context.textAlign =
      'center';

    context.textBaseline =
      'top';

    context.fillStyle =
      '#64748b';


    const labelEvery =
      Math.max(
        1,
        Math.ceil(
          points.length / 6
        )
      );


    points.forEach(
      (
        point,
        index
      ) => {

        context.beginPath();

        context.arc(
          point.x,
          point.y,
          3.6,
          0,
          Math.PI * 2
        );

        context.fillStyle =
          colors.line;

        context.fill();


        if (
          index % labelEvery === 0
          ||
          index ===
            points.length - 1
        ) {

          context.fillStyle =
            '#64748b';

          context.fillText(
            formatDate(
              point.at
            ),

            point.x,

            height
              - bottom
              + 13
          );

        }

      }
    );

  };


  /*
  |--------------------------------------------------------------------------
  | CREATE CARD AFTER SYNC
  |--------------------------------------------------------------------------
  */

  const cardMarkup = (
    row,
    board
  ) => {

    const code =
      String(
        row.quote_currency
        || ''
      ).toUpperCase();


    const [
      flag,
      name,
    ] =
      metadata[code]
      || [
        'world',
        code,
      ];


    const mid =
      number(
        row.display_rate
      )
      ||
      (
        number(
          row.rate
        ) > 0
          ? 1
            / number(
                row.rate
              )
          : 0
      );


    const spread =
      Math.max(
        0,
        number(
          board.dataset.spread
          || .25
        )
      );


    const buy =
      number(
        row.buy_rate
      )
      ||
      mid
      * (
          1
          - spread / 100
        );


    const sell =
      number(
        row.sell_rate
      )
      ||
      mid
      * (
          1
          + spread / 100
        );


    const change =
      number(
        row.change_percent
      );


    const direction =
      [
        'up',
        'down',
        'flat',
      ].includes(
        row.direction
      )
        ? row.direction
        : (
            change > 0
              ? 'up'
              : (
                  change < 0
                    ? 'down'
                    : 'flat'
                )
          );


    const trend =
      parseTrend(
        row.trend,
        mid
      );


    const values =
      trend.map(
        (point) =>
          point.value
      );


    const minimum =
      Math.min(
        ...values
      );


    const maximum =
      Math.max(
        ...values
      );


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


    const symbol =
      direction === 'up'
        ? '↗'
        : (
            direction === 'down'
              ? '↘'
              : '→'
          );


    const sprite =
      board.dataset.flagSprite
      || '';


    const encodedTrend =
      escapeHtml(
        JSON.stringify(
          trend
        )
      );


    return `
      <article
        class="fx-modern-card"
        tabindex="0"
        role="button"
        aria-label="Lihat detail kurs ${escapeHtml(code)}"
        data-fx-card
        data-code="${escapeHtml(code)}"
        data-name="${escapeHtml(name)}"
        data-flag="${escapeHtml(flag)}"
        data-search="${escapeHtml((code + ' ' + name).toLowerCase())}"
        data-mid="${mid}"
        data-buy="${buy}"
        data-sell="${sell}"
        data-change="${change}"
        data-direction="${direction}"
        data-trend="${encodedTrend}"
      >

        <span
          class="fx-modern-card-glow"
          aria-hidden="true"
        ></span>

        <header class="fx-modern-card-head">

          <div class="fx-modern-currency">

            <span class="fx-modern-flag">

              <svg viewBox="0 0 64 48">

                <use
                  href="${escapeHtml(sprite + '#flag-' + flag)}"
                ></use>

              </svg>

            </span>

            <div>

              <strong>
                ${escapeHtml(code)}
              </strong>

              <small>
                ${escapeHtml(name)}
              </small>

            </div>

          </div>

          <span
            class="fx-modern-change ${direction}"
            data-role="change"
          >
            ${symbol}
            ${escapeHtml(
              Math.abs(change)
                .toLocaleString(
                  'id-ID',
                  {
                    minimumFractionDigits:
                      3,
                    maximumFractionDigits:
                      3,
                  }
                )
            )}%
          </span>

        </header>


        <div class="fx-modern-value">

          <span>
            1 ${escapeHtml(code)} =
          </span>

          <strong data-role="mid">
            ${escapeHtml(money(mid))}
          </strong>

        </div>


        <canvas
          class="fx-modern-chart"
          data-values="${encodedTrend}"
          data-direction="${direction}"
          aria-label="Grafik historis ${escapeHtml(code)}"
        ></canvas>


        <div class="fx-modern-range">

          <div class="fx-modern-range-head">

            <span>
              Rentang historis
            </span>

            <strong data-role="position-label">
              ${Math.round(position)}%
            </strong>

          </div>


          <div class="fx-modern-range-track">

            <i
              data-role="range-fill"
              style="width:${position}%"
            ></i>

            <b
              data-role="range-marker"
              style="left:${position}%"
            ></b>

          </div>


          <div class="fx-modern-range-values">

            <span data-role="minimum">
              ${escapeHtml(
                money(
                  minimum,
                  0
                )
              )}
            </span>

            <span data-role="maximum">
              ${escapeHtml(
                money(
                  maximum,
                  0
                )
              )}
            </span>

          </div>

        </div>


        <div class="fx-modern-buy-sell">

          <div>

            <span>
              Beli BI
            </span>

            <strong data-role="buy">
              ${escapeHtml(money(buy))}
            </strong>

          </div>

          <div>

            <span>
              Jual BI
            </span>

            <strong data-role="sell">
              ${escapeHtml(money(sell))}
            </strong>

          </div>

        </div>


        <span class="fx-modern-card-hint">
          ↗ Klik untuk detail
        </span>

      </article>
    `;

  };


  /*
  |--------------------------------------------------------------------------
  | OPEN MODAL
  |--------------------------------------------------------------------------
  */

  const openModal = (
    board,
    card
  ) => {

    /*
     * Login tidak membuka detail.
     */
    if (
      board.dataset.context ===
      'login'
    ) {
      return;
    }


    const modal =
      q(
        '[data-fx-modal]',
        board
      );


    if (!modal) {
      return;
    }


    qa(
      '[data-fx-card].is-selected',
      board
    ).forEach(
      (item) =>
        item.classList.remove(
          'is-selected'
        )
    );


    card.classList.add(
      'is-selected'
    );


    const code =
      card.dataset.code
      || 'USD';


    const name =
      card.dataset.name
      || code;


    const flag =
      card.dataset.flag
      || 'world';


    const mid =
      number(
        card.dataset.mid
      );


    const buy =
      number(
        card.dataset.buy
      );


    const sell =
      number(
        card.dataset.sell
      );


    const change =
      number(
        card.dataset.change
      );


    const direction =
      card.dataset.direction
      || 'flat';


    const trend =
      parseTrend(
        card.dataset.trend
        || '[]',
        mid
      );


    const values =
      trend.map(
        (point) =>
          point.value
      );


    const flagNode =
      q(
        '[data-detail-flag]',
        modal
      );


    const sprite =
      board.dataset.flagSprite
      || '';


    if (flagNode) {

      flagNode.innerHTML =
        `
          <svg viewBox="0 0 64 48">
            <use
              href="${escapeHtml(sprite + '#flag-' + flag)}"
            ></use>
          </svg>
        `;

    }


    const codeNode =
      q(
        '[data-detail-code]',
        modal
      );


    const nameNode =
      q(
        '[data-detail-name]',
        modal
      );


    const rateNode =
      q(
        '[data-detail-rate]',
        modal
      );


    const buyNode =
      q(
        '[data-detail-buy]',
        modal
      );


    const sellNode =
      q(
        '[data-detail-sell]',
        modal
      );


    const minNode =
      q(
        '[data-detail-min]',
        modal
      );


    const maxNode =
      q(
        '[data-detail-max]',
        modal
      );


    const dateNode =
      q(
        '[data-detail-date]',
        modal
      );


    if (codeNode) {
      codeNode.textContent =
        code;
    }


    if (nameNode) {
      nameNode.textContent =
        name;
    }


    if (rateNode) {
      rateNode.textContent =
        money(mid);
    }


    if (buyNode) {
      buyNode.textContent =
        money(buy);
    }


    if (sellNode) {
      sellNode.textContent =
        money(sell);
    }


    if (minNode) {
      minNode.textContent =
        money(
          Math.min(
            ...values
          )
        );
    }


    if (maxNode) {
      maxNode.textContent =
        money(
          Math.max(
            ...values
          )
        );
    }


    if (dateNode) {
      dateNode.textContent =
        formatDate(
          trend[
            trend.length - 1
          ]?.at
          || ''
        );
    }


    const changeNode =
      q(
        '[data-detail-change]',
        modal
      );


    const symbol =
      direction === 'up'
        ? '↗'
        : (
            direction === 'down'
              ? '↘'
              : '→'
          );


    if (changeNode) {

      changeNode.className =
        `fx-detail-change ${direction}`;

      changeNode.textContent =
        `${symbol} ${percent(
          Math.abs(change)
        )}`;

    }


    /*
     * Tampilkan modal.
     */
    modal.hidden =
      false;


    modal.setAttribute(
      'aria-hidden',
      'false'
    );


    document.body.classList.add(
      'fx-modal-open'
    );


    /*
     * Selalu mulai dari atas.
     */
    const dialog =
      q(
        '.fx-detail-dialog',
        modal
      );


    if (dialog) {

      dialog.scrollTop =
        0;

    }


    /*
     * Gambar chart setelah modal tampil.
     */
    requestAnimationFrame(
      () => {

        drawDetailChart(
          q(
            '[data-detail-chart]',
            modal
          ),
          trend,
          direction
        );


        q(
          '.fx-detail-close',
          modal
        )?.focus(
          {
            preventScroll:
              true,
          }
        );

      }
    );

  };


  /*
  |--------------------------------------------------------------------------
  | CLOSE MODAL
  |--------------------------------------------------------------------------
  */

  const closeModal = (
    board
  ) => {

    const modal =
      q(
        '[data-fx-modal]',
        board
      );


    if (!modal) {
      return;
    }


    modal.hidden =
      true;


    modal.setAttribute(
      'aria-hidden',
      'true'
    );


    document.body.classList.remove(
      'fx-modal-open'
    );


    qa(
      '[data-fx-card].is-selected',
      board
    ).forEach(
      (item) =>
        item.classList.remove(
          'is-selected'
        )
    );

  };


  /*
  |--------------------------------------------------------------------------
  | INITIALIZE BOARD
  |--------------------------------------------------------------------------
  */

  const initialize = (
    board
  ) => {

    /*
     * Hindari init dua kali.
     */
    if (
      board.dataset.fxV22Ready ===
      '1'
    ) {
      return;
    }


    board.dataset.fxV22Ready =
      '1';


    const grid =
      q(
        '[data-fx-grid]',
        board
      );


    const search =
      q(
        '[data-fx-search]',
        board
      );


    const count =
      q(
        '[data-fx-count]',
        board
      );


    const empty =
      q(
        '[data-fx-empty]',
        board
      );


    const warning =
      q(
        '[data-fx-warning]',
        board
      );


    const status =
      q(
        '[data-fx-status]',
        board
      );


    const provider =
      q(
        '[data-fx-provider]',
        board
      );


    const updated =
      q(
        '[data-fx-updated]',
        board
      );


    const endpoint =
      String(
        board.dataset.endpoint
        || ''
      );


    /*
    |--------------------------------------------------------------------------
    | Render chart cards
    |--------------------------------------------------------------------------
    */

    const renderCharts =
      () => {

        qa(
          '.fx-modern-chart',
          board
        ).forEach(
          drawInlineChart
        );

      };


    /*
    |--------------------------------------------------------------------------
    | Filter search
    |--------------------------------------------------------------------------
    */

    const filter =
      () => {

        const keyword =
          String(
            search?.value
            || ''
          )
            .trim()
            .toLowerCase();


        let visible =
          0;


        qa(
          '[data-fx-card]',
          board
        ).forEach(
          (card) => {

            const show =
              keyword === ''
              ||
              String(
                card.dataset.search
                || ''
              ).includes(
                keyword
              );


            card.hidden =
              !show;


            if (show) {
              visible += 1;
            }

          }
        );


        if (count) {

          count.textContent =
            String(visible);

        }


        if (empty) {

          empty.hidden =
            visible > 0;

        }


        requestAnimationFrame(
          renderCharts
        );

      };


    search?.addEventListener(
      'input',
      filter
    );


    /*
    |--------------------------------------------------------------------------
    | Refresh / Sync
    |--------------------------------------------------------------------------
    */

    let loading =
      false;


    const refresh = async (
      force = false
    ) => {

      if (
        loading
        ||
        !endpoint
        ||
        !grid
      ) {
        return;
      }


      loading =
        true;


      board.classList.add(
        'is-loading'
      );


      const refreshButtons =
        qa(
          '[data-fx-refresh]',
          board
        );


      const syncButtons =
        qa(
          '.fx-modern-sync',
          board
        );


      refreshButtons.forEach(
        (button) => {

          button.classList.add(
            'loading'
          );

          button.disabled =
            true;

        }
      );


      syncButtons.forEach(
        (button) => {

          button.classList.add(
            'loading'
          );

          button.disabled =
            true;

        }
      );


      if (status) {

        status.textContent =
          force
            ? 'Sinkronisasi BI...'
            : 'Memeriksa BI...';

      }


      try {

        const url =
          new URL(
            endpoint,
            window.location.origin
          );


        if (force) {

          url.searchParams.set(
            'refresh',
            '1'
          );

        }


        if (
          board.dataset.quotes
        ) {

          url.searchParams.set(
            'quotes',
            board.dataset.quotes
          );

        }


        /*
         * Cache bust browser.
         */
        url.searchParams.set(
          '_',
          Date.now()
            .toString()
        );


        const response =
          await fetch(
            url.toString(),
            {
              cache:
                'no-store',

              headers: {
                Accept:
                  'application/json',

                'X-Requested-With':
                  'XMLHttpRequest',
              },
            }
          );


        let payload;


        try {

          payload =
            await response.json();

        } catch (_error) {

          throw new Error(
            'Respons server bukan JSON yang valid.'
          );

        }


        if (
          !response.ok
        ) {

          throw new Error(
            payload?.error
            || `HTTP ${response.status}`
          );

        }


        const rates =
          Array.isArray(
            payload?.rates
          )
            ? payload.rates
            : [];


        if (
          rates.length === 0
        ) {

          throw new Error(
            payload?.error
            || 'Data kurs BI belum tersedia.'
          );

        }


        /*
         * Render card terbaru.
         */
        grid.innerHTML =
          rates
            .map(
              (row) =>
                cardMarkup(
                  row,
                  board
                )
            )
            .join('');


        /*
         * Update informasi header.
         */
        if (provider) {

          provider.textContent =
            String(
              payload.provider
              || 'Bank Indonesia'
            );

        }


        if (updated) {

          updated.textContent =
            formatDate(
              payload.updated_at
              || payload.rate_date,
              true
            );

        }


        const isCached =
          Boolean(
            payload.cached
          );


        const isStale =
          Boolean(
            payload.stale
          );


        const hasWarning =
          Boolean(
            payload.error
          );


        if (status) {

          status.textContent =
            isCached
              ? 'Cache BI aktif'
              : 'Data BI terbaru';

        }


        if (warning) {

          warning.hidden =
            !hasWarning;

          warning.textContent =
            payload.error
            || '';

        }


        if (count) {

          count.textContent =
            String(
              rates.length
            );

        }


        if (empty) {

          empty.hidden =
            rates.length > 0;

        }


        /*
         * Render ulang grafik.
         */
        renderCharts();

        filter();


        /*
        |--------------------------------------------------------------------------
        | TOAST SINKRONISASI
        |--------------------------------------------------------------------------
        */

        if (force) {

          if (isStale) {

            showFxToast(
              'Sinkronisasi menggunakan data terakhir',

              payload.error
              || 'Bank Indonesia belum memberikan data baru.',

              'warning'
            );

          } else if (
            hasWarning
          ) {

            showFxToast(
              'Sinkronisasi selesai dengan catatan',

              payload.error,

              'warning'
            );

          } else {

            showFxToast(
              'Sinkronisasi berhasil',

              `Kurs Bank Indonesia berhasil diperbarui${
                payload.rate_date
                  ? ` untuk ${formatDate(payload.rate_date)}`
                  : ''
              }.`,

              'success'
            );

          }

        }


      } catch (error) {

        console.error(
          'Pembaruan kurs BI gagal:',
          error
        );


        const message =
          error instanceof Error
            ? error.message
            : 'Pembaruan kurs gagal.';


        if (status) {

          status.textContent =
            'Koneksi BI tertunda';

        }


        if (warning) {

          warning.hidden =
            false;

          warning.textContent =
            message;

        }


        if (force) {

          showFxToast(
            'Sinkronisasi gagal',
            message,
            'danger'
          );

        }


      } finally {

        loading =
          false;


        board.classList.remove(
          'is-loading'
        );


        refreshButtons.forEach(
          (button) => {

            button.classList.remove(
              'loading'
            );

            button.disabled =
              false;

          }
        );


        syncButtons.forEach(
          (button) => {

            button.classList.remove(
              'loading'
            );

            button.disabled =
              false;

          }
        );

      }

    };


    /*
    |--------------------------------------------------------------------------
    | SINKRON BUTTON
    |--------------------------------------------------------------------------
    |
    | Form POST bawaan tidak dibiarkan reload halaman.
    | Sinkron dilakukan menggunakan API FX.
    |
    */

    qa(
      'form',
      board
    ).forEach(
      (form) => {

        const syncButton =
          q(
            '.fx-modern-sync',
            form
          );


        if (!syncButton) {
          return;
        }


        form.addEventListener(
          'submit',
          (event) => {

            event.preventDefault();

            refresh(
              true
            );

          }
        );

      }
    );


    /*
    |--------------------------------------------------------------------------
    | CLICK HANDLER
    |--------------------------------------------------------------------------
    */

    board.addEventListener(
      'click',
      (event) => {

        /*
         * Refresh icon.
         */
        if (
          event.target.closest(
            '[data-fx-refresh]'
          )
        ) {

          refresh(
            true
          );

          return;

        }


        /*
         * Close modal.
         */
        if (
          event.target.closest(
            '[data-fx-close]'
          )
        ) {

          closeModal(
            board
          );

          return;

        }


        /*
         * Currency card.
         */
        const card =
          event.target.closest(
            '[data-fx-card]'
          );


        if (card) {

          openModal(
            board,
            card
          );

        }

      }
    );


    /*
    |--------------------------------------------------------------------------
    | KEYBOARD CARD
    |--------------------------------------------------------------------------
    */

    board.addEventListener(
      'keydown',
      (event) => {

        const card =
          event.target.closest(
            '[data-fx-card]'
          );


        if (
          card
          &&
          (
            event.key ===
              'Enter'
            ||
            event.key ===
              ' '
          )
        ) {

          event.preventDefault();

          openModal(
            board,
            card
          );

        }

      }
    );


    /*
    |--------------------------------------------------------------------------
    | ESC CLOSE
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
      'keydown',
      (event) => {

        if (
          event.key ===
          'Escape'
        ) {

          closeModal(
            board
          );

        }

      }
    );


    /*
    |--------------------------------------------------------------------------
    | RESIZE
    |--------------------------------------------------------------------------
    */

    let resizeTimer;


    window.addEventListener(
      'resize',
      () => {

        clearTimeout(
          resizeTimer
        );


        resizeTimer =
          window.setTimeout(
            renderCharts,
            150
          );

      }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL RENDER
    |--------------------------------------------------------------------------
    |
    | TIDAK melakukan force sync saat halaman baru dibuka.
    |
    */

    renderCharts();

    filter();


    /*
    |--------------------------------------------------------------------------
    | REFRESH OTOMATIS SETIAP 30 MENIT
    |--------------------------------------------------------------------------
    */

    window.setInterval(
      () =>
        refresh(false),

      30
      * 60
      * 1000
    );

  };


  /*
  |--------------------------------------------------------------------------
  | BOOT
  |--------------------------------------------------------------------------
  */

  const boot =
    () => {

      qa(
        '[data-fx-board]'
      ).forEach(
        initialize
      );

    };


  if (
    document.readyState ===
    'loading'
  ) {

    document.addEventListener(
      'DOMContentLoaded',
      boot
    );

  } else {

    boot();

  }

})();