<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('streaks', function (Blueprint $table) {
            // Store login dates as JSON for heatmap visualization
            // Format: ["2024-01-15", "2024-01-16", "2024-01-17", ...]
            $table->json('login_dates')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streaks', function (Blueprint $table) {
            $table->dropColumn('login_dates');
        });
    }
};
