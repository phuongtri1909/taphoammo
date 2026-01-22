<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::setConfig(
            'level_amount',
            100000,
            'Số tiền cần để đạt Level Tiếp Theo'
        );

        Config::setConfig(
            'refund_hours',
            24,
            'Số giờ để khiếu nại hoàn trả sản phẩm'
        );

        Config::setConfig(
            'commission_rate',
            10,
            'Phần trăm hoa hồng sàn trên mỗi đơn hàng (%)'
        );

        Config::setConfig(
            'dispute_response_hours',
            48,
            'Số giờ seller phải phản hồi khiếu nại trước khi tự động chấp nhận'
        );

        Config::setConfig(
            'telegram_bot_token',
            '',
            'Telegram Bot Token (Bắt buộc) - Token của bot Telegram (ví dụ: 123456:ABC-DEF...)'
        );

        Config::setConfig(
            'telegram_bot_username',
            '',
            'Telegram Bot Username (Bắt buộc) - Username của bot Telegram (ví dụ: YourBotName, không có @)'
        );

        Config::setConfig(
            'telegram_chat_id_1',
            '',
            'Telegram Chat ID 1 (Bắt buộc) - Chat ID để nhận thông báo đơn hàng (ví dụ: -1001234567890)'
        );

        Config::setConfig(
            'telegram_chat_id_2',
            '',
            'Telegram Chat ID 2 (Tùy chọn) - Chat ID để nhận thông báo rút tiền, nếu không có sẽ dùng chat_id_1'
        );

        Config::setConfig(
            'service_order_completion_hours',
            96,
            'Số giờ seller phải xác nhận đã hoàn thành dịch vụ (mặc định: 96 giờ = 4 ngày)'
        );

        Config::setConfig(
            'service_order_buyer_confirm_hours',
            96,
            'Số giờ buyer xác nhận sau khi seller đã hoàn thành (mặc định: 96 giờ = 4 ngày)'
        );

        Config::setConfig(
            'featured_price',
            10000,
            'Số tiền mỗi lần đề xuất sản phẩm/dịch vụ (VNĐ)'
        );

        Config::setConfig(
            'featured_hours',
            24,
            'Số giờ đề xuất mỗi lần thanh toán (mặc định: 24 giờ)'
        );
    }
} 