<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\ProductVariant;
use App\Models\ProductValue;
use App\Enums\ProductStatus;
use App\Enums\CommonStatus;
use App\Enums\ProductValueStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellers = User::where('role', User::ROLE_SELLER)->get();
        $subCategories = SubCategory::active()->get();

        if ($sellers->isEmpty() || $subCategories->isEmpty()) {
            $this->command->warn('Không tìm thấy sellers hoặc subcategories. Vui lòng chạy AdminSeeder và CategorySeeder trước.');
            return;
        }

        $productsPerSeller = 35; 
        $subCategoriesCount = $subCategories->count();
        $productsPerCategory = (int) ceil($productsPerSeller / $subCategoriesCount);

        foreach ($sellers as $sellerIndex => $seller) {
            $this->command->info("Đang tạo sản phẩm cho seller: {$seller->email}");

            $createdCount = 0;
            $subCategoryIndex = 0;

            foreach ($subCategories as $subCategory) {
                if ($createdCount >= $productsPerSeller) {
                    break;
                }

                $productsToCreate = ($subCategoryIndex < ($productsPerSeller % $subCategoriesCount)) 
                    ? $productsPerCategory + 1 
                    : $productsPerCategory;

                for ($i = 0; $i < $productsToCreate && $createdCount < $productsPerSeller; $i++) {
                    $product = $this->createProduct($seller, $subCategory, $createdCount + 1);
                    $createdCount++;
                }

                $subCategoryIndex++;
            }
        }

        $this->command->info('Đã tạo xong tất cả sản phẩm!');
    }

    protected function createProduct(User $seller, SubCategory $subCategory, int $index): Product
    {
        $productName = $subCategory->name . ' #' . $index . ' - ' . $seller->full_name;
        
        $product = Product::create([
            'sub_category_id' => $subCategory->id,
            'seller_id' => $seller->id,
            'name' => $productName,
            'description' => 'Mô tả sản phẩm ' . $productName . '. Đây là sản phẩm chất lượng cao, uy tín.',
            'long_description' => 'Mô tả chi tiết về sản phẩm ' . $productName . '. Sản phẩm này được kiểm tra kỹ lưỡng trước khi đưa ra thị trường.',
            'status' => ProductStatus::APPROVED,
        ]);

        $variantCount = rand(1, 3);
        $variantNames = ['Gói Cơ Bản', 'Gói Tiêu Chuẩn', 'Gói Cao Cấp'];

        for ($v = 0; $v < $variantCount; $v++) {
            $variantName = $variantCount > 1 ? $variantNames[$v] : 'Mặc định';
            $price = rand(10000, 500000); 
            $stockQuantity = rand(10, 50); 

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'name' => $variantName,
                'price' => $price,
                'stock_quantity' => $stockQuantity,
                'sold_count' => 0,
                'field_name' => $subCategory->field_name,
                'order' => $v,
                'status' => CommonStatus::ACTIVE,
            ]);

            $this->createProductValues($variant, $subCategory, $stockQuantity);
        }

        return $product;
    }

    protected function createProductValues(ProductVariant $variant, SubCategory $subCategory, int $count): void
    {
        $fieldName = $subCategory->field_name ?? 'data';
        $fields = explode('|', $fieldName);

        for ($i = 0; $i < $count; $i++) {
            $data = [];
            
            foreach ($fields as $field) {
                $field = trim($field);
                $data[$field] = $this->generateFieldValue($field, $i);
            }

            $encryptedData = Crypt::encryptString(json_encode(['value' => $this->formatFieldValue($data, $fieldName)]));

            ProductValue::create([
                'product_variant_id' => $variant->id,
                'encrypted_data' => $encryptedData,
                'status' => ProductValueStatus::AVAILABLE,
            ]);
        }
    }

    protected function generateFieldValue(string $field, int $index): string
    {
        $field = strtolower($field);
        
        return match (true) {
            str_contains($field, 'email') => 'test' . ($index + 1) . '@example.com',
            str_contains($field, 'password') => 'password' . ($index + 1) . '!@#',
            str_contains($field, 'phone') => '0' . rand(100000000, 999999999),
            str_contains($field, 'username') => 'user' . ($index + 1),
            str_contains($field, 'key') => Str::random(32),
            str_contains($field, 'token') => Str::random(40),
            str_contains($field, 'api_key') => 'api_' . Str::random(30),
            str_contains($field, 'secret') => 'secret_' . Str::random(30),
            str_contains($field, 'code') => strtoupper(Str::random(12)),
            str_contains($field, 'serial') => strtoupper(Str::random(16)),
            str_contains($field, 'ip') => rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255),
            default => 'value' . ($index + 1),
        };
    }

    protected function formatFieldValue(array $data, string $fieldName): string
    {
        $parts = [];
        foreach ($data as $key => $value) {
            $parts[] = $value;
        }
        return implode('|', $parts);
    }
}
