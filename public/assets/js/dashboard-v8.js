(() => {
    'use strict';

    const one = (
        selector,
        root = document
    ) => root.querySelector(selector);

    const all = (
        selector,
        root = document
    ) => Array.from(
        root.querySelectorAll(selector)
    );

    const toNumber = (value) => {
        const result = Number(value);

        return Number.isFinite(result)
            ? result
            : 0;
    };

    const rupiah = new Intl.NumberFormat(
        'id-ID',
        {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }
    );

    const rupiahCompact =
        new Intl.NumberFormat(
            'id-ID',
            {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            }
        );


    /*
    |--------------------------------------------------------------------------
    | Canvas
    |--------------------------------------------------------------------------
    */

    const prepareCanvas = (
        canvas,
        minimumHeight
    ) => {
        const rectangle =
            canvas.getBoundingClientRect();

        const ratio = Math.max(
            1,
            window.devicePixelRatio || 1
        );

        const width = Math.max(
            240,
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


    const drawEmpty = (
        canvas,
        message
    ) => {
        const canvasData =
            prepareCanvas(
                canvas,
                180
            );

        if (!canvasData) {
            return;
        }

        const {
            context,
            width,
            height,
        } = canvasData;

        context.fillStyle = '#8493a4';
        context.font =
            '11px Segoe UI, Arial, sans-serif';

        context.textAlign = 'center';
        context.textBaseline = 'middle';

        context.fillText(
            message,
            width / 2,
            height / 2
        );
    };


    /*
    |--------------------------------------------------------------------------
    | Grafik tren
    |--------------------------------------------------------------------------
    */

    const drawTrend = (
        canvas,
        rows
    ) => {
        if (!Array.isArray(rows) || rows.length === 0) {
            drawEmpty(
                canvas,
                'Belum ada data tren.'
            );

            return;
        }

        const normalizedRows = rows.map(
            (row) => ({
                label: String(
                    row.date
                    ?? row.label
                    ?? ''
                ),
                value: Math.max(
                    0,
                    toNumber(
                        row.total
                        ?? row.value
                        ?? 0
                    )
                ),
            })
        );

        const canvasData =
            prepareCanvas(
                canvas,
                230
            );

        if (!canvasData) {
            return;
        }

        const {
            context,
            width,
            height,
        } = canvasData;

        const padding = {
            top: 20,
            right: 17,
            bottom: 36,
            left: 40,
        };

        const chartWidth =
            width
            - padding.left
            - padding.right;

        const chartHeight =
            height
            - padding.top
            - padding.bottom;

        const maximum = Math.max(
            1,
            ...normalizedRows.map(
                (row) => row.value
            )
        );

        for (let step = 0; step <= 4; step += 1) {
            const ratio = step / 4;

            const y =
                padding.top
                + chartHeight
                - chartHeight * ratio;

            context.beginPath();

            context.moveTo(
                padding.left,
                y
            );

            context.lineTo(
                width - padding.right,
                y
            );

            context.strokeStyle = '#e7edf4';
            context.lineWidth = 1;
            context.stroke();

            context.fillStyle = '#8795a6';
            context.font =
                '8px Segoe UI, Arial, sans-serif';

            context.textAlign = 'right';
            context.textBaseline = 'middle';

            context.fillText(
                String(
                    Math.round(
                        maximum * ratio
                    )
                ),
                padding.left - 8,
                y
            );
        }

        const points = normalizedRows.map(
            (
                row,
                index
            ) => ({
                x: normalizedRows.length === 1
                    ? padding.left + chartWidth / 2
                    : padding.left
                        + (
                            chartWidth
                            * index
                            / (
                                normalizedRows.length
                                - 1
                            )
                        ),

                y: padding.top
                    + chartHeight
                    - (
                        chartHeight
                        * row.value
                        / maximum
                    ),

                label: row.label,
            })
        );

        const gradient =
            context.createLinearGradient(
                0,
                padding.top,
                0,
                height - padding.bottom
            );

        gradient.addColorStop(
            0,
            'rgba(42, 127, 215, 0.24)'
        );

        gradient.addColorStop(
            1,
            'rgba(42, 127, 215, 0.01)'
        );

        context.beginPath();

        context.moveTo(
            points[0].x,
            height - padding.bottom
        );

        points.forEach(
            (point) => {
                context.lineTo(
                    point.x,
                    point.y
                );
            }
        );

        context.lineTo(
            points[points.length - 1].x,
            height - padding.bottom
        );

        context.closePath();

        context.fillStyle = gradient;
        context.fill();

        context.beginPath();

        points.forEach(
            (
                point,
                index
            ) => {
                if (index === 0) {
                    context.moveTo(
                        point.x,
                        point.y
                    );
                } else {
                    context.lineTo(
                        point.x,
                        point.y
                    );
                }
            }
        );

        context.strokeStyle = '#287fd3';
        context.lineWidth = 2.5;
        context.lineJoin = 'round';
        context.lineCap = 'round';
        context.stroke();

        const labelStep = Math.max(
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
                    3,
                    0,
                    Math.PI * 2
                );

                context.fillStyle = '#ffffff';
                context.fill();

                context.strokeStyle = '#287fd3';
                context.lineWidth = 2;
                context.stroke();

                if (
                    index % labelStep === 0
                    || index === points.length - 1
                ) {
                    context.fillStyle = '#78879a';
                    context.font =
                        '7px Segoe UI, Arial, sans-serif';

                    context.textAlign = 'center';
                    context.textBaseline = 'top';

                    context.fillText(
                        point.label,
                        point.x,
                        height - padding.bottom + 10
                    );
                }
            }
        );
    };


    /*
    |--------------------------------------------------------------------------
    | Diagram donut
    |--------------------------------------------------------------------------
    */

    const drawStatus = (
        canvas,
        rows
    ) => {
        const normalizedRows = Array.isArray(rows)
            ? rows
                .map(
                    (row) => ({
                        label: String(
                            row.label
                            ?? row.status
                            ?? 'UNKNOWN'
                        ),

                        value: Math.max(
                            0,
                            toNumber(
                                row.total
                                ?? 0
                            )
                        ),
                    })
                )
                .filter(
                    (row) => row.value > 0
                )
            : [];

        if (normalizedRows.length === 0) {
            drawEmpty(
                canvas,
                'Belum ada status.'
            );

            return;
        }

        const canvasData =
            prepareCanvas(
                canvas,
                180
            );

        if (!canvasData) {
            return;
        }

        const {
            context,
            width,
            height,
        } = canvasData;

        const colors = [
            '#2f80ed',
            '#20ae76',
            '#f09a32',
            '#e75865',
            '#7658d7',
            '#159ca4',
            '#718094',
        ];

        const total = normalizedRows.reduce(
            (
                sum,
                row
            ) => sum + row.value,
            0
        );

        const radius = Math.min(
            width,
            height
        ) * 0.36;

        const innerRadius = radius * 0.66;

        const centerX = width / 2;
        const centerY = height / 2;

        let startAngle = -Math.PI / 2;

        normalizedRows.forEach(
            (
                row,
                index
            ) => {
                const angle =
                    (
                        row.value
                        / total
                    )
                    * Math.PI
                    * 2;

                context.beginPath();

                context.arc(
                    centerX,
                    centerY,
                    radius,
                    startAngle,
                    startAngle + angle
                );

                context.arc(
                    centerX,
                    centerY,
                    innerRadius,
                    startAngle + angle,
                    startAngle,
                    true
                );

                context.closePath();

                context.fillStyle =
                    colors[
                        index
                        % colors.length
                    ];

                context.fill();

                startAngle += angle;
            }
        );

        context.fillStyle = '#14243a';
        context.textAlign = 'center';
        context.textBaseline = 'middle';

        context.font =
            '800 23px Segoe UI, Arial, sans-serif';

        context.fillText(
            String(total),
            centerX,
            centerY - 7
        );

        context.fillStyle = '#8190a1';
        context.font =
            '7px Segoe UI, Arial, sans-serif';

        context.fillText(
            'TOTAL',
            centerX,
            centerY + 13
        );
    };


    /*
    |--------------------------------------------------------------------------
    | Sparkline kurs
    |--------------------------------------------------------------------------
    */

    const readSparklineValues = (
        canvas
    ) => {
        try {
            const values = JSON.parse(
                canvas.dataset.values || '[]'
            );

            if (!Array.isArray(values)) {
                return [];
            }

            return values
                .map(toNumber)
                .filter(
                    (value) => value > 0
                );
        } catch {
            return [];
        }
    };


    const drawSparkline = (
        canvas,
        values,
        direction
    ) => {
        const canvasData =
            prepareCanvas(
                canvas,
                38
            );

        if (!canvasData) {
            return;
        }

        const {
            context,
            width,
            height,
        } = canvasData;

        if (values.length < 2) {
            context.beginPath();

            context.moveTo(
                4,
                height / 2
            );

            context.lineTo(
                width - 4,
                height / 2
            );

            context.strokeStyle =
                'rgba(222, 240, 255, 0.30)';

            context.setLineDash([4, 4]);
            context.stroke();
            context.setLineDash([]);

            return;
        }

        const minimum = Math.min(...values);
        const maximum = Math.max(...values);

        const range = Math.max(
            maximum - minimum,
            Math.abs(maximum) * 0.001,
            0.000001
        );

        const padding = 4;

        const points = values.map(
            (
                value,
                index
            ) => ({
                x: padding
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

                y: padding
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

        const strokeColor =
            direction === 'up'
                ? '#68e2a9'
                : (
                    direction === 'down'
                        ? '#ff8f9b'
                        : '#78d2ff'
                );

        context.beginPath();

        points.forEach(
            (
                point,
                index
            ) => {
                if (index === 0) {
                    context.moveTo(
                        point.x,
                        point.y
                    );
                } else {
                    context.lineTo(
                        point.x,
                        point.y
                    );
                }
            }
        );

        context.strokeStyle = strokeColor;
        context.lineWidth = 2;
        context.lineJoin = 'round';
        context.lineCap = 'round';
        context.stroke();
    };


    const renderSparklines = () => {
        all('.dash8-fx-sparkline').forEach(
            (canvas) => {
                const card = canvas.closest(
                    '.dash8-fx-card'
                );

                const change = one(
                    '.dash8-change',
                    card
                );

                let direction = 'flat';

                if (change?.classList.contains('up')) {
                    direction = 'up';
                } else if (
                    change?.classList.contains('down')
                ) {
                    direction = 'down';
                }

                drawSparkline(
                    canvas,
                    readSparklineValues(canvas),
                    direction
                );
            }
        );
    };


    /*
    |--------------------------------------------------------------------------
    | Filter dan refresh kurs
    |--------------------------------------------------------------------------
    */

    const initializeFx = () => {
        const board = one('#dash8FxBoard');

        if (!board) {
            return;
        }

        const search = one('#dash8FxSearch');
        const count = one('#dash8FxCount');
        const empty = one('#dash8FxEmpty');
        const refresh = one('#dash8FxRefresh');
        const status = one('#dash8FxStatus');
        const provider = one('#dash8FxProvider');
        const updated = one('#dash8FxUpdated');

        const endpoint = String(
            board.dataset.endpoint || ''
        );

        let loading = false;


        const filterCards = () => {
            const term = String(
                search?.value || ''
            )
                .trim()
                .toLowerCase();

            let visible = 0;

            all(
                '.dash8-fx-card',
                board
            ).forEach(
                (card) => {
                    const text = String(
                        card.dataset.search || ''
                    );

                    const show =
                        term === ''
                        || text.includes(term);

                    card.hidden = !show;

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
                empty.hidden = visible > 0;
            }
        };


        const refreshRates = async () => {
            if (
                loading
                || endpoint === ''
            ) {
                return;
            }

            loading = true;

            refresh?.classList.add('loading');

            if (status) {
                status.textContent =
                    'Mengambil data...';
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
                            Accept: 'application/json',
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
                    && typeof payload.data === 'object'
                        ? payload.data
                        : payload;

                if (provider) {
                    provider.textContent = String(
                        source.provider
                        || payload.provider
                        || 'Cache'
                    );
                }

                if (updated) {
                    const now = new Date();

                    updated.textContent =
                        now.toLocaleString(
                            'id-ID',
                            {
                                dateStyle: 'short',
                                timeStyle: 'short',
                            }
                        );
                }

                if (status) {
                    status.textContent =
                        source.cached
                            ? 'Cache aktif'
                            : 'Data diperbarui';
                }
            } catch (error) {
                console.error(
                    'Gagal memperbarui kurs:',
                    error
                );

                if (status) {
                    status.textContent =
                        'Koneksi tertunda';
                }
            } finally {
                loading = false;

                refresh?.classList.remove(
                    'loading'
                );
            }
        };


        search?.addEventListener(
            'input',
            filterCards
        );

        refresh?.addEventListener(
            'click',
            refreshRates
        );

        const intervalSeconds = Math.max(
            30,
            toNumber(
                board.dataset.pollSeconds
                || 60
            )
        );

        window.setInterval(
            refreshRates,
            intervalSeconds * 1000
        );

        filterCards();
    };


    /*
    |--------------------------------------------------------------------------
    | Inisialisasi
    |--------------------------------------------------------------------------
    */

    const dashboardData =
        window.dashboardV8Data || {};

    const trendCanvas = one(
        '#dash8TrendChart'
    );

    const statusCanvas = one(
        '#dash8StatusChart'
    );


    const renderDashboard = () => {
        if (trendCanvas) {
            drawTrend(
                trendCanvas,
                Array.isArray(
                    dashboardData.trend
                )
                    ? dashboardData.trend
                    : []
            );
        }

        if (statusCanvas) {
            drawStatus(
                statusCanvas,
                Array.isArray(
                    dashboardData.statuses
                )
                    ? dashboardData.statuses
                    : []
            );
        }

        renderSparklines();
    };


    renderDashboard();
    initializeFx();

    let resizeTimer = null;

    window.addEventListener(
        'resize',
        () => {
            window.clearTimeout(
                resizeTimer
            );

            resizeTimer = window.setTimeout(
                renderDashboard,
                140
            );
        }
    );
})();