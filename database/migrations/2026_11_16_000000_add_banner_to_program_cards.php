<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->string('banner_image')->nullable();
            $table->string('banner_title')->nullable();
            $table->string('banner_description', 300)->nullable();
            $table->string('banner_levels', 40)->nullable();
            $table->string('banner_timeline', 60)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->dropColumn(['banner_image', 'banner_title', 'banner_description', 'banner_levels', 'banner_timeline']);
        });
    }
};
