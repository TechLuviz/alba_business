<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_stories', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('paragraph_1')->nullable();
            $table->text('paragraph_2')->nullable();
            $table->text('quote')->nullable();
            $table->string('person_name')->nullable();
            $table->string('person_role')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('about_stories')->insert([
            'eyebrow' => 'Our Story',
            'heading' => 'Building a New Generation of',
            'heading_accent' => 'Business Builders',
            'paragraph_1' => 'Albatech Business School was founded with a clear purpose - to create a learning ecosystem where Aspiration meets Real-world Business. We believe in practical education, industry mentorship and a strong community that helps individuals turn ideas into meaningful and sustainable businesses.',
            'paragraph_2' => 'Guided by a vision to empower future leaders and innovators, Albatech is more than a business school - it\'s a platform for people who dare to build, grow and create impact.',
            'quote' => 'I started Albatech to make business education practical, accessible and truly impactful - a place where ideas turn into real opportunities and people grow into confident business builders.',
            'person_name' => 'Mithun',
            'person_role' => 'Visionary',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('about_stories');
    }
};
