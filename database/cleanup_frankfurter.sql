/*
|--------------------------------------------------------------------------
| Bersihkan seluruh data provider lama
|--------------------------------------------------------------------------
*/

DELETE FROM fx_rate_history
WHERE provider = 'Frankfurter';

DELETE FROM fx_rates
WHERE provider = 'Frankfurter';

DELETE FROM fx_live_rates
WHERE provider = 'Frankfurter';


/*
|--------------------------------------------------------------------------
| Bersihkan variasi penulisan provider lama
|--------------------------------------------------------------------------
*/

DELETE FROM fx_rate_history
WHERE LOWER(provider) LIKE '%frankfurter%';

DELETE FROM fx_rates
WHERE LOWER(provider) LIKE '%frankfurter%';

DELETE FROM fx_live_rates
WHERE LOWER(provider) LIKE '%frankfurter%';


/*
|--------------------------------------------------------------------------
| Pemeriksaan hasil
|--------------------------------------------------------------------------
*/

SELECT
    quote_currency,
    provider,
    rate,
    rate_date,
    fetched_at
FROM fx_rates
ORDER BY
    quote_currency ASC;