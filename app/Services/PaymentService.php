<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentService extends BaseService
{
    public function createVnPayUrl(Request $request, array $validated): ?string
    {
        $orderCode = 'SP' . random_int(100000, 999999);

        $vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_Returnurl = config('vnpay.vnp_Returnurl');

        if (!$vnp_TmnCode || !$vnp_HashSecret || !$vnp_Url || !$vnp_Returnurl) {
            $this->logError('VNPAY configuration invalid');
            return null;
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
        $vnp_SecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

        return $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;
    }

    public function processVnPayReturn(Request $request): string
    {
        $inputData = $request->all();
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

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
        $frontendUrl = env('URL_FRONTEND', 'http://localhost:5173/vnpay_return');

        if ($secureHash === $vnp_SecureHash) {
            return $frontendUrl . '?' . http_build_query($request->all());
        }

        return $frontendUrl . '?status=fail&message=' . urlencode('Chữ ký không hợp lệ');
    }

    public function createZaloPayPayment(array $validated): array
    {
        $orderCode = $validated['order_code'] ?? '';
        if (empty($orderCode)) {
            $orderCode = 'SP' . random_int(100000, 999999);
        }

        $config = config('zalopay');
        $items = json_encode([]);
        $embeddata = json_encode([
            'order_code' => $orderCode,
            "redirecturl" => $config['return_url']
        ]);
        $transID = rand(0, 1000000);

        $order = [
            "app_id" => $config["appid"],
            "app_time" => round(microtime(true) * 1000),
            "app_trans_id" => date("ymd") . "_" . $transID,
            "app_user" => "demo",
            "item" => $items,
            "embed_data" => $embeddata,
            "amount" => $validated['amount'],
            "description" => "ZaloPay Integration Demo",
            "bank_code" => ""
        ];

        $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"]
            . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];
        $order["mac"] = hash_hmac("sha256", $data, $config["key1"]);

        $zaloUrl = rtrim($config["endpoint"], '/') . '/v2/create';

        try {
            $context = stream_context_create([
                "http" => [
                    "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                    "method" => "POST",
                    "content" => http_build_query($order)
                ]
            ]);
            $resp = @file_get_contents($zaloUrl, false, $context);
            if ($resp === false) {
                $error = error_get_last();
                Log::error('ZaloPay API call failed', ['url' => $zaloUrl, 'order' => $order, 'error' => $error]);
                return [
                    'success' => false,
                    'message' => 'Không thể kết nối ZaloPay: ' . ($error['message'] ?? 'unknown error')
                ];
            }
            $result = json_decode($resp, true);
        } catch (\Throwable $ex) {
            Log::error('ZaloPay API exception', ['url' => $zaloUrl, 'order' => $order, 'exception' => $ex->getMessage()]);
            return [
                'success' => false,
                'message' => 'Lỗi gọi ZaloPay: ' . $ex->getMessage()
            ];
        }

        if (isset($result['return_code']) && $result['return_code'] == 1) {
            return [
                'success' => true,
                'payment_url' => $result['order_url'] ?? '',
                'order_code' => $order["app_trans_id"],
                'method' => 'zalo'
            ];
        }

        return [
            'success' => false,
            'message' => $result['return_message'] ?? 'Lỗi tạo đơn hàng ZaloPay'
        ];
    }
}
