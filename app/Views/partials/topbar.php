<?php

declare(strict_types=1);

$dayNames = [
    1 => 'Senin',
    2 => 'Selasa',
    3 => 'Rabu',
    4 => 'Kamis',
    5 => 'Jumat',
    6 => 'Sabtu',
    7 => 'Minggu',
];

$monthNames = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

$todayLabel = (
    $dayNames[(int) date('N')] ?? ''
)
    . ', '
    . date('d')
    . ' '
    . (
        $monthNames[(int) date('n')]
        ?? ''
    )
    . ' '
    . date('Y');

$currentUser = auth_user() ?? [];

$currentName = trim(
    (string) (
        $currentUser['name']
        ?? 'Pengguna'
    )
);

$currentRoleName = trim(
    (string) (
        $currentUser['role_name']
        ?? $currentUser['role_code']
        ?? 'Pengguna Sistem'
    )
);

$initial = strtoupper(
    substr(
        $currentName,
        0,
        1
    )
);
?>

<header
    class="topbar"
    id="appTopbar"
>

    <div class="d-flex align-items-center gap-3">

        <button
            class="icon-btn mobile-menu-button"
            id="sidebarToggle"
            type="button"
            aria-label="Buka navigasi"
        >
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-title">

            <h1>
                <?= e(
                    $title
                    ?? 'Dashboard'
                ) ?>
            </h1>

            <p>
                <?= e($todayLabel) ?>
            </p>

        </div>

    </div>


    <div class="topbar-actions">

        <span class="connection-pill">

            <span></span>

            Sistem aktif

        </span>


        <div
            class="dropdown"
            data-dropdown
        >

            <button
                class="user-menu"
                type="button"
                data-dropdown-toggle
                aria-expanded="false"
            >

                <div class="avatar-sm">
                    <?= e($initial) ?>
                </div>

                <span class="user-menu-name">
                    <?= e($currentName) ?>
                </span>

                <i class="bi bi-chevron-down"></i>

            </button>


            <div
                class="dropdown-menu"
                data-dropdown-menu
            >

                <div
                    style="
                        padding: 10px 11px 12px;
                        border-bottom: 1px solid #edf1f5;
                        margin-bottom: 5px;
                    "
                >

                    <strong
                        class="d-block text-truncate"
                    >
                        <?= e($currentName) ?>
                    </strong>

                    <small class="text-muted">
                        <?= e($currentRoleName) ?>
                    </small>

                </div>


                <a
                    class="dropdown-item"
                    href="<?= url('/profile') ?>"
                >

                    <i class="bi bi-person"></i>

                    Profil pengguna

                </a>


                <div class="dropdown-divider"></div>


                <form
                    method="post"
                    action="<?= url('/logout') ?>"
                >

                    <?= csrf_field() ?>

                    <button
                        class="dropdown-item danger"
                        type="submit"
                        data-no-loading="1"
                    >

                        <i class="bi bi-box-arrow-right"></i>

                        Keluar

                    </button>

                </form>

            </div>

        </div>

    </div>

</header>