<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use App\Enums\CommonStatus;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tài khoản',
                'description' => 'Fb, BM, key window, kaspersky....',
                'subcategories' => [
                    ['name' => 'Tài khoản FB', 'field_name' => 'email|password'],
                    ['name' => 'Tài Khoản BM', 'field_name' => 'email|password'],
                    ['name' => 'Tài Khoản Zalo', 'field_name' => 'phone|password'],
                    ['name' => 'Tài Khoản Twitter', 'field_name' => 'email|password'],
                    ['name' => 'Tài Khoản Telegram', 'field_name' => 'phone|password'],
                    ['name' => 'Tài Khoản Instagram', 'field_name' => 'username|password'],
                    ['name' => 'Tài Khoản Shopee', 'field_name' => 'phone|password'],
                    ['name' => 'Tài Khoản Discord', 'field_name' => 'email|password'],
                    ['name' => 'Tài khoản TikTok', 'field_name' => 'email|password'],
                    ['name' => 'Key Diệt Virus', 'field_name' => 'key'],
                    ['name' => 'Key Window', 'field_name' => 'key'],
                    ['name' => 'Tài Khoản Khác', 'field_name' => 'email|password'],
                ],
            ],
            [
                'name' => 'Email',
                'description' => 'Gmail, yahoo mail, hot mail... và nhiều hơn thế nữa',
                'subcategories' => [
                    ['name' => 'Gmail', 'field_name' => 'email|password'],
                    ['name' => 'HotMail', 'field_name' => 'email|password'],
                    ['name' => 'OutlookMail', 'field_name' => 'email|password'],
                    ['name' => 'RuMail', 'field_name' => 'email|password'],
                    ['name' => 'DomainMail', 'field_name' => 'email|password'],
                    ['name' => 'YahooMail', 'field_name' => 'email|password'],
                    ['name' => 'ProtonMail', 'field_name' => 'email|password'],
                    ['name' => 'Loại Mail Khác', 'field_name' => 'email|password'],
                ],
            ],
            [
                'name' => 'Phần mềm',
                'description' => 'Các phần mềm chuyên dụng cho kiếm tiền online từ những coder uy tín',
                'subcategories' => [
                    ['name' => 'Phần Mềm FB', 'field_name' => 'token|key'],
                    ['name' => 'Phần Mềm Google', 'field_name' => 'token|key'],
                    ['name' => 'Phần Mềm Youtube', 'field_name' => 'token|key'],
                    ['name' => 'Phần Mềm Tiền Ảo', 'field_name' => 'api_key|secret'],
                    ['name' => 'Phần Mềm PTC', 'field_name' => 'username|password'],
                    ['name' => 'Phần Mềm Capcha', 'field_name' => 'api_key'],
                    ['name' => 'Phần Mềm Offer', 'field_name' => 'token'],
                    ['name' => 'Phần Mềm PTU', 'field_name' => 'token|key'],
                    ['name' => 'Phần Mềm Khác', 'field_name' => 'token|key'],
                ],
            ],
            [
                'name' => 'Khác',
                'description' => 'Thẻ nạp, VPS, Khác...',
                'subcategories' => [
                    ['name' => 'Thẻ nạp', 'field_name' => 'code|serial'],
                    ['name' => 'VPS', 'field_name' => 'ip|username|password'],
                    ['name' => 'Khác', 'field_name' => 'data'],
                ],
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            $category = Category::firstOrCreate(
                ['name' => $categoryData['name']],
                [
                    'order' => $index,
                    'status' => CommonStatus::ACTIVE->value,
                    'description' => $categoryData['description'],
                ]
            );

            if (!$category->wasRecentlyCreated) {
                $category->update(['order' => $index]);
            }

            foreach ($categoryData['subcategories'] as $subIndex => $subCategoryData) {
                $subCategory = SubCategory::firstOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $subCategoryData['name'],
                    ],
                    [
                        'field_name' => $subCategoryData['field_name'] ?? null,
                        'order' => $subIndex,
                        'status' => CommonStatus::ACTIVE->value,
                    ]
                );

                if (!$subCategory->wasRecentlyCreated) {
                    $subCategory->update([
                        'field_name' => $subCategoryData['field_name'] ?? null,
                        'order' => $subIndex,
                    ]);
                }
            }
        }
    }
}

