<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insight_article_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('insight_articles', function (Blueprint $table) {
            $table->id();
            $table->string('category', 40);
            $table->string('title');
            $table->text('description');
            $table->date('published_on');
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('insight_article_sections')->insert([
            'eyebrow' => 'Thought Leadership',
            'heading' => 'Insights to Build, Scale & Lead',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $articles = [
            ['Productivity', '2025-07-03', 'Healthy Habits for a More Productive You', 'Actionable routines and focus frameworks to optimize your executive output and avoid cognitive fatigue.'],
            ['Strategy', '2025-05-28', 'From Side Hustle to Scalable Business - A Practical Roadmap', 'The critical milestones for transitioning from early validation to reliable operational revenue and team growth.'],
            ['Marketing', '2025-08-17', 'The Power of Content Marketing', 'How consistent, high-value editorial content attracts, engages, and converts discerning business clients.'],
        ];
        foreach ($articles as $i => [$category, $date, $title, $desc]) {
            DB::table('insight_articles')->insert([
                'category' => $category, 'title' => $title, 'description' => $desc, 'published_on' => $date,
                'sort_order' => $i + 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('insight_articles');
        Schema::dropIfExists('insight_article_sections');
    }
};
