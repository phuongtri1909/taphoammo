<?php

namespace Database\Seeders;

use App\Models\HeaderConfig;
use Illuminate\Database\Seeder;

class HeaderConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HeaderConfig::updateOrCreate(
            ['key' => 'support_bar'],
            [
                'label' => 'Thanh hỗ trợ',
                'is_active' => true,
                'config_data' => [
                    'facebook_url' => 'https://facebook.com/shoptaphoazalo',
                    'facebook_text' => 'facebook.com/shoptaphoazalo',
                    'email' => 'shoptaphoazalo@gmail.com',
                    'email_text' => 'shoptaphoazalo@gmail.com',
                    'operating_hours_text' => 'Thời gian hoạt động của sàn 24/7',
                ],
            ]
        );

        HeaderConfig::updateOrCreate(
            ['key' => 'promotional_banner'],
            [
                'label' => 'Banner quảng cáo',
                'is_active' => true,
                'config_data' => [
                    'content' => 'Tạp Hóa MMO - Sàn thương mại điện tử sản phẩm số phục vụ Kiếm tiền online. Mọi giao dịch trên trang đều hoàn toàn tự động và được giữ tiền 3 ngày, thay thế cho hình thức trung gian, các bạn yên tâm giao dịch nhé. (2) Cảnh báo gian hàng không uy tín: Nếu chủ shop bán cho bạn sản phẩm không đúng định dạng: tài-khoản|mật-khẩu..., mà là 1 chuỗi không liên quan ở đầu, có nghĩa là hàng đó đang cố pass hệ thống check trùng của sàn, hãy nhanh chóng khiếu nại đơn hàng và báo cho bên mình nhé, vì sản phẩm bạn mua có thể đã từng bán cho người khác trên sàn.',
                ],
            ]
        );

        HeaderConfig::updateOrCreate(
            ['key' => 'search_background'],
            [
                'label' => 'Background tìm kiếm',
                'is_active' => true,
                'config_data' => [
                    'background_image' => null,
                ],
            ]
        );
    }
}
