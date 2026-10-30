<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_sections', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('email', 254);
            $table->string('phone', 30);
            $table->string('course');
            $table->text('message');
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('contact_sections')->insert([
            'heading' => 'Get in touch',
            'heading_accent' => 'with Albatech',
            'description' => 'Have questions, or ready to be part of a learning ecosystem that turns ideas into real-world impact? We\'d love to hear from you.',
            'email' => 'admissions@albatech.edu',
            'phone' => '+91 98765 43210',
            'address' => '123 Innovation Drive, Knowledge Park, Bengaluru, Karnataka 560102, India',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('contact_sections');
    }
};
