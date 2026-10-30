<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('primary_image')->nullable();
            $table->string('primary_alt')->nullable();
            $table->string('secondary_image')->nullable();
            $table->string('secondary_alt')->nullable();
            $table->string('image_position', 10)->default('left');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('about_sections')->insert([
            'eyebrow' => 'About Alba Business School',
            'heading' => 'Empowering Ambitious People to Build a Better Business Future.',
            'description' => 'At Alba Business School, we equip entrepreneurs, business owners, and ambitious professionals with real-world learning, practical tools, and a global community to build, grow and scale businesses with purpose.',
            'button_text' => 'Our Story',
            'button_url' => '#',
            'image_position' => 'left',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('about_sections');
    }
};
