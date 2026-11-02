<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->json('tool_items')->nullable();
        });

        // carry the old one-per-line tools over as items without an icon
        foreach (DB::table('program_cards')->whereNotNull('tools')->get(['id', 'tools']) as $row) {
            $items = collect(preg_split('/\r\n|\r|\n/', (string) $row->tools))
                ->map('trim')->filter()->map(fn ($n) => ['name' => $n, 'icon' => null])->values()->all();
            DB::table('program_cards')->where('id', $row->id)->update(['tool_items' => json_encode($items)]);
        }

        Schema::table('program_cards', function (Blueprint $table) {
            $table->dropColumn('tools');
        });
    }

    public function down(): void
    {
        Schema::table('program_cards', function (Blueprint $table) {
            $table->text('tools')->nullable();
            $table->dropColumn('tool_items');
        });
    }
};