<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_principle_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('about_principles', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 20)->default('target');
            $table->string('label', 40);
            $table->string('heading');
            $table->string('heading_accent');
            $table->text('description');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('about_principle_sections')->insert([
            'eyebrow' => 'Our Guiding Principles',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $items = [
            ['target', 'Our Mission', 'Empower Individuals', 'to Build Meaningful Businesses', 'To provide practical business education, industry mentorship, and real-world exposure that help aspiring and existing entrepreneurs turn their ideas into sustainable businesses.'],
            ['eye', 'Our Vision', 'A Global Community', 'of Confident Business Builders', 'To be a leading business school that nurtures entrepreneurial talent and creates a community of innovators, business leaders, and change-makers across industries.'],
        ];
        foreach ($items as $n => [$icon, $label, $heading, $accent, $desc]) {
            DB::table('about_principles')->insert([
                'icon' => $icon, 'label' => $label, 'heading' => $heading, 'heading_accent' => $accent,
                'description' => $desc, 'sort_order' => $n + 1, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('about_principles');
        Schema::dropIfExists('about_principle_sections');
    }
};
