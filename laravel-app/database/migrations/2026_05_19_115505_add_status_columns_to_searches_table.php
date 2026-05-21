<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('searches', function (Blueprint $table) {

            $table->boolean('paused')
                  ->default(false);

            $table->boolean('stopped')
                  ->default(false);

        });
    }

    public function down(): void
    {
        Schema::table('searches', function (Blueprint $table) {

            $table->dropColumn([
                'paused',
                'stopped'
            ]);

        });
    }
};