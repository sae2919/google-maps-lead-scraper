<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This adds the column to your leads table.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Using JSON is smart as it allows us to store the headline, 
            // about, and services in one structured field.
            $table->json('ai_metadata')->nullable()->after('rating'); 
        });
    }

    /**
     * Reverse the migrations.
     * This removes the column if you run 'php artisan migrate:rollback'.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('ai_metadata');
        });
    }
};