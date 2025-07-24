<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Xin lỗi & Tặng mã giảm giá</title>
</head>

<body style="background: #f7f7f7; font-family: Arial, sans-serif;">
    <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);overflow:hidden;">
        <img src="https://tools.dalathasfarm.com/assets/_file/2025/2025-06/diudang_banner11.jpg" style="width:100%;display:block;">
        <div style="padding:32px 24px;text-align:center;">
            <h2 style="color:#d32f2f;">Xin lỗi về sự cố sản phẩm</h2>
            <p style="color:#333;">
                Chúng tôi rất tiếc vì sản phẩm trong đơn hàng của bạn gặp vấn đề và chân thành xin lỗi về trải nghiệm chưa tốt này.
            </p>
            <p style="color:#333;">
                Để bày tỏ sự tri ân và mong muốn phục vụ bạn tốt hơn, chúng tôi gửi tặng bạn một mã giảm giá:
            </p>
            <div style="margin:24px 0;">
                <strong>Mã giảm giá:</strong>
                <div style="margin:12px auto;max-width:220px;background:#e3f2fd;color:#1976d2;font-weight:600;padding:12px 0;border-radius:8px;font-size:18px;">
                    {{ $discount->name }}
                </div>
                <div style="margin-top:8px;color:#388e3c;">
                    Giá trị: {{ number_format($discount->value, 0, ',', '.') }}đ<br>
                    Hạn sử dụng: {{ \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y') }}
                </div>
            </div>
            <p style="color:#888;font-size:15px;">
                Mã giảm giá này có thể sử dụng cho đơn hàng tiếp theo.<br>
                Nếu cần hỗ trợ, hãy liên hệ với chúng tôi bất cứ lúc nào.
            </p>
            <!-- <div style="margin-top:24px;color:#888;font-size:13px;">
                Trân trọng,<br>
                {{ config('app.name') }}
            </div> -->
        </div>
    </div>
</body>

</html>