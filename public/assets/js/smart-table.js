(() => {
    'use strict';

    const normalizeText = (value) => {
        return String(value ?? '')
            .toLocaleLowerCase('id-ID')
            .replace(/\s+/g, ' ')
            .trim();
    };

    const csvEscape = (value) => {
        const text = String(value ?? '')
            .replace(/\r?\n/g, ' ')
            .trim();

        return `"${text.replace(/"/g, '""')}"`;
    };

    const getTargetTables = () => {
        const selectors = [
            '[data-smart-table]',
            'main table:not([data-no-smart-table])',
            '.app-content table:not([data-no-smart-table])',
            '.content-wrapper table:not([data-no-smart-table])'
        ];

        const tables = [];

        selectors.forEach((selector) => {
            document
                .querySelectorAll(selector)
                .forEach((table) => {
                    if (
                        table instanceof HTMLTableElement
                        && !tables.includes(table)
                    ) {
                        tables.push(table);
                    }
                });
        });

        return tables;
    };

    const buildToolbar = (table, index) => {
        if (table.dataset.smartReady === '1') {
            return;
        }

        const tbody = table.tBodies[0];

        if (!tbody) {
            return;
        }

        const rows = Array.from(tbody.rows);

        if (rows.length === 0) {
            return;
        }

        table.dataset.smartReady = '1';

        if (!table.id) {
            table.id = `smartTable${index + 1}`;
        }

        const headers = Array
            .from(table.querySelectorAll('thead th'))
            .map((th, headerIndex) => ({
                index: headerIndex,
                name: th.textContent.trim()
                    || `Kolom ${headerIndex + 1}`
            }));

        const toolbar = document.createElement('div');
        toolbar.className = 'smart-table-toolbar';
        toolbar.dataset.tableTarget = table.id;

        const searchWrap = document.createElement('div');
        searchWrap.className = 'smart-table-search-wrap';

        const icon = document.createElement('span');
        icon.className = 'smart-table-search-icon';
        icon.innerHTML = '⌕';

        const searchInput = document.createElement('input');
        searchInput.type = 'search';
        searchInput.className = 'smart-table-search';
        searchInput.placeholder =
            'Cari data... Tekan Ctrl + F';
        searchInput.autocomplete = 'off';

        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'smart-table-clear';
        clearButton.title = 'Hapus pencarian';
        clearButton.textContent = '×';

        searchWrap.append(
            icon,
            searchInput,
            clearButton
        );

        const columnSelect = document.createElement('select');
        columnSelect.className = 'smart-table-column';

        const allOption = document.createElement('option');
        allOption.value = '-1';
        allOption.textContent = 'Semua kolom';

        columnSelect.appendChild(allOption);

        headers.forEach((header) => {
            const option = document.createElement('option');
            option.value = String(header.index);
            option.textContent = header.name;
            columnSelect.appendChild(option);
        });

        const countBadge = document.createElement('span');
        countBadge.className = 'smart-table-count';

        const exportButton = document.createElement('button');
        exportButton.type = 'button';
        exportButton.className = 'smart-table-export';
        exportButton.innerHTML = '⇩ Export CSV';

        toolbar.append(
            searchWrap,
            columnSelect,
            countBadge,
            exportButton
        );

        const tableContainer =
            table.closest('.table-responsive')
            || table.parentElement;

        tableContainer.parentElement.insertBefore(
            toolbar,
            tableContainer
        );

        const updateCount = () => {
            const visibleRows = rows.filter(
                (row) => row.hidden === false
            ).length;

            countBadge.textContent =
                `${visibleRows} dari ${rows.length} data`;
        };

        const applyFilter = () => {
            const query = normalizeText(
                searchInput.value
            );

            const selectedColumn = Number(
                columnSelect.value
            );

            rows.forEach((row) => {
                let sourceText = '';

                if (
                    selectedColumn >= 0
                    && row.cells[selectedColumn]
                ) {
                    sourceText =
                        row.cells[selectedColumn]
                            .textContent;
                } else {
                    sourceText = row.textContent;
                }

                row.hidden = query !== ''
                    && !normalizeText(sourceText)
                        .includes(query);
            });

            updateCount();
        };

        searchInput.addEventListener(
            'input',
            applyFilter
        );

        columnSelect.addEventListener(
            'change',
            applyFilter
        );

        clearButton.addEventListener(
            'click',
            () => {
                searchInput.value = '';
                applyFilter();
                searchInput.focus();
            }
        );

        exportButton.addEventListener(
            'click',
            () => {
                const visibleRows = rows.filter(
                    (row) => !row.hidden
                );

                const csvRows = [];

                const headerCells = table.querySelectorAll(
                    'thead tr:first-child th'
                );

                if (headerCells.length > 0) {
                    csvRows.push(
                        Array.from(headerCells)
                            .map((cell) =>
                                csvEscape(
                                    cell.textContent
                                )
                            )
                            .join(',')
                    );
                }

                visibleRows.forEach((row) => {
                    csvRows.push(
                        Array.from(row.cells)
                            .map((cell) =>
                                csvEscape(
                                    cell.textContent
                                )
                            )
                            .join(',')
                    );
                });

                const blob = new Blob(
                    [
                        '\uFEFF'
                        + csvRows.join('\r\n')
                    ],
                    {
                        type:
                            'text/csv;charset=utf-8'
                    }
                );

                const link =
                    document.createElement('a');

                link.href =
                    URL.createObjectURL(blob);

                link.download =
                    `${table.id}-${Date.now()}.csv`;

                document.body.appendChild(link);
                link.click();
                link.remove();

                URL.revokeObjectURL(link.href);
            }
        );

        updateCount();
    };

    const initTables = () => {
        const tables = getTargetTables();

        tables.forEach(buildToolbar);

        document.addEventListener(
            'keydown',
            (event) => {
                if (
                    !(event.ctrlKey || event.metaKey)
                    || event.key.toLowerCase() !== 'f'
                ) {
                    return;
                }

                const activeElement =
                    document.activeElement;

                const isEditable =
                    activeElement instanceof HTMLInputElement
                    || activeElement instanceof HTMLTextAreaElement
                    || activeElement?.isContentEditable;

                if (isEditable) {
                    return;
                }

                const visibleSearch =
                    Array.from(
                        document.querySelectorAll(
                            '.smart-table-search'
                        )
                    ).find((input) => {
                        const rect =
                            input.getBoundingClientRect();

                        return (
                            rect.width > 0
                            && rect.height > 0
                        );
                    });

                if (visibleSearch) {
                    event.preventDefault();
                    visibleSearch.focus();
                    visibleSearch.select();
                }
            }
        );
    };

    document.addEventListener(
        'DOMContentLoaded',
        initTables
    );
})();