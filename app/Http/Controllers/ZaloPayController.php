<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZaloPayController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/zalopay",
     *     tags={"ZaloPay"},
     *     summary="Tạo URL thanh toán ZaloPay",
     *     description="Tạo URL thanh toán dựa trên mã đơn hàng và số tiền.",
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
     *             @OA\Property(property="returncode", type="integer", example=1000),
     *             @OA\Property(property="returnmessage", type="string", example="Success"),
     *             @OA\Property(property="orderurl", type="string", example="https://zalopay.vn/...")
     *         )
     *     )
     * )
     */
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'order_code' => 'nullable|string',
        ]);

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
                Log::error('ZaloPay API call failed', [
                    'url' => $zaloUrl,
                    'order' => $order,
                    'error' => $error,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể kết nối ZaloPay: ' . ($error['message'] ?? 'unknown error')
                ], 500);
            }
            $result = json_decode($resp, true);
        } catch (\Throwable $ex) {
            Log::error('ZaloPay API exception', [
                'url' => $zaloUrl,
                'order' => $order,
                'exception' => $ex->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi gọi ZaloPay: ' . $ex->getMessage()
            ], 500);
        }

        Log::info('ZaloPay API response', [
            'url' => $zaloUrl,
            'order' => $order,
            'response' => $resp,
            'result' => $result,
        ]);

        if (isset($result['return_code']) && $result['return_code'] == 1) {
            return response()->json([
                'success' => true,
                'payment_url' => $result['order_url'] ?? '',
                'order_code' => $order["app_trans_id"],
                'method' => 'zalo'
            ]);
        } else {
            Log::error('ZaloPay API error', [
                'url' => $zaloUrl,
                'order' => $order,
                'response' => $resp,
                'result' => $result,
            ]);
            return response()->json([
                'success' => false,
                'message' => $result['return_message'] ?? 'Lỗi tạo đơn hàng ZaloPay'
            ], 500);
        }
    }

    public function zaloReturn(Request $request)
    {
        $key2 = config('zalopay.key2');
        $frontendUrl = env('URL_ZALO_FRONTEND', 'http://localhost:5173/zalo_return');
        $result = [];

        $postdata = file_get_contents('php://input');
        $postdatajson = json_decode($postdata, true);

        // Log dữ liệu callback từ ZaloPay
        \Log::info('ZaloPay callback received', [
            'raw_postdata' => $postdata,
            'decoded_postdata' => $postdatajson,
            'query' => $request->query(),
        ]);

        // Nếu không có POST data, đây là redirect từ trình duyệt người dùng
        if (empty($postdata) || !is_array($postdatajson)) {
            // Lấy tất cả query params và redirect về FE
            $params = $request->query();
            \Log::info('ZaloPay FE redirect', ['params' => $params]);
            return redirect($frontendUrl . '?' . http_build_query($params));
        }

        // Nếu có POST data (callback server-to-server)
        if (!isset($postdatajson["data"], $postdatajson["mac"])) {
            \Log::error('ZaloPay callback: missing data or mac', [
                'decoded_postdata' => $postdatajson,
            ]);
            return redirect($frontendUrl . '?status=fail&message=Thiếu dữ liệu hoặc mã xác thực');
        }

        $mac = hash_hmac("sha256", $postdatajson["data"], $key2);
        $requestmac = $postdatajson["mac"];

        if (strcmp($mac, $requestmac) != 0) {
            \Log::warning('ZaloPay callback MAC mismatch', [
                'calculated_mac' => $mac,
                'received_mac' => $requestmac,
            ]);
            return redirect($frontendUrl . '?status=fail&message=MAC không hợp lệ');
        } else {
            $datajson = json_decode($postdatajson["data"], true);
            \Log::info('ZaloPay callback datajson', [
                'datajson' => $datajson,
            ]);
            if (isset($datajson['return_code']) && $datajson['return_code'] == 1) {
                return redirect($frontendUrl . '?' . http_build_query($datajson));
            } else {
                $message = $datajson['return_message'] ?? 'Giao dịch thất bại';
                \Log::error('ZaloPay callback failed', [
                    'datajson' => $datajson,
                    'message' => $message,
                ]);
                return redirect($frontendUrl . '?status=fail&message=' . urlencode($message));
            }
        }
    }
}
