<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZaloPayController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

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

        $result = $this->paymentService->createZaloPayPayment($validated);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    public function zaloReturn(Request $request)
    {
        $key2 = config('zalopay.key2');
        $frontendUrl = env('URL_ZALO_FRONTEND', 'http://localhost:5173/zalo_return');

        $postdata = file_get_contents('php://input');
        $postdatajson = json_decode($postdata, true);

        Log::info('ZaloPay callback received', [
            'raw_postdata' => $postdata,
            'decoded_postdata' => $postdatajson,
            'query' => $request->query(),
        ]);

        if (empty($postdata) || !is_array($postdatajson)) {
            $params = $request->query();
            Log::info('ZaloPay FE redirect', ['params' => $params]);
            return redirect($frontendUrl . '?' . http_build_query($params));
        }

        if (!isset($postdatajson["data"], $postdatajson["mac"])) {
            Log::error('ZaloPay callback: missing data or mac', [
                'decoded_postdata' => $postdatajson,
            ]);
            return redirect($frontendUrl . '?status=fail&message=Thiếu dữ liệu hoặc mã xác thực');
        }

        $mac = hash_hmac("sha256", $postdatajson["data"], $key2);
        $requestmac = $postdatajson["mac"];

        if (strcmp($mac, $requestmac) != 0) {
            Log::warning('ZaloPay callback MAC mismatch', [
                'calculated_mac' => $mac,
                'received_mac' => $requestmac,
            ]);
            return redirect($frontendUrl . '?status=fail&message=MAC không hợp lệ');
        } else {
            $datajson = json_decode($postdatajson["data"], true);
            Log::info('ZaloPay callback datajson', [
                'datajson' => $datajson,
            ]);
            if (isset($datajson['return_code']) && $datajson['return_code'] == 1) {
                return redirect($frontendUrl . '?' . http_build_query($datajson));
            } else {
                $message = $datajson['return_message'] ?? 'Giao dịch thất bại';
                Log::error('ZaloPay callback failed', [
                    'datajson' => $datajson,
                    'message' => $message,
                ]);
                return redirect($frontendUrl . '?status=fail&message=' . urlencode($message));
            }
        }
    }
}
