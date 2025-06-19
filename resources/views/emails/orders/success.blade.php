<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
</head>
<body style="background: #f7f7f7; font-family: Arial, sans-serif;">
    <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);overflow:hidden;">
        <img src="https://tools.dalathasfarm.com/assets/_file/2025/2025-06/diudang_banner11.jpg" style="width:100%;display:block;">
        <div style="padding:32px 24px;text-align:center;">
            <h2 style="color:#388e3c;">Cảm ơn bạn đã đặt hàng!</h2>
            <p style="color:#333;">Đơn hàng #{{ $order->id }} đã được tiếp nhận.</p>
            <p><strong>Tổng tiền:</strong> {{ number_format((float) $order->total_price) }} VND</p>
            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</p>

            <div style="margin-top:24px;text-align:left;">
                <h4>Sản phẩm:</h4>
                <ul>
                    @foreach ($order->orderDetails as $detail)
                        <li>{{ $detail->product->name }} (x{{ $detail->quantity }}) - {{ number_format($detail->subtotal) }} VND</li>
                    @endforeach
                </ul>
            </div>

            <p style="color:#888;font-size:14px;margin-top:24px;">Chúng tôi sẽ sớm liên hệ để giao hàng cho bạn.</p>
        </div>
    </div>
</body>
</html>
