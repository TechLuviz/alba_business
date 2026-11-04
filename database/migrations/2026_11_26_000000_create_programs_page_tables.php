<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_page_banners', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('program_points', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('program_page_banners')->insert([
            'title' => "The Right Knowledge to Build What's Next.",
            'description' => 'ALBA HUB provides practical business education and essential skills to help you build, grow, and lead.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([
            ['Practical Learning', 'Real business applications'],
            ['Expert Mentorship', 'Learn from business builders'],
            ['Structured Growth', 'From foundation to scale'],
            ['Recognized Completion', 'Certificate for every program'],
        ] as $i => [$title, $desc]) {
            DB::table('program_points')->insert([
                'title' => $title, 'description' => $desc, 'sort_order' => $i + 1,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // The "Programs" menu item now opens the Programs page instead of scrolling the home page.
        DB::table('nav_items')->where('label', 'Programs')->where('url', '#programs')->update(['url' => '/programs']);
    }

    public function down(): void
    {
        DB::table('nav_items')->where('label', 'Programs')->where('url', '/programs')->update(['url' => '#programs']);
        Schema::dropIfExists('program_points');
        Schema::dropIfExists('program_page_banners');
    }
};
