<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VnPayController extends Controller
{
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

        $vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_Returnurl = config('vnpay.vnp_Returnurl');

        if (!$vnp_TmnCode || !$vnp_HashSecret || !$vnp_Url || !$vnp_Returnurl) {
            return response()->json([
                'status' => 500,
                'message' => 'Cấu hình VNPAY không hợp lệ!',
            ]);
        }

        $vnp_TxnRef = $orderCode;
        $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $vnp_TxnRef;
        $vnp_OrderType = 'other';
        $vnp_Amount = $validated['amount'] * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = http_build_query($inputData);
        $hashData = $query;
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $paymentUrl = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

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
        $inputData = $request->all();
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // if ($secureHash === $vnp_SecureHash) {
        //     if ($request->vnp_ResponseCode == '00') {
        //         return response()->json([
        //             'status' => 200,
        //             'message' => 'Thanh toán thành công',
        //             'data' => $request->all()
        //         ]);
        //     } else {
        //         return response()->json([
        //             'status' => 400,
        //             'message' => 'Thanh toán thất bại',
        //             'data' => $request->all()
        //         ]);
        //     }
        // } else {
        //     return response()->json([
        //         'status' => 403,
        //         'message' => 'Chữ ký không hợp lệ'
        //     ]);
        // } $frontendUrl = 'http://localhost:5173/vnpay_return';
        $frontendUrl = env('URL_FRONTEND', 'http://localhost:5173/vnpay_return');
        if ($secureHash === $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {

                return redirect($frontendUrl . '?' . http_build_query($request->all()));
            } else {
                return redirect($frontendUrl . '?' . http_build_query($request->all()));
            }
        } else {
            // Chữ ký không hợp lệ, cũng redirect về frontend với thông báo lỗi
            return redirect($frontendUrl . '?status=fail&message=Chữ ký không hợp lệ');
        }
    }
}
