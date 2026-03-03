<?php
// ── routes/web.php ────────────────────────────────────────────────

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ReceivingWebController;
use App\Http\Controllers\Web\BbsuWebController;
use App\Http\Controllers\Web\AcidTestingWebController;

// Guest — login page
Route::get('/',      [AuthWebController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login.get');

// Authenticated pages
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::post('/logout',   [AuthWebController::class, 'logout'])->name('logout');

Route::prefix('admin/mes/receiving')->name('admin.mes.receiving.')->group(function () {
    Route::get('/',           [ReceivingWebController::class, 'index'])->name('index');
    Route::get('/create',     [ReceivingWebController::class, 'create'])->name('create');
    Route::get('/{id}/edit',  [ReceivingWebController::class, 'edit'])->name('edit');
});

Route::prefix('admin/mes/bbsu')->name('admin.mes.bbsu.')->group(function () {
    Route::get('/',           [BbsuWebController::class, 'index'])->name('index');
    Route::get('/create',     [BbsuWebController::class, 'create'])->name('create');
    Route::get('/{id}/edit',  [BbsuWebController::class, 'edit'])->name('edit');
});
Route::prefix('admin/mes/acidTesting')->name('admin.mes.acidTesting.')->group(function () {
    Route::get('/',           [AcidTestingWebController::class, 'index'])->name('index');
    Route::get('/create',     [AcidTestingWebController::class, 'create'])->name('create');
    Route::get('/{id}/edit',  [AcidTestingWebController::class, 'edit'])->name('edit');
});

