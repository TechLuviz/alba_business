<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->string('heading_accent')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation');
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Initial content only: from here on everything is edited in the admin panel.
        DB::table('team_sections')->insert([
            'eyebrow' => 'Our Team Members',
            'heading' => 'Meet Our',
            'heading_accent' => 'Team',
            'description' => 'A diverse team of educators, industry experts, and innovators who are passionate about empowering ambitious minds and shaping the next generation of business leaders.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $members = [
            ['Dr. Alexander Reed', 'Dean of Business Studies'],
            ['Prof. Sofia Martinez', 'Head of Entrepreneurship'],
            ['Priya Shah', 'Director of Innovation'],
            ['James Carter', 'Lead Faculty in Finance'],
        ];
        foreach ($members as $i => [$name, $role]) {
            DB::table('team_members')->insert([
                'name' => $name, 'designation' => $role, 'sort_order' => $i + 1, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('team_sections');
    }
};
