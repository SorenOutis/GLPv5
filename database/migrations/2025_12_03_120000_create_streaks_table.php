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
        Schema::create('streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('current_streak')->default(0); // Current consecutive days
            $table->integer('longest_streak')->default(0); // Longest streak achieved
            $table->date('last_login_date')->nullable(); // Last date user logged in
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            
            // Unique constraint to ensure one streak record per user
            $table->unique('user_id');
            
            // Index for querying
            $table->index('current_streak');
            $table->index('longest_streak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streaks');
    }
};
