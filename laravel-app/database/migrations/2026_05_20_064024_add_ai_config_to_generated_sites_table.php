<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run: php artisan migrate
     * File name: 2026_05_20_XXXXXX_add_ai_config_to_generated_sites_table.php
     *
     * BEFORE RUNNING: check your generated_sites table in TablePlus/DB viewer.
     * If any of these columns already exist, remove them from this migration.
     */
    public function up(): void
    {
        Schema::table('generated_sites', function (Blueprint $table) {

            // Full AI-generated config: theme, layout, all section content
            if (!Schema::hasColumn('generated_sites', 'ai_config')) {
                $table->json('ai_config')->nullable()->after('slug');
            }

            // pending | generating | done | failed
            if (!Schema::hasColumn('generated_sites', 'generation_status')) {
                $table->string('generation_status', 20)->default('pending')->after('ai_config');
            }

            // When AI generation completed
            if (!Schema::hasColumn('generated_sites', 'generated_at')) {
                $table->timestamp('generated_at')->nullable()->after('generation_status');
            }

        });
    }

    public function down(): void
    {
        Schema::table('generated_sites', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('generated_sites', 'ai_config')          ? 'ai_config'          : null,
                Schema::hasColumn('generated_sites', 'generation_status')  ? 'generation_status'  : null,
                Schema::hasColumn('generated_sites', 'generated_at')       ? 'generated_at'       : null,
            ]));
        });
    }
};