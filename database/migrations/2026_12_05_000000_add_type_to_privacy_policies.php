<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->string('type', 20)->default('privacy')->after('id');
        });

        // Starter text only (edit it in the admin panel; have it reviewed for your business and country).
        DB::table('privacy_policies')->insert([
            'type' => 'terms',
            'title' => 'Terms of Service',
            'content' => '<h2>Using this website</h2><p>By using this website you agree to these terms. Please read them carefully before enrolling in a program or contacting us.</p>'
                . '<h2>Programs and enrolment</h2><ul><li>Program details, schedules and fees may change, and the latest information is always on this website.</li><li>An enquiry or enrolment request does not guarantee a place until we confirm it.</li></ul>'
                . '<h2>Intellectual property</h2><p>All text, images, videos and course material on this website belong to us or our partners and may not be copied or reused without written permission.</p>'
                . '<h2>Limitation of liability</h2><p>We work to keep the information on this website accurate, but we do not guarantee it is always complete or up to date.</p>'
                . '<h2>Contact</h2><p>If you have any questions about these terms, please contact us through the Contact page.</p>',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('privacy_policies')->where('type', 'terms')->delete();
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
