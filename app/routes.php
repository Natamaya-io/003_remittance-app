<?php

declare(strict_types=1);

use App\Controllers\AnalysisController;
use App\Controllers\AuditController;
use App\Controllers\AuthController;
use App\Controllers\CompanyController;
use App\Controllers\ComplaintController;
use App\Controllers\DashboardController;
use App\Controllers\DocumentCenterController;
use App\Controllers\FxController;
use App\Controllers\InvoiceController;
use App\Controllers\PayerAccountController;
use App\Controllers\ProfileController;
use App\Controllers\ReportController;
use App\Controllers\TransactionController;
use App\Controllers\TransactionBankController;
use App\Controllers\UserController;
use App\Core\Router;

$router = new Router();

$router->get(
    '/login',
    [
        AuthController::class,
        'showLogin',
    ]
);

$router->post(
    '/login',
    [
        AuthController::class,
        'login',
    ]
);

$router->post(
    '/logout',
    [
        AuthController::class,
        'logout',
    ]
);

$router->get(
    '/api/fx',
    [
        FxController::class,
        'publicJson',
    ]
);

$router->get(
    '/',
    [
        DashboardController::class,
        'index',
    ]
);

$router->get(
    '/documents',
    [
        DocumentCenterController::class,
        'index',
    ]
);

$router->post(
    '/fx/refresh',
    [
        FxController::class,
        'refresh',
    ]
);

/*
|--------------------------------------------------------------------------
| Data PT
|--------------------------------------------------------------------------
*/

$router->get(
    '/companies',
    [
        CompanyController::class,
        'index',
    ]
);

$router->get(
    '/companies/create',
    [
        CompanyController::class,
        'create',
    ]
);

$router->post(
    '/companies',
    [
        CompanyController::class,
        'store',
    ]
);

$router->get(
    '/companies/{id}/edit',
    [
        CompanyController::class,
        'edit',
    ]
);

$router->put(
    '/companies/{id}',
    [
        CompanyController::class,
        'update',
    ]
);

$router->delete(
    '/companies/{id}',
    [
        CompanyController::class,
        'destroy',
    ]
);

/*
|--------------------------------------------------------------------------
| Rekening Payer
|--------------------------------------------------------------------------
*/

$router->get(
    '/payer-accounts',
    [
        PayerAccountController::class,
        'index',
    ]
);

$router->get(
    '/payer-accounts/create',
    [
        PayerAccountController::class,
        'create',
    ]
);

$router->post(
    '/payer-accounts',
    [
        PayerAccountController::class,
        'store',
    ]
);

$router->get(
    '/payer-accounts/{id}/edit',
    [
        PayerAccountController::class,
        'edit',
    ]
);

$router->put(
    '/payer-accounts/{id}',
    [
        PayerAccountController::class,
        'update',
    ]
);

$router->delete(
    '/payer-accounts/{id}',
    [
        PayerAccountController::class,
        'destroy',
    ]
);

/*
|--------------------------------------------------------------------------
| Transaksi
|--------------------------------------------------------------------------
*/

$router->get(
    '/transactions',
    [
        TransactionBankController::class,
        'index',
    ]
);

$router->get(
    '/transactions/bri',
    [
        TransactionController::class,
        'importForm',
    ]
);

$router->get(
    '/transactions/bri/history',
    [
        TransactionController::class,
        'index',
    ]
);

$router->get(
    '/transactions/bni',
    [
        TransactionBankController::class,
        'bniForm',
    ]
);

$router->post(
    '/transactions/bni',
    [
        TransactionBankController::class,
        'bniStore',
    ]
);

$router->get(
    '/transactions/bni/batches/{id}',
    [
        TransactionBankController::class,
        'bniBatch',
    ]
);

$router->delete(
    '/transactions/bni/batches/{id}',
    [
        TransactionBankController::class,
        'destroyBniBatch',
    ]
);

