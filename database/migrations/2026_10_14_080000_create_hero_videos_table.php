<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_videos', function (Blueprint $table) {
            $table->id();
            $table->string('video');
            $table->string('heading_line_1')->nullable();
            $table->string('heading_line_2')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Video now lives in hero_videos; hero_banners keeps brand + fallback text + carousel interval.
        Schema::table('hero_banners', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'background_video', 'background_image']);
        });
    }

    public function down(): void
    {
        Schema::table('hero_banners', function (Blueprint $table) {
            $table->string('background_type')->default('slides');
            $table->string('background_video')->nullable();
            $table->string('background_image')->nullable();
        });
        Schema::dropIfExists('hero_videos');
    }
};