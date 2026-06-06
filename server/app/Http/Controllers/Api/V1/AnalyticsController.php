<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Middleware;

#[Middleware(['auth:api'])]
class AnalyticsController extends Controller
{
    use ApiResponser;

    private AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get Dashboard KPIs and Activity feeds.
     */
    public function dashboard(): JsonResponse
    {
        $stats = $this->analyticsService->getDashboardStats();
        return $this->success($stats);
    }

    /**
     * Get consolidated spend reports and vendor performance stats (Admin / Manager only).
     */
    public function spendSummary(Request $request): JsonResponse
    {
        $summary = $this->analyticsService->getSpendSummary($request->only(['start_date', 'end_date']));
        return $this->success($summary);
    }
}
