<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
        });

        $used = [];
        foreach (DB::table('program_cards')->orderBy('id')->get(['id', 'title']) as $row) {
            $base = Str::slug($row->title) ?: 'program';
            $slug = $base;
            for ($n = 2; in_array($slug, $used, true); $n++) {
                $slug = "{$base}-{$n}";
            }
            $used[] = $slug;
            DB::table('program_cards')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
