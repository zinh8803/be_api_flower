<?php

namespace App\Repositories\Eloquent;

use App\Events\OrderCreated;
use App\Helpers\ImageHelper;
use App\Jobs\SendOrderMail;
use App\Jobs\SendOrderStatusMailJob;
use App\Models\Discount;
use App\Models\ImportReceiptDetail;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderSuccessMail;
use App\Models\OrderDetail;
use App\Models\ProductReport;
use App\Models\ProductSize;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\UploadedFile;

class OrderRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }
    protected function validateDeliveryTime(array $data)
    {
        $now = Carbon::now();

        if ($now->hour > 16 || ($now->hour == 16 && $now->minute > 0)) {
            $requested = Carbon::parse($data['delivery_date'] . ' ' . ($data['delivery_time'] ?? '08:00'));
            if (Carbon::parse($data['delivery_date'])->isToday()) {
                throw new \Exception('Sau 16h không nhận đơn giao trong ngày hôm nay.');
            }
            if ($requested->hour < 8 || $requested->hour > 18 || ($requested->hour == 18 && $requested->minute > 0)) {
                throw new \Exception('Thời gian giao hàng phải từ 08:00 đến 18:00.');
            }
            return;
        }

        if (!empty($data['is_express'])) {
            $minExpressTime = $now->copy()->addHours(3);
            $requested = Carbon::parse($data['delivery_date'] . ' ' . ($data['delivery_time'] ?? '08:00'));
            if ($requested->lt($minExpressTime)) {
                throw new \Exception('Giao nhanh phải cách thời điểm đặt ít nhất 3 tiếng.');
            }
            if ($requested->hour < 8 || $requested->hour > 18 || ($requested->hour == 18 && $requested->minute > 0)) {
                throw new \Exception('Giao nhanh chỉ trong khung giờ 08:00 đến 18:00.');
            }
        } else if (!empty($data['delivery_date']) && !empty($data['delivery_time'])) {
            $requested = Carbon::parse($data['delivery_date'] . ' ' . $data['delivery_time']);
            if ($requested->lt(now())) {
                throw new \Exception('Không thể chọn thời gian giao hàng trong quá khứ.');
            }
            if ($requested->hour < 8 || $requested->hour > 18 || ($requested->hour == 18 && $requested->minute > 0)) {
                throw new \Exception('Thời gian giao hàng phải từ 08:00 đến 18:00.');
            }
        }
    }
    public function createOrder(array $data)
    {
        $this->validateDeliveryTime($data);
        Log::info("1");
        return DB::transaction(function () use ($data) {

            $items = $data['products'];
            unset($data['products']);

            $orderDetails = [];
            $orderSubtotal = 0;
            $discountAmount = 0;

            foreach ($items as $item) {
                $productSize = ProductSize::with(['recipes.flower', 'product'])->findOrFail($item['product_size_id']);
                $qty = $item['quantity'];

                // if ($this->isForcedFlower($productSize)) {
                //     throw new \Exception("Sản phẩm {$productSize->product->name} hiện đang ở trạng thái hoa ép, không thể đặt mua.");
                // }

                foreach ($productSize->recipes as $recipe) {
                    $need = $recipe->quantity * $qty;
                    $stock = ImportReceiptDetail::where('flower_id', $recipe->flower_id)->sum(DB::raw('quantity - used_quantity'));

                    if ($stock < $need) {
                        throw new \Exception("Không đủ tồn kho cho hoa {$recipe->flower->name}");
                    }
                    $deliveryDate = !empty($data['is_express']) ? now()->format('Y-m-d') : $data['delivery_date'];
                    if (Carbon::parse($deliveryDate)->gt(now())) {
                        continue;
                    }
                    $this->deductStock($recipe->flower_id, $need);
                }

                $unitPrice = $productSize->price;
                $subTotal = $unitPrice * $qty;
                $orderSubtotal += $subTotal;

                $orderDetails[] = [
                    'product_id' => $productSize->product_id,
                    'product_size_id' => $productSize->id,
                    'size' => $productSize->size ?? null,
                    'quantity' => $qty,
                    'price' => $unitPrice,
                    'subtotal' => $subTotal,
                ];
            }

            if (!empty($data['discount_id'])) {
                $discount = Discount::findOrFail($data['discount_id']);

                if (!$discount->isActive()) {
                    throw new \Exception('Mã giảm giá không còn hiệu lực.');
                }
                if ($orderSubtotal < ($discount->min_total ?? 0)) {
                    throw new \Exception('Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã giảm giá.');
                }
                $discountAmount = $discount->type === 'percent'
                    ? $orderSubtotal * $discount->value / 100
                    : $discount->value;

                $discountAmount = min($discountAmount, $orderSubtotal);
            }

            $orderTotal = $orderSubtotal - $discountAmount;
            do {
                $orderCode = 'SP' . date('YmdHis') . rand(100, 999);
            } while (Order::where('order_code', $orderCode)->exists());
            $order = Order::create([
                'order_code' => $orderCode,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'] ?? null,
                'payment_method' => $data['payment_method'],
                // 'user_id' => $data['user_id'] ?? auth()->id() ?? null,

                'delivery_date' => $data['delivery_date'] ?? null,
                'delivery_time' => !empty($data['is_express']) ? null : ($data['delivery_time'] ?? null),
                'is_express' => $data['is_express'] ?? false,
                'status_stock' => (
                    (!empty($data['delivery_date']) && Carbon::parse($data['delivery_date'])->isToday())
                    ? 'đã trừ kho'
                    : 'chưa trừ kho'
                ),
                'user_id' => $data['user_id'] ?? auth()->id() ?? 4,
                'status' => 'đang xử lý',
                'buy_at' => now(),
                'discount_id' => $data['discount_id'] ?? null,
                'discount_amount' => $discountAmount,
                'total_price' => $orderTotal,
            ]);
            foreach ($orderDetails as $detail) {
                $order->orderDetails()->create($detail);
            }

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new OrderPlacedNotification($order));
            }

            $employees = User::where('role', 'employee')->get();
            foreach ($employees as $employee) {
                $employee->notify(new OrderPlacedNotification($order));
            }

            $order->load(['user', 'discount']);
            event(new OrderCreated($order));

            $order->load('discount', 'orderDetails.productSize.product');
            if (!empty($order->email)) {
                SendOrderMail::dispatch($order, $order->email);
            }


            return $order;
        });
    }

    public function processOrdersForToday()
    {
        $today = now()->format('Y-m-d');
        $orders = Order::where('delivery_date', $today)
            ->where('status_stock', '!=', 'đã trừ kho')
            ->get();

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $productSize = $detail->productSize;
                foreach ($productSize->recipes as $recipe) {
                    $need = $recipe->quantity * $detail->quantity;
                    $this->deductStock($recipe->flower_id, $need);
                }
            }
            $order->status_stock = 'đã trừ kho';
            $order->save();
        }
    }

    protected function isForcedFlower(ProductSize $productSize)
    {
        foreach ($productSize->recipes as $recipe) {
            $importReceiptDetail = $recipe->flower
                ? $recipe->flower->importReceiptDetails()->orderByDesc('import_date')->first()
                : null;
            $status = null;
            if ($importReceiptDetail && !empty($importReceiptDetail->import_date)) {
                $importDate = Carbon::parse($importReceiptDetail->import_date);
                $now = Carbon::now();
                $nextDayTenPM = $importDate->copy()->addDay()->setTime(22, 0, 0);

                if ($now->lt($nextDayTenPM)) {
                    $status = 'hoa tươi';
                } else {
                    $status = 'hoa ép';
                }
            }
            if ($status === 'hoa ép') {
                return true;
            }
        }
        return false;
    }

    public function deductStock($flowerId, $neededQty)
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        $detailsYesterday = ImportReceiptDetail::where('flower_id', $flowerId)
            ->whereDate('import_date', $yesterday)
            ->orderBy('import_date')
            ->get();

        $detailsToday = ImportReceiptDetail::where('flower_id', $flowerId)
            ->whereDate('import_date', $today)
            ->orderBy('import_date')
            ->get();

        $details = $detailsYesterday->concat($detailsToday);

        $stock = $details->sum(function ($detail) {
            return $detail->quantity - $detail->used_quantity;
        });

        if ($stock < $neededQty) {
            throw new \Exception("Không đủ tồn kho cho sản phẩm này!");
        }

        foreach ($details as $detail) {
            $available = $detail->quantity - $detail->used_quantity;
            if ($neededQty <= 0)
                break;
            if ($available <= 0)
                continue;

            $used = min($neededQty, $available);
            $detail->used_quantity += $used;
            $detail->save();

            $neededQty -= $used;
        }

        if ($neededQty > 0) {
            throw new \Exception("Không đủ tồn kho cho sản phẩm này!");
        }
    }
    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function update(int $id, array $data)
    {
        $order = $this->findById($id);
        $status = $order->status;

        $statusFlow = ['đang xử lý', 'đã xác nhận', 'đang giao hàng', 'hoàn thành'];
        $currentIndex = array_search($status, $statusFlow);

        if (isset($data['status'])) {
            $newStatus = $data['status'];

            if ($newStatus === 'đã hủy') {
                if ($status !== 'đang xử lý') {
                    throw new \Exception('Chỉ đơn hàng đang xử lý mới được hủy.');
                }

                foreach ($order->orderDetails as $detail) {
                    $productSize = $detail->productSize;
                    if ($productSize && $productSize->recipes) {
                        foreach ($productSize->recipes as $recipe) {
                            $qtyReturn = $recipe->quantity * $detail->quantity;
                            $importDetails = ImportReceiptDetail::where('flower_id', $recipe->flower_id)
                                ->orderByDesc('import_date')
                                ->get();

                            $remain = $qtyReturn;
                            foreach ($importDetails as $importDetail) {
                                $used = $importDetail->used_quantity;
                                if ($used > 0) {
                                    $returnQty = min($remain, $used);
                                    $importDetail->used_quantity -= $returnQty;
                                    $importDetail->save();
                                    $remain -= $returnQty;
                                    if ($remain <= 0) break;
                                }
                            }
                        }
                    }
                }
            } else {
                $nextStatus = $currentIndex !== false && $currentIndex < count($statusFlow) - 1
                    ? $statusFlow[$currentIndex + 1]
                    : $statusFlow[$currentIndex];

                if ($newStatus !== $nextStatus) {
                    throw new \Exception('Không thể chuyển trạng thái đơn hàng không đúng thứ tự.');
                }
            }
        }

        $order->update($data);

        if (isset($data['status']) && $data['status'] !== $status && !empty($order->email)) {
            try {
                SendOrderStatusMailJob::dispatch($order, $data['status']);
            } catch (\Exception $e) {
                Log::error('Gửi mail trạng thái đơn hàng thất bại', [
                    'order_id' => $order->id,
                    'email' => $order->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $order;
    }

    public function cancelOrderByUser(int $id)
    {
        $user = Auth()->user();
        if (!$user) {
            throw new \Exception('Bạn cần đăng nhập để hủy đơn hàng.');
        }

        $order = $this->findById($id);
        if (!$order) {
            throw new \Exception('Đơn hàng không tồn tại.');
        }

        if ($order->user_id !== $user->id) {
            throw new \Exception('Bạn không có quyền hủy đơn hàng này.');
        }

        if ($order->status !== 'đang xử lý') {
            throw new \Exception('Chỉ đơn hàng đang xử lý mới được hủy.');
        }

        // Trả lại tồn kho cho các hoa đã dùng
        if (!empty($order->delivery_date) && Carbon::parse($order->delivery_date)->isToday() && $order->status_stock === 'đã trừ kho') {
        foreach ($order->orderDetails as $detail) {
            $productSize = $detail->productSize;
            if ($productSize && $productSize->recipes) {
                foreach ($productSize->recipes as $recipe) {
                    $qtyReturn = $recipe->quantity * $detail->quantity;
                    $importDetails = ImportReceiptDetail::where('flower_id', $recipe->flower_id)
                        ->orderByDesc('import_date')
                        ->get();

                    $remain = $qtyReturn;
                    foreach ($importDetails as $importDetail) {
                        $used = $importDetail->used_quantity;
                        if ($used > 0) {
                            $returnQty = min($remain, $used);
                            $importDetail->used_quantity -= $returnQty;
                            $importDetail->save();
                            $remain -= $returnQty;
                            if ($remain <= 0) break;
                        }
                    }
                }
            }
        }
    }

        $order->status = 'đã hủy';
        $order->status_stock = 'đã hủy';
        $order->save();

        // Gửi mail nếu có email
        if (!empty($order->email)) {
            try {
                SendOrderStatusMailJob::dispatch($order, 'đã hủy');
            } catch (\Exception $e) {
                Log::error('Gửi mail trạng thái đơn hàng thất bại', [
                    'order_id' => $order->id,
                    'email' => $order->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $order;
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function all($filters = [])
    {
        $this->processOrdersForToday();
        // return $this->model->orderBy('id', 'desc')->paginate(10);
        $query = $this->model->with('orderDetails.productSize.product', 'productReports')
            ->orderBy('buy_at', 'desc')
            ->orderBy('id', 'desc');

        if (!empty($filters['from_date'])) {
            $query->whereDate('buy_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('buy_at', '<=', $filters['to_date']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(10);
    }

    public function findByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    //su dung token

    public function OrderByUser()
    {
        $user = Auth()->user();
        if (!$user) {
            abort(401, 'Bạn cần đăng nhập để xem đơn hàng của mình.');
        }

        $orders = $this->model->orderBy('buy_at', 'desc')->where('user_id', $user->id)->paginate(10);

        // Log::info('Lấy danh sách đơn hàng của người dùng', ['user_id' => $user->id]);
        // Log::info('Danh sách đơn hàng', ['orders' => $orders->toArray()]);

        return $orders;
    }

    public function OrderDetailById(int $id)
    {
        $user = Auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Bạn cần đăng nhập để xem đơn hàng của mình.'], 401);
        }

        $order = $this->model->with('orderDetails.productSize.product', 'productReports')->find($id);
        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại.'], 404);
        }

        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Bạn không có quyền xem đơn hàng này.'], 403);
        }

        return $order;
    }
    public function show(int $id)
    {
        $order = $this->model->with('orderDetails.productSize.product', 'productReports')->find($id);
        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại.'], 404);
        }
        return $order;
    }
    protected function handleImageUpload(&$data)
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile && $data['image']->isValid()) {
            try {
                $imageUrl = ImageHelper::uploadImage($data['image'], 'products');
                Log::info('Image uploaded successfully', ['image_url' => $imageUrl]);
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                }
                unset($data['image']);
            } catch (\Exception $e) {
                Log::error('Image upload failed', ['error' => $e->getMessage()]);
            }
        }
    }
    public function createReport(array $data)
    {
        if (!isset($data['reports']) || !is_array($data['reports']) || count($data['reports']) === 0) {
            throw new \Exception('Không có dữ liệu báo cáo.');
        }

        $orderId = $data['reports'][0]['order_id'] ?? null;
        $order = Order::find($orderId);
        if (!$order || $order->status !== 'hoàn thành') {
            throw new \Exception('Chỉ báo cáo đơn đã giao thành công!');
        }

        $userId = $data['user_id'] ?? auth()->id();
        $toInsert = [];
        foreach ($data['reports'] as $report) {
            $this->handleImageUpload($report);
            $orderDetail = OrderDetail::find($report['order_detail_id']);
            if (!$orderDetail) {
                throw new \Exception('Chi tiết đơn hàng không hợp lệ!');
            }
            if ($report['quantity'] > $orderDetail->quantity) {
                throw new \Exception('Số lượng báo cáo vượt quá số lượng đã mua!');
            }
            $toInsert[] = [
                'order_id' => $report['order_id'],
                'order_detail_id' => $report['order_detail_id'],
                'user_id' => $userId,
                'quantity' => $report['quantity'],
                'reason' => $report['reason'] ?? null,
                'image_url' => $report['image_url'] ?? null,
                'action' => $report['action'] ?? null,
                'status' => 'đang xử lý',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }



        return DB::transaction(function () use ($order, $toInsert) {
            ProductReport::insert($toInsert);
            $order->status = 'đang xử lý báo cáo';
            $order->save();

            return true;
        });
    }

    public function handleProductReport(array $data)
    {
        if (!isset($data['order_id'])) {
            throw new \Exception('Thiếu ID báo cáo!');
        }

        $report = ProductReport::where('order_id', $data['order_id'])->first();
        if (!$report) {
            throw new \Exception('Báo cáo không tồn tại!');
        }

        return DB::transaction(function () use ($report, $data) {
            if (isset($data['admin_note'])) {
                $report->admin_note = $data['admin_note'];
            }

            if (isset($data['status'])) {
                $report->status = $data['status'];
            }

            $report->save();

            if (isset($data['order_status'])) {
                $order = Order::find($report->order_id);
                if ($order) {
                    $order->status = $data['order_status'];
                    $order->save();
                }
            }

            if (!isset($data['order_status'])) {
                $order = Order::find($report->order_id);
                if ($order) {
                    $pendingReports = ProductReport::where('order_id', $order->id)
                        ->where('status', 'đang xử lý')
                        ->count();

                    if ($pendingReports === 0) {
                        $order->status = 'hoàn thành';
                        $order->save();
                    }
                }
            }

            return $report;
        });
    }

    public function updateReport(int $reportId, array $data)
    {
        $report = Order::find($reportId);
        if (!$report) {
            throw new \Exception('Báo cáo không tồn tại!');
        }
        $report->update($data);
        return $report;
    }

    public function deleteReport($orderId): bool
    {
        $reports = ProductReport::where('order_id', $orderId)->get();
        if ($reports->isEmpty()) {
            throw new \Exception('Báo cáo không tồn tại!');
        }
        foreach ($reports as $report) {
            $report->delete();
        }
        $order = Order::find($orderId);
        $order->status = 'hoàn thành';
        $order->save();
        return true;
    }
}
