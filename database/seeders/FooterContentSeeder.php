<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FooterContent;
use App\Models\ContactLink;

class FooterContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo hoặc cập nhật Footer Contents
        $sections = [
            [
                'section' => 'contact',
                'title' => 'Liên hệ',
                'description' => 'Liên hệ ngay nếu bạn có khó khăn khi sử dụng dịch vụ hoặc cần hợp tác.',
                'order' => 0,
            ],
            [
                'section' => 'information',
                'title' => 'Thông tin',
                'description' => 'Một ứng dụng nhằm kết nối, trao đổi, mua bán trong cộng đồng kiếm tiền online.',
                'order' => 1,
            ],
            [
                'section' => 'seller_registration',
                'title' => 'Đăng ký bán hàng',
                'description' => 'Tạo một gian hàng của bạn trên trang của chúng tôi. Đội ngũ hỗ trợ sẽ liên lạc để giúp bạn tối ưu khả năng bán hàng.',
                'order' => 2,
            ],
        ];

        foreach ($sections as $section) {
            FooterContent::updateOrCreate(
                ['section' => $section['section']],
                $section
            );
        }

        // Tạo Contact Links
        $contactLinks = [
            [
                'name' => 'Chat với hỗ trợ viên',
                'url' => '#',
                'icon' => 'fas fa-comment-dots',
                'order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Tạp hóa Zalo',
                'url' => 'https://m.facebook.com/shoptaphoazalo?mibextid=LQQJ4d',
                'icon' => 'fab fa-facebook',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Shoptaphoazalo@gmail.com',
                'url' => 'mailto:Shoptaphoazalo@gmail.com',
                'icon' => 'fas fa-envelope',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Thời gian hoạt động của sàn 24/7',
                'url' => '#',
                'icon' => 'fas fa-clock',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($contactLinks as $link) {
            ContactLink::updateOrCreate(
                [
                    'name' => $link['name'],
                    'url' => $link['url'],
                ],
                $link
            );
        }

        $this->command->info('Footer content và contact links đã được seed thành công!');
    }
}
