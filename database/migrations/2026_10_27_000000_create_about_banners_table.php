<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_banners', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('heading_line_1')->nullable();
            $table->string('heading_line_2')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('about_banners')->insert([
            'heading_line_1' => 'Turning Ambition',
            'heading_line_2' => 'Into Real Impact',
            'description' => 'We empower ambitious people with practical knowledge, real-world skills, and the confidence to build, grow, and lead.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // The "About" menu item now opens the About page instead of scrolling the home page.
        DB::table('nav_items')->where('label', 'About')->where('url', '#about')->update(['url' => '/about']);
    }

    public function down(): void
    {
        DB::table('nav_items')->where('label', 'About')->where('url', '/about')->update(['url' => '#about']);
        Schema::dropIfExists('about_banners');
    }
};
