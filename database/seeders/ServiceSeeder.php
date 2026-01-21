<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\ServiceSubCategory;
use App\Models\ServiceVariant;
use App\Enums\ServiceStatus;
use App\Enums\CommonStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellers = User::where('role', User::ROLE_SELLER)->get();
        $subCategories = ServiceSubCategory::active()->get();

        if ($sellers->isEmpty() || $subCategories->isEmpty()) {
            $this->command->warn('Không tìm thấy sellers hoặc service subcategories. Vui lòng chạy AdminSeeder và ServiceCategorySeeder trước.');
            return;
        }

        $servicesPerSeller = 25; 
        $subCategoriesCount = $subCategories->count();
        $servicesPerCategory = (int) ceil($servicesPerSeller / $subCategoriesCount);

        foreach ($sellers as $sellerIndex => $seller) {
            $this->command->info("Đang tạo dịch vụ cho seller: {$seller->email}");

            $createdCount = 0;
            $subCategoryIndex = 0;

            foreach ($subCategories as $subCategory) {
                if ($createdCount >= $servicesPerSeller) {
                    break;
                }

                $servicesToCreate = ($subCategoryIndex < ($servicesPerSeller % $subCategoriesCount)) 
                    ? $servicesPerCategory + 1 
                    : $servicesPerCategory;

                for ($i = 0; $i < $servicesToCreate && $createdCount < $servicesPerSeller; $i++) {
                    $service = $this->createService($seller, $subCategory, $createdCount + 1);
                    $createdCount++;
                }

                $subCategoryIndex++;
            }
        }

        $this->command->info('Đã tạo xong tất cả dịch vụ!');
    }

    protected function createService(User $seller, ServiceSubCategory $subCategory, int $index): Service
    {
        $serviceNames = [
            'Dịch vụ tối ưu',
            'Dịch vụ chuyên nghiệp',
            'Dịch vụ cao cấp',
            'Dịch vụ tiêu chuẩn',
            'Dịch vụ nhanh chóng',
            'Dịch vụ chất lượng',
            'Dịch vụ uy tín',
            'Dịch vụ đáng tin cậy',
        ];

        $serviceName = $subCategory->name . ' - ' . $serviceNames[array_rand($serviceNames)] . ' #' . $index;
        
        $service = Service::create([
            'service_sub_category_id' => $subCategory->id,
            'seller_id' => $seller->id,
            'name' => $serviceName,
            'description' => 'Mô tả dịch vụ ' . $serviceName . '. Đây là dịch vụ chất lượng cao, uy tín và đáng tin cậy.',
            'long_description' => 'Mô tả chi tiết về dịch vụ ' . $serviceName . '. ' . 
                'Dịch vụ này được thực hiện bởi đội ngũ chuyên nghiệp với nhiều năm kinh nghiệm. ' .
                'Chúng tôi cam kết mang đến chất lượng dịch vụ tốt nhất cho khách hàng. ' .
                'Quy trình làm việc minh bạch, thời gian giao hàng nhanh chóng và hỗ trợ 24/7.',
            'status' => ServiceStatus::APPROVED,
        ]);

        $variantCount = rand(1, 4);
        $variantNames = [
            'Gói Cơ Bản',
            'Gói Tiêu Chuẩn', 
            'Gói Nâng Cao',
            'Gói VIP',
            'Gói Doanh Nghiệp'
        ];

        for ($v = 0; $v < $variantCount; $v++) {
            $variantName = $variantCount > 1 ? ($variantNames[$v] ?? 'Gói ' . ($v + 1)) : 'Gói Mặc định';
            
            $basePrice = rand(50000, 200000);
            $price = $basePrice * ($v + 1);
            
            $variant = ServiceVariant::create([
                'service_id' => $service->id,
                'name' => $variantName,
                'price' => $price,
                'sold_count' => rand(0, 20),
                'order' => $v,
                'status' => CommonStatus::ACTIVE,
            ]);
        }

        return $service;
    }
}
