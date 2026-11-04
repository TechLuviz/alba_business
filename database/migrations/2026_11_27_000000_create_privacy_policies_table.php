<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Starter text only (edit it in the admin panel; have it reviewed for your business and country).
        DB::table('privacy_policies')->insert([
            'title' => 'Privacy Policy',
            'content' => '<h2>Information we collect</h2><p>We collect the details you give us through our contact, enrolment and newsletter forms, such as your name, email address, phone number and message.</p>'
                . '<h2>How we use your information</h2><ul><li>To reply to your enquiries and process enrolments.</li><li>To send updates you have asked for.</li><li>To improve our website and programs.</li></ul>'
                . '<h2>Sharing and protection</h2><p>We do not sell your personal information. We take reasonable steps to keep it safe and share it only when needed to run our services or when the law requires it.</p>'
                . '<h2>Your choices</h2><p>You can ask us to update or delete your information, or unsubscribe from our emails, at any time by contacting us.</p>',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('privacy_policies');
    }
};
