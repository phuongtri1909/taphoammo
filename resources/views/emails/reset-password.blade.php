<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #02B3B9; margin-top: 0;">Đặt lại mật khẩu</h2>
        
        <p>Xin chào,</p>
        
        <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản tại <strong>{{ config('app.name') }}</strong>.</p>
        
        <p>Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="display: inline-block; background: linear-gradient(272deg, #00BA9F -9.16%, #00B7B7 17.33%, #07AABF 73.37%, #0698B5 113.91%); color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">Đặt lại mật khẩu</a>
        </div>
        
        <p>Hoặc copy và dán link sau vào trình duyệt:</p>
        <p style="word-break: break-all; color: #02B3B9;">{{ $resetUrl }}</p>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            <strong>Lưu ý:</strong> Link này sẽ hết hạn sau 60 phút.
        </p>
        
        <p style="margin-top: 20px; color: #666; font-size: 14px;">
            Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Mật khẩu của bạn sẽ không thay đổi.
        </p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
        
        <p style="color: #999; font-size: 12px; text-align: center;">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>

