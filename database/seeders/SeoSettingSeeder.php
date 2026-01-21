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
        $appName = config('app.name', 'Tạp Hóa MMO');
        
        $seoSettings = [
            // ===== TRANG CHỦ =====
            [
                'page_key' => 'home',
                'title' => 'Trang chủ - ' . $appName,
                'description' => $appName . ' - Sàn thương mại điện tử chuyên cung cấp tài khoản số, dịch vụ MMO uy tín. Mua bán tài khoản game, phần mềm, dịch vụ số với giá tốt nhất. Giao dịch tự động 24/7, bảo vệ quyền lợi người mua với cơ chế giữ tiền 3 ngày.',
                'keywords' => $appName . ', mua bán tài khoản, tài khoản số, dịch vụ MMO, mua acc game, bán tài khoản, shop tài khoản, MMO, kiếm tiền online',
                'is_active' => true
            ],
            
            // ===== DANH SÁCH SẢN PHẨM =====
            [
                'page_key' => 'products',
                'title' => 'Danh sách sản phẩm - ' . $appName,
                'description' => 'Khám phá hàng ngàn sản phẩm số chất lượng tại ' . $appName . '. Tài khoản game, phần mềm, mã kích hoạt, license key với giá cạnh tranh. Giao hàng tự động 24/7.',
                'keywords' => 'mua sản phẩm số, tài khoản game, mã kích hoạt, license key, phần mềm bản quyền, ' . $appName,
                'is_active' => true
            ],
            
            // ===== DANH SÁCH DỊCH VỤ =====
            [
                'page_key' => 'services',
                'title' => 'Danh sách dịch vụ - ' . $appName,
                'description' => 'Dịch vụ MMO chuyên nghiệp tại ' . $appName . '. Nạp game, tăng follow, dịch vụ marketing, SEO và nhiều dịch vụ số khác. Đội ngũ seller uy tín, hỗ trợ 24/7.',
                'keywords' => 'dịch vụ MMO, dịch vụ số, nạp game, tăng follow, marketing online, dịch vụ công nghệ, ' . $appName,
                'is_active' => true
            ],
            
            // ===== LIÊN HỆ =====
            [
                'page_key' => 'contact',
                'title' => 'Liên hệ - ' . $appName,
                'description' => 'Liên hệ với ' . $appName . ' để được hỗ trợ mua bán tài khoản, dịch vụ MMO. Đội ngũ hỗ trợ chuyên nghiệp, phản hồi nhanh chóng 24/7.',
                'keywords' => 'liên hệ, hỗ trợ khách hàng, chăm sóc khách hàng, ' . $appName . ', hotline, email hỗ trợ',
                'is_active' => true
            ],
            
            // ===== CÂU HỎI THƯỜNG GẶP =====
            [
                'page_key' => 'faqs',
                'title' => 'Câu hỏi thường gặp (FAQ) - ' . $appName,
                'description' => 'Giải đáp các thắc mắc thường gặp về mua bán tài khoản, nạp tiền, rút tiền, khiếu nại tại ' . $appName . '. Hướng dẫn sử dụng chi tiết cho người mới.',
                'keywords' => 'câu hỏi thường gặp, FAQ, hướng dẫn sử dụng, hướng dẫn mua hàng, hướng dẫn bán hàng, ' . $appName,
                'is_active' => true
            ],
            
            // ===== ĐIỀU KHOẢN SỬ DỤNG =====
            [
                'page_key' => 'terms_of_service',
                'title' => 'Điều khoản sử dụng - ' . $appName,
                'description' => 'Điều khoản sử dụng và chính sách của ' . $appName . '. Quy định về mua bán, thanh toán, bảo mật thông tin, giải quyết tranh chấp và nghĩa vụ của các bên.',
                'keywords' => 'điều khoản sử dụng, chính sách, quy định, bảo mật, quyền riêng tư, ' . $appName,
                'is_active' => true
            ],
            
            // ===== CHIA SẺ / BLOG =====
            [
                'page_key' => 'shares',
                'title' => 'Chia sẻ kinh nghiệm MMO - ' . $appName,
                'description' => 'Chia sẻ kinh nghiệm, thủ thuật kiếm tiền online MMO từ cộng đồng ' . $appName . '. Cập nhật trend mới nhất, hướng dẫn chi tiết từ các seller uy tín.',
                'keywords' => 'chia sẻ MMO, kinh nghiệm kiếm tiền online, thủ thuật MMO, hướng dẫn MMO, blog kiếm tiền, ' . $appName,
                'is_active' => true
            ],
            
            // ===== ĐĂNG NHẬP =====
            [
                'page_key' => 'login',
                'title' => 'Đăng nhập - ' . $appName,
                'description' => 'Đăng nhập vào tài khoản ' . $appName . ' để mua bán tài khoản số, dịch vụ MMO. Giao dịch an toàn, bảo mật thông tin người dùng.',
                'keywords' => 'đăng nhập, login, tài khoản, ' . $appName,
                'is_active' => true
            ],
            
            // ===== ĐĂNG KÝ =====
            [
                'page_key' => 'register',
                'title' => 'Đăng ký tài khoản - ' . $appName,
                'description' => 'Đăng ký tài khoản miễn phí tại ' . $appName . ' để mua bán tài khoản số, dịch vụ MMO. Nhận ngay ưu đãi cho thành viên mới.',
                'keywords' => 'đăng ký, tạo tài khoản, đăng ký miễn phí, ' . $appName,
                'is_active' => true
            ],
            
            // ===== QUÊN MẬT KHẨU =====
            [
                'page_key' => 'forgot_password',
                'title' => 'Quên mật khẩu - ' . $appName,
                'description' => 'Khôi phục mật khẩu tài khoản ' . $appName . '. Nhập email để nhận liên kết đặt lại mật khẩu an toàn.',
                'keywords' => 'quên mật khẩu, khôi phục mật khẩu, reset password, ' . $appName,
                'is_active' => true
            ],
            
            // ===== ĐĂNG KÝ BÁN HÀNG =====
            [
                'page_key' => 'seller_register',
                'title' => 'Đăng ký bán hàng - ' . $appName,
                'description' => 'Trở thành seller tại ' . $appName . '. Bán tài khoản số, dịch vụ MMO với hàng triệu khách hàng tiềm năng. Hoa hồng cạnh tranh, hỗ trợ 24/7.',
                'keywords' => 'đăng ký bán hàng, trở thành seller, bán tài khoản, kiếm tiền online, ' . $appName,
                'is_active' => true
            ],
            
            // ===== NẠP TIỀN =====
            [
                'page_key' => 'deposit',
                'title' => 'Nạp tiền - ' . $appName,
                'description' => 'Nạp tiền vào tài khoản ' . $appName . ' nhanh chóng qua ngân hàng. Hỗ trợ chuyển khoản 24/7, giao dịch tự động.',
                'keywords' => 'nạp tiền, chuyển khoản, thanh toán, ' . $appName,
                'is_active' => true
            ],
            
            // ===== RÚT TIỀN =====
            [
                'page_key' => 'withdrawal',
                'title' => 'Rút tiền - ' . $appName,
                'description' => 'Rút tiền từ tài khoản seller ' . $appName . '. Xử lý nhanh trong 24-48 giờ, hỗ trợ nhiều ngân hàng.',
                'keywords' => 'rút tiền, withdrawal, thanh toán seller, ' . $appName,
                'is_active' => true
            ],
            
            // ===== TRANG CÁ NHÂN =====
            [
                'page_key' => 'profile',
                'title' => 'Trang cá nhân - ' . $appName,
                'description' => 'Quản lý tài khoản cá nhân tại ' . $appName . '. Xem lịch sử giao dịch, cập nhật thông tin và bảo mật tài khoản.',
                'keywords' => 'trang cá nhân, profile, tài khoản, ' . $appName,
                'is_active' => true
            ],
            
            // ===== ĐƠN HÀNG =====
            [
                'page_key' => 'orders',
                'title' => 'Đơn hàng của tôi - ' . $appName,
                'description' => 'Xem và quản lý đơn hàng đã mua tại ' . $appName . '. Theo dõi trạng thái đơn hàng, tải thông tin sản phẩm và khiếu nại nếu cần.',
                'keywords' => 'đơn hàng, lịch sử mua hàng, orders, ' . $appName,
                'is_active' => true
            ],
            
            // ===== YÊU THÍCH =====
            [
                'page_key' => 'favorites',
                'title' => 'Sản phẩm yêu thích - ' . $appName,
                'description' => 'Danh sách sản phẩm và dịch vụ yêu thích của bạn tại ' . $appName . '. Dễ dàng theo dõi và mua sắm nhanh chóng.',
                'keywords' => 'yêu thích, favorites, danh sách yêu thích, ' . $appName,
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
