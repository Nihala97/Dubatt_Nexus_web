<?php
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

    public function acidTestStatus()
    {
        return view('admin.reports.acid_test_status');
    }
    public function bbsu()
    {
        return view('admin.reports.bbsu_dashboard');
    }
    public function smeltingDashboard()
    {
        return view('admin.reports.smelting_dashboard');
    }
    public function refiningDashboard()
    {
        return view('admin.reports.refining_dashboard');
    }
    public function userActivity()
    {
        return view('admin.reports.user_activity');
    }

}