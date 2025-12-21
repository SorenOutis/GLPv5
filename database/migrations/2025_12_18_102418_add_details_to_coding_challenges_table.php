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
        Schema::table('coding_challenges', function (Blueprint $table) {
            $table->text('problem_statement')->nullable()->after('description');
            $table->json('requirements')->nullable()->after('problem_statement');
            $table->text('example_input')->nullable()->after('requirements');
            $table->text('example_output')->nullable()->after('example_input');
            $table->json('tips')->nullable()->after('example_output');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coding_challenges', function (Blueprint $table) {
            $table->dropColumn([
                'problem_statement',
                'requirements',
                'example_input',
                'example_output',
                'tips',
            ]);
        });
    }
};
