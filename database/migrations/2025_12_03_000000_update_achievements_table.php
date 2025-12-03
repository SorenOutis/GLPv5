<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            // Rename title to name if it exists
            if (Schema::hasColumn('achievements', 'title')) {
                $table->renameColumn('title', 'name');
            }
            
            // Drop criteria if it exists
            if (Schema::hasColumn('achievements', 'criteria')) {
                $table->dropColumn('criteria');
            }
            
            // Add new columns if they don't exist
            if (!Schema::hasColumn('achievements', 'category')) {
                $table->string('category')->default('General');
            }
            
            if (!Schema::hasColumn('achievements', 'difficulty')) {
                $table->enum('difficulty', ['Easy', 'Medium', 'Hard', 'Legendary'])->default('Medium');
            }
            
            // Rename xp_reward if needed
            if (Schema::hasColumn('achievements', 'xp_reward') && !Schema::hasColumn('achievements', 'xp_reward')) {
                // It already exists, do nothing
            }
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            if (Schema::hasColumn('achievements', 'name')) {
                $table->renameColumn('name', 'title');
            }
            
            if (Schema::hasColumn('achievements', 'category')) {
                $table->dropColumn('category');
            }
            
            if (Schema::hasColumn('achievements', 'difficulty')) {
                $table->dropColumn('difficulty');
            }
        });
    }
};
