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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // 'course', 'leaderboard', 'achievement', 'reward', 'community', 'assignment'
            $table->string('title');
            $table->text('message');
            $table->string('icon')->default('🔔');
            $table->json('data')->nullable(); // Store additional data like course_id, level_rank, etc.
            $table->timestamp('read_at')->nullable(); // NULL = unread
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