$router->get(
    '/transactions/bni/rows/{id}/print',
    [
        TransactionBankController::class,
        'bniPrint',
    ]
);

$router->get(
    '/transactions/import',
    [
        TransactionController::class,
        'importForm',
    ]
);

$router->post(
    '/transactions/import',
    [
        TransactionController::class,
        'import',
    ]
);

$router->get(
    '/transactions/create',
    [
        TransactionController::class,
        'create',
    ]
);

$router->post(
    '/transactions',
    [
        TransactionController::class,
        'store',
    ]
);

$router->get(
    '/transactions/jobs/{id}/export-xlsx',
    [
        TransactionController::class,
        'exportJobXlsx',
    ]
);

$router->get(
    '/transactions/jobs/{id}/export-xls',
    [
        TransactionController::class,
        'exportJobXls',
    ]
);

$router->get(
    '/transactions/jobs/{id}',
    [
        TransactionController::class,
        'job',
    ]
);

$router->get(
    '/transactions/{id}/export-bri',
    [
        TransactionController::class,
        'exportSingleBri',
    ]
);

$router->get(
    '/transactions/{id}',
    [
        TransactionController::class,
        'show',
    ]
);

$router->get(
    '/transactions/{id}/edit',
    [
        TransactionController::class,
        'edit',
    ]
);

$router->put(
    '/transactions/{id}',
    [
        TransactionController::class,
        'update',
    ]
);

$router->delete(
    '/transactions/{id}',
    [
        TransactionController::class,
        'destroy',
    ]
);

/*
|--------------------------------------------------------------------------
| Report Harian
|--------------------------------------------------------------------------
|
| Route khusus harus ditempatkan sebelum /reports/{id}.
|
*/

$router->get(
    '/reports',
    [
        ReportController::class,
        'index',
    ]
);

$router->get(
    '/reports/search',
    [
        ReportController::class,
        'search',
    ]
);

$router->get(
    '/reports/create',
    [
        ReportController::class,
        'create',
    ]
);

$router->post(
    '/reports',
    [
        ReportController::class,
        'store',
    ]
);

$router->get(
    '/reports/daily',
    [
        ReportController::class,
        'legacyDaily',
    ]
);

$router->get(
    '/reports/month/{month}',
    [
        ReportController::class,
        'month',
    ]
);

$router->get(
    '/reports/date/{date}/export',
    [
        ReportController::class,
        'exportDate',
    ]
);

$router->get(
    '/reports/date/{date}',
    [
        ReportController::class,
        'date',
    ]
);

$router->get(
    '/reports/{id}/edit',
    [
        ReportController::class,
        'edit',
    ]
);

$router->put(
    '/reports/{id}',
    [
        ReportController::class,
        'update',
    ]
);

$router->get(
    '/reports/{id}/export',
    [
        ReportController::class,
        'export',
    ]
);

$router->post(
    '/reports/{id}/bulk-status',
    [
        ReportController::class,
        'bulkStatus',
    ]
);

$router->post(
    '/reports/{id}/rows/{rowId}/status',
    [
        ReportController::class,
        'updateRow',
    ]
);

$router->delete(
    '/reports/{id}',
    [
        ReportController::class,
        'destroy',
    ]
);

$router->get(
    '/reports/{id}',
    [
        ReportController::class,
        'show',
    ]
);

/*
|--------------------------------------------------------------------------
| Complain
|--------------------------------------------------------------------------
*/

$router->get(
    '/complaints',
    [
        ComplaintController::class,
        'index',
    ]
);

$router->get(
    '/complaints/create',
    [
        ComplaintController::class,
        'create',
    ]
);

$router->post(
    '/complaints',
    [
        ComplaintController::class,
        'store',
    ]
);

$router->get(
    '/complaints/{id}/edit',
    [
        ComplaintController::class,
        'edit',
    ]
);

$router->put(
    '/complaints/{id}',
    [
        ComplaintController::class,
        'update',
    ]
);

