<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * GET /api/dashboard/summary
     *
     * Draft / submitted / total counts for all plant modules (any authenticated user).
     */
    public function summary(): JsonResponse
    {
        $modules = $this->dashboardService->summary();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'modules' => $modules,
            ],
        ]);
    }
}
