<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsApiController extends Controller
{
    public function index(Request $request, ReportingService $reportingService)
    {
        $shopId = Auth::user()->shop_id;
        if (!$shopId) {
            return response()->json(['error' => 'No shop assigned'], 403);
        }

        $analytics = $reportingService->getDashboardAnalytics($shopId);

        return response()->json($analytics);
    }
}
