<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FAQ;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FAQ::truncate();

        $faqs = [
            [
                'question' => 'Làm thế nào để đăng ký tài khoản?',
                'answer' => 'Bạn có thể đăng ký tài khoản bằng cách:\n1. Nhấn vào nút "Đăng ký" ở góc phải trên cùng\n2. Điền đầy đủ thông tin: Họ tên, Email, Mật khẩu\n3. Xác nhận email để kích hoạt tài khoản\n4. Đăng nhập và bắt đầu sử dụng dịch vụ',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Tôi quên mật khẩu, làm sao để lấy lại?',
                'answer' => 'Nếu bạn quên mật khẩu, hãy thực hiện các bước sau:\n1. Nhấn vào "Quên mật khẩu" ở trang đăng nhập\n2. Nhập email đã đăng ký\n3. Kiểm tra email và nhấn vào link đặt lại mật khẩu\n4. Nhập mật khẩu mới và xác nhận',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Làm thế nào để nạp tiền vào tài khoản?',
                'answer' => 'Để nạp tiền vào tài khoản:\n1. Đăng nhập vào tài khoản của bạn\n2. Vào mục "Nạp tiền"\n3. Chọn ngân hàng và nhập số tiền muốn nạp\n4. Thực hiện chuyển khoản theo thông tin được cung cấp\n5. Hệ thống sẽ tự động cập nhật số dư sau khi nhận được tiền',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Thời gian xử lý nạp tiền là bao lâu?',
                'answer' => 'Thời gian xử lý nạp tiền:\n- Tự động: Trong vòng 5-15 phút sau khi chuyển khoản\n- Thủ công: Trong vòng 1-2 giờ làm việc\nLưu ý: Nếu quá thời gian trên, vui lòng liên hệ bộ phận hỗ trợ với mã giao dịch của bạn.',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Làm thế nào để rút tiền?',
                'answer' => 'Để rút tiền từ tài khoản:\n1. Đăng nhập vào tài khoản\n2. Vào mục "Rút tiền"\n3. Nhập số tiền muốn rút (tối thiểu 50,000 VNĐ)\n4. Chọn ngân hàng và nhập thông tin tài khoản\n5. Xác nhận OTP qua email\n6. Đợi xử lý (thời gian: 1-3 ngày làm việc)',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'Phí giao dịch là bao nhiêu?',
                'answer' => 'Các loại phí trên hệ thống:\n- Nạp tiền: Miễn phí\n- Rút tiền: 5,000 VNĐ/lần\n- Mua hàng: Không có phí\n- Bán hàng: Hoa hồng 5-10% tùy danh mục',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'question' => 'Làm thế nào để đăng ký bán hàng?',
                'answer' => 'Để đăng ký bán hàng:\n1. Đăng nhập vào tài khoản\n2. Vào mục "Đăng ký bán hàng"\n3. Điền đầy đủ thông tin: Số điện thoại, Thông tin ngân hàng, Link Facebook/Telegram\n4. Gửi yêu cầu và đợi admin duyệt\n5. Sau khi được duyệt, bạn có thể đăng sản phẩm/dịch vụ',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'question' => 'Làm sao để đăng sản phẩm/dịch vụ?',
                'answer' => 'Sau khi tài khoản được duyệt bán hàng:\n1. Vào mục "Quản lý sản phẩm" hoặc "Quản lý dịch vụ"\n2. Nhấn "Thêm mới"\n3. Điền đầy đủ thông tin: Tên, Mô tả, Hình ảnh, Giá\n4. Chọn danh mục phù hợp\n5. Đợi admin duyệt sản phẩm\n6. Sau khi duyệt, sản phẩm sẽ hiển thị trên website',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'question' => 'Tôi có thể hủy đơn hàng không?',
                'answer' => 'Bạn có thể hủy đơn hàng trong các trường hợp:\n- Đơn hàng chưa được người bán xác nhận: Hủy trực tiếp, tiền được hoàn lại ngay\n- Đơn hàng đã được xử lý: Liên hệ người bán hoặc tạo tranh chấp\n- Sau khi nhận hàng: Không thể hủy, nhưng có thể yêu cầu hoàn tiền nếu có vấn đề',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'question' => 'Làm sao để giải quyết tranh chấp?',
                'answer' => 'Nếu có vấn đề với đơn hàng:\n1. Liên hệ với người bán trước để giải quyết\n2. Nếu không giải quyết được, tạo "Tranh chấp" trong đơn hàng\n3. Mô tả chi tiết vấn đề và cung cấp bằng chứng (ảnh, chat)\n4. Admin sẽ xem xét và giải quyết trong 3-5 ngày làm việc\n5. Quyết định của admin là cuối cùng',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'question' => 'Hệ thống có hỗ trợ 2FA (Xác thực 2 yếu tố) không?',
                'answer' => 'Có, hệ thống hỗ trợ xác thực 2 yếu tố (2FA) để tăng cường bảo mật:\n1. Vào "Cài đặt tài khoản"\n2. Bật "Xác thực 2 yếu tố"\n3. Quét mã QR bằng ứng dụng Google Authenticator\n4. Nhập mã xác nhận để kích hoạt\n5. Sau khi bật, mỗi lần đăng nhập cần mã 2FA',
                'order' => 11,
                'is_active' => true,
            ],
            [
                'question' => 'Làm thế nào để liên hệ hỗ trợ?',
                'answer' => 'Bạn có thể liên hệ hỗ trợ qua:\n- Email: Shoptaphoazalo@gmail.com\n- Trang "Liên hệ" trên website\n- Facebook: Tạp hóa Zalo\n- Thời gian hỗ trợ: 24/7\nChúng tôi sẽ phản hồi trong vòng 24 giờ.',
                'order' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            FAQ::create($faq);
        }
    }
}
