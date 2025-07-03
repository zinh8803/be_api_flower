<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cập nhật trạng thái đơn hàng</title>
</head>
<body style="background: #f7f7f7; font-family: Arial, sans-serif;">
    <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);overflow:hidden;">
        <img src="https://tools.dalathasfarm.com/assets/_file/2025/2025-06/diudang_banner11.jpg" style="width:100%;display:block;">
        <div style="padding:32px 24px;text-align:center;">
            <h2 style="color:#388e3c;">Cập nhật đơn hàng của bạn</h2>
            <p style="color:#333;">Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn đã được cập nhật.</p>
            <div style="margin:24px 0;">
                <strong>Trạng thái mới:</strong>
                <div style="margin:12px auto;max-width:220px;background:#e3f2fd;color:#1976d2;font-weight:600;padding:12px 0;border-radius:8px;font-size:18px;">
                    {{ $statusText }}
                </div>
            </div>
            <p style="color:#888;font-size:15px;">
                Cảm ơn bạn đã mua hàng tại cửa hàng của chúng tôi.<br>
                Nếu có thắc mắc, hãy liên hệ chúng tôi bất cứ lúc nào.
            </p>
            <div style="margin-top:24px;color:#888;font-size:13px;">
                Thanks,<br>
                {{ config('app.name') }}
            </div>
        </div>
    </div>
</body>
</html>
