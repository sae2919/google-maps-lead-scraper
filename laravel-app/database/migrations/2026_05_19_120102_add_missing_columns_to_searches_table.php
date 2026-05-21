<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('searches', function (Blueprint $table) {

            $table->string('status')
                  ->default('pending');

            $table->integer('processed_count')
                  ->default(0);

        });
    }

    public function down(): void
    {
        Schema::table('searches', function (Blueprint $table) {

            $table->dropColumn([
                'status',
                'processed_count'
            ]);

        });
    }
};