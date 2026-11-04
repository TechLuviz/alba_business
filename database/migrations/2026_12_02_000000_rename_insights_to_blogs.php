<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menu item (the URL /insights stays the same so existing links keep working).
        DB::table('nav_items')->where('url', '/insights')->where('label', 'Insights')->update(['label' => 'Blogs', 'updated_at' => now()]);
        // Home page section label.
        DB::table('insight_sections')->where('eyebrow', 'Insights')->update(['eyebrow' => 'Blogs', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('nav_items')->where('url', '/insights')->where('label', 'Blogs')->update(['label' => 'Insights', 'updated_at' => now()]);
        DB::table('insight_sections')->where('eyebrow', 'Blogs')->update(['eyebrow' => 'Insights', 'updated_at' => now()]);
    }
};
