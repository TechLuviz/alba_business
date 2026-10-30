<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audience_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow', 100)->nullable()->default('OUR EXPECTED AUDIENCE');
            $table->string('heading', 255)->default('Who Is Alba Business School For?');
            $table->string('highlight_text', 100)->nullable()->default('Alba Business School');
            $table->text('description')->nullable();
            $table->string('heading_align', 20)->default('left');
            $table->string('bg_color', 30)->default('#FBF9F5');
            $table->string('heading_color', 30)->default('#0A1628');
            $table->string('highlight_color', 30)->default('#C8914A');
            $table->string('description_color', 30)->default('#525866');
            $table->string('accent_line_color', 30)->default('#C8914A');
            $table->unsignedInteger('padding_top')->default(90);
            $table->unsignedInteger('padding_bottom')->default(90);
            $table->boolean('autoplay')->default(true);
            $table->unsignedInteger('autoplay_interval')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('audience_sections')->insert([
            'eyebrow' => 'OUR EXPECTED AUDIENCE',
            'heading' => 'Who Is Alba Business School For?',
            'highlight_text' => 'Alba Business School',
            'description' => 'Built for ambitious minds ready to learn, build, lead, and create meaningful impact.',
            'heading_align' => 'left',
            'bg_color' => '#FBF9F5',
            'heading_color' => '#0A1628',
            'highlight_color' => '#C8914A',
            'description_color' => '#525866',
            'accent_line_color' => '#C8914A',
            'padding_top' => 90,
            'padding_bottom' => 90,
            'autoplay' => true,
            'autoplay_interval' => 5,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('audience_sections');
    }
};
