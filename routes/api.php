<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\SupplierBatchController;
use App\Http\Controllers\Api\MaterialBatchController;
use App\Http\Controllers\Api\ReceivingController;
use App\Http\Controllers\Api\AcidTestingController;
use App\Http\Controllers\Api\AcidStockConditionController;
use App\Http\Controllers\Api\BbsuBatchController;
use App\Http\Controllers\Api\SmeltingBatchController;
use App\Http\Controllers\Api\RefiningBatchController;

// future imports:
// use App\Http\Controllers\Api\BbsuController;
// use App\Http\Controllers\Api\SmeltingController;
// use App\Http\Controllers\Api\RefiningController;

// ═══════════════════════════════════════════════════════════════
//  PUBLIC — Auth Routes (no token needed)
// ═══════════════════════════════════════════════════════════════
Route::prefix('auth')->group(function () {
     Route::post('/login', [AuthController::class, 'login']);
});

// ═══════════════════════════════════════════════════════════════
//  PROTECTED — All routes below require a valid Sanctum token
// ═══════════════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

     // ── Auth ────────────────────────────────────────────────────
     Route::prefix('auth')->group(function () {
          Route::post('/logout', [AuthController::class, 'logout']);
          Route::post('/refresh', [AuthController::class, 'refresh']);
          Route::get('/me', [AuthController::class, 'me']);
     });

     // ── Modules list (used for building permissions UI) ─────────
     Route::get('/modules', [ModuleController::class, 'index']);

     // ── User Management (Admin only) ────────────────────────────
     Route::middleware('role:admin')->prefix('users')->group(function () {
          Route::get('/', [UserController::class, 'index']);
          Route::post('/', [UserController::class, 'store']);
          Route::get('/{id}', [UserController::class, 'show']);
          Route::put('/{id}', [UserController::class, 'update']);
          Route::delete('/{id}', [UserController::class, 'destroy']);
          Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus']);
          Route::put('/{id}/permissions', [UserController::class, 'updatePermissions']);
          Route::get('/{id}/permissions', [UserController::class, 'getPermissions']);

          // Module management
          Route::post('/modules', [ModuleController::class, 'store']);
          Route::put('/modules/{id}', [ModuleController::class, 'update']);
     });

     // Admin + user can change their own password
     Route::put('/users/{id}/change-password', [UserController::class, 'changePassword']);

     // ═══════════════════════════════════════════════════════════
     //  MES MODULES
     //  Pattern: middleware('module:{slug}') for view
     //           middleware('module:{slug},can_create') for write
     // ═══════════════════════════════════════════════════════════

     // ── Suppliers (reference data) ────────────────────────────────────
     Route::prefix('suppliers')->middleware('module:suppliers')->group(function () {
          Route::get('/', [SupplierBatchController::class, 'index']);
          Route::get('/{id}', [SupplierBatchController::class, 'show']);
          Route::post('/', [SupplierBatchController::class, 'store'])->middleware('module:suppliers,can_create');
          Route::put('/{id}', [SupplierBatchController::class, 'update'])->middleware('module:suppliers,can_edit');
          Route::delete('/{id}', [SupplierBatchController::class, 'destroy'])->middleware('module:suppliers,can_delete');
     });

     // ── Materials (reference data) ────────────────────────────────────
     Route::prefix('materials')->middleware('module:materials')->group(function () {
          Route::get('/', [MaterialBatchController::class, 'index']);
          Route::get('/{id}', [MaterialBatchController::class, 'show']);
          Route::post('/', [MaterialBatchController::class, 'store'])->middleware('module:materials,can_create');
          Route::put('/{id}', [MaterialBatchController::class, 'update'])->middleware('module:materials,can_edit');
          Route::delete('/{id}', [MaterialBatchController::class, 'destroy'])->middleware('module:materials,can_delete');
     });


     // ── Receiving ─────────────────────────────────────────────────────
     Route::prefix('receivings')->middleware('module:receiving')->group(function () {
          Route::get('/', [ReceivingController::class, 'index']);
          Route::get('/approved-lots', [ReceivingController::class, 'getApprovedLots']);
          Route::get('/lot/{lotNo}', [ReceivingController::class, 'getByLot']);
          Route::get('/{id}', [ReceivingController::class, 'show']);

          Route::post('/', [ReceivingController::class, 'store'])
               ->middleware('module:receiving,can_create');

          Route::put('/{id}', [ReceivingController::class, 'update'])
               ->middleware('module:receiving,can_edit');

          Route::patch('/{id}/status', [ReceivingController::class, 'updateStatus'])
               ->middleware('module:receiving,can_edit');

          Route::delete('/{id}', [ReceivingController::class, 'destroy'])
               ->middleware('module:receiving,can_delete');
     });
     // ── Acid Stock Conditions (master dropdown data) ──────────────────
     // Put this inside Route::middleware('auth:sanctum')->group(...)
     Route::prefix('acid-stock-conditions')->group(function () {
          Route::get('/', [AcidStockConditionController::class, 'index']);
          Route::post('/', [AcidStockConditionController::class, 'store']);
          Route::put('/{id}', [AcidStockConditionController::class, 'update']);
          Route::delete('/{id}', [AcidStockConditionController::class, 'destroy']);
     });

     // ── Acid Testing ──────────────────────────────────────────────────
     // ── Acid Testing ──────────────────────────────────────────────
     // IMPORTANT: all static/named routes BEFORE the /{id} wildcard
     Route::prefix('acid-testings')->middleware('module:acid_testing')->group(function () {
          Route::get('/', [AcidTestingController::class, 'index']);
          Route::get('/stock-conditions', [AcidTestingController::class, 'stockConditions']);
          Route::get('/available-lots', [AcidTestingController::class, 'availableLots']);
          Route::get('/lot-check/{lotNo}', [AcidTestingController::class, 'lotCheck']);
          Route::get('/{id}', [AcidTestingController::class, 'show']);
          Route::get('/{id}/print', [AcidTestingController::class, 'printView']);
          Route::post('/', [AcidTestingController::class, 'store'])
               ->middleware('module:acid_testing,can_create');
          Route::put('/{id}', [AcidTestingController::class, 'update'])
               ->middleware('module:acid_testing,can_edit');
          Route::patch('/{id}/status', [AcidTestingController::class, 'updateStatus'])
               ->middleware('module:acid_testing,can_edit');
          Route::delete('/{id}', [AcidTestingController::class, 'destroy'])
               ->middleware('module:acid_testing,can_delete');
     });
     // Route::prefix('acid-testings')->middleware('module:acid-testing')->group(function () {
     //      Route::get('/',                  [AcidTestingController::class, 'index']);
     //      Route::get('/prefill/{lotNo}',   [AcidTestingController::class, 'prefill']);
     //      Route::get('/lot/{lotNo}',       [AcidTestingController::class, 'getByLot']);
     //      Route::get('/{id}',              [AcidTestingController::class, 'show']);

     //      Route::post('/',                 [AcidTestingController::class, 'store'])
     //           ->middleware('module:acid-testing,can_create');

     //      Route::put('/{id}',              [AcidTestingController::class, 'update'])
     //           ->middleware('module:acid-testing,can_edit');

     //      Route::patch('/{id}/status',     [AcidTestingController::class, 'updateStatus'])
     //           ->middleware('module:acid-testing,can_edit');

     //      Route::delete('/{id}',           [AcidTestingController::class, 'destroy'])
     //           ->middleware('module:acid-testing,can_delete');
     // });

     // ── BBSU ──────────────────────────────────────────────────────
     Route::prefix('bbsu-batches')->middleware('module:bbsu')->group(function () {
          Route::get('/', [BbsuBatchController::class, 'index']);
          Route::post('/', [BbsuBatchController::class, 'store'])->middleware('module:bbsu,can_create');
          // ⚠ Static-prefix routes MUST come before /{bbsu_batch} wildcard
          Route::get('/generate-batch-no', [BbsuBatchController::class, 'generateBatchNo']);
          Route::get('/acid-test-lot-numbers', [BbsuBatchController::class, 'acidTestLotNumbers']);
          Route::get('/acid-summary/{lotNo}', [BbsuBatchController::class, 'acidSummaryByLot']);
          Route::get('/acid-summary-all', [BbsuBatchController::class, 'acidSummaryAllLots']);
          Route::get('/reports/acid-summary', [BbsuBatchController::class, 'acidSummary']);
          Route::get('/output-material-info', [BbsuBatchController::class, 'outputMaterialInfo']);
          Route::post('/{id}/submit', [BbsuBatchController::class, 'submit'])->middleware('module:bbsu,can_edit');
          Route::get('/{bbsu_batch}', [BbsuBatchController::class, 'show']);
          Route::put('/{bbsu_batch}', [BbsuBatchController::class, 'update'])->middleware('module:bbsu,can_edit');
          Route::delete('/{bbsu_batch}', [BbsuBatchController::class, 'destroy'])->middleware('module:bbsu,can_delete');
          Route::patch('/{bbsu_batch}/status', [BbsuBatchController::class, 'updateStatus'])->middleware('module:bbsu,can_edit');
     });
     // ── Smelting ──────────────────────────────────────────────────
     Route::prefix('smelting-batches')->middleware('module:smelting')->group(function () {
          Route::get('/', [SmeltingBatchController::class, 'index']);
          Route::get('/generate-batch-no', [SmeltingBatchController::class, 'generateBatchNo']);
          Route::get('/bbsu-lots/all', [SmeltingBatchController::class, 'getAllBbsuLots']);
          Route::get('/bbsu-lots/{materialId}', [SmeltingBatchController::class, 'getBbsuLots']);
          Route::post('/', [SmeltingBatchController::class, 'store']);
          Route::get('/{id}', [SmeltingBatchController::class, 'show']);
          Route::put('/{id}', [SmeltingBatchController::class, 'update']);
          Route::delete('/{id}', [SmeltingBatchController::class, 'destroy']);
          Route::post('/{id}/autosave', [SmeltingBatchController::class, 'autosave']);
          Route::post('/{id}/submit', [SmeltingBatchController::class, 'submit']);
          Route::patch('/{id}/status', [SmeltingBatchController::class, 'updateStatus'])->middleware('module:smeltings,can_edit');
     });

     // ── Refining ──────────────────────────────────────────────────
     Route::prefix('refining')->middleware('module:refining')->group(function () {

          Route::get('/generate-batch-no', [RefiningBatchController::class, 'generateBatchNo']);
          Route::get('/smelting-lots', [RefiningBatchController::class, 'getAllSmeltingLots']);
          Route::get('/smelting-lots/{materialId}', [RefiningBatchController::class, 'getSmeltingLots']);
          Route::get('/process-names', [RefiningBatchController::class, 'getProcessNames']);

          Route::get('/', [RefiningBatchController::class, 'index']);
          Route::post('/', [RefiningBatchController::class, 'store']);

          Route::get('/{id}', [RefiningBatchController::class, 'show']);
          Route::put('/{id}', [RefiningBatchController::class, 'update']);
          Route::post('/{id}/autosave', [RefiningBatchController::class, 'autosave']);
          Route::post('/{id}/submit', [RefiningBatchController::class, 'submit']);
          Route::delete('/{id}', [RefiningBatchController::class, 'destroy']);
     });
     Route::prefix('material')->group(function () {
          Route::get('/', [MaterialBatchController::class, 'index']);
          Route::post('/', [MaterialBatchController::class, 'store']);
          Route::get('/{id}/stock', [\App\Http\Controllers\Api\MaterialController::class, 'getStock']);
          Route::put('/{id}', [MaterialBatchController::class, 'update']);
          Route::delete('/{id}', [MaterialBatchController::class, 'destroy']);
     });
     Route::prefix('supplier')->group(function () {
          Route::get('/', [SupplierBatchController::class, 'index']);
          Route::post('/', [SupplierBatchController::class, 'store']);
          Route::put('/{id}', [SupplierBatchController::class, 'update']);
          Route::delete('/{id}', [SupplierBatchController::class, 'destroy']);
     });
     Route::prefix('reports')->name('reports.')->group(function () {

          Route::get('material-inward/filters', [\App\Http\Controllers\Api\ReportController::class, 'materialInwardFilters'])
               ->middleware('module:report_material_inward')
               ->name('materialInward.filters');

          // ↓ FIXED: was '/reports/material-inward/dashboard' → now 'material-inward/dashboard'
          Route::get('material-inward/dashboard', [\App\Http\Controllers\Api\ReportController::class, 'materialInwardDashboard'])
               ->middleware('module:report_material_inward')
               ->name('materialInward.dashboard');

          Route::get('material-inward', [\App\Http\Controllers\Api\ReportController::class, 'materialInward'])
               ->middleware('module:report_material_inward')
               ->name('materialInward');

          Route::get('acid-test-status', [\App\Http\Controllers\Api\ReportController::class, 'acidTestStatus'])
               ->middleware('module:report_acid_test_status')
               ->name('acidTestStatus');
          Route::get('acid-test-status/filters', [\App\Http\Controllers\Api\ReportController::class, 'acidTestStatusFilters'])
               ->middleware('module:report_acid_test_status')
               ->name('acidTestStatus.filters');

     });
     Route::prefix('admin')->name('admin.')->group(function () {

          // ── Users ────────────────────────────────────────────────────
          Route::get('users', [\App\Http\Controllers\Api\AdminController::class, 'userIndex'])->name('users.index');
          Route::post('users', [\App\Http\Controllers\Api\AdminController::class, 'userStore'])->name('users.store');
          Route::get('users/{id}', [\App\Http\Controllers\Api\AdminController::class, 'userShow'])->name('users.show');
          Route::put('users/{id}', [\App\Http\Controllers\Api\AdminController::class, 'userUpdate'])->name('users.update');
          Route::delete('users/{id}', [\App\Http\Controllers\Api\AdminController::class, 'userDestroy'])->name('users.destroy');
          Route::patch('users/{id}/toggle-status', [\App\Http\Controllers\Api\AdminController::class, 'userToggleStatus'])->name('users.toggleStatus');
          Route::get('users/{id}/permissions', [\App\Http\Controllers\Api\AdminController::class, 'userPermissions'])->name('users.permissions');
          Route::put('users/{id}/permissions', [\App\Http\Controllers\Api\AdminController::class, 'userUpdatePermissions'])->name('users.permissions.update');
          Route::post('users/{id}/apply-profile', [\App\Http\Controllers\Api\AdminController::class, 'applyProfileToUser'])->name('users.applyProfile');

          // ── Roles ────────────────────────────────────────────────────
          Route::get('roles', [\App\Http\Controllers\Api\AdminController::class, 'roleIndex'])->name('roles.index');
          Route::post('roles', [\App\Http\Controllers\Api\AdminController::class, 'roleStore'])->name('roles.store');
          Route::put('roles/{id}', [\App\Http\Controllers\Api\AdminController::class, 'roleUpdate'])->name('roles.update');
          Route::delete('roles/{id}', [\App\Http\Controllers\Api\AdminController::class, 'roleDestroy'])->name('roles.destroy');

          // ── Profiles ─────────────────────────────────────────────────
          Route::get('profiles', [\App\Http\Controllers\Api\AdminController::class, 'profileIndex'])->name('profiles.index');
          Route::get('profiles/{id}', [\App\Http\Controllers\Api\AdminController::class, 'profileShow'])->name('profiles.show');
          Route::post('profiles', [\App\Http\Controllers\Api\AdminController::class, 'profileStore'])->name('profiles.store');
          Route::put('profiles/{id}', [\App\Http\Controllers\Api\AdminController::class, 'profileUpdate'])->name('profiles.update');
          Route::delete('profiles/{id}', [\App\Http\Controllers\Api\AdminController::class, 'profileDestroy'])->name('profiles.destroy');
          Route::put('profiles/{id}/permissions', [\App\Http\Controllers\Api\AdminController::class, 'profileUpdatePermissions'])->name('profiles.permissions.update');

          // ── Modules ──────────────────────────────────────────────────
          Route::get('modules', [\App\Http\Controllers\Api\AdminController::class, 'moduleIndex'])->name('modules.index');
          Route::post('modules', [\App\Http\Controllers\Api\AdminController::class, 'moduleStore'])->name('modules.store');
          Route::put('modules/{id}', [\App\Http\Controllers\Api\AdminController::class, 'moduleUpdate'])->name('modules.update');
          Route::delete('modules/{id}', [\App\Http\Controllers\Api\AdminController::class, 'moduleDestroy'])->name('modules.destroy');
     });

});