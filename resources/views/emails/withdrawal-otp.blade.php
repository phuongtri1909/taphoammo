<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xác nhận rút tiền - Mã OTP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 100%; max-width: 600px; border-collapse: collapse; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center; background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px 16px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">
                                Xác nhận rút tiền
                            </h1>
                            <p style="margin: 10px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 14px;">
                                Mã OTP xác thực giao dịch
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Xin chào <strong>{{ $user->full_name }}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px; color: #374151; font-size: 16px; line-height: 1.6;">
                                Bạn đã yêu cầu rút tiền với thông tin sau:
                            </p>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 30px; background-color: #f9fafb; border-radius: 12px;">
                                <tr>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #e5e7eb;">
                                        <span style="color: #6b7280; font-size: 14px;">Số tiền:</span>
                                    </td>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #e5e7eb; text-align: right;">
                                        <strong style="color: #10b981; font-size: 18px;">{{ $withdrawal->amount_formatted }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #e5e7eb;">
                                        <span style="color: #6b7280; font-size: 14px;">Ngân hàng:</span>
                                    </td>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #e5e7eb; text-align: right;">
                                        <strong style="color: #111827; font-size: 14px;">{{ $withdrawal->bank_name }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #e5e7eb;">
                                        <span style="color: #6b7280; font-size: 14px;">Số tài khoản:</span>
                                    </td>
                                    <td style="padding: 15px 20px; border-bottom: 1px solid #e5e7eb; text-align: right;">
                                        <strong style="color: #111827; font-size: 14px;">{{ $withdrawal->bank_account_number }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 15px 20px;">
                                        <span style="color: #6b7280; font-size: 14px;">Chủ tài khoản:</span>
                                    </td>
                                    <td style="padding: 15px 20px; text-align: right;">
                                        <strong style="color: #111827; font-size: 14px;">{{ $withdrawal->bank_account_name }}</strong>
                                    </td>
                                </tr>
                            </table>

                            <div style="text-align: center; margin-bottom: 30px;">
                                <p style="margin: 0 0 10px; color: #6b7280; font-size: 14px;">
                                    Mã OTP xác thực của bạn:
                                </p>
                                <div style="display: inline-block; padding: 20px 40px; background: linear-gradient(135deg, #fef3c7, #fde68a); border-radius: 12px; border: 2px dashed #f59e0b;">
                                    <span style="font-size: 36px; font-weight: 700; color: #b45309; letter-spacing: 8px; font-family: monospace;">
                                        {{ $otpCode }}
                                    </span>
                                </div>
                                <p style="margin: 15px 0 0; color: #ef4444; font-size: 13px;">
                                    Mã có hiệu lực trong {{ $expiryMinutes }} phút
                                </p>
                            </div>

                            <div style="padding: 15px 20px; background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 4px; margin-bottom: 20px;">
                                <p style="margin: 0; color: #991b1b; font-size: 14px; line-height: 1.6;">
                                    <strong> Lưu ý bảo mật:</strong><br>
                                    Không chia sẻ mã OTP này với bất kỳ ai. {{ config('app.name') }} không bao giờ yêu cầu mã OTP qua điện thoại hoặc tin nhắn.
                                </p>
                            </div>

                            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này hoặc liên hệ với chúng tôi ngay.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f9fafb; border-radius: 0 0 16px 16px; text-align: center;">
                            <p style="margin: 0 0 10px; color: #6b7280; font-size: 13px;">
                                Trân trọng,<br>
                                <strong style="color: #111827;">{{ config('app.name') }}</strong>
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
