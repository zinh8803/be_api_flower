<?php
namespace App\Repositories\Eloquent;
use App\Models\Discount;
use App\Models\ImportReceiptDetail;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use DB;
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
                $product = Product::with('recipes.flower')->findOrFail($item['product_id']);
                $qty = $item['quantity'];

                foreach ($product->recipes as $recipe) {
                    $need = $recipe->quantity * $qty;
                    $stock = ImportReceiptDetail::where('flower_id', $recipe->flower_id)->sum('quantity');

                    if ($stock < $need) {
                        throw new \Exception("Không đủ tồn kho cho hoa {$recipe->flower->name}");
                    }
                    $this->deductStock($recipe->flower_id, $need);
                }

                $unitPrice = $product->price;
                $subTotal = $unitPrice * $qty;
                $orderSubtotal += $subTotal;

                $orderDetails[] = [
                    'product_id' => $product->id,
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

            $order = Order::create([
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

            return $order->load('orderDetails.product', 'discount');
        });
    }


    public function deductStock($flowerId, $neededQty)
    {
        $details = ImportReceiptDetail::where('flower_id', $flowerId)
            ->orderBy('created_at')
            ->get();

        foreach ($details as $detail) {
            if ($neededQty <= 0)
                break;

            $used = min($neededQty, $detail->quantity);
            $detail->quantity -= $used;
            $detail->save();

            $neededQty -= $used;
        }
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
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
        return $this->model->all();
    }

    public function findByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}