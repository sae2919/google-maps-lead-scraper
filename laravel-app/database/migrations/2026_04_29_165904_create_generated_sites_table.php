<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_sites', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('business_name');
            $table->string('category');
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->longText('html_content');
            $table->json('pexels_images')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_sites');
    }
};