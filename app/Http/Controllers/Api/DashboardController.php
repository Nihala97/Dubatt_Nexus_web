<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * GET /api/dashboard/summary
     *
     * Optional query params:
     *   ?from=2025-01-01   ISO date — range start (inclusive)
     *   ?to=2025-12-31     ISO date — range end   (inclusive)
     *   ?month=5           integer 1–12
     *   ?year=2025         integer
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'from'  => ['nullable', 'date_format:Y-m-d'],
            'to'    => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year'  => ['nullable', 'integer', 'digits:4', 'max:' . now()->year],
        ]);

        $filters = $request->only(['from', 'to', 'month', 'year']);

        $modules = $this->dashboardService->summary($filters);

        return response()->json([
            'status' => 'ok',
            'data'   => [
                'modules' => $modules,
            ],
        ]);
    }
}