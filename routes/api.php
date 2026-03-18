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
          Route::get('/',        [SupplierBatchController::class, 'index']);
          Route::get('/{id}',    [SupplierBatchController::class, 'show']);
          Route::post('/',       [SupplierBatchController::class, 'store']);
          Route::put('/{id}',    [SupplierBatchController::class, 'update']);
          Route::delete('/{id}', [SupplierBatchController::class, 'destroy']);
     });
     
     // ── Materials (reference data) ────────────────────────────────────
     Route::prefix('materials')->group(function () {
          Route::get('/',        [MaterialBatchController::class, 'index']);
          Route::get('/{id}',    [MaterialBatchController::class, 'show']);
          Route::post('/',       [MaterialBatchController::class, 'store']);
          Route::put('/{id}',    [MaterialBatchController::class, 'update']);
          Route::delete('/{id}', [MaterialBatchController::class, 'destroy']);
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
     // ── Acid Testing ──────────────────────────────────────────────
     // IMPORTANT: all static/named routes BEFORE the /{id} wildcard
     Route::prefix('acid-testings')->middleware('module:acid-testing')->group(function () {
          Route::get('/', [AcidTestingController::class, 'index']);
          Route::get('/stock-conditions', [AcidTestingController::class, 'stockConditions']);
          Route::get('/available-lots', [AcidTestingController::class, 'availableLots']);
          Route::get('/lot-check/{lotNo}', [AcidTestingController::class, 'lotCheck']);
          Route::get('/{id}', [AcidTestingController::class, 'show']);
          Route::post('/', [AcidTestingController::class, 'store'])
               ->middleware('module:acid-testing,can_create');
          Route::put('/{id}', [AcidTestingController::class, 'update'])
               ->middleware('module:acid-testing,can_edit');
          Route::patch('/{id}/status', [AcidTestingController::class, 'updateStatus'])
               ->middleware('module:acid-testing,can_edit');
          Route::delete('/{id}', [AcidTestingController::class, 'destroy'])
               ->middleware('module:acid-testing,can_delete');
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

     Route::prefix('bbsu-batches')->middleware('module:bbsu')->group(function () {

          Route::get('/', [BbsuBatchController::class, 'index']);
      
          // reports or helper routes FIRST
          Route::get('/acid-summary/{lotNo}', [BbsuBatchController::class, 'acidSummaryByLot']);
          Route::get('/acid-test-lot-numbers', [BbsuBatchController::class, 'lotNumbers']);
          Route::post('/{id}/submit', [BbsuBatchController::class, 'submit']);
      
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
     Route::prefix('smelting-batches')->middleware('module:smelting')->group(function () {
          Route::get('/',                      [SmeltingBatchController::class, 'index']);
          Route::get('/generate-batch-no',     [SmeltingBatchController::class, 'generateBatchNo']);
          Route::get('/bbsu-lots/{materialId}',[SmeltingBatchController::class, 'getBbsuLots']);
          Route::post('/',                     [SmeltingBatchController::class, 'store']);
          Route::get('/{id}',                  [SmeltingBatchController::class, 'show']);
          Route::put('/{id}',                  [SmeltingBatchController::class, 'update']);
          Route::delete('/{id}',               [SmeltingBatchController::class, 'destroy']);
          Route::post('/{id}/autosave',        [SmeltingBatchController::class, 'autosave']);
          Route::post('/{id}/submit',          [SmeltingBatchController::class, 'submit']);
          Route::patch('/{id}/status',         [SmeltingBatchController::class, 'updateStatus'])->middleware('module:smeltings,can_edit');
     });

     // ── Refining ──────────────────────────────────────────────────
     Route::prefix('refining')->middleware('module:refining')->group(function () {

          Route::get('/generate-batch-no', [RefiningBatchController::class, 'generateBatchNo']);
          Route::get('/smelting-lots/{materialId}',[RefiningBatchController::class, 'getSmeltingLots']);
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
          Route::get('/',        [MaterialBatchController::class, 'index']);
          Route::post('/',       [MaterialBatchController::class, 'store']);
          Route::get('/{id}/stock', [\App\Http\Controllers\Api\MaterialController::class, 'getStock']);
          Route::put('/{id}',    [MaterialBatchController::class, 'update']);
          Route::delete('/{id}', [MaterialBatchController::class, 'destroy']);
     });
     Route::prefix('supplier')->group(function () {
          Route::get('/',        [SupplierBatchController::class, 'index']);
          Route::post('/',       [SupplierBatchController::class, 'store']);
          Route::put('/{id}',    [SupplierBatchController::class, 'update']);
          Route::delete('/{id}', [SupplierBatchController::class, 'destroy']);
     });
});

