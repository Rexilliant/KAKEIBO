<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FinancialAccountController;
use App\Http\Controllers\SavingTargetController;
use App\Http\Controllers\SavingContributionController;
use App\Http\Controllers\MonthlyReflectionController;
use App\Http\Controllers\FinancialAccountLedgerController;
use App\Http\Controllers\AccountTransferController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\MonthlyClosingController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // route dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportReportCsv'])->name('dashboard.export.csv');

    // route financial account
    Route::get('/financial-accounts/{financialAccount}/ledger', [FinancialAccountLedgerController::class, 'index'])
        ->name('financial-accounts.ledger');
    Route::resource('financial-accounts', FinancialAccountController::class);

    // route category
    Route::resource('categories', CategoryController::class);

    // route transaction
    Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])
        ->name('transactions.export.csv');
    Route::get('/transactions/summary-report', [TransactionController::class, 'summaryReport'])
        ->name('transactions.summary-report');
    Route::post('/transactions/{transaction}/attachments', [TransactionController::class, 'uploadAttachment'])
        ->name('transactions.attachments.upload');
    Route::delete('/transactions/attachments/{attachment}', [TransactionController::class, 'deleteAttachment'])
        ->name('transactions.attachments.delete');
    Route::resource('transactions', TransactionController::class);

    // route budget
    Route::resource('budgets', BudgetController::class);

    // route saving target
    Route::resource('saving-targets', SavingTargetController::class);

    // route saving contribution
    Route::resource('saving-contributions', SavingContributionController::class);

    // route recurring transaction
    Route::get('/recurring-transactions/generate/due', [RecurringTransactionController::class, 'generateDueTransactions'])
        ->name('recurring-transactions.generate-due');
    Route::patch('/recurring-transactions/{recurringTransaction}/toggle-status', [RecurringTransactionController::class, 'updateStatus'])
        ->name('recurring-transactions.toggle-status');
    Route::resource('recurring-transactions', RecurringTransactionController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    // route account transfer
    Route::resource('account-transfers', AccountTransferController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    // route monthly reflection
    Route::resource('monthly-reflections', MonthlyReflectionController::class);

    // route monthly closing
    Route::get('/monthly-closings', [MonthlyClosingController::class, 'index'])
        ->name('monthly-closings.index');
    Route::post('/monthly-closings/close', [MonthlyClosingController::class, 'closeMonth'])
        ->name('monthly-closings.close');
    Route::get('/monthly-closings/{monthlyClosing}', [MonthlyClosingController::class, 'show'])
        ->name('monthly-closings.show');
    Route::delete('/monthly-closings/{monthlyClosing}', [MonthlyClosingController::class, 'destroy'])
        ->name('monthly-closings.destroy');
});

require __DIR__ . '/auth.php';