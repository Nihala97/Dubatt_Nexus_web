<?php
// ── routes/web.php ────────────────────────────────────────────────

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SupplierWebController;
use App\Http\Controllers\Web\MaterialWebController;
use App\Http\Controllers\Web\ReceivingWebController;
use App\Http\Controllers\Web\BbsuWebController;
use App\Http\Controllers\Web\AcidTestingWebController;
use App\Http\Controllers\Web\Smeltingwebcontroller;
use App\Http\Controllers\Web\RefiningWebController;
// Guest — login page
Route::get('/', [AuthWebController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login.get');

// Authenticated pages
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

Route::prefix('admin/mes/supplier')->name('admin.mes.supplier.')->group(function () {
    Route::get('/', [SupplierWebController::class, 'index'])->name('index');
    Route::get('/create', [SupplierWebController::class, 'create'])->name('create');
    Route::post('/', [SupplierWebController::class, 'store'])->name('store');        // ✅ Add this
    Route::get('/{id}/edit', [SupplierWebController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SupplierWebController::class, 'update'])->name('update');    // ✅ Add this
    Route::delete('/{id}', [SupplierWebController::class, 'destroy'])->name('destroy');
});

// ── Materials ───────────────────────────────────────────────────
Route::prefix('admin/mes/material')->name('admin.mes.material.')->group(function () {
    Route::get('/', [MaterialWebController::class, 'index'])->name('index');
    Route::get('/create', [MaterialWebController::class, 'create'])->name('create');
    Route::post('/', [MaterialWebController::class, 'store'])->name('store');        // ✅ Add this
    Route::get('/{id}/edit', [MaterialWebController::class, 'edit'])->name('edit');
    Route::put('/{id}', [MaterialWebController::class, 'update'])->name('update');    // ✅ Add this
    Route::delete('/{id}', [MaterialWebController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin/mes/receiving')->name('admin.mes.receiving.')->group(function () {
    Route::get('/', [ReceivingWebController::class, 'index'])->name('index');
    Route::get('/create', [ReceivingWebController::class, 'create'])->name('create');
    Route::get('/{id}/edit', [ReceivingWebController::class, 'edit'])->name('edit');
    Route::delete('/{id}', [ReceivingWebController::class, 'destroy'])->name('destroy');

});
Route::prefix('admin/mes/acidTesting')->name('admin.mes.acidTesting.')->group(function () {
    Route::get('/', [AcidTestingWebController::class, 'index'])->name('index');
    Route::get('/create', [AcidTestingWebController::class, 'create'])->name('create');
    Route::get('/{id}/edit', [AcidTestingWebController::class, 'edit'])->name('edit');
    Route::get('/{id}/print', [AcidTestingWebController::class, 'printView'])->name('print');
    Route::delete('/{id}', [AcidTestingWebController::class, 'destroy'])->name('destroy');
});
// Route::prefix('admin/mes/acidTesting')->name('admin.mes.acidTesting.')->group(function () {
//     Route::get('/',           [AcidTestingWebController::class, 'index'])->name('index');
//     Route::get('/create',     [AcidTestingWebController::class, 'create'])->name('create');
//     Route::get('/{id}/edit',  [AcidTestingWebController::class, 'edit'])->name('edit');
// });
Route::prefix('admin/mes/bbsu')->name('admin.mes.bbsu.')->group(function () {
    Route::get('/', [BbsuWebController::class, 'index'])->name('index');
    Route::get('/create', [BbsuWebController::class, 'create'])->name('create');
    Route::get('/{id}/edit', [BbsuWebController::class, 'edit'])->name('edit');
});
Route::prefix('admin/mes/smelting')->name('admin.mes.smelting.')->group(function () {
    Route::get('/', [Smeltingwebcontroller::class, 'index'])->name('index');
    Route::get('/create', [Smeltingwebcontroller::class, 'create'])->name('create');
    Route::get('/{id}/edit', [Smeltingwebcontroller::class, 'edit'])->name('edit');
    Route::delete('/{id}', [Smeltingwebcontroller::class, 'destroy'])->name('destroy');

});
Route::prefix('admin/mes/refining')->name('admin.mes.refining.')->group(function () {
    Route::get('/', [RefiningWebController::class, 'index'])->name('index');
    Route::get('/create', [RefiningWebController::class, 'create'])->name('create');
    Route::get('/{id}/edit', [RefiningWebController::class, 'edit'])->name('edit');
    Route::delete('/{id}', [RefiningWebController::class, 'destroy'])->name('destroy');

});
Route::prefix('admin/reports')->name('admin.reports.')->group(function () {

    Route::get('material-inward', [\App\Http\Controllers\Web\ReportWebController::class, 'materialInward'])
        ->name('materialInward');

    Route::get('acid-test-status', [\App\Http\Controllers\Web\ReportWebController::class, 'acidTestStatus'])
        ->name('acidTestStatus');

    // Add future report web routes here — same controller, new method:
});