<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('searches', function (Blueprint $table) {
            if (!Schema::hasColumn('searches', 'is_paused')) {
                $table->boolean('is_paused')->default(false)->after('is_stopped');
            }
        });
    }

    public function down(): void
    {
        Schema::table('searches', function (Blueprint $table) {
            $table->dropColumn('is_paused');
        });
    }
};