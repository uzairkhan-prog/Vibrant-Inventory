<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductLedgerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SaleReturnController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::get('/', function () {
    return view('index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource Routes
    Route::resource('products', ProductController::class);
    Route::get('ledger/products', [ProductLedgerController::class, 'index'])->name('ledger.products');
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('ledgers', LedgerController::class);
    Route::resource('assets-inventory', AssetController::class);
    Route::resource('sale-returns', SaleReturnController::class);
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::post('suppliers/{supplier}/payments', [SupplierController::class, 'storePayment'])->name('suppliers.payments.store');
    Route::get('/supplier-payments/{payment}/edit', [SupplierPaymentController::class, 'edit'])->name('supplier-payments.edit');
    Route::put('/supplier-payments/{payment}', [SupplierPaymentController::class, 'update'])->name('supplier-payments.update');
    Route::delete('/supplier-payments/{payment}', [SupplierPaymentController::class, 'destroy'])->name('supplier-payments.destroy');
    Route::post('customers/{customer}/payments', [CustomerPaymentController::class, 'store'])->name('customers.payments.store');
    Route::get('customer-payments/{payment}/edit', [CustomerPaymentController::class, 'edit'])->name('customer-payments.edit');
    Route::put('customer-payments/{payment}', [CustomerPaymentController::class, 'update'])->name('customer-payments.update');
    Route::delete('customer-payments/{payment}', [CustomerPaymentController::class, 'destroy'])->name('customer-payments.destroy');
});

require __DIR__ . '/auth.php';
