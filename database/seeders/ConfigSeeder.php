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
    }
} 