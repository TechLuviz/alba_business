<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_banners', function (Blueprint $table) {
            $table->string('brand_logo')->nullable();
            $table->string('background_type')->default('slides'); // slides | video
            $table->string('background_video')->nullable();
            $table->unsignedTinyInteger('autoplay_interval')->default(6); // seconds
        });

        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('heading_line_1');
            $table->string('heading_line_2')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
        Schema::table('hero_banners', function (Blueprint $table) {
            $table->dropColumn(['brand_logo', 'background_type', 'background_video', 'autoplay_interval']);
        });
    }
};