<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLogo extends Model
{
    protected $table = 'system_logos';

    protected $fillable = [
        'name',
        'logo_path',
        'logo_dark_path',
        'favicon_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active logo
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Deactivate all logos and activate this one
     */
    public function activate()
    {
        self::query()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
}
