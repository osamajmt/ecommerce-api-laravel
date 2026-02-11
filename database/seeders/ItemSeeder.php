<?php

namespace Database\Seeders;

use App\Models\category;
use App\Models\item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // item::truncate();
          $items = [
            // Clothing Items
            [
                'category_type' => 'Clothing',
                'items' => [
                    [
                        'name' => 'Men\'s T-Shirt',
                        'name_ar' => 'تيشرت رجالي',
                        'desc' => 'Cotton t-shirt for men, comfortable and durable',
                        'desc_ar' => 'تيشرت قطني للرجال، مريح ومتين',
                        'price' => 2500,
                        'count' => 50,
                        'discount' => 10,
                        'is_active' => true,
                    ],
                     [
                        'name' => 'Women\'s Dress',
                        'name_ar' => 'فستان نسائي',
                        'desc' => 'Elegant summer dress for women',
                        'desc_ar' => 'فستان صيفي أنيق للنساء',
                        'price' => 4500,
                        'count' => 30,
                        'discount' => 15,
                        'is_active' => true,
                    ],
                      [
                        'name' => 'Jeans Pants',
                        'name_ar' => 'بنطلون جينز',
                        'desc' => 'Blue denim jeans, regular fit',
                        'desc_ar' => 'بنطلون جينز أزرق، مقاس عادي',
                        'price' => 3500,
                        'count' => 40,
                        'discount' => 5,
                        'is_active' => true,
                    ],
                ]
            ],
           // Electronics Items
            [
                'category_type' => 'Electronics',
                'items' => [
                    [
                        'name' => 'Wireless Headphones',
                        'name_ar' => 'سماعات لاسلكية',
                        'desc' => 'Noise cancelling wireless headphones',
                        'desc_ar' => 'سماعات لاسلكية مع إلغاء الضوضاء',
                        'price' => 12000,
                        'count' => 25,
                        'discount' => 20,
                        'is_active' => true,
                    ],
                      [
                        'name' => 'Smart Watch',
                        'name_ar' => 'ساعة ذكية',
                        'desc' => 'Fitness tracker with heart rate monitor',
                        'desc_ar' => 'متتبع اللياقة مع مراقب معدل ضربات القلب',
                        'price' => 8500,
                        'count' => 20,
                        'discount' => 12,
                        'is_active' => true,
                    ],
                       [
                        'name' => 'Bluetooth Speaker',
                        'name_ar' => 'مكبر صوت بلوتوث',
                        'desc' => 'Portable waterproof bluetooth speaker',
                        'desc_ar' => 'مكبر صوت بلوتوث محمول ومقاوم للماء',
                        'price' => 5500,
                        'count' => 35,
                        'discount' => 8,
                        'is_active' => true,
                    ],
                ]
            ],
              // Mobile Phones Items
            [
                'category_type' => 'Mobile Phones',
                'items' => [
                    [
                        'name' => 'Smartphone X',
                        'name_ar' => 'هاتف ذكي X',
                        'desc' => 'Latest smartphone with 128GB storage',
                        'desc_ar' => 'أحدث هاتف ذكي بسعة 128 جيجابايت',
                        'price' => 45000,
                        'count' => 15,
                        'discount' => 5,
                        'is_active' => true,
                    ],
                     [
                        'name' => 'Tablet Pro',
                        'name_ar' => 'تابلت برو',
                        'desc' => '10-inch tablet with stylus support',
                        'desc_ar' => 'تابلت 10 بوصة مع دعم القلم',
                        'price' => 32000,
                        'count' => 12,
                        'discount' => 10,
                        'is_active' => true,
                    ],
                       [
                        'name' => 'Wireless Earbuds',
                        'name_ar' => 'سماعات أذن لاسلكية',
                        'desc' => 'True wireless earbuds with charging case',
                        'desc_ar' => 'سماعات أذن لاسلكية حقيقية مع حافظة شحن',
                        'price' => 7500,
                        'count' => 30,
                        'discount' => 15,
                        'is_active' => true,
                    ],
                ]
            ],
            // Furniture Items
            [
                'category_type' => 'Furniture',
                'items' => [
                    [
                        'name' => 'Office Chair',
                        'name_ar' => 'كرسي مكتب',
                        'desc' => 'Ergonomic office chair with lumbar support',
                        'desc_ar' => 'كرسي مكتب مريح مع دعم للظهر',
                        'price' => 15000,
                        'count' => 18,
                        'discount' => 12,
                        'is_active' => true,
                    ],
                        [
                        'name' => 'Dining Table',
                        'name_ar' => 'طاولة طعام',
                        'desc' => 'Wooden dining table for 6 persons',
                        'desc_ar' => 'طاولة طعام خشبية لـ 6 أشخاص',
                        'price' => 28000,
                        'count' => 8,
                        'discount' => 8,
                        'is_active' => true,
                    ],
                     [
                        'name' => 'Bookshelf',
                        'name_ar' => 'رف كتب',
                        'desc' => '5-tier wooden bookshelf',
                        'desc_ar' => 'رف كتب خشبي من 5 طبقات',
                        'price' => 12000,
                        'count' => 22,
                        'discount' => 10,
                        'is_active' => true,
                    ],
                ]
            ],
              // Home Appliances Items
            [
                'category_type' => 'Home Appliances',
                'items' => [
                    [
                        'name' => 'Air Conditioner',
                        'name_ar' => 'مكيف هواء',
                        'desc' => 'Split AC with inverter technology',
                        'desc_ar' => 'مكيف سبليت بتقنية العاكس',
                        'price' => 35000,
                        'count' => 10,
                        'discount' => 7,
                        'is_active' => true,
                    ],
                      [
                        'name' => 'Washing Machine',
                        'name_ar' => 'غسالة ملابس',
                        'desc' => 'Automatic washing machine 8kg capacity',
                        'desc_ar' => 'غسالة أوتوماتيكية بسعة 8 كجم',
                        'price' => 28000,
                        'count' => 14,
                        'discount' => 9,
                        'is_active' => true,
                    ],
                      [
                        'name' => 'Microwave Oven',
                        'name_ar' => 'فرن ميكروويف',
                        'desc' => 'Digital microwave oven 25L capacity',
                        'desc_ar' => 'فرن ميكروويف رقمي بسعة 25 لتر',
                        'price' => 8500,
                        'count' => 25,
                        'discount' => 12,
                        'is_active' => true,
                    ],
                ]
            ],
        ];
     foreach ($items as $categoryGroup) {
            $category = category::where('name', $categoryGroup['category_type'])->first();

            if ($category) {
                foreach ($categoryGroup['items'] as $itemData) {
                    item::create([
                        'category_id' => $category->id,
                        'name' => $itemData['name'],
                        'name_ar' => $itemData['name_ar'],
                        'desc' => $itemData['desc'],
                        'desc_ar' => $itemData['desc_ar'],
                        'price' => $itemData['price'],
                        'count' => $itemData['count'],
                        'discount' => $itemData['discount'],
                        'is_active' => $itemData['is_active'],
                        'image' => $this->generateItemImage($categoryGroup['category_type'])
                    ]);
                      }
            }}
    }
       private function generateItemImage(string $categoryType): string
    {
        Storage::makeDirectory('public/items');

        $imageNames = [
            'Clothing' => ['tshirt.jpg', 'dress.jpg', 'jeans.jpg', 'jacket.jpg'],
            'Electronics' => ['headphones.jpg', 'smartwatch.jpg', 'speaker.jpg', 'camera.jpg'],
            'Mobile Phones' => ['phone.jpg', 'tablet.jpg', 'earbuds.jpg', 'charger.jpg'],
            'Furniture' => ['chair.jpg', 'table.jpg', 'sofa.jpg', 'shelf.jpg'],
            'Home Appliances' => ['ac.jpg', 'washing.jpg', 'microwave.jpg', 'fridge.jpg'],
            'Books' => ['novel.jpg', 'educational.jpg', 'children.jpg', 'biography.jpg'],
            'Sports Equipment' => ['ball.jpg', 'racket.jpg', 'dumbbell.jpg', 'yogamat.jpg'],
            'Beauty Products' => ['perfume.jpg', 'cream.jpg', 'shampoo.jpg', 'makeup.jpg'],
        ];
           $images = $imageNames[$categoryType] ?? ['item.jpg'];
        $randomImage = $images[array_rand($images)];

        // return 'items/' . uniqid() . '_' . $randomImage;
        return  uniqid() . '_' . $randomImage;
    }
}
