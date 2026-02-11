<?php

namespace Database\Seeders;

use App\Models\category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // category::truncate();
        $categories = [
            [
                'name' => 'Clothing',
                'name_ar' => 'الملابس',
                'image' => 'clothing.jpg'
            ],
            [
                'name' => 'electronics',
                'name_ar' => 'الإلكترونيات',
                'image' => 'electronics.jpg'
            ],
              [
                'name' => 'Mobile Phones',
                'name_ar' => 'الهواتف المحمولة',
                'image' => 'mobiles.jpg'
            ],
            [
                'name' => 'Furniture',
                'name_ar' => 'الأثاث',
                'image' => 'furniture.jpg'
            ],
               [
                'name' => 'Home Appliances',
                'name_ar' => 'الأجهزة المنزلية',
                'image' => 'appliances.jpg'
            ],
            [
                'name' => 'Books',
                'name_ar' => 'الكتب',
                'image' => 'books.jpg'
            ],
             [
                'name' => 'Sports Equipment',
                'name_ar' => 'معدات الرياضة',
                'image' => 'sports.jpg'
            ],
            [
                'name' => 'Beauty Products',
                'name_ar' => 'منتجات التجميل',
                'image' => 'beauty.jpg'
            ],
            ];
            foreach ($categories as $category) {
            category::create([
                'name' => $category['name'],
                'name_ar' => $category['name_ar'],
                'image' => $this->generateImagePath($category['image'], 'categories')
            ]);
        }
    }
     private function generateImagePath(string $filename, string $folder): string
    {
        Storage::makeDirectory("public/images/$folder");
        // return "$folder/" . uniqid() . '_' . $filename;
        return uniqid() . '_' . $filename;
    }
}
