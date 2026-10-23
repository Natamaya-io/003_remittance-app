# Cleanup FX V22

File lama yang dihapus karena ganda atau tidak digunakan runtime:

- `app/Views/partials/fx-board.php`
- `public/fx_rates_live.php`
- `public/assets/css/dashboard-v8.css`
- `public/assets/js/dashboard-v8.js`
- `public/assets/css/fable-ui.css`
- `public/assets/css/remitops-v4.css`
- `public/assets/css/ui-v2.css`
- `public/assets/js/remitops-v4.js`
- `public/assets/js/remitops-v5.js`

Komponen FX aktif hanya menggunakan:

- `app/Services/FxService.php`
- `app/Controllers/FxController.php`
- `app/Views/partials/fx-idr-board.php`
- `public/assets/css/fx-idr-board.css`
- `public/assets/js/fx-idr-board.js`
