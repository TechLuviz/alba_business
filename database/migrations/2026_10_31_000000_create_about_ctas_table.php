<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_ctas', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading_line_1')->nullable();
            $table->string('heading_line_2')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            $table->string('image_main')->nullable();
            $table->string('image_small')->nullable();
            $table->string('image_caption')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('about_ctas')->insert([
            'eyebrow' => 'Ready for what\'s next?',
            'heading_line_1' => 'Let\'s Build',
            'heading_line_2' => 'What Comes',
            'heading_accent' => 'Next.',
            'description' => 'Whether you\'re starting an idea, growing a business, or preparing to lead what comes next, let\'s start the conversation.',
            'image_caption' => 'Global Cohort',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('about_ctas');
    }
};
