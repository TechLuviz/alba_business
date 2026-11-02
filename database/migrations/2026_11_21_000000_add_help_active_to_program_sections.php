<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_sections', function (Blueprint $table) {
            $table->boolean('help_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('program_sections', function (Blueprint $table) {
            $table->dropColumn('help_active');
        });
    }
};
