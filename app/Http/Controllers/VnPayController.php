<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class VnPayController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @OA\Post(
     *     path="/api/payment",
     *     tags={"VnPay"},
     *     summary="Tạo URL thanh toán VNPAY",
     *     description="Tạo URL thanh toán dựa trên mã đơn hàng và số tiền. Người dùng chọn ngân hàng ở trang VNPAY.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_code", "amount"},
     *             @OA\Property(property="amount", type="number", example=150000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="URL thanh toán thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Tạo URL thanh toán thành công"),
     *             @OA\Property(property="payment_url", type="string", example="https://sandbox.vnpayment.vn/...")
     *         )
     *     )
     * )
     */
    public function createPayment(Request $request)
    {
        $orderCode = 'SP' . random_int(100000, 999999);
        $validated = $request->validate([
            'order_code' => $orderCode,
            'amount' => 'required|numeric|min:1000',
        ]);

        $paymentUrl = $this->paymentService->createVnPayUrl($request, $validated);

        if (!$paymentUrl) {
            return response()->json([
                'status' => 500,
                'message' => 'Cấu hình VNPAY không hợp lệ!',
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Tạo URL thanh toán thành công',
            'payment_url' => $paymentUrl
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/vnpay_return",
     *     tags={"VnPay"},
     *     summary="Nhận kết quả thanh toán từ VNPAY",
     *     description="Xử lý kết quả thanh toán và xác minh chữ ký.",
     *     @OA\Response(
     *         response=200,
     *         description="Thanh toán thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Thanh toán thành công")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Thanh toán thất bại hoặc sai chữ ký"
     *     )
     * )
     */
    public function vnpayReturn(Request $request)
    {
        $redirectUrl = $this->paymentService->processVnPayReturn($request);
        return redirect($redirectUrl);
    }
}
