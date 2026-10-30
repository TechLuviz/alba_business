<?php

namespace Database\Seeders;

use App\Models\HeroBanner;
use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@albatech.com'],
            ['name' => 'Admin', 'password' => Hash::make('Admin@12345')]
        );

        $hero = HeroBanner::latest('id')->first();

        if ($hero && HeroSlide::count() === 0) {
            HeroSlide::create([
                'heading_line_1' => $hero->heading_line_1,
                'heading_line_2' => $hero->heading_line_2,
                'heading_accent' => $hero->heading_accent,
                'description' => $hero->description,
                'button_text' => $hero->button_text,
                'button_url' => $hero->button_url,
                'image' => 'images/hero.jpg', 
                'sort_order' => 1,
            ]);
        }
    }
}