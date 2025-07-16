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
            <p style="color:#333;">Đơn hàng #{{ $order->order_code }} đã được tiếp nhận.</p>
            <p><strong>Tổng tiền:</strong> {{ number_format((float) $order->total_price) }} VND</p>
            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</p>

            <div style="margin-top:24px;text-align:left;">
                <h4>Sản phẩm:</h4>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="border-bottom:1px solid #eee;padding:8px 4px;text-align:center;">Hình ảnh</th>
                            <th style="border-bottom:1px solid #eee;padding:8px 4px;text-align:left;">Tên sản phẩm</th>
                            <th style="border-bottom:1px solid #eee;padding:8px 4px;text-align:center;">Số lượng</th>
                            <th style="border-bottom:1px solid #eee;padding:8px 4px;text-align:right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $detail)
                        <tr>
                            <td style="padding:8px 4px;text-align:center;">
                                @if(!empty($detail->product->image_url))
                                <img src="{{ $detail->product->image_url }}" alt="{{ $detail->product->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
                                @else
                                <span style="color:#ccc;">Không có ảnh</span>
                                @endif
                            </td>
                            <td style="padding:8px 4px;">{{ $detail->product->name }}</td>
                            <td style="padding:8px 4px;text-align:center;">x{{ $detail->quantity }}</td>
                            <td style="padding:8px 4px;text-align:right;">{{ number_format($detail->subtotal) }} VND</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p style="color:#888;font-size:14px;margin-top:24px;">Chúng tôi sẽ sớm liên hệ để giao hàng cho bạn.</p>
        </div>
    </div>
</body>

</html>