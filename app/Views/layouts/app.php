<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Flash;

$pageTitle = isset($title)
    ? trim((string) $title)
    : 'Dashboard';

$content = isset($content)
    ? (string) $content
    : '';

$appName = trim(
    (string) App::config(
        'app.name'
    )
);

if ($appName === '') {
    $appName = 'RemitGo!';
}

$flashes = Flash::all();

?>
<!doctype html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="theme-color"
        content="#f5dff3"
    >

    <meta
        name="color-scheme"
        content="light"
    >

    <title>
        <?= e($pageTitle) ?>
        ·
        <?= e($appName) ?>
    </title>

    <!-- =====================================================
         GOOGLE FONTS: DM SANS (HEADINGS/CTA) & ROBOTO (BODY)
    ====================================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Preline UI native responsive sidebar -->


    <!-- =====================================================
         CSS UTAMA
    ====================================================== -->

    <link
        rel="stylesheet"
        href="<?= e(asset('css/app.css?v=22.0.2')) ?>"
    >





    <!-- =====================================================
         CSS FX
    ====================================================== -->

    <link
        rel="stylesheet"
        href="<?= e(asset('css/fx-idr-board.css?v=23.2.0')) ?>"
    >


    <link rel="stylesheet" href="<?= e(asset('css/ui-final-polish.css?v=1.0.0')) ?>">

    <link rel="stylesheet" href="<?= e(asset('css/transaction-bank-flow.css?v=1.0.0')) ?>">

    <!-- =====================================================
         TAILWIND CSS & PRELINE UI
    ====================================================== -->

    <link
        rel="stylesheet"
        href="<?= e(asset('css/tailwind.css?v=1.0.0')) ?>"
    >

    <!-- =====================================================
         VIBRANT PASTEL COLOR + TYPOGRAPHY OVERRIDE

         TIDAK MENGUBAH:
         layout
         width
         height
         padding
         margin
         gap
         position
         grid
         flex
         font-size
         border-radius
    ====================================================== -->

    <style>

        /* =================================================
           01. ROOT COLOR + FONT SYSTEM
        ================================================= */

        :root {

            --navy-950:
                #51355e;

            --navy-900:
                #60406c;

            --navy-800:
                #73517f;

            --blue-700:
                #7444dc;

            --blue-600:
                #914be0;

            --blue-500:
                #45b8e5;

            --cyan:
                #32bdd5;

            --green:
                #31c98d;

            --amber:
                #ff9d3f;

            --red:
                #eb4f7b;

            --purple:
                #9b4cdd;

            --teal:
                #27b9ac;

            --bg:
                #fbf6fb;

            --surface:
                #ffffff;

            --surface-soft:
                #fff9fc;

            --text:
                #51385c;

            --muted:
                #847488;

            --border:
                #eaddeb;

            --shadow:
                0 14px 38px
                rgba(
                    101,
                    59,
                    116,
                    .08
                );

            --shadow-lg:
                0 24px 58px
                rgba(
                    92,
                    51,
                    110,
                    .15
                );

            /* TYPOGRAPHY */

            --font-app:
                'Roboto',
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                sans-serif;

            --font-heading:
                'DM Sans',
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                sans-serif;

            --font-cta:
                'DM Sans',
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                sans-serif;
        }


        /* =================================================
           02. GLOBAL SCALING & TYPOGRAPHY
        ================================================= */

        html {
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        @media (min-width: 640px) {
            html {
                font-size: 16.5px;
            }
        }

        @media (min-width: 1024px) {
            html {
                font-size: 17.5px;
            }
        }

        @media (min-width: 1440px) {
            html {
                font-size: 18px;
            }
        }

        body.app-body {

            font-family:
                var(--font-app)
                !important;

            font-size:
                0.95rem;

            line-height:
                1.6;

            font-weight:
                450;

            color:
                #111827
                !important;

            -webkit-font-smoothing:
                antialiased;

            -moz-osx-font-smoothing:
                grayscale;

            text-rendering:
                optimizeLegibility;

            background:
                #f8fafc
                !important;
        }

        .app-body button,
        .app-body input,
        .app-body select,
        .app-body textarea,
        .app-body option {

            font-family:
                var(--font-app)
                !important;
        }


        .app-body h1,
        .app-body h2,
        .app-body h3,
        .app-body h4,
        .app-body h5,
        .app-body h6 {

            font-family:
                var(--font-heading)
                !important;

            font-weight:
                600;

            letter-spacing:
                -.015em;
        }


        .app-body button,
        .app-body .btn,
        .app-body .btn-primary,
        .app-body [role="button"] {

            font-family:
                var(--font-cta)
                !important;
        }


        .app-body strong,
        .app-body b {

            font-weight:
                600;
        }


        .app-body small {

            color:
                #867889;

            font-weight:
                500;
        }


        /* =================================================
           03. MAIN APPLICATION BACKGROUND
        ================================================= */

        .app-main {

            background:
                #f8fafc
                !important;
        }

        .content-wrap {

            color:
                #51385c
                !important;
        }


        /* =================================================
           04. SIDEBAR
        ================================================= */

        .sidebar {

            color:
                #111827
                !important;

            background:
                #ffffff
                !important;

            box-shadow:
                1px 0 3px
                rgba(0, 0, 0, 0.05)
                !important;
        }

        /* =================================================
           05. BRAND
        ================================================= */

        .brand {

            border-bottom-color:
                rgba(
                    130,
                    74,
                    140,
                    .14
                )
                !important;
        }


        .brand-mark {

            color:
                #ffffff
                !important;

            background:
                #2563eb
                !important;

            box-shadow:
                0 2px 8px
                rgba(37, 99, 235, 0.25)
                !important;
        }

        .brand strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #50345c
                !important;

            font-weight:
                820
                !important;

            letter-spacing:
                -.025em;
        }


        .brand small {

            color:
                #916c8d
                !important;

            font-weight:
                600
                !important;

            letter-spacing:
                .07em;
        }


        /* =================================================
           06. SIDEBAR NAVIGATION
        ================================================= */

        .nav-label {

            color:
                #a477a2
                !important;

            font-weight:
                700
                !important;

            letter-spacing:
                .13em;
        }


        .nav-item {

            color:
                #5f4c65
                !important;

            font-family:
                var(--font-app)
                !important;

            font-weight:
                650
                !important;

            letter-spacing:
                -.01em;
        }


        .nav-item i {

            color:
                #9160ba
                !important;
        }


        .nav-item:hover {

            color:
                #6b32c9
                !important;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .76
                )
                !important;
        }


        .nav-item.active {

            color:
                #1d4ed8
                !important;

            font-weight:
                600
                !important;

            background:
                #eff6ff
                !important;

            box-shadow:
                none
                !important;
        }

        .nav-item.active i {

            color:
                #7440db
                !important;
        }


        .nav-item.active::after {

            background:
                #ef408e
                !important;

            box-shadow:
                0 0 12px
                rgba(
                    239,
                    64,
                    142,
                    .48
                )
                !important;
        }


        /* =================================================
           07. SIDEBAR FOOTER
        ================================================= */

        .sidebar-footer {

            color:
                #543e5c
                !important;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .64
                )
                !important;

            border-top-color:
                rgba(
                    133,
                    77,
                    142,
                    .14
                )
                !important;
        }


        .sidebar-footer strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #50335b
                !important;

            font-weight:
                800
                !important;

            letter-spacing:
                -.015em;
        }


        .sidebar-footer small {

            color:
                #89758e
                !important;

            font-weight:
                550
                !important;
        }


        /* =================================================
           08. TOPBAR
        ================================================= */

        .topbar {

            color:
                #51385c
                !important;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .96
                )
                !important;

            border-bottom-color:
                #eddfeb
                !important;

            box-shadow:
                0 8px 28px
                rgba(
                    98,
                    63,
                    110,
                    .045
                )
                !important;
        }


        .topbar-title h1 {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4d3258
                !important;

            font-weight:
                820
                !important;

            letter-spacing:
                -.035em;
        }


        .topbar-title p {

            color:
                #867789
                !important;

            font-weight:
                520
                !important;
        }


        /* =================================================
           09. CONNECTION STATUS
        ================================================= */

        .connection-pill {

            color:
                #1f7d5f
                !important;

            background:
                #e7faf2
                !important;

            border-color:
                #c5ebdc
                !important;

            font-weight:
                720
                !important;
        }


        .connection-pill > span {

            background:
                #2dcc8d
                !important;

            box-shadow:
                0 0 0 4px
                rgba(
                    45,
                    204,
                    141,
                    .15
                )
                !important;
        }


        /* =================================================
           10. USER AVATAR
        ================================================= */

        .avatar-sm {

            color:
                #ffffff
                !important;

            font-weight:
                600
                !important;

            background:
                #2563eb
                !important;
        }

        .user-menu {

            color:
                #503d58
                !important;

            background:
                #ffffff
                !important;

            font-weight:
                700
                !important;

            box-shadow:
                0 5px 18px
                rgba(
                    97,
                    58,
                    107,
                    .08
                )
                !important;
        }


        /* =================================================
           11. DROPDOWN
        ================================================= */

        .dropdown-menu {

            color:
                #58455f
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #ebddeb
                !important;

            box-shadow:
                0 24px 58px
                rgba(
                    81,
                    48,
                    91,
                    .16
                )
                !important;
        }


        .dropdown-item {

            color:
                #5c4862
                !important;

            font-weight:
                550
                !important;
        }


        .dropdown-item:hover {

            color:
                #6530c0
                !important;

            background:
                #faeff9
                !important;
        }


        .dropdown-item.danger {

            color:
                #d1436a
                !important;
        }


        .dropdown-divider {

            background:
                #f0e5ef
                !important;
        }


        .topbar
        .dropdown-menu
        div[style*="border-bottom"] {

            border-bottom-color:
                #f0e5ef
                !important;
        }


        /* =================================================
           12. GENERAL TITLES
        ================================================= */

        :is(
            .page-heading h2,
            .panel-head h3,
            .panel-card > h3,
            .dashboard-section-head h3,
            .dashboard-hero-modern h2,
            .fx-modern-header h2,
            .fx-detail-header h3,
            .fx-panel-head h3,
            .detail-hero h1,
            .detail-hero h2,
            .detail-hero h3
        ) {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4b3056
                !important;

            font-weight:
                820
                !important;

            letter-spacing:
                -.025em;
        }


        :is(
            .page-heading p,
            .panel-head p,
            .dashboard-section-head p,
            .dashboard-hero-modern p,
            .fx-modern-header p,
            .fx-detail-header p,
            .fx-panel-head p
        ) {

            color:
                #847488
                !important;

            font-weight:
                520
                !important;
        }


        :is(
            .eyebrow.dark,
            .dashboard-section-label,
            .dashboard-kicker,
            .fx-modern-kicker,
            .fx-detail-kicker
        ) {

            color:
                #7940dc
                !important;

            font-weight:
                800
                !important;

            letter-spacing:
                .13em;
        }


        .text-muted {

            color:
                #847488
                !important;

            font-weight:
                500
                !important;
        }


        .text-primary,
        .dashboard-table-link,
        .table-link {

            color:
                #7440da
                !important;

            font-weight:
                700
                !important;
        }


        .text-danger {

            color:
                #d64069
                !important;
        }


        .text-warning {

            color:
                #d98420
                !important;
        }


        /* =================================================
           13. STANDARD PANELS
        ================================================= */

        :is(
            .panel-card,
            .form-card,
            .card,
            .dashboard-panel,
            .report-batch-card,
            .report-month-card,
            .report-date-card,
            .report-team-card,
            .search-batch-card,
            .report-table-card,
            .filter-panel,
            .detail-hero,
            .rank-card,
            .period-card,
            .fx-idr-panel,
            .fx-rate-card
        ) {

            color:
                #58465f
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #eaddeb
                !important;

            box-shadow:
                0 10px 30px
                rgba(
                    96,
                    57,
                    109,
                    .06
                )
                !important;
        }


        /* =================================================
           14. EMPTY
        ================================================= */

        .empty-card {

            color:
                #58465f
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #dbc5e2
                !important;
        }


        .empty-card > i {

            color:
                #8a4ddb
                !important;
        }


        .empty-card p,
        .empty-state {

            color:
                #827286
                !important;

            font-weight:
                520
                !important;
        }


        /* =================================================
           15. BUTTON PRIMARY
        ================================================= */

        .btn,
        .btn-primary,
        .btn-secondary,
        .btn-light,
        .btn-danger,
        .btn-outline-primary,
        .btn-outline-danger,
        .dashboard-action-primary,
        .dashboard-action-secondary,
        .dashboard-small-link {

            font-family:
                var(--font-app)
                !important;

            font-weight:
                750
                !important;

            letter-spacing:
                -.01em;
        }


        .btn-primary,
        .dashboard-action-primary {

            color:
                #ffffff
                !important;

            background:
                #2563eb
                !important;

            border:
                1px solid #1d4ed8
                !important;

            text-shadow:
                none
                !important;

            box-shadow:
                0 2px 8px
                rgba(37, 99, 235, 0.25)
                !important;
        }

        /* =================================================
           16. BUTTON SECONDARY
        ================================================= */

        .btn-secondary {

            color:
                #ffffff
                !important;

            background:
                #4b5563
                !important;

            border:
                1px solid #374151
                !important;

            text-shadow:
                none
                !important;
        }

        .btn-light,
        .dashboard-action-secondary {

            color:
                #5e356a
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #dfcce2
                !important;
        }


        .btn-danger {

            color:
                #ffffff
                !important;

            background:
                #eb4f7b
                !important;

            text-shadow:
                0 1px 2px
                rgba(
                    92,
                    25,
                    49,
                    .18
                );
        }


        .btn-outline-primary {

            color:
                #7139d0
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #c9a9df
                !important;
        }


        .btn-outline-danger {

            color:
                #d3446b
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #efb9c8
                !important;
        }


        .icon-btn,
        .dashboard-small-link {

            color:
                #713bc8
                !important;

            background:
                #fbf5fc
                !important;

            border-color:
                #e5d6e6
                !important;
        }


        /* =================================================
           17. FORM COLORS + FONT
        ================================================= */

        :is(
            .form-control,
            .form-select,
            textarea,
            .report-global-search input,
            .report-inline-filter input,
            .report-inline-filter select,
            .fx-modern-search input
        ) {

            font-family:
                var(--font-app)
                !important;

            color:
                #59465f
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #e6d7e6
                !important;

            font-weight:
                500
                !important;
        }


        :is(
            .form-control,
            textarea,
            .fx-modern-search input
        )::placeholder {

            color:
                #a08fa1
                !important;

            font-weight:
                450
                !important;
        }


        :is(
            .form-control,
            .form-select,
            textarea,
            .fx-modern-search input
        ):focus {

            color:
                #513d58
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #b677db
                !important;

            box-shadow:
                0 0 0 3px
                rgba(
                    121,
                    68,
                    218,
                    .09
                )
                !important;
        }


        .form-label,
        label > span:first-child {

            color:
                #56415c
                !important;

            font-weight:
                650
                !important;
        }


        .form-check-input {

            accent-color:
                #7440da
                !important;
        }


        /* =================================================
           18. ALERT
        ================================================= */

        .alert-success {

            color:
                #237e60
                !important;

            background:
                #e5f9f0
                !important;

            border-color:
                #c5ebdc
                !important;
        }


        .alert-danger,
        .alert-error {

            color:
                #c83d63
                !important;

            background:
                #ffeaf0
                !important;

            border-color:
                #f2cbd6
                !important;
        }


        .alert-warning {

            color:
                #a75f10
                !important;

            background:
                #fff1dc
                !important;

            border-color:
                #f2d5ae
                !important;
        }


        .alert-info {

            color:
                #6934bd
                !important;

            background:
                #f0e7ff
                !important;

            border-color:
                #dac6ee
                !important;
        }


        /* =================================================
           19. TABLE
        ================================================= */

        .table {

            font-family:
                var(--font-app)
                !important;

            color:
                #5f4e65
                !important;

            background:
                transparent
                !important;
        }


        .table th,
        .table td {

            border-color:
                #eee3ee
                !important;
        }


        .table thead th {

            color:
                #374151
                !important;

            font-weight:
                600
                !important;

            background:
                #f9fafb
                !important;

            border-bottom:
                1px solid #e5e7eb
                !important;

            text-shadow:
                none
                !important;
        }

        .table tbody tr {

            background:
                #ffffff
                !important;
        }


        .table tbody tr:hover {

            background:
                #fff2f8
                !important;
        }


        .table tbody td {

            color:
                #58495f
                !important;

            font-weight:
                500
                !important;
        }


        /* =================================================
           20. SMART TABLE
        ================================================= */

        .bulk-toolbar,
        .smart-table-toolbar {

            color:
                #58475f
                !important;

            background:
                #fff8fc
                !important;

            border-color:
                #e7d9e8
                !important;
        }


        .smart-table-search {

            color:
                #55425c
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #dfcfe3
                !important;
        }


        .smart-table-count {

            color:
                #ffffff
                !important;

            font-weight:
                600
                !important;

            background:
                #2563eb
                !important;

            border:
                1px solid #1d4ed8
                !important;

            text-shadow:
                none
                !important;
        }

        /* =================================================
           21. BADGES
        ================================================= */

        .status-pill,
        .badge {

            font-family:
                var(--font-app)
                !important;

            font-weight:
                700
                !important;

            border-color:
                #e5d6e6
                !important;
        }


        .text-bg-success,
        .status-pill.success,
        .status-pill.green {

            color:
                #126f52
                !important;

            background:
                #dcf8eb
                !important;
        }


        .text-bg-warning,
        .status-pill.warning,
        .status-pill.amber {

            color:
                #a55e10
                !important;

            background:
                #ffedd2
                !important;
        }


        .status-pill.info,
        .status-pill.blue {

            color:
                #6730bd
                !important;

            background:
                #ecddff
                !important;
        }


        .status-pill.red {

            color:
                #c5365e
                !important;

            background:
                #ffe3ed
                !important;
        }


        /* =================================================
           22. DASHBOARD HERO
        ================================================= */

        .dashboard-hero-modern {

            grid-template-columns:
                1fr
                !important;

            color:
                #f5f2f2
                !important;

            background:
                #2b2a2a
                !important;

            border:
                1px solid #2b2a2a
                !important;

            box-shadow:
                0 4px 16px -2px
                rgba(0, 0, 0, 0.08)
                !important;
        }


        .dashboard-hero-modern::before {

            display:
                none
                !important;
        }


        .dashboard-hero-modern h2 {

            font-family:
                var(--font-heading)
                !important;

            color:
                #ffffff
                !important;

            font-weight:
                600
                !important;

            letter-spacing:
                -.025em;
        }


        .dashboard-hero-modern p {

            color:
                #d1d5db
                !important;

            font-weight:
                400
                !important;
        }


        .dashboard-kicker {

            color:
                #feb05d
                !important;

            font-weight:
                600
                !important;

            letter-spacing:
                .13em;
        }
        /* =================================================
           23. DASHBOARD ACTIVITY CARD
        ================================================= */

        .dashboard-activity-card {

            color:
                #f5f2f2
                !important;

            background:
                #1f1e1e
                !important;

            border:
                1px solid #383535
                !important;

            backdrop-filter:
                none
                !important;

            -webkit-backdrop-filter:
                none
                !important;

            box-shadow:
                none
                !important;
        }


        .dashboard-activity-head span {

            color:
                #9ca3af
                !important;

            font-weight:
                500
                !important;
        }


        .dashboard-activity-head strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #ffffff
                !important;

            font-weight:
                600
                !important;

            font-variant-numeric:
                tabular-nums;
        }


        .dashboard-activity-head small {

            color:
                #9ca3af
                !important;

            font-weight:
                500
                !important;
        }


        .dashboard-activity-head b {

            color:
                #ffffff
                !important;

            background:
                #059669
                !important;

            border:
                1px solid #047857
                !important;

            font-weight:
                600
                !important;
        }


        .dashboard-activity-meta {

            color:
                #9ca3af
                !important;

            font-weight:
                500
                !important;
        }
        /* =================================================
           24. DASHBOARD PANEL
        ================================================= */

        .dashboard-panel {

            color:
                #111827
                !important;

            background:
                #ffffff
                !important;

            border:
                1px solid #e5e7eb
                !important;

            box-shadow:
                0 1px 3px
                rgba(0, 0, 0, 0.05)
                !important;
        }


        .dashboard-section-label {

            color:
                #2563eb
                !important;

            font-weight:
                600
                !important;

            letter-spacing:
                .13em;
        }


        .dashboard-section-head h3 {

            font-family:
                var(--font-heading)
                !important;

            color:
                #111827
                !important;

            font-weight:
                600
                !important;
        }


        .dashboard-section-head p {

            color:
                #6b7280
                !important;

            font-weight:
                400
                !important;
        }
        /* =================================================
           25. FX SUMMARY
        ================================================= */

        .dashboard-fx-summary {

            color:
                #374151
                !important;

            background:
                #f8fafc
                !important;

            border:
                1px solid #e2e8f0
                !important;

            box-shadow:
                none
                !important;
        }


        .dashboard-fx-summary > i {

            color:
                #2563eb
                !important;

            background:
                #eff6ff
                !important;

            border:
                1px solid #dbeafe
                !important;
        }


        .dashboard-fx-summary strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #111827
                !important;

            font-weight:
                600
                !important;

            text-shadow:
                none
                !important;
        }


        .dashboard-fx-summary small {

            color:
                #6b7280
                !important;

            font-weight:
                500
                !important;

            text-shadow:
                none
                !important;
        }
        /* =================================================
           26. QUICK ACCESS CARDS
        ================================================= */

        .dashboard-quick-item {

            color:
                #111827
                !important;

            background:
                #ffffff
                !important;

            border:
                1px solid #e5e7eb
                !important;

            box-shadow:
                0 1px 2px
                rgba(0, 0, 0, 0.04)
                !important;
        }


        .dashboard-quick-item:hover {

            color:
                #111827
                !important;

            background:
                #f9fafb
                !important;

            border-color:
                #d1d5db
                !important;

            box-shadow:
                0 4px 6px -1px
                rgba(0, 0, 0, 0.06)
                !important;
        }


        .dashboard-quick-copy strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #111827
                !important;

            font-weight:
                600
                !important;
        }


        .dashboard-quick-copy small {

            color:
                #6b7280
                !important;

            font-weight:
                400
                !important;
        }


        .dashboard-quick-arrow {

            color:
                #9ca3af
                !important;
        }


        .dashboard-quick-item.blue
        .dashboard-quick-icon {

            color:
                #2563eb
                !important;

            background:
                #eff6ff
                !important;

            border:
                1px solid #dbeafe
                !important;
        }


        .dashboard-quick-item.green
        .dashboard-quick-icon {

            color:
                #059669
                !important;

            background:
                #ecfdf5
                !important;

            border:
                1px solid #a7f3d0
                !important;
        }


        .dashboard-quick-item.purple
        .dashboard-quick-icon {

            color:
                #7c3aed
                !important;

            background:
                #f5f3ff
                !important;

            border:
                1px solid #ddd6fe
                !important;
        }


        .dashboard-quick-item.orange
        .dashboard-quick-icon {

            color:
                #d97706
                !important;

            background:
                #fffbeb
                !important;

            border:
                1px solid #fde68a
                !important;
        }


        .dashboard-quick-item.cyan
        .dashboard-quick-icon {

            color:
                #0d9488
                !important;

            background:
                #f0fdfa
                !important;

            border:
                1px solid #99f6e4
                !important;
        }

        /* =================================================
           27. KPI CARDS FULL COLOR
        ================================================= */

        /* =================================================
           27. KPI CARDS FULL COLOR
        ================================================= */

        .dashboard-kpi-card,
        .dashboard-kpi-grid
        .dashboard-kpi-card:nth-child(1),
        .dashboard-kpi-card:nth-child(1),
        .dashboard-kpi-grid
        .dashboard-kpi-card:nth-child(2),
        .dashboard-kpi-card:nth-child(2),
        .dashboard-kpi-grid
        .dashboard-kpi-card:nth-child(3),
        .dashboard-kpi-card:nth-child(3),
        .dashboard-kpi-grid
        .dashboard-kpi-card:nth-child(4),
        .dashboard-kpi-card:nth-child(4),
        .dashboard-kpi-grid
        .dashboard-kpi-card:nth-child(5),
        .dashboard-kpi-card:nth-child(5),
        .dashboard-kpi-grid
        .dashboard-kpi-card:nth-child(6),
        .dashboard-kpi-card:nth-child(6) {

            font-family:
                var(--font-app)
                !important;

            color:
                #111827
                !important;

            background:
                #ffffff
                !important;

            border:
                1px solid #e5e7eb
                !important;

            box-shadow:
                0 1px 3px
                rgba(0, 0, 0, 0.05)
                !important;
        }


        /* KPI LABEL */

        .dashboard-kpi-card
        div > span {

            color:
                #4b5563
                !important;

            font-weight:
                600
                !important;

            text-shadow:
                none
                !important;
        }


        /* KPI NUMBER */

        .dashboard-kpi-card
        div > strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #111827
                !important;

            font-weight:
                600
                !important;

            letter-spacing:
                -.025em;

            font-variant-numeric:
                tabular-nums;

            text-shadow:
                none
                !important;
        }


        /* KPI DESCRIPTION */

        .dashboard-kpi-card
        div > small {

            color:
                #6b7280
                !important;

            font-weight:
                500
                !important;

            text-shadow:
                none
                !important;
        }


        /* KPI ICON SOLID ACCENTS */

        .dashboard-kpi-card
        .dashboard-kpi-icon {

            text-shadow:
                none
                !important;
        }

        .dashboard-kpi-icon.blue {

            color:
                #2563eb
                !important;

            background:
                #eff6ff
                !important;

            border:
                1px solid #dbeafe
                !important;
        }

        .dashboard-kpi-icon.green {

            color:
                #059669
                !important;

            background:
                #ecfdf5
                !important;

            border:
                1px solid #a7f3d0
                !important;
        }

        .dashboard-kpi-icon.red {

            color:
                #dc2626
                !important;

            background:
                #fef2f2
                !important;

            border:
                1px solid #fecaca
                !important;
        }

        .dashboard-kpi-icon.orange {

            color:
                #d97706
                !important;

            background:
                #fffbeb
                !important;

            border:
                1px solid #fde68a
                !important;
        }

        .dashboard-kpi-icon.purple {

            color:
                #7c3aed
                !important;

            background:
                #f5f3ff
                !important;

            border:
                1px solid #ddd6fe
                !important;
        }

        .dashboard-kpi-icon.teal {

            color:
                #0d9488
                !important;

            background:
                #f0fdfa
                !important;

            border:
                1px solid #99f6e4
                !important;
        }
        /* =================================================
           28. DASHBOARD STATUS
        ================================================= */

        .dashboard-status-item {

            background:
                #f9fafb
                !important;

            border:
                1px solid #e5e7eb
                !important;
        }


        .dashboard-status-item span {

            color:
                #4b5563
                !important;

            font-weight:
                500
                !important;
        }


        .dashboard-status-item strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #111827
                !important;

            font-weight:
                600
                !important;
        }
        .dashboard-status-item:nth-child(6n + 1)
        span i {

            background:
                #7740dd
                !important;
        }


        .dashboard-status-item:nth-child(6n + 2)
        span i {

            background:
                #2fca8b
                !important;
        }


        .dashboard-status-item:nth-child(6n + 3)
        span i {

            background:
                #ff9b3e
                !important;
        }


        .dashboard-status-item:nth-child(6n + 4)
        span i {

            background:
                #eb4c79
                !important;
        }


        .dashboard-status-item:nth-child(6n + 5)
        span i {

            background:
                #a247dd
                !important;
        }


        .dashboard-status-item:nth-child(6n + 6)
        span i {

            background:
                #31bad5
                !important;
        }


        .dashboard-empty-cell {

            color:
                #806f84
                !important;

            font-weight:
                500
                !important;
        }


        /* =================================================
           29. SUMMARY CARDS FULL COLORS
        ================================================= */

        .summary-card {
        .summary-card {

            border:
                1px solid #e5e7eb
                !important;

            box-shadow:
                0 1px 3px
                rgba(0, 0, 0, 0.05)
                !important;
        }


        .summary-card.blue {

            color:
                #1e40af
                !important;

            background:
                #eff6ff
                !important;

            border:
                1px solid #bfdbfe
                !important;
        }


        .summary-card.green {

            color:
                #065f46
                !important;

            background:
                #ecfdf5
                !important;

            border:
                1px solid #a7f3d0
                !important;
        }


        .summary-card.amber {

            color:
                #92400e
                !important;

            background:
                #fffbeb
                !important;

            border:
                1px solid #fde68a
                !important;
        }


        .summary-card.red {

            color:
                #991b1b
                !important;

            background:
                #fef2f2
                !important;

            border:
                1px solid #fecaca
                !important;
        }


        .summary-card.blue *,
        .summary-card.green *,
        .summary-card.amber *,
        .summary-card.red * {

            color:
                inherit
                !important;
        }
        .summary-card.blue strong,
        .summary-card.green strong,
        .summary-card.amber strong,
        .summary-card.red strong {

            font-family:
                var(--font-heading)
                !important;

            font-weight:
                850
                !important;

            text-shadow:
                0 1px 3px
                rgba(
                    44,
                    18,
                    52,
                    .22
                );

            font-variant-numeric:
                tabular-nums;
        }


        .summary-card.blue span,
        .summary-card.green span,
        .summary-card.amber span,
        .summary-card.red span {

            color:
                rgba(
                    255,
                    255,
                    255,
                    .94
                )
                !important;

            font-weight:
                620
                !important;
        }


        .summary-card.blue small,
        .summary-card.green small,
        .summary-card.amber small,
        .summary-card.red small {

            color:
                rgba(
                    255,
                    255,
                    255,
                    .88
                )
                !important;

            font-weight:
                520
                !important;
        }


        /* =================================================
           30. REPORT
        ================================================= */

        .report-month-chip {

            color:
                #ffffff
                !important;

            font-weight:
                600
                !important;

            background:
                #2563eb
                !important;

            border:
                1px solid #1d4ed8
                !important;

            text-shadow:
                none
                !important;
        }

        :is(
            .report-batch-card,
            .report-month-card,
            .report-date-card,
            .report-team-card
        ) > p {

            color:
                #807083
                !important;

            font-weight:
                500
                !important;
        }


        .report-batch-card h3,
        .report-month-card h3,
        .report-date-card h3,
        .report-team-card h3 {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4c3556
                !important;

            font-weight:
                820
                !important;
        }


        .report-progress {

            background:
                #eee0ef
                !important;
        }


        .report-progress span,
        .report-progress span,
        .report-progress > span {

            background-image:
                none
                !important;

            background:
                #2563eb
                !important;
        }

        .report-stats div,
        .report-card-stats > div {

            background:
                #fcf8fc
                !important;
        }


        .report-stats strong,
        .report-card-stats strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4d3258
                !important;

            font-weight:
                820
                !important;

            font-variant-numeric:
                tabular-nums;
        }


        .report-stats small,
        .report-card-stats small {

            color:
                #806f84
                !important;

            font-weight:
                520
                !important;
        }


        .report-global-search,
        .report-global-search form,
        .report-filter-panel,
        .report-inline-filter {

            color:
                #59475f
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #eaddeb
                !important;
        }


        .report-filter-panel > summary {

            color:
                #55405d
                !important;

            background:
                #fff8fc
                !important;

            border-color:
                #eaddeb
                !important;

            font-weight:
                700
                !important;
        }


        .report-filter-panel[open] > summary {

            background:
                #f7e8fa
                !important;
        }


        .report-filter-panel > summary > small {

            color:
                #847287
                !important;

            font-weight:
                500
                !important;
        }


        .report-hierarchy-note {

            color:
                #6b5273
                !important;

            background:
                #f6eafb
                !important;

            border-color:
                #dfcae7
                !important;

            font-weight:
                520
                !important;
        }


        .report-hierarchy-note > span,
        .report-hierarchy-note > a {

            color:
                #743ec8
                !important;

            font-weight:
                650
                !important;
        }


        .report-hierarchy-note > a:hover,
        .report-hierarchy-note > span.active,
        .report-hierarchy-note > a.active {

            color:
                #d73c92
                !important;
        }


        .report-card-icon {

            color:
                #703dcf
                !important;

            background:
                #ecddff
                !important;
        }


        .report-card-code,
        .team-chip,
        .batch-chip {

            color:
                #bd2d76
                !important;

            background:
                #ffe2f0
                !important;

            font-weight:
                700
                !important;
        }


        .report-card-footer {

            border-color:
                #eee2ee
                !important;
        }


        .report-result-strip {

            color:
                #67526d
                !important;

            background:
                #faf2fa
                !important;

            border-color:
                #e5d3e8
                !important;

            font-weight:
                520
                !important;
        }


        .report-result-strip .success {

            color:
                #197c5b
                !important;

            background:
                #dcf7ea
                !important;

            font-weight:
                700
                !important;
        }


        .report-result-strip .danger {

            color:
                #ca3d65
                !important;

            background:
                #ffe3ed
                !important;

            font-weight:
                700
                !important;
        }


        /* =================================================
           31. ANALYSIS / TOPSIS
        ================================================= */

        .rank-no {

            font-family:
                var(--font-heading)
                !important;

            color:
                #7440da
                !important;

            font-weight:
                850
                !important;
        }


        .period-card {

            background:
                #fcf8fc
                !important;

            font-weight:
                600
                !important;
        }


        .period-card.active {

            color:
                #6430bf
                !important;

            background:
                #ecddff
                !important;

            border-color:
                #c493dd
                !important;

            font-weight:
                750
                !important;
        }


        .company-selector label span {

            color:
                #57435e
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #e6d6e7
                !important;

            font-weight:
                600
                !important;
        }


        .company-selector input:checked + span {

            color:
                #6430bf
                !important;

            background:
                #ecddff
                !important;

            border-color:
                #c38fdd
                !important;

            font-weight:
                750
                !important;
        }


        .method-steps span {

            color:
                #79687e
                !important;

            background:
                #f1e7f1
                !important;

            font-weight:
                600
                !important;
        }


        .method-steps .active {

            color:
                #ffffff
                !important;

            font-weight:
                600
                !important;

            background:
                #2563eb
                !important;

            border:
                1px solid #1d4ed8
                !important;

            text-shadow:
                none
                !important;
        }

        .method-steps .done {

            color:
                #167b5b
                !important;

            background:
                #dcf7ea
                !important;

            font-weight:
                750
                !important;
        }


        /* =================================================
           32. FX BOARD
        ================================================= */

        .fx-modern-board {

            --fx-bg:
                #ffffff
                !important;

            --fx-surface:
                #f9fafb
                !important;

            --fx-border:
                #e5e7eb
                !important;

            color:
                #111827
                !important;

            background:
                #ffffff
                !important;

            border:
                1px solid #e5e7eb
                !important;

            box-shadow:
                0 1px 3px
                rgba(0, 0, 0, 0.05)
                !important;
        }

        .fx-modern-pill {

            color:
                #59465f
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #e5d6e7
                !important;

            font-weight:
                650
                !important;
        }


        .fx-modern-live-dot {

            background:
                #2fc98c
                !important;

            box-shadow:
                0 0 0 4px
                rgba(
                    47,
                    201,
                    140,
                    .15
                )
                !important;
        }


        .fx-modern-refresh {

            color:
                #7038cf
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #dfcae2
                !important;

            font-weight:
                700
                !important;
        }


        .fx-modern-sync {
        .fx-modern-sync {

            color:
                #ffffff
                !important;

            font-weight:
                600
                !important;

            background:
                #2563eb
                !important;

            border:
                1px solid #1d4ed8
                !important;

            text-shadow:
                none
                !important;
        }

        .fx-modern-search > i {

            color:
                #97839a
                !important;
        }


        .fx-modern-meta,
        .fx-modern-buy-sell span,
        .fx-modern-card-hint,
        .fx-modern-footer {

            color:
                #817185
                !important;

            font-weight:
                500
                !important;
        }


        .fx-modern-meta strong,
        .fx-modern-buy-sell strong,
        .fx-modern-value strong {

            color:
                #4d3258
                !important;
        }


        /* =================================================
           33. FX RATE CARDS
        ================================================= */

        .fx-modern-card {

            color:
                #58465f
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #e9dcea
                !important;

            box-shadow:
                0 8px 22px
                rgba(
                    95,
                    56,
                    108,
                    .05
                )
                !important;
        }


        .fx-modern-card:hover,
        .fx-modern-card:focus-visible,
        .fx-modern-card.is-selected {

            border-color:
                #c888d8
                !important;

            background:
                #fff5fb
                !important;
        }


        .fx-modern-flag {

            background:
                #f5eef6
                !important;

            border-color:
                #e9ddea
                !important;
        }


        .fx-modern-currency strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4e3658
                !important;

            font-weight:
                800
                !important;
        }


        .fx-modern-currency small {

            color:
                #806f84
                !important;

            font-weight:
                500
                !important;
        }


        .fx-modern-value span,
        .fx-modern-range-head,
        .fx-modern-range-values {

            color:
                #806f84
                !important;

            font-weight:
                520
                !important;
        }


        .fx-modern-value strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4d3258
                !important;

            font-weight:
                850
                !important;

            letter-spacing:
                -.02em;

            font-variant-numeric:
                tabular-nums;
        }


        .fx-modern-change {

            font-weight:
                750
                !important;
        }


        .fx-modern-change.up {

            color:
                #167d5b
                !important;

            background:
                #dcf8eb
                !important;
        }


        .fx-modern-change.down {

            color:
                #c9365e
                !important;

            background:
                #ffe2ec
                !important;
        }


        .fx-modern-change.flat {

            color:
                #705f74
                !important;

            background:
                #f1e6f0
                !important;
        }


        .fx-modern-range-track {

            background:
                #eedfef
                !important;
        }


        .fx-modern-range-track i {

            background:
                #2563eb
                !important;
        }

        .fx-modern-range-track b {

            background:
                #ffffff
                !important;

            border-color:
                #7140dc
                !important;
        }


        .fx-modern-buy-sell > div {

            background:
                #fcf8fc
                !important;

            border-color:
                #eaddeb
                !important;
        }


        .fx-modern-buy-sell strong {

            font-family:
                var(--font-heading)
                !important;

            font-weight:
                760
                !important;
        }


        .fx-modern-empty {

            color:
                #817185
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #d8c6df
                !important;
        }


        .fx-modern-empty strong {

            color:
                #4d3258
                !important;

            font-weight:
                780
                !important;
        }


        .fx-modern-footer {

            border-top-color:
                #ece0ec
                !important;
        }


        /* =================================================
           34. FX DETAIL
        ================================================= */

        .fx-detail-backdrop {

            background:
                rgba(
                    73,
                    42,
                    79,
                    .39
                )
                !important;
        }


        .fx-detail-dialog {

            color:
                #111827
                !important;

            background:
                #ffffff
                !important;

            border:
                1px solid #e5e7eb
                !important;

            box-shadow:
                0 20px 25px -5px
                rgba(0, 0, 0, 0.1),
                0 10px 10px -5px
                rgba(0, 0, 0, 0.04)
                !important;
        }

        .fx-detail-close {

            color:
                #6d4c75
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #e3d2e5
                !important;
        }


        .fx-detail-header h3 {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4d3258
                !important;

            font-weight:
                850
                !important;
        }


        .fx-detail-header p {

            color:
                #817185
                !important;

            font-weight:
                500
                !important;
        }


        .fx-detail-rate {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4d3258
                !important;

            font-weight:
                850
                !important;

            font-variant-numeric:
                tabular-nums;
        }


        .fx-detail-stats strong {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4d3258
                !important;

            font-weight:
                800
                !important;
        }


        .fx-detail-date,
        .fx-detail-stats span,
        .fx-detail-source {

            color:
                #817185
                !important;

            font-weight:
                500
                !important;
        }


        .fx-detail-chart-wrap,
        .fx-detail-stats > div {

            background:
                #fcf8fc
                !important;

            border-color:
                #e9dcea
                !important;
        }


        /* =================================================
           35. FX TOAST
        ================================================= */

        .fx-toast {

            font-family:
                var(--font-app)
                !important;

            color:
                #57445e
                !important;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .98
                )
                !important;

            border-color:
                #eaddeb
                !important;

            box-shadow:
                0 18px 46px
                rgba(
                    79,
                    46,
                    90,
                    .15
                )
                !important;
        }


        .fx-toast strong {

            font-weight:
                750
                !important;
        }


        .fx-toast small {

            color:
                #7e6e82
                !important;

            font-weight:
                500
                !important;
        }


        .fx-toast__icon {

            color:
                #7440da
                !important;

            background:
                #ecddff
                !important;
        }


        .fx-toast--warning
        .fx-toast__icon {

            color:
                #d97910
                !important;

            background:
                #ffe7c5
                !important;
        }


        .fx-toast--danger
        .fx-toast__icon {

            color:
                #d33d67
                !important;

            background:
                #ffe1eb
                !important;
        }


        /* =================================================
           36. OLD FX COMPONENTS
        ================================================= */

        .fx-idr-panel,
        .fx-rate-card {

            color:
                #58465f
                !important;

            background:
                #ffffff
                !important;

            border-color:
                #e8dbea
                !important;
        }


        .fx-panel-head h3 {

            font-family:
                var(--font-heading)
                !important;

            color:
                #4d3258
                !important;

            font-weight:
                820
                !important;
        }


        .fx-panel-head p,
        .fx-panel-footer {

            color:
                #817185
                !important;

            font-weight:
                500
                !important;
        }


        .fx-provider {

            color:
                #ffffff
                !important;

            font-weight:
                600
                !important;

            background:
                #2563eb
                !important;

            border:
                1px solid #1d4ed8
                !important;

            text-shadow:
                none
                !important;
        }

        .fx-rate-card header strong,
        .fx-rate-pair strong {

            color:
                #4d3258
                !important;

            font-weight:
                750
                !important;
        }


        .fx-rate-card header span,
        .fx-rate-pair small {

            color:
                #817185
                !important;

            font-weight:
                500
                !important;
        }


        /* =================================================
           37. TAG
        ================================================= */

        .tag-list span {

            color:
                #77467f
                !important;

            background:
                #f4e7f3
                !important;

            font-weight:
                650
                !important;
        }


        .pagination-wrap {

            color:
                #817185
                !important;

            font-weight:
                500
                !important;
        }


        /* =================================================
           38. GENERIC UTILITIES
        ================================================= */

        .bg-white {

            background:
                #ffffff
                !important;
        }


        .bg-light {

            background:
                #fff7fb
                !important;
        }


        .bg-primary {

            color:
                #ffffff
                !important;

            background:
                #7140dc
                !important;
        }


        .bg-success {

            color:
                #ffffff
                !important;

            background:
                #2fc98c
                !important;
        }


        .bg-danger {

            color:
                #ffffff
                !important;

            background:
                #eb4c79
                !important;
        }


        .bg-warning {

            color:
                #ffffff
                !important;

            background:
                #ff9b3e
                !important;
        }


        .bg-info {

            color:
                #ffffff
                !important;

            background:
                #3cb9e2
                !important;
        }


        .bg-primary *,
        .bg-success *,
        .bg-danger *,
        .bg-warning *,
        .bg-info * {

            color:
                #ffffff
                !important;
        }


        .bg-primary strong,
        .bg-success strong,
        .bg-danger strong,
        .bg-warning strong,
        .bg-info strong {

            font-family:
                var(--font-heading)
                !important;

            font-weight:
                800
                !important;

            text-shadow:
                0 1px 2px
                rgba(
                    40,
                    16,
                    48,
                    .20
                );
        }


        .bg-primary small,
        .bg-success small,
        .bg-danger small,
        .bg-warning small,
        .bg-info small {

            color:
                rgba(
                    255,
                    255,
                    255,
                    .91
                )
                !important;

            font-weight:
                520
                !important;
        }


        /* =================================================
           39. GLOBAL HIGH-CONTRAST FULL COLOR ELEMENTS
        ================================================= */

        .dashboard-kpi-card,
        .dashboard-fx-summary,
        .summary-card.blue,
        .summary-card.green,
        .summary-card.amber,
        .summary-card.red,
        .btn-primary,
        .btn-secondary,
        .dashboard-action-primary,
        .fx-modern-sync,
        .fx-provider,
        .smart-table-count {

            -webkit-font-smoothing:
                antialiased;

            text-rendering:
                optimizeLegibility;
        }


        /* =================================================
           40. NUMERIC READABILITY
        ================================================= */

        .dashboard-kpi-card strong,
        .summary-card strong,
        .fx-modern-value strong,
        .fx-detail-rate,
        .report-stats strong,
        .report-card-stats strong,
        .dashboard-activity-head strong {

            font-variant-numeric:
                tabular-nums;
        }


        /* =================================================
           41. SCROLLBAR COLOR
        ================================================= */

        .app-body {

            scrollbar-color:
                #d3afd9
                transparent;
        }


        .app-body
        ::-webkit-scrollbar-thumb {

            background:
                #cbd5e1
                !important;
        }


        .app-body
        ::-webkit-scrollbar-thumb:hover {

            background:
                #94a3b8
                !important;
        }

        /* =================================================
           42. COLLAPSIBLE SIDEBAR - SMOOTH / MINIMAL

           DESKTOP:
           - Tidak ada tombol besar di sisi sidebar.
           - Double click area kosong sidebar untuk menutup.
           - Saat sidebar tertutup, double click tepi kiri layar
             untuk membukanya kembali.
           - Penanda tepi hanya muncul sangat tipis saat hover.
           - Ctrl + B tetap dapat digunakan sebagai shortcut.

           MOBILE / TABLET:
           - Sidebar menjadi overlay.
           - Tap tepi kiri layar untuk membuka.
           - Tap backdrop untuk menutup.
        ================================================= */

        :root {

            --sidebar-live-width:
                360px;

            --sidebar-motion:
                420ms
                cubic-bezier(
                    .22,
                    1,
                    .36,
                    1
                );
        }


        html,
        body.app-body {

            max-width:
                100%;

            overflow-x:
                hidden;
        }


        /* =========================================================
           HUKUM SOLID COLOR & BEBAS TRANSFORM MUTLAK (ANTISLOP)
           ========================================================= */

        /* 1. Hilangkan seluruh gradien latar belakang ke warna solid */
        body.app-body,
        .app-main,
        .content-wrap {
            background-image: none !important;
            background: #f8fafc !important;
        }

        .sidebar {
            background-image: none !important;
            background: #1e152a !important;
        }

        .brand-mark {
            background-image: none !important;
            background: #1f6bf0 !important;
        }

        .avatar-sm,
        .avatar-initial {
            background-image: none !important;
            background: #2563eb !important;
        }

        .nav-item.active {
            background-image: none !important;
            background: #1f6bf0 !important;
        }

        .btn-primary,
        .btn.btn-primary,
        button.btn-primary,
        a.btn-primary {
            background-image: none !important;
            background: var(--btn-primary-base, #1f6bf0) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08) !important;
            text-shadow: none !important;
        }

        .btn-success,
        .btn.btn-success {
            background-image: none !important;
            background: #16a34a !important;
        }

        .btn-danger,
        .btn.btn-danger {
            background-image: none !important;
            background: #dc2626 !important;
        }

        .btn-warning,
        .btn.btn-warning {
            background-image: none !important;
            background: #d97706 !important;
        }

        .dashboard-hero,
        .fx-idr-panel,
        .login-visual {
            background-image: none !important;
            background: #0f172a !important;
        }

        .report-progress > span,
        .progress-bar {
            background-image: none !important;
            background: #2563eb !important;
        }

        /* 2. Pemusnahan Transform pada Seluruh Interaksi (Bebas Transform Mutlak) */
        *, *::before, *::after {
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
        }

        .btn,
        .btn-primary,
        button,
        a,
        [role="button"],
        .card,
        .nav-item,
        .dropdown-item,
        .badge {
            transform: none !important;
        }

        .btn:hover,
        .btn-primary:hover,
        button:hover,
        a:hover,
        [role="button"]:hover,
        .btn:active,
        .btn-primary:active,
        button:active,
        a:active,
        [role="button"]:active,
        .btn:focus,
        .btn-primary:focus,
        button:focus,
        a:focus,
        [role="button"]:focus,
        .nav-item:hover,
        .card:hover {
            transform: none !important;
        }

        /* Sidebar and Shell layout handled natively by Preline UI */

        /* =================================================
           END COLOR + TYPOGRAPHY OVERRIDE
        ================================================= */

    </style>


    <!-- =====================================================
         CANVAS CHART DISCIPLINE (GEMINI.md Bagian 11)
         Grafik canvas menggunakan warna solid asli (#2563EB,
         #059669, #DC2626) tanpa manipulasi gradien pastel.
    ====================================================== -->
    <!-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link rel="stylesheet" href="<?= e(asset('css/invoice-hub.css?v=1.0.0')) ?>">
</head>


<body class="bg-slate-50 text-gray-800 antialiased font-sans min-h-screen">

    <!-- ========== SPA BOTTOM NAVIGATION & FLOATING DOCK ========== -->
    <?php
    require dirname(__DIR__)
        . '/partials/sidebar.php';
    ?>
    <!-- ========== END NAVIGATION ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <main id="content" class="w-full min-h-screen pb-24 sm:pb-28">
        <div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">

            <?php foreach ($flashes as $flash): ?>
                <?php
                $flashType = trim((string) ($flash['type'] ?? 'info'));
                $flashMessage = trim((string) ($flash['message'] ?? ''));
                $alertClass = match($flashType) {
                    'success' => 'alert-success',
                    'danger', 'error' => 'alert-danger',
                    'warning' => 'alert-warning',
                    default => 'alert-info',
                };
                $alertIcon = match($flashType) {
                    'success' => 'bi-check-circle-fill',
                    'danger', 'error' => 'bi-x-circle-fill',
                    'warning' => 'bi-exclamation-triangle-fill',
                    default => 'bi-info-circle-fill',
                };
                ?>

                <?php if ($flashMessage !== ''): ?>
                    <div
                        class="alert <?= $alertClass ?> mb-4"
                        role="alert"
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
                        <div class="alert-content">
                            <span class="alert-icon">
                                <i class="bi <?= $alertIcon ?>"></i>
                            </span>
                            <div class="alert-body">
                                <div class="alert-message"><?= e($flashMessage) ?></div>
                            </div>
                        </div>

                        <button
                            class="alert-close"
                            type="button"
                            @click="show = false"
                            aria-label="Tutup notifikasi"
                        >
                            <i class="bi bi-x text-lg leading-none"></i>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?= $content ?>

        </div>
    </main>
    <!-- ========== END MAIN CONTENT ========== -->


<!-- =========================================================
     GLOBAL CUSTOM DIALOG & MODAL SYSTEM (ANTI-NATIVE BROWSER)
     ========================================================= -->
<div x-data="remitGlobalDialogs" x-cloak>
    <!-- CONFIRM MODAL -->
    <div
        x-show="confirm.open"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        role="dialog"
        aria-modal="true"
        style="display: none;"
    >
        <div
            class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-gray-200"
            @click.outside="onCancel()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <div class="flex items-start gap-3.5 mb-4">
                <span
                    class="size-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                    :class="confirm.isDanger ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600'"
                >
                    <i :class="confirm.isDanger ? 'bi bi-exclamation-triangle-fill' : 'bi bi-question-circle-fill'"></i>
                </span>
                <div class="min-w-0">
                    <strong class="text-sm font-bold text-gray-900 block" x-text="confirm.title"></strong>
                    <p class="text-xs text-gray-600 mt-1 leading-relaxed" x-text="confirm.message"></p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button
                    type="button"
                    class="py-2 px-3.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
                    @click="onCancel()"
                    x-text="confirm.cancelText"
                ></button>
                <button
                    type="button"
                    class="btn-primary py-2 px-4 text-xs font-semibold rounded-lg text-white"
                    :class="confirm.isDanger ? 'bg-red-600 border-red-700 hover:bg-red-700' : ''"
                    @click="onConfirm()"
                    x-text="confirm.confirmText"
                ></button>
            </div>
        </div>
    </div>

    <!-- ALERT MODAL -->
    <div
        x-show="alert.open"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        role="alertdialog"
        aria-modal="true"
        style="display: none;"
    >
        <div
            class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-gray-200"
            @click.outside="closeAlert()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <div class="flex items-start gap-3.5 mb-4">
                <span
                    class="size-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                    :class="alert.type === 'danger' || alert.type === 'error' ? 'bg-red-50 text-red-600' : (alert.type === 'warning' ? 'bg-amber-50 text-amber-600' : (alert.type === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600'))"
                >
                    <i :class="alert.type === 'danger' || alert.type === 'error' ? 'bi bi-x-circle-fill' : (alert.type === 'warning' ? 'bi bi-exclamation-triangle-fill' : (alert.type === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-info-circle-fill'))"></i>
                </span>
                <div class="min-w-0">
                    <strong class="text-sm font-bold text-gray-900 block" x-text="alert.title"></strong>
                    <p class="text-xs text-gray-600 mt-1 leading-relaxed" x-text="alert.message"></p>
                </div>
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-gray-100">
                <button
                    type="button"
                    class="btn-primary py-2 px-4 text-xs font-semibold rounded-lg text-white"
                    @click="closeAlert()"
                    x-text="alert.buttonText"
                ></button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================
     GLOBAL SEARCH MODAL (SEARCH BAR SEBAGAI MODAL TERPISAH)
     ========================================================= -->
<div
    x-data="remitSearchModal({ defaultAction: '<?= e(url('/reports/search')) ?>' })"
    x-cloak
    @open-search-modal.window="openModal($event.detail)"
>
    <div
        x-show="isOpen"
        class="fixed inset-0 z-[9998] flex items-start justify-center p-4 pt-16 sm:pt-24 bg-gray-900/60 backdrop-blur-xs overflow-y-auto"
        @click.self="closeModal()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        role="dialog"
        aria-modal="true"
        style="display: none;"
    >
        <div
            class="bg-white rounded-2xl max-w-xl w-full p-5 shadow-2xl border border-gray-200 relative"
            @keydown.escape.window="closeModal()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <span class="size-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                        <i class="bi bi-search"></i>
                    </span>
                    <div>
                        <strong class="text-sm font-bold text-gray-900 block" x-text="title"></strong>
                        <span class="text-xs text-gray-500 block" x-text="subtitle"></span>
                    </div>
                </div>
                <button
                    type="button"
                    @click="closeModal()"
                    class="size-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors"
                    aria-label="Tutup pencarian"
                >
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Form Pencarian Modal -->
            <form :action="action" method="get" @submit="submitSearch($event)">
                <template x-for="(param, idx) in extraParamsList" :key="idx">
                    <input type="hidden" :name="param.name" :value="param.value">
                </template>

                <div class="custom-search-bar mb-4">
                    <span class="search-leading-icon" aria-hidden="true">
                        <i class="bi bi-search text-base"></i>
                    </span>
                    <input
                        x-ref="modalSearchInput"
                        x-model="query"
                        type="search"
                        name="q"
                        class="custom-search-input h-11 text-sm ps-10 pe-10 rounded-xl"
                        :placeholder="placeholder"
                        autocomplete="off"
                        required
                    >
                    <button
                        type="button"
                        x-show="query && query.length > 0"
                        x-cloak
                        @click="query = ''; $refs.modalSearchInput.focus()"
                        class="search-clear-btn"
                        aria-label="Hapus kata kunci pencarian"
                    >
                        <i class="bi bi-x-circle-fill text-xs"></i>
                    </button>
                </div>

                <!-- Footer Aksi -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-gray-100 text-xs">
                    <div class="flex items-center gap-1.5 text-gray-400 text-[11px]">
                        <span>Tekan</span>
                        <kbd class="py-0.5 px-1.5 bg-gray-100 border border-gray-200 rounded font-mono text-gray-600">Enter ↵</kbd>
                        <span>untuk mencari,</span>
                        <kbd class="py-0.5 px-1.5 bg-gray-100 border border-gray-200 rounded font-mono text-gray-600">ESC</kbd>
                        <span>untuk tutup</span>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button
                            type="button"
                            @click="closeModal()"
                            class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary py-2 px-4 text-xs font-semibold rounded-lg inline-flex items-center gap-1.5"
                        >
                            <i class="bi bi-search text-xs"></i>
                            <span>Cari Sekarang</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================
     JAVASCRIPT UTAMA
========================================================= -->

<script
    src="<?= e(asset('js/app.js?v=22.0.1')) ?>"
    defer
></script>


<!-- =========================================================
     JAVASCRIPT FX
========================================================= -->

<script
    src="<?= e(asset('js/fx-idr-board.js?v=23.1.0')) ?>"
    defer
></script>


<!-- =========================================================
     ALPINE.JS & EXTENSIONS
========================================================= -->

<script
    src="<?= e(asset('js/custom-dialogs.js?v=1.0.0')) ?>"
    defer
></script>

<script
    src="<?= e(asset('js/custom-datepicker.js?v=1.0.0')) ?>"
    defer
></script>

<script
    src="<?= e(asset('vendor/alpine/alpine.min.js')) ?>"
    defer
></script>

<script
    src="<?= e(asset('vendor/preline/preline.min.js')) ?>"
    defer
></script>


<!-- =========================================================
     PRELINE UI AUTO-INITIALIZATION
========================================================= -->
<script>
window.addEventListener('load', () => {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});
</script>

</body>

</html>