<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_text_sections', function (Blueprint $table) {
            $table->id();
            $table->string('heading_line_1')->nullable();
            $table->string('heading_line_2')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_text_sections');
    }
};
