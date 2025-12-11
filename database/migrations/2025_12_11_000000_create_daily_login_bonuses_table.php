<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_login_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('bonus_date');
            $table->integer('xp_awarded')->default(20);
            $table->timestamp('claimed_at')->useCurrent();
            
            // Composite unique constraint: each user can only claim once per day
            $table->unique(['user_id', 'bonus_date']);
            
            // Index for efficient queries
            $table->index('user_id');
            $table->index('bonus_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_login_bonuses');
    }
};
