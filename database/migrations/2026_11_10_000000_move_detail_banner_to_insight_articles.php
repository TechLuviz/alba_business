<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insight_articles', function (Blueprint $table) {
            $table->string('banner_image')->nullable()->after('image');
        });

        // The single shared banner becomes each article's own banner (an independent copy, so it can be replaced per article).
        $shared = DB::table('insight_detail_banners')->orderByDesc('id')->value('image');
        if ($shared && File::exists(public_path($shared))) {
            foreach (DB::table('insight_articles')->pluck('id') as $id) {
                $copy = dirname($shared) . '/insights-detail-banner_' . uniqid() . '.' . pathinfo($shared, PATHINFO_EXTENSION);
                File::copy(public_path($shared), public_path($copy));
                DB::table('insight_articles')->where('id', $id)->update(['banner_image' => $copy]);
            }
            File::delete(public_path($shared));
        }

        Schema::dropIfExists('insight_detail_banners');
    }

    public function down(): void
    {
        Schema::create('insight_detail_banners', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('insight_articles', function (Blueprint $table) {
            $table->dropColumn('banner_image');
        });
    }
};
