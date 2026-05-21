<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {

            $table->index('search_id');
            $table->index('phone');
            $table->index('created_at');

          
            
            $table->integer('reviews')->nullable();
            $table->string('city')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {

            $table->dropIndex(['search_id']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['created_at']);

            $table->dropColumn([
                'email',
                'rating',
                'reviews',
                'city'
            ]);
        });
    }
};