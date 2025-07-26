<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Nhận mã giảm giá từ Hoa Shop</title>
</head>

<body style="background: #f7f7f7; font-family: Arial, sans-serif;">
    <div style="max-width:500px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);overflow:hidden;">
        <img src="https://tools.dalathasfarm.com/assets/_file/2025/2025-06/diudang_banner11.jpg" style="width:100%;display:block;">
        <div style="padding:32px 24px;text-align:center;">
            <h2 style="color:#1976d2;">Chào mừng bạn đến với Hoa Shop!</h2>
            <p style="color:#333;">
                Cảm ơn bạn đã đăng ký nhận thông tin khuyến mãi từ chúng tôi.<br>
                Dưới đây là các mã giảm giá dành riêng cho bạn:
            </p>
            @if(isset($discounts) && (is_array($discounts) || $discounts instanceof \Illuminate\Support\Collection))
            <table style="margin:24px auto;max-width:420px;background:#e3f2fd;border-radius:8px;font-size:16px;width:100%;box-shadow:0 2px 8px rgba(25,118,210,0.08);">
                <thead>
                    <tr>
                        <th style="padding:12px 8px;color:#1976d2;text-align:left;">Mã giảm giá</th>
                        <th style="padding:12px 8px;color:#1976d2;text-align:right;">Giá trị</th>
                        <th style="padding:12px 8px;color:#1976d2;text-align:right;">Đơn hàng tối thiểu</th>

                        <th style="padding:12px 8px;color:#1976d2;text-align:right;">Hạn sử dụng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discounts as $discount)
                    @if($discount && is_object($discount) && isset($discount->name))
                    <tr>
                        <td style="padding:12px 8px;font-weight:600;color:#1976d2;text-align:left;">
                            {{ $discount->name }}
                        </td>
                        <td style="padding:12px 8px;color:#388e3c;text-align:right;">
                            @if($discount->type == 'percent')
                            {{ number_format($discount->value, 0, ',', '.') }}%
                            @else
                            {{ number_format($discount->value, 0, ',', '.') }}đ
                            @endif
                        </td>
                        <td style="padding:12px 8px;color:#388e3c;text-align:right;">
                            @if($discount->min_total > 0)
                            {{ number_format($discount->min_total, 0, ',', '.') }}đ
                            @else
                            Không giới hạn
                            @endif
                        </td>
                        <td style="padding:12px 8px;text-align:right;">
                            {{ \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y') }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            @elseif(isset($discount) && is_object($discount))
            <!-- Hiển thị cho trường hợp chỉ có 1 discount -->
            <table style="margin:24px auto;max-width:420px;background:#e3f2fd;border-radius:8px;font-size:16px;width:100%;box-shadow:0 2px 8px rgba(25,118,210,0.08);">
                <thead>
                    <tr>
                        <th style="padding:12px 8px;color:#1976d2;text-align:left;">Mã giảm giá</th>
                        <th style="padding:12px 8px;color:#1976d2;text-align:right;">Giá trị</th>
                        <th style="padding:12px 8px;color:#1976d2;text-align:right;">Hạn sử dụng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:12px 8px;font-weight:600;color:#1976d2;text-align:left;">
                            {{ $discount->name }}
                        </td>
                        <td style="padding:12px 8px;color:#388e3c;text-align:right;">
                            @if($discount->type == 'percent')
                            {{ number_format($discount->value, 0, ',', '.') }}%
                            @else
                            {{ number_format($discount->value, 0, ',', '.') }}đ
                            @endif
                        </td>
                        <td style="padding:12px 8px;text-align:right;">
                            {{ \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y') }}
                        </td>
                    </tr>
                </tbody>
            </table>
            @else
            <p>Không có mã giảm giá nào được gửi.</p>
            @endif
            <p style="color:#888;font-size:15px;">
                Hãy sử dụng các mã này cho đơn hàng tiếp theo của bạn.<br>
                Chúc bạn có trải nghiệm mua sắm tuyệt vời tại Hoa Shop!
            </p>
        </div>
    </div>
</body>

</html>