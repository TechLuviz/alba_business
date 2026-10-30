<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insight_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('insight_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('insight_sections')->insert([
            'eyebrow' => 'Insights',
            'heading' => 'Ideas that',
            'heading_accent' => 'Shape Whats Next',
            'description' => 'Explore expert perspectives, industry trends, and practical insights from the Alba community to help you learn, lead, and create a greater impact.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cards = [
            ['Industry Insights', 'In-depth analysis, emerging trends and expert perspectives from global business leaders.'],
            ['Alba Perspectives', 'Thought leadership, faculty insights and real-world lessons for todays challenges.'],
            ['Stories of Impact', 'Inspiring stories from our students, alumni and global community.'],
        ];
        foreach ($cards as $i => [$title, $desc]) {
            DB::table('insight_cards')->insert([
                'title' => $title, 'description' => $desc, 'sort_order' => $i + 1, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('insight_cards');
        Schema::dropIfExists('insight_sections');
    }
};
