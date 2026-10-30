<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insight_feature_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('insight_feature_cards', function (Blueprint $table) {
            $table->id();
            $table->string('category', 40);
            $table->string('title');
            $table->date('published_on');
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('insight_feature_sections')->insert([
            'eyebrow' => 'Featured Insights',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cards = [
            ['Productivity', '2025-07-03', 'Healthy Habits for a More Productive You'],
            ['Strategy', '2025-05-28', 'From Side Hustle to Scalable Business - A Practical Roadmap'],
            ['Leadership', '2025-04-12', 'Executive Presence in Modern Teams'],
            ['Growth', '2025-03-20', 'The First 100 Customers - A Practical Growth Guide'],
        ];
        foreach ($cards as $i => [$category, $date, $title]) {
            DB::table('insight_feature_cards')->insert([
                'category' => $category, 'title' => $title, 'published_on' => $date, 'sort_order' => $i + 1,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('insight_feature_cards');
        Schema::dropIfExists('insight_feature_sections');
    }
};
