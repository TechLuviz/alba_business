<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reel_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reel_cards', function (Blueprint $table) {
            $table->id();
            $table->string('tag', 40);
            $table->string('title');
            $table->string('url', 255);
            $table->string('video')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('reel_sections')->insert([
            'eyebrow' => 'Insights in Motion',
            'heading' => 'Real Perspectives',
            'heading_accent' => 'for Real Growth',
            'description' => 'Watch expert talks, success stories, and practical insights to help you build, lead and grow your business with confidence.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('reel_cards');
        Schema::dropIfExists('reel_sections');
    }
};
