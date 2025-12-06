<?php

namespace App\Helpers;

use App\Models\SystemLogo;

class LogoHelper
{
    /**
     * Get the active system logo
     */
    public static function getActiveLogo()
    {
        return SystemLogo::getActive();
    }

    /**
     * Get the logo path for light mode
     */
    public static function getLightLogo()
    {
        $logo = self::getActiveLogo();
        return $logo?->logo_path ? asset('storage/' . $logo->logo_path) : null;
    }

    /**
     * Get the logo path for dark mode
     */
    public static function getDarkLogo()
    {
        $logo = self::getActiveLogo();
        return $logo?->logo_dark_path ? asset('storage/' . $logo->logo_dark_path) : null;
    }

    /**
     * Get favicon path
     */
    public static function getFavicon()
    {
        $logo = self::getActiveLogo();
        return $logo?->favicon_path ? asset('storage/' . $logo->favicon_path) : null;
    }

    /**
     * Check if logo exists
     */
    public static function hasLogo(): bool
    {
        return self::getActiveLogo() !== null;
    }
}
