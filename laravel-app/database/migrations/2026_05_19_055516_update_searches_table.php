<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('searches', function (Blueprint $table) {

            $table->index('user_id');
            $table->index('query');
            $table->index('created_at');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->integer('duration')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('searches', function (Blueprint $table) {

            $table->dropIndex(['user_id']);
            $table->dropIndex(['query']);
            $table->dropIndex(['created_at']);

            $table->dropColumn([
                'started_at',
                'completed_at',
                'duration'
            ]);
        });
    }
};