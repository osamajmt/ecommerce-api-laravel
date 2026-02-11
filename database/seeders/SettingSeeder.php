<?php

namespace Database\Seeders;

use App\Models\setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       setting::truncate();
         setting::create([
            'home_title' => 'Welcome to Our Store',
            'home_body' => 'We offer the best products with fast delivery',
            'delivery_time' => '30-45 minutes',
        ]);
    }
}
