<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_banners', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('heading_line_1')->nullable();
            $table->string('heading_line_2')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('contact_banners')->insert([
            'heading_line_1' => 'Ready to take the',
            'heading_line_2' => 'Next step?',
            'description' => 'Your next step starts with a conversation',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // The "Contact" menu item now opens the Contact page instead of scrolling to the footer.
        DB::table('nav_items')->where('label', 'Contact')->where('url', '#contact')->update(['url' => '/contact']);
    }

    public function down(): void
    {
        DB::table('nav_items')->where('label', 'Contact')->where('url', '/contact')->update(['url' => '#contact']);
        Schema::dropIfExists('contact_banners');
    }
};
