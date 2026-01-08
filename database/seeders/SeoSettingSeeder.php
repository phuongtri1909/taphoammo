<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SeoSetting;

class SeoSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seoSettings = [
            [
                'page_key' => 'home',
                'title' => 'Trang chủ - ' . config('app.name'),
                'description' => config('app.name') . ' là dự án được đầu tư bởi Tập đoàn Hoàng Gia Việt Nam, với quy hoạch tổng thể như một tổ hợp khu công nghiệp và đô thị xanh, tuân thủ các tiêu chuẩn sinh thái.',
                'keywords' => config('app.name') . ', khu công nghiệp, đô thị xanh, phát triển bền vững, Việt Nam',
                'is_active' => true
            ],
        ];

        foreach ($seoSettings as $setting) {
            SeoSetting::updateOrCreate(
                ['page_key' => $setting['page_key']],
                $setting
            );
        }
    }
}
