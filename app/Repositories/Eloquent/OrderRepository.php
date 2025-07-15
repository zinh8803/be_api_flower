<?php

namespace App\Repositories\Eloquent;

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
use App\Models\ProductSize;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class OrderRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {

            $items = $data['products'];
            unset($data['products']);

            $orderDetails = [];
            $orderSubtotal = 0;
            $discountAmount = 0;

            foreach ($items as $item) {
                $productSize = ProductSize::with(['recipes.flower', 'product'])->findOrFail($item['product_size_id']);
                $qty = $item['quantity'];

                if ($this->isForcedFlower($productSize)) {
                    throw new \Exception("Sản phẩm {$productSize->product->name} hiện đang ở trạng thái hoa ép, không thể đặt mua.");
                }

                foreach ($productSize->recipes as $recipe) {
                    $need = $recipe->quantity * $qty;
                    $stock = ImportReceiptDetail::where('flower_id', $recipe->flower_id)->sum(DB::raw('quantity - used_quantity'));

                    if ($stock < $need) {
                        throw new \Exception("Không đủ tồn kho cho hoa {$recipe->flower->name}");
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
                'user_id' => $data['user_id'] ?? auth()->id() ?? null,
                'status' => 'đang xử lý',
                'buy_at' => now(),
                'discount_id' => $data['discount_id'] ?? null,
                'discount_amount' => $discountAmount,
                'total_price' => $orderTotal,
            ]);

            foreach ($orderDetails as $detail) {
                $order->orderDetails()->create($detail);
            }
            $order->load('orderDetails.product', 'discount', 'orderDetails.productSize');

            if (!empty($order->email)) {
                SendOrderMail::dispatch($order, $order->email);
            }


            return $order;
        });
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
        $now = now();
        $tenPmNextDay = $now->copy()->addDay()->setTime(22, 0, 0);

        $stock = ImportReceiptDetail::where('flower_id', $flowerId)
            ->select(DB::raw('SUM(quantity - used_quantity) as remaining'))
            ->value('remaining') ?? 0;

        if ($stock < $neededQty) {
            throw new \Exception("Không đủ tồn kho cho sản phẩm này!");
        }

        $details = ImportReceiptDetail::where('flower_id', $flowerId)
            ->where(function ($query) use ($tenPmNextDay) {
                $query->where('import_date', '>=', now()->subDays(2))
                    ->where('import_date', '<=', $tenPmNextDay);
            })
            ->orderByDesc('import_date')
            ->get();

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
    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function all($filters = [])
    {
        // return $this->model->orderBy('id', 'desc')->paginate(10);
        $query = $this->model->with('orderDetails.product', 'orderDetails.productSize')
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

        $order = $this->model->with('orderDetails.product', 'orderDetails.productSize')->find($id);
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
        $order = $this->model->with('orderDetails.product', 'orderDetails.productSize')->find($id);
        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại.'], 404);
        }
        return $order;
    }
}