$router->delete(
    '/complaints/{id}',
    [
        ComplaintController::class,
        'destroy',
    ]
);

/*
|--------------------------------------------------------------------------
| Invoice
|--------------------------------------------------------------------------
*/

$router->get(
    '/invoices',
    [
        InvoiceController::class,
        'index',
    ]
);

$router->get(
    '/invoices/create',
    [
        InvoiceController::class,
        'create',
    ]
);

$router->post(
    '/invoices',
    [
        InvoiceController::class,
        'store',
    ]
);

$router->get(
    '/invoices/{id}/edit',
    [
        InvoiceController::class,
        'edit',
    ]
);

$router->put(
    '/invoices/{id}',
    [
        InvoiceController::class,
        'update',
    ]
);

$router->delete(
    '/invoices/{id}',
    [
        InvoiceController::class,
        'destroy',
    ]
);

$router->get(
    '/invoices/{id}/print',
    [
        InvoiceController::class,
        'print',
    ]
);

/*
|--------------------------------------------------------------------------
| Entropy TOPSIS - Modul Analisis Standalone
|--------------------------------------------------------------------------
*/

$router->get('/analysis', [AnalysisController::class, 'index']);
$router->post('/analysis/sources/upload', [AnalysisController::class, 'uploadSource']);

$router->get('/analysis/criteria', [AnalysisController::class, 'criteria']);
$router->post('/analysis/criteria/save', [AnalysisController::class, 'saveCriterion']);
$router->post('/analysis/criteria/{id}/delete', [AnalysisController::class, 'deleteCriterion']);

$router->get('/analysis/subcriteria', [AnalysisController::class, 'subcriteria']);
$router->post('/analysis/subcriteria/save', [AnalysisController::class, 'saveSubcriterion']);
$router->post('/analysis/subcriteria/{id}/delete', [AnalysisController::class, 'deleteSubcriterion']);

$router->get('/analysis/alternatives', [AnalysisController::class, 'alternatives']);
$router->post('/analysis/alternatives/save', [AnalysisController::class, 'saveAlternative']);
$router->post('/analysis/alternatives/{id}/delete', [AnalysisController::class, 'deleteAlternative']);

$router->get('/analysis/assessment', [AnalysisController::class, 'assessment']);
$router->post('/analysis/assessment/save', [AnalysisController::class, 'saveAssessment']);
$router->post('/analysis/assessment/reset', [AnalysisController::class, 'resetAssessment']);

$router->get('/analysis/calculation', [AnalysisController::class, 'calculation']);
$router->post('/analysis/calculation/save', [AnalysisController::class, 'saveCalculation']);

$router->get('/analysis/results', [AnalysisController::class, 'results']);
$router->get('/analysis/results/{id}', [AnalysisController::class, 'results']);

/*
|--------------------------------------------------------------------------
| Pengguna dan Audit
|--------------------------------------------------------------------------
*/

$router->get(
    '/users',
    [
        UserController::class,
        'index',
    ]
);

$router->get(
    '/users/create',
    [
        UserController::class,
        'create',
    ]
);

$router->post(
    '/users',
    [
        UserController::class,
        'store',
    ]
);

$router->get(
    '/users/{id}/edit',
    [
        UserController::class,
        'edit',
    ]
);

$router->put(
    '/users/{id}',
    [
        UserController::class,
        'update',
    ]
);

$router->delete(
    '/users/{id}',
    [
        UserController::class,
        'destroy',
    ]
);

$router->get(
    '/audit',
    [
        AuditController::class,
        'index',
    ]
);

$router->get(
    '/profile',
    [
        ProfileController::class,
        'index',
    ]
);

$router->put(
    '/profile',
    [
        ProfileController::class,
        'update',
    ]
);

$router->put(
    '/profile/password',
    [
        ProfileController::class,
        'password',
    ]
);

return $router;