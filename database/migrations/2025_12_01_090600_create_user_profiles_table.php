<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_xp')->default(0);
            $table->integer('level')->default(1);
            $table->integer('current_level_xp')->default(0);
            $table->integer('xp_for_next_level')->default(500);
            $table->integer('streak_days')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->string('rank_title')->default('Beginner');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
