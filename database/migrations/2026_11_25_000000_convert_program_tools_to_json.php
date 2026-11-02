<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** `tools` used to hold one tool per line; it now holds JSON [{name, icon}, ...]. */
    public function up(): void
    {
        $icons = ['box', 'document', 'shield', 'megaphone', 'user', 'chart', 'laptop', 'globe'];
        foreach (DB::table('program_cards')->whereNotNull('tools')->get(['id', 'tools']) as $row) {
            if ($row->tools === '' || str_starts_with(ltrim($row->tools), '[')) {
                continue;
            }
            $names = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $row->tools)), 'strlen'));
            $items = [];
            foreach ($names as $i => $n) {
                $items[] = ['name' => $n, 'icon' => $icons[$i % count($icons)]];
            }
            DB::table('program_cards')->where('id', $row->id)->update(['tools' => json_encode($items)]);
        }
    }

    public function down(): void
    {
        foreach (DB::table('program_cards')->whereNotNull('tools')->get(['id', 'tools']) as $row) {
            $items = json_decode($row->tools, true);
            if (is_array($items)) {
                DB::table('program_cards')->where('id', $row->id)->update(['tools' => implode("\n", array_column($items, 'name'))]);
            }
        }
    }
};