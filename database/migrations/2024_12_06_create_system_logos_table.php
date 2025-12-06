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
        Schema::create('system_logos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default Logo');
            $table->string('logo_path')->nullable()->comment('Light mode logo');
            $table->string('logo_dark_path')->nullable()->comment('Dark mode logo');
            $table->string('favicon_path')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logos');
    }
};
