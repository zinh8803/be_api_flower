<?php

namespace App\Http\Controllers;

use App\Models\DatSan;
use App\Models\LichSan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DatSanController extends Controller
{
    public function lichTrong($san_id, $ngay)
    {
        $lich = LichSan::where('san_id', $san_id)
            ->where('ngay', $ngay)
            ->where('trang_thai', 'trong')
            ->get();

        return response()->json($lich);
    }

    public function store(Request $request)
    {
        $request->validate([
            'san_id' => 'required|exists:san,id',
            'ngay_dat' => 'required|date',
            'gio_bat_dau' => 'required',
            'gio_ket_thuc' => 'required|after:gio_bat_dau'
        ]);

        // Kiểm tra trùng lịch
        $trung = DatSan::where('san_id', $request->san_id)
            ->where('ngay_dat', $request->ngay_dat)
            ->where('trang_thai', '!=', 'da_huy')
            ->where(function ($q) use ($request) {
                $q->whereBetween('gio_bat_dau', [$request->gio_bat_dau, $request->gio_ket_thuc])
                    ->orWhereBetween('gio_ket_thuc', [$request->gio_bat_dau, $request->gio_ket_thuc])
                    ->orWhereRaw('? BETWEEN gio_bat_dau AND gio_ket_thuc', [$request->gio_bat_dau]);
            })->exists();

        if ($trung) {
            return response()->json(['message' => 'Khung giờ đã được đặt'], 400);
        }

        $datSan = DatSan::create([
            'san_id' => $request->san_id,
            'user_id' => $request->user()->id,
            'ngay_dat' => $request->ngay_dat,
            'gio_bat_dau' => $request->gio_bat_dau,
            'gio_ket_thuc' => $request->gio_ket_thuc,
            'trang_thai' => 'cho_duyet',
            'tong_gia' => $request->tong_gia
        ]);

        return response()->json(['message' => 'Đặt sân thành công', 'dat_san' => $datSan], 201);
    }

    public function myBooking(Request $request)
    {
        $bookings = DatSan::with('san')->where('user_id', $request->user()->id)->get();
        return response()->json($bookings);
    }
    public function lichSuDat(Request $request)
{
    $ownerId = auth()->id();

    // Trạng thái muốn lấy, mặc định = đã thanh toán
    $status = $request->trang_thai ?? 'da_thanh_toan';

    $data = DatSan::where('trang_thai', $status)
        ->whereHas('san', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })
        ->with([
            'user:id,name,email,phone',
            'san:id,ten_san'
        ])
        ->orderBy('ngay_dat', 'asc')
        ->get();

    return response()->json($data);
}
    public function chiTiet(Request $request, $id)
    {
        $datSan = DatSan::with('user', 'san')->findOrFail($id);
        return response()->json($datSan);
    }

    /**
     * VNPay return URL (xác thực, lưu DB và chuyển hướng về frontend)
     */
    public function taoThanhToanDatSan(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $lichId = $request->input('lich_id');
            if (!$lichId) {
                return response()->json(['message' => 'Thiếu ID lịch trống'], 400);
            }

            $lich = LichSan::find($lichId);
            if (!$lich) {
                return response()->json(['message' => 'Không tìm thấy lịch trống'], 404);
            }

            $amount = $lich->gia ?? 0;
            if ($amount < 1000) {
                return response()->json(['message' => 'Giá trị thanh toán không hợp lệ'], 400);
            }

            // Thêm lựa chọn phương thức thanh toán
            $paymentMethod = $request->input('payment_method', 'vnpay');

            if ($paymentMethod === 'zalo') {
                // ZaloPay integration
                $config = config('zalo');
                $embeddata = json_encode([
                    'user_id' => $user->id,
                    'san_id' => $lich->san_id,
                    'ngay' => $lich->ngay,
                    'gio_bat_dau' => $lich->gio_bat_dau,
                    'gio_ket_thuc' => $lich->gio_ket_thuc,
                    "redirecturl" => env('ZALO_DAT_SAN_RETURN_URL', 'http://localhost:5173/ket-qua-thanh-toan'),

                ]);
                $items = json_encode([]); // luôn là chuỗi JSON array rỗng
                $transID = rand(0,1000000);
                $order = [
                    "app_id" => $config["app_id"],
                    "app_time" => round(microtime(true) * 1000),
                    "app_trans_id" => date("ymd") . "_" . $transID,
                    "app_user" => "user_" . $user->id,
                    "item" => $items,
                    "embed_data" => $embeddata,
                    "amount" => $amount,
                    "description" => "Thanh toán đặt sân #" . $lichId,
                    "bank_code" => "",
                ];
                $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"]
                    . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];
                $order["mac"] = hash_hmac("sha256", $data, $config["key1"]);

                // Đảm bảo endpoint chỉ là domain, không có /v2/create phía sau
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
                    // Lưu cache để xử lý khi ZaloPay trả về
                    Cache::put('zalo_dat_san_' . $order["app_trans_id"], [
                        'user_id'    => $user->id,
                        'san_id'     => $lich->san_id,
                        'ngay'       => $lich->ngay,
                        'gio_bat_dau'=> $lich->gio_bat_dau,
                        'gio_ket_thuc'=> $lich->gio_ket_thuc,
                    ], now()->addMinutes(30));

                    return response()->json([
                        'success' => true,
                        'payment_url' => $result['order_url'] ?? '',
                        'order_code' => $order["app_trans_id"],
                        'lich_id' => $lichId,
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

            // VNPay code
            // Tạo mã đơn hàng VNPay
            $orderCode = 'DS' . random_int(100000, 999999);

            $vnp_TmnCode    = config('vnpay.vnp_TmnCode');
            $vnp_HashSecret = config('vnpay.vnp_HashSecret');
            $vnp_Url        = config('vnpay.vnp_Url');
            $vnp_ReturnUrl  = config('vnpay.vnp_DatSan_Returnurl');

            $inputData = [
                'vnp_Version'   => '2.1.0',
                'vnp_TmnCode'   => $vnp_TmnCode,
                'vnp_Amount'    => $amount * 100,
                'vnp_Command'   => 'pay',
                'vnp_CreateDate'=> now()->format('YmdHis'),
                'vnp_CurrCode'  => 'VND',
                'vnp_IpAddr'    => $request->ip(),
                'vnp_Locale'    => 'vn',
                'vnp_OrderInfo' => 'Thanh toán lịch #' . $lichId,
                'vnp_OrderType' => 'billpayment',
                'vnp_ReturnUrl' => $vnp_ReturnUrl,
                'vnp_TxnRef'    => $orderCode,
            ];

            ksort($inputData);
            $query = http_build_query($inputData);
            $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

            $paymentUrl = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $secureHash;

            // Lưu cache để xử lý khi VNPay trả về
            Cache::put('vnp_dat_san_' . $orderCode, [
                'user_id'    => $user->id,
                'san_id'     => $lich->san_id,
                'ngay'       => $lich->ngay,
                'gio_bat_dau'=> $lich->gio_bat_dau,
                'gio_ket_thuc'=> $lich->gio_ket_thuc,
            ], now()->addMinutes(30));

            return response()->json([
                'success'     => true,
                'payment_url' => $paymentUrl,
                'order_code'  => $orderCode,
                'lich_id'     => $lichId,
                'method'      => 'vnpay'
            ]);
        } catch (\Throwable $e) {
            Log::error('VNPay/ZaloPay taoThanhToanDatSan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Lỗi server'], 500);
        }
    }

    public function vnpayReturnDatSan(Request $request)
    {
        Log::info('VNPay Return Data: ' . json_encode($request->all()));
        try {
            Log::info('VNPay Return Data1: ' . json_encode($request->all()));
            $inputData = $request->all();
            $vnp_HashSecret = config('vnpay.vnp_HashSecret');

            $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
            unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

            ksort($inputData);
            $hashData = '';
            $i = 0;
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . '=' . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            $frontendUrl = env(
                'URL_FRONTEND_DAT_SAN_RETURN',
                'http://localhost/HeThongChoThueSanTheThao/frontend/customer/vnpay_return.php'
            );

            if (
                $secureHash === $vnp_SecureHash &&
                ($inputData['vnp_ResponseCode'] ?? '') === '00'
            ) {

                $orderCode = $inputData['vnp_TxnRef'] ?? null;
                Log::info('OrderCode for cache: vnp_dat_san_' . $orderCode);
                $meta = $orderCode ? Cache::pull('vnp_dat_san_' . $orderCode) : null;
                Log::info('Meta from cache: ' . json_encode($meta));

                if (!$meta) {
                    Log::warning('Không lấy được thông tin từ cache với key: vnp_dat_san_' . $orderCode);
                }

                $userId = $meta['user_id'] ?? null;
                $sanId = $meta['san_id'] ?? null;
                $ngay = $meta['ngay'] ?? null;
                $gio_bat_dau = $meta['gio_bat_dau'] ?? null;
                $gio_ket_thuc = $meta['gio_ket_thuc'] ?? null;
                if (!$userId) $userId = $inputData['user_id'] ?? null;
                if (!$sanId)  $sanId  = $inputData['san_id'] ?? null;
                Log::info('VNPay Return Meta: ' . json_encode($meta));
                if ($sanId && $userId) {

                    DB::table('dat_san')->insert([
                        'san_id' => $sanId,
                        'user_id' => $userId,
                        'ngay_dat' => $ngay,
                        'gio_bat_dau' => $gio_bat_dau,
                        'gio_ket_thuc' => $gio_ket_thuc,
                        'tong_gia' => $inputData['vnp_Amount'] / 100,
                        'trang_thai' => 'da_thanh_toan',
                    ]);

                    DB::table('thanh_toan')->insert([
                        'dat_san_id' => $sanId,
                        'so_tien'    => $inputData['vnp_Amount'] / 100,
                        'ma_giao_dich' => $inputData['vnp_TransactionNo'] ?? '',
                        'phuong_thuc'  => 'VNPay',
                        'ngay_thanh_toan'    => now(),
                    ]);

                    DB::table('lich_san')
                        ->where('san_id', $sanId)
                        ->where('ngay', $ngay)
                        ->where('gio_bat_dau', $gio_bat_dau)
                        ->where('gio_ket_thuc', $gio_ket_thuc)
                        ->update([
                            'trang_thai' => 'da_dat',
                            'nguoi_dat_id' => $userId
                        ]);
                }
            }

            return redirect($frontendUrl . '?' . http_build_query($request->all()));
        } catch (\Throwable $e) {
            Log::error('VNPay vnpayReturnDatSan: ' . $e->getMessage());
            $frontendUrl = env(
                'URL_FRONTEND_DAT_SAN_RETURN',
                'http://localhost:5173/vnpay-return'
            );
            return redirect($frontendUrl . '?status=fail&message=' . urlencode('Lỗi server'));
        }
    }

    /**
     * Xử lý callback từ ZaloPay khi thanh toán thành công
     */
    public function zaloReturnDatSan(Request $request)
    {
        $result = [];
        try {
            $key2 = config('zalo.key2');
            // Lấy dữ liệu từ body (POST JSON hoặc form-data)
            $rawBody = $request->getContent();
            Log::info('ZaloPay Return Data (body): ' . $rawBody);

            $postdatajson = json_decode($rawBody, true);
            if (!is_array($postdatajson) || !isset($postdatajson["data"]) || !isset($postdatajson["mac"])) {
                // Nếu không phải JSON, thử lấy từ form-data (trường hợp test thủ công)
                $postdatajson = $request->all();
            }

            if (!is_array($postdatajson) || !isset($postdatajson["data"]) || !isset($postdatajson["mac"])) {
                Log::error('ZaloPay callback error: Dữ liệu callback không hợp lệ', ['body' => $rawBody]);
                $result["return_code"] = -1;
                $result["return_message"] = "invalid callback data";
                return response()->json($result);
            }

            $mac = hash_hmac("sha256", $postdatajson["data"], $key2);
            $requestmac = $postdatajson["mac"];

            if (strcmp($mac, $requestmac) != 0) {
                $result["return_code"] = -1;
                $result["return_message"] = "mac not equal";
            } else {
                $datajson = json_decode($postdatajson["data"], true);
                $appTransId = $datajson["app_trans_id"] ?? null;
                // Lấy thông tin từ cache
                $meta = $appTransId ? Cache::pull('zalo_dat_san_' . $appTransId) : null;

                if ($meta) {
                    // Tạo đơn đặt sân và cập nhật lịch
                    $datSanId = DB::table('dat_san')->insertGetId([
                        'san_id' => $meta['san_id'],
                        'user_id' => $meta['user_id'],
                        'ngay_dat' => $meta['ngay'],
                        'gio_bat_dau' => $meta['gio_bat_dau'],
                        'gio_ket_thuc' => $meta['gio_ket_thuc'],
                        'tong_gia' => $datajson['amount'] ?? 0,
                        'trang_thai' => 'da_thanh_toan',
                    ]);

                    DB::table('thanh_toan')->insert([
                        'dat_san_id' => $datSanId,
                        'so_tien'    => $datajson['amount'] ?? 0,
                        'ma_giao_dich' => $datajson['zp_trans_id'] ?? '',
                        'phuong_thuc'  => 'zalopay',
                        'ngay_thanh_toan'    => now(),
                    ]);

                    DB::table('lich_san')
                        ->where('san_id', $meta['san_id'])
                        ->where('ngay', $meta['ngay'])
                        ->where('gio_bat_dau', $meta['gio_bat_dau'])
                        ->where('gio_ket_thuc', $meta['gio_ket_thuc'])
                        ->update([
                            'trang_thai' => 'da_dat',
                            'nguoi_dat_id' => $meta['user_id']
                        ]);
                }

                $result["return_code"] = 1;
                $result["return_message"] = "success";
            }
        } catch (\Exception $e) {
            $result["return_code"] = 0;
            $result["return_message"] = $e->getMessage();
            Log::error('ZaloPay callback error: ' . $e->getMessage());
        }
        return response()->json($result);
    }

    public function thongKe(Request $request)
{
    $ownerId = auth()->id();

    $query = DatSan::where('trang_thai', 'da_thanh_toan')
        ->whereHas('san', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        });

    if ($request->ngay) {
        $query->whereDate('ngay_dat', $request->ngay);
    }

    if ($request->thang) {
        $query->whereMonth('ngay_dat', $request->thang);
    }

    if ($request->nam) {
        $query->whereYear('ngay_dat', $request->nam);
    }

    if ($request->from && $request->to) {
        $query->whereBetween('ngay_dat', [$request->from, $request->to]);
    }

    $lich = $query->with('san:id,ten_san')->get();

    return response()->json([
        'doanh_thu' => $lich->sum('tong_gia'),
        'so_don'    => $lich->count(),
        'lich'      => $lich
    ]);
}

public function customerMyBookings(Request $request)
    {
        $userId = $request->user()->id;

        $bookings = DatSan::with(['san:id,ten_san,dia_chi,hinh_anh'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

            
        return response()->json([
            'success' => true,
            'data'    => $bookings
        ]);
    }

    /**
     * Xử lý redirect từ ZaloPay về sau khi thanh toán (khác với callback server)
     */
   
}
