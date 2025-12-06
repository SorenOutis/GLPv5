<?php

namespace App\Http\Controllers;

use App\Models\SystemLogo;
use Illuminate\Http\JsonResponse;

class LogoController extends Controller
{
    /**
     * Get active system logo configuration
     */
    public function getActive(): JsonResponse
    {
        $logo = SystemLogo::where('is_active', true)->first();

        if (!$logo) {
            return response()->json([
                'hasLogo' => false,
                'logo' => null,
            ]);
        }

        return response()->json([
            'hasLogo' => true,
            'logo' => [
                'name' => $logo->name,
                'lightPath' => $logo->logo_path ? asset('storage/' . $logo->logo_path) : null,
                'darkPath' => $logo->logo_dark_path ? asset('storage/' . $logo->logo_dark_path) : null,
                'faviconPath' => $logo->favicon_path ? asset('storage/' . $logo->favicon_path) : null,
            ],
        ]);
    }
}
