<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Three fixed feature columns become one free-length "features" field (one feature per line). */
    public function up(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->text('features')->nullable()->after('title');
        });

        foreach (DB::table('program_cards')->get() as $card) {
            $lines = array_filter([$card->feature_1, $card->feature_2, $card->feature_3]);
            DB::table('program_cards')->where('id', $card->id)->update(['features' => implode("\n", $lines)]);
        }

        Schema::table('program_cards', function (Blueprint $table) {
            $table->dropColumn(['feature_1', 'feature_2', 'feature_3']);
        });
    }

    public function down(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->string('feature_1')->nullable();
            $table->string('feature_2')->nullable();
            $table->string('feature_3')->nullable();
        });

        foreach (DB::table('program_cards')->get() as $card) {
            $f = array_pad(explode("\n", (string) $card->features), 3, null);
            DB::table('program_cards')->where('id', $card->id)->update(['feature_1' => $f[0], 'feature_2' => $f[1], 'feature_3' => $f[2]]);
        }

        Schema::table('program_cards', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};
