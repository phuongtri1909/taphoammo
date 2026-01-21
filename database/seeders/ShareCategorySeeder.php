<?php

namespace Database\Seeders;

use App\Models\ShareCategory;
use Illuminate\Database\Seeder;

class ShareCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kinh nghiệm kiếm tiền online',
                'description' => 'Chia sẻ các cách kiếm tiền online hiệu quả',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Hướng dẫn sử dụng sản phẩm',
                'description' => 'Hướng dẫn chi tiết cách sử dụng các sản phẩm trên sàn',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Mẹo bảo mật tài khoản',
                'description' => 'Các mẹo bảo vệ tài khoản và thông tin cá nhân',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Review sản phẩm',
                'description' => 'Đánh giá và review các sản phẩm số',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Tin tức & Cập nhật',
                'description' => 'Tin tức mới nhất về thị trường sản phẩm số',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Khác',
                'description' => 'Các bài viết khác',
                'order' => 99,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ShareCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
