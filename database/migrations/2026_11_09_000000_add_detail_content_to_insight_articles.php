<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insight_articles', function (Blueprint $table) {
            if (! Schema::hasColumn('insight_articles', 'author')) {
                $table->string('author')->nullable()->after('title');
            }
        });

        Schema::create('insight_article_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insight_article_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insight_article_contents');
        Schema::table('insight_articles', function (Blueprint $table) {
            $table->dropColumn('author');
        });
    }
};
