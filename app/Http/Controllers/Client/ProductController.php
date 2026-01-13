<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'Tài khoản');
        $sortBy = $request->get('sort', 'popular');
        $filters = $request->get('filters', []);
        
        $products = [
            [
                'id' => 1,
                'title' => 'ZALO NĂM CỔ SIÊU TRÂU BẢO HÀNH 24H',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews_count' => 0,
                'sold_count' => 0,
                'complaint_rate' => 0.0,
                'seller' => 'mhanhcuti00',
                'category' => 'Tài Khoản Zalo',
                'description' => 'CÓ ZALO CỖ NĂM ĐẢM BẢO TRÂU, BẢO HÀNH 24H',
                'stock' => 0,
                'price' => 650000,
            ],
            [
                'id' => 2,
                'title' => 'Zalo năm cổ siêu trâu chó',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews_count' => 0,
                'sold_count' => 0,
                'complaint_rate' => 0.0,
                'seller' => 'nhincaiconcac302',
                'category' => 'Tài Khoản Zalo',
                'description' => 'Zalo cổ siêu trâu',
                'stock' => 5,
                'price' => 700000,
            ],
            [
                'id' => 3,
                'title' => 'BÁN ZALO THÁNG XMDT, BẢO HÀNH 24H',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews_count' => 0,
                'sold_count' => 0,
                'complaint_rate' => 0.0,
                'seller' => 'mhanhcuti00',
                'category' => 'Tài Khoản Zalo',
                'description' => 'Zalo tháng XMDT',
                'stock' => 10,
                'price' => 500000,
            ],
            [
                'id' => 4,
                'title' => 'BÁN ZALO NEW XMDT',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews_count' => 0,
                'sold_count' => 0,
                'complaint_rate' => 0.0,
                'seller' => 'mhanhcuti00',
                'category' => 'Tài Khoản Zalo',
                'description' => 'Zalo mới XMDT',
                'stock' => 8,
                'price' => 600000,
            ],
        ];

        if ($sortBy === 'price_asc') {
            usort($products, fn($a, $b) => $a['price'] <=> $b['price']);
        } elseif ($sortBy === 'price_desc') {
            usort($products, fn($a, $b) => $b['price'] <=> $a['price']);
        }

        $totalProducts = count($products);

        $filterOptions = [
            'Tài khoản FB',
            'Tài Khoản BM',
            'Tài Khoản Zalo',
            'Tài Khoản Twitter',
            'Tài Khoản Telegram',
            'Tài Khoản Instagram',
            'Tài Khoản Shopee',
            'Tài Khoản Discord',
            'Tài khoản Tik Tok',
            'Key Diệt Virus',
            'Key Window',
            'Tài Khoản Khác',
        ];

        return view('client.pages.products.index', compact(
            'category',
            'products',
            'totalProducts',
            'sortBy',
            'filters',
            'filterOptions'
        ));
    }

    public function show($id)
    {
        $product = [
            'id' => $id,
            'title' => 'Sản phẩm captcha',
            'name' => 'captcha',
            'image' => 'images/placeholder.jpg',
            'rating' => 5.0,
            'reviews_count' => 0,
            'sold_count' => 0,
            'complaint_rate' => 0.0,
            'seller' => 'nhincaiconcac302',
            'seller_online' => true,
            'category' => 'Phần Mềm Capcha',
            'stock' => 10,
            'price' => 0,
            'description' => 'Mô tả chi tiết về sản phẩm captcha. Đây là một phần mềm chuyên dụng cho việc giải captcha tự động, hỗ trợ nhiều loại captcha khác nhau.',
            'reviews' => [],
        ];

        $similarProducts = [
            [
                'id' => 1,
                'title' => 'Khắc phục lỗi khi chấp nhận tk vào BMm',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews' => 0,
                'category' => 'Dịch vụ',
                'subcategory' => 'Dịch vụ khác',
                'price_min' => 100000,
                'price_max' => 100000,
            ],
            [
                'id' => 2,
                'title' => 'Cập nhật cách fix lỗi FB mới',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews' => 0,
                'category' => 'Dịch vụ',
                'subcategory' => 'Dịch vụ khác',
                'price_min' => 100000,
                'price_max' => 100000,
            ],
            [
                'id' => 3,
                'title' => 'Cách kháng hold tiền',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews' => 0,
                'category' => 'Dịch vụ',
                'subcategory' => 'Dịch vụ khác',
                'price_min' => 100000,
                'price_max' => 100000,
            ],
            [
                'id' => 4,
                'title' => 'Một số thủ thuật add thẻ',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews' => 0,
                'category' => 'Dịch vụ',
                'subcategory' => 'Dịch vụ khác',
                'price_min' => 100000,
                'price_max' => 100000,
            ],
            [
                'id' => 5,
                'title' => 'Tut kháng Tài Khoản Facebook bị treo vĩnh viễn',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews' => 0,
                'category' => 'Dịch vụ',
                'subcategory' => 'Dịch vụ khác',
                'price_min' => 100000,
                'price_max' => 100000,
            ],
            [
                'id' => 6,
                'title' => 'Tut kháng Tài Khoản Facebook bị treo vĩnh viễn',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews' => 0,
                'category' => 'Dịch vụ',
                'subcategory' => 'Dịch vụ khác',
                'price_min' => 100000,
                'price_max' => 100000,
            ],
            [
                'id' => 7,
                'title' => 'Tut kháng Tài Khoản Facebook bị treo vĩnh viễn',
                'image' => 'images/placeholder.jpg',
                'rating' => 4.5,
                'reviews' => 0,
                'category' => 'Dịch vụ',
                'subcategory' => 'Dịch vụ khác',
                'price_min' => 100000,
                'price_max' => 100000,
            ],
        ];

        return view('client.pages.products.show', compact('product', 'similarProducts'));
    }
}

