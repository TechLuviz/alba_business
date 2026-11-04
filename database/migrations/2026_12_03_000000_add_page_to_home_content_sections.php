<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_content_sections', function (Blueprint $table) {
            $table->string('page', 20)->default('home')->after('id');
        });

        // The About page gets its own content section (hidden until it is filled in and switched on).
        DB::table('home_content_sections')->insert([
            'page' => 'about', 'image_position' => 'right', 'is_active' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('home_content_sections')->where('page', 'about')->delete();
        Schema::table('home_content_sections', function (Blueprint $table) {
            $table->dropColumn('page');
        });
    }
};
