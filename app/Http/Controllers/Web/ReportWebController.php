<?php
// ─────────────────────────────────────────────────────────────────
// app/Http/Controllers/Web/ReportWebController.php
//
// Single web controller for ALL report views.
// Only serves Blade views — all data comes from the API via JS.
// Add new report view methods here as new sections are built.
// ─────────────────────────────────────────────────────────────────
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ReportWebController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // GET /admin/reports/material-inward
    // ─────────────────────────────────────────────────────────────
    public function materialInward()
    {
        return view('admin.reports.material_inward');
    }

    // ─────────────────────────────────────────────────────────────
    // GET /admin/reports/acid-test-status
    // ─────────────────────────────────────────────────────────────
    public function acidTestStatus()
    {
        return view('admin.reports.acid_test_status');
    }

}