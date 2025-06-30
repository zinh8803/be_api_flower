<?php
namespace App\Repositories\Eloquent;
use App\Jobs\SendOrderMail;
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
                $productSize = ProductSize::with('recipes.flower', 'product')->findOrFail($item['product_size_id']);
                $qty = $item['quantity'];

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
                'user_id' => $data['user_id'] ?? auth()->id() ?? 1,
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

            // if (!empty($order->email)) {
            //     SendOrderMail::dispatch($order, $order->email);
            // }
           

            return $order;
        });
    }


    public function deductStock($flowerId, $neededQty)
    {
        $now = now();
        $today = $now->format('Y-m-d');
        $stock = ImportReceiptDetail::where('flower_id', $flowerId)
            ->select(DB::raw('SUM(quantity - used_quantity) as remaining'))
            ->value('remaining') ?? 0;

        if ($stock < $neededQty) {
            throw new \Exception("Không đủ tồn kho cho sản phẩm này!");
        }
        $todayDetails = ImportReceiptDetail::where('flower_id', $flowerId)
        ->whereDate('import_date', $today)
        ->orderByDesc('import_date')
        ->get();

        foreach ($todayDetails as $detail) {
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
        // if ($neededQty > 0) {
        //     $previousDetails = ImportReceiptDetail::where('flower_id', $flowerId)
        //         ->whereDate('import_date', '<', $today)
        //         ->orderByDesc('import_date')
        //         ->get();

        //     foreach ($previousDetails as $detail) {
        //         $available = $detail->quantity - $detail->used_quantity;
        //         if ($neededQty <= 0)
        //             break;
        //         if ($available <= 0)
        //             continue;

        //         $used = min($neededQty, $available);
        //         $detail->used_quantity += $used;
        //         $detail->save();

        //         $neededQty -= $used;
        //     }
        // }
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
        $order->update($data);
        return $order;
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function all()
    {
        // return $this->model->orderBy('id', 'desc')->paginate(10);
        return $this->model->with('orderDetails.product', 'orderDetails.productSize')->orderBy('buy_at', 'desc')->paginate(10);
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
}