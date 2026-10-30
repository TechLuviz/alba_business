<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insight_cta_cards', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('insight_cta_cards')->insert([
            'heading' => 'Build Your Business',
            'heading_accent' => 'with Expert Guidance',
            'description' => 'Gain real-world skills, strategic insights, and hands-on learning to turn your ideas into impactful businesses.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('insight_cta_cards');
    }
};
