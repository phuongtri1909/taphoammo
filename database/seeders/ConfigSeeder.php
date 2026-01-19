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
    }
} 