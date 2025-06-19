<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mã OTP của bạn</title>
</head>
<body style="background: #f7f7f7; font-family: Arial, sans-serif; margin:0; padding:0;">
    <div style="max-width:400px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);overflow:hidden;">
        <img src="https://tools.dalathasfarm.com/assets/_file/2025/2025-06/diudang_banner11.jpg" style="width:100%;display:block;">
        <div style="padding:32px 24px 24px 24px;text-align:center;">
            <p style="color:#e74c3c;font-size:18px;margin-bottom:8px;">Xin chào,</p>
            <p style="color:#333;font-size:16px;margin-bottom:16px;">Mã xác thực OTP của bạn là:</p>
            <div style="display:inline-block;padding:12px 32px;background:#f1f8e9;color:#388e3c;font-size:32px;letter-spacing:8px;border-radius:8px;font-weight:bold;box-shadow:0 2px 8px rgba(56,142,60,0.08);margin-bottom:16px;">
                {{ $otp }}
            </div>
            <p style="color:#888;font-size:14px;margin-top:24px;">Mã này có hiệu lực trong <b>5 phút</b>.</p>
        </div>
    </div>
</body>
</html>
