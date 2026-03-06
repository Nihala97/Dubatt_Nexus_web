<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\ReceivingController;
use App\Http\Controllers\Api\AcidTestingController;
use App\Http\Controllers\Api\AcidStockConditionController;
use App\Http\Controllers\Api\BbsuBatchController;



// future imports:
// use App\Http\Controllers\Api\BbsuController;
// use App\Http\Controllers\Api\SmeltingController;
// use App\Http\Controllers\Api\RefiningController;

// ═══════════════════════════════════════════════════════════════
//  PUBLIC — Auth Routes (no token needed)
// ═══════════════════════════════════════════════════════════════
Route::prefix('auth')->group(function () {
    Route::post('/login',  [AuthController::class, 'login']);
});

// ═══════════════════════════════════════════════════════════════
//  PROTECTED — All routes below require a valid Sanctum token
// ═══════════════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/logout',  [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me',       [AuthController::class, 'me']);
    });

    // ── Modules list (used for building permissions UI) ─────────
    Route::get('/modules', [ModuleController::class, 'index']);

    // ── User Management (Admin only) ────────────────────────────
    Route::middleware('role:admin')->prefix('users')->group(function () {
        Route::get('/',                          [UserController::class, 'index']);
        Route::post('/',                         [UserController::class, 'store']);
        Route::get('/{id}',                      [UserController::class, 'show']);
        Route::put('/{id}',                      [UserController::class, 'update']);
        Route::delete('/{id}',                   [UserController::class, 'destroy']);
        Route::patch('/{id}/toggle-status',      [UserController::class, 'toggleStatus']);
        Route::put('/{id}/permissions',          [UserController::class, 'updatePermissions']);
        Route::get('/{id}/permissions',          [UserController::class, 'getPermissions']);

        // Module management
        Route::post('/modules',    [ModuleController::class, 'store']);
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
     Route::prefix('suppliers')->group(function () {
          Route::get('/',        [SupplierController::class, 'index']);
          Route::get('/{id}',    [SupplierController::class, 'show']);
          Route::post('/',       [SupplierController::class, 'store']);
          Route::put('/{id}',    [SupplierController::class, 'update']);
          Route::delete('/{id}', [SupplierController::class, 'destroy']);
     });
     
     // ── Materials (reference data) ────────────────────────────────────
     Route::prefix('materials')->group(function () {
          Route::get('/',        [MaterialController::class, 'index']);
          Route::get('/{id}',    [MaterialController::class, 'show']);
          Route::post('/',       [MaterialController::class, 'store']);
          Route::put('/{id}',    [MaterialController::class, 'update']);
          Route::delete('/{id}', [MaterialController::class, 'destroy']);
     });

 
    // ── Receiving ─────────────────────────────────────────────────────
     Route::prefix('receivings')->middleware('module:receiving')->group(function () {
          Route::get('/',               [ReceivingController::class, 'index']);
          Route::get('/approved-lots',  [ReceivingController::class, 'getApprovedLots']);
          Route::get('/lot/{lotNo}',    [ReceivingController::class, 'getByLot']);
          Route::get('/{id}',           [ReceivingController::class, 'show']);
     
          Route::post('/',              [ReceivingController::class, 'store'])
               ->middleware('module:receiving,can_create');
     
          Route::put('/{id}',           [ReceivingController::class, 'update'])
               ->middleware('module:receiving,can_edit');
     
          Route::patch('/{id}/status',  [ReceivingController::class, 'updateStatus'])
               ->middleware('module:receiving,can_edit');
     
          Route::delete('/{id}',        [ReceivingController::class, 'destroy'])
               ->middleware('module:receiving,can_delete');
     });
     // ── Acid Stock Conditions (master dropdown data) ──────────────────
     // Put this inside Route::middleware('auth:sanctum')->group(...)
     Route::prefix('acid-stock-conditions')->group(function () {
          Route::get('/',        [AcidStockConditionController::class, 'index']);
          Route::post('/',       [AcidStockConditionController::class, 'store']);
          Route::put('/{id}',    [AcidStockConditionController::class, 'update']);
          Route::delete('/{id}', [AcidStockConditionController::class, 'destroy']);
     });
     
     // ── Acid Testing ──────────────────────────────────────────────────
     Route::prefix('acid-testings')->middleware('module:acid-testing')->group(function () {
          Route::get('/',                  [AcidTestingController::class, 'index']);
          Route::get('/prefill/{lotNo}',   [AcidTestingController::class, 'prefill']);
          Route::get('/lot/{lotNo}',       [AcidTestingController::class, 'getByLot']);
          Route::get('/{id}',              [AcidTestingController::class, 'show']);
     
          Route::post('/',                 [AcidTestingController::class, 'store'])
               ->middleware('module:acid-testing,can_create');
     
          Route::put('/{id}',              [AcidTestingController::class, 'update'])
               ->middleware('module:acid-testing,can_edit');
     
          Route::patch('/{id}/status',     [AcidTestingController::class, 'updateStatus'])
               ->middleware('module:acid-testing,can_edit');
     
          Route::delete('/{id}',           [AcidTestingController::class, 'destroy'])
               ->middleware('module:acid-testing,can_delete');
     });

     Route::prefix('bbsu-batches')->middleware('module:bbsu')->group(function () {

          Route::get('/', [BbsuBatchController::class, 'index']);
      
          // reports or helper routes FIRST
          Route::get('/acid-summary/{lotNo}', [BbsuBatchController::class, 'acidSummaryByLot']);
          Route::get('/acid-test-lot-numbers', [BbsuBatchController::class, 'lotNumbers']);
      
          // show batch
          Route::get('/{id}', [BbsuBatchController::class, 'show']);
      
          // create
          Route::post('/', [BbsuBatchController::class, 'store'])
              ->middleware('module:bbsu,can_create');
      
          // update
          Route::put('/{id}', [BbsuBatchController::class, 'update'])
              ->middleware('module:bbsu,can_edit');
      
          // update status
          Route::patch('/{id}/status', [BbsuBatchController::class, 'updateStatus'])
              ->middleware('module:bbsu,can_edit');
      
          // delete
          Route::delete('/{id}', [BbsuBatchController::class, 'destroy'])
              ->middleware('module:bbsu,can_delete');
      
      });
    // ── Smelting ──────────────────────────────────────────────────
    // Route::prefix('smelting')->middleware('module:smelting')->group(function () { ... });

    // ── Refining ──────────────────────────────────────────────────
    // Route::prefix('refining')->middleware('module:refining')->group(function () { ... });

});
