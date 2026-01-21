<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServiceSubCategory;
use App\Enums\CommonStatus;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tăng tương tác',
                'order' => 1,
                'description' => 'Các dịch vụ tăng tương tác trên mạng xã hội',
                'subcategories' => [
                    ['name' => 'Dịch vụ Facebook', 'order' => 1],
                    ['name' => 'Dịch vụ Tiktok', 'order' => 2],
                    ['name' => 'Dịch vụ Google', 'order' => 3],
                    ['name' => 'Dịch vụ Telegram', 'order' => 4],
                    ['name' => 'Dịch vụ Shopee', 'order' => 5],
                    ['name' => 'Dịch vụ Discord', 'order' => 6],
                    ['name' => 'Dịch vụ Twitter', 'order' => 7],
                    ['name' => 'Dịch vụ Youtube', 'order' => 8],
                    ['name' => 'Dịch vụ Zalo', 'order' => 9],
                    ['name' => 'Dịch vụ Instagram', 'order' => 10],
                    ['name' => 'Tương tác khác', 'order' => 11],
                ]
            ],
            [
                'name' => 'Dịch vụ phần mềm',
                'order' => 2,
                'description' => 'Các dịch vụ liên quan đến phần mềm và công cụ',
                'subcategories' => [
                    ['name' => 'Dịch vụ code tool', 'order' => 1],
                    ['name' => 'Dịch vụ đồ họa', 'order' => 2],
                    ['name' => 'Dịch vụ video', 'order' => 3],
                    ['name' => 'Dịch vụ tool khác', 'order' => 4],
                ]
            ],
            [
                'name' => 'BlockChain',
                'order' => 3,
                'description' => 'Các dịch vụ liên quan đến blockchain và tiền điện tử',
                'subcategories' => [
                    ['name' => 'Dịch vụ tiền ảo', 'order' => 1],
                    ['name' => 'Dịch vụ NFT', 'order' => 2],
                    ['name' => 'Dịch vụ Coinlist', 'order' => 3],
                    ['name' => 'Blockchain khác', 'order' => 4],
                ]
            ],
            [
                'name' => 'Dịch vụ khác',
                'order' => 4,
                'description' => 'Các dịch vụ khác',
                'subcategories' => [
                    ['name' => 'Dịch vụ khác', 'order' => 1],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $subcategories = $categoryData['subcategories'];
            unset($categoryData['subcategories']);

            $category = ServiceCategory::create([
                'name' => $categoryData['name'],
                'order' => $categoryData['order'],
                'description' => $categoryData['description'],
                'status' => CommonStatus::ACTIVE,
            ]);

            foreach ($subcategories as $subcategoryData) {
                ServiceSubCategory::create([
                    'service_category_id' => $category->id,
                    'name' => $subcategoryData['name'],
                    'order' => $subcategoryData['order'],
                    'status' => CommonStatus::ACTIVE,
                ]);
            }
        }

        $this->command->info('Đã tạo xong Service Categories và SubCategories!');
    }
}
