<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageHelper
{
    /**
     * Giảm dung lượng và lưu ảnh dưới định dạng webp.
     * Resize nếu có truyền chiều rộng.
     */
    public static function optimizeAndSave($uploadedFile, $path = 'uploads', $width = null, $quality = 80)
    {
        $image = Image::make($uploadedFile);

        if ($width) {
            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $filename = uniqid() . '.webp';
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