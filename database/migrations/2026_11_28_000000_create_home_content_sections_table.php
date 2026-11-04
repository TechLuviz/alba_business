<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_content_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('image_position', 10)->default('right');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Starts hidden and empty: the section appears on the home page once it is filled in and switched on.
        DB::table('home_content_sections')->insert([
            'image_position' => 'right', 'is_active' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_content_sections');
    }
};
