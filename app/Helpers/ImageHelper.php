<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    /**
     * Giảm dung lượng và lưu ảnh dưới định dạng webp.
     * Resize nếu có truyền chiều rộng.
     * 
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     * @param string $path
     * @param int|null $width - Nếu null thì không resize
     * @param int $quality - Chất lượng ảnh (1-100), mặc định 85
     * @param bool $useUuid - Sử dụng UUID thay vì timestamp, mặc định true
     * @return string - Đường dẫn file đã lưu
     */
    public static function optimizeAndSave($uploadedFile, $path = 'uploads', $width = null, $quality = 85, $useUuid = true)
    {
        $image = Image::make($uploadedFile);

        if ($width) {
            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        if ($useUuid) {
            $filename = Str::uuid() . '.webp';
        } else {
            $filename = time() . '_' . Str::random(10) . '.webp';
        }
        
        $fullPath = "$path/$filename";

        Storage::disk('public')->put($fullPath, (string) $image->encode('webp', $quality));

        return $fullPath;
    }

    /**
     * Xoá ảnh cũ khỏi storage/public.
     */
    public static function delete($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}


// use App\Helpers\ImageHelper;

// // Xoá ảnh cũ nếu cần
// ImageHelper::delete($product->image_path);

// // Lưu ảnh mới (không resize, chỉ giảm dung lượng)
// $product->image_path = ImageHelper::optimizeAndSave($request->file('image'), 'products');

// // Hoặc có resize nếu muốn
// $product->image_path = ImageHelper::optimizeAndSave($request->file('image'), 'products', 800); // resize width 800