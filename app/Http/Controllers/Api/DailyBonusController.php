<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DailyLoginBonusService;
use Illuminate\Http\JsonResponse;

class DailyBonusController extends Controller
{
    /**
     * Claim daily login bonus
     */
    public function claim(DailyLoginBonusService $service): JsonResponse
    {
        $user = auth()->user();
        $result = $service->awardDailyBonus($user);

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
