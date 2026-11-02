<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->string('help_image')->nullable();
            $table->string('help_heading')->nullable();
            $table->string('help_description', 200)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->dropColumn(['help_image', 'help_heading', 'help_description']);
        });
    }
};
