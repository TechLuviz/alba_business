<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Menu: Home, About Us, Programs, Insights, Contact (the header and footer read this table in sort_order). */
    private const MENU = [
        ['/', 'Home'],
        ['/about', 'About Us'],
        ['/programs', 'Programs'],
        ['/insights', 'Insights'],
        ['/contact', 'Contact'],
    ];

    public function up(): void
    {
        foreach (self::MENU as $i => [$url, $label]) {
            $row = DB::table('nav_items')->where('url', $url)->first();
            $values = ['label' => $label, 'sort_order' => $i + 1, 'updated_at' => now()];

            if ($row) {
                DB::table('nav_items')->where('id', $row->id)->update($values);
            } else {
                DB::table('nav_items')->insert($values + ['url' => $url, 'is_active' => true, 'created_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        DB::table('nav_items')->where('url', '/')->delete();
        DB::table('nav_items')->where('url', '/about')->update(['label' => 'About', 'sort_order' => 1]);
        DB::table('nav_items')->where('url', '/programs')->update(['sort_order' => 2]);
        DB::table('nav_items')->where('url', '/insights')->update(['label' => 'Insight', 'sort_order' => 3]);
        DB::table('nav_items')->where('url', '/contact')->update(['sort_order' => 4]);
    }
};
