<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            for ($i = 1; $i <= 4; $i++) {
                $table->string("card{$i}_title")->nullable();
                $table->string("card{$i}_image")->nullable();
            }
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('problem_sections')->insert([
            'eyebrow' => 'The Problem',
            'heading' => 'Building a business is exciting.',
            'heading_accent' => 'Building it right is harder.',
            'description' => 'Great ideas are everywhere. But turning them into sustainable, impactful businesses comes with real challenges, from strategy and funding to people, competition and constant change.',
            'card1_title' => 'Standing out in market',
            'card2_title' => 'Turning ideas into plan',
            'card3_title' => 'Building the right team',
            'card4_title' => 'Funding and resources',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_sections');
    }
};
