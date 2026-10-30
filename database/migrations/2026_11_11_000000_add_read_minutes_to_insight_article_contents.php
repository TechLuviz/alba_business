<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insight_article_contents', function (Blueprint $table) {
            $table->unsignedTinyInteger('read_minutes')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('insight_article_contents', function (Blueprint $table) {
            $table->dropColumn('read_minutes');
        });
    }
};
