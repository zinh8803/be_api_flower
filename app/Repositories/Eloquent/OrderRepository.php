<?php
namespace App\Repositories\Eloquent;
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
            $products = $data['products']; // [{ product_id, quantity }]
            
            unset($data['products']);

            $total = 0;
            $orderDetails = [];

            foreach ($products as $item) {
                $product = Product::with('recipes.flower')->findOrFail($item['product_id']);
                $quantity = $item['quantity'];

                // Lấy giá bán sản phẩm từ DB
                $price = $product->price;
                $subtotal = $price * $quantity;
                $total += $subtotal;

                // Trừ tồn kho như cũ
                foreach ($product->recipes as $recipe) {
                    $flower = $recipe->flower;
                    $neededQty = $recipe->quantity * $quantity;
                    $stock = ImportReceiptDetail::where('flower_id', $flower->id)->sum('quantity');
                    if ($stock < $neededQty) {
                        throw new \Exception("Không đủ tồn kho cho hoa: " . $flower->name);
                    }
                    $this->deductStock($flower->id, $neededQty);
                }

                $orderDetails[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price, // Giá bán 1 sản phẩm
                    'subtotal' => $subtotal, // Tổng tiền cho sản phẩm này
                ];
            }

            // ✅ Tạo đơn hàng
            $order = Order::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'] ?? null,
                'total_amount' => $total,
                'payment_method' => $data['payment_method'],
                'user_id' => $data['user_id'] ?? auth()->id() ?? 1,
                'status' => 'đang xử lý', // hoặc 'pending'
                'buy_at' => now(),
                'total_price' => $total,
            ]);

            // ✅ Lưu chi tiết đơn hàng
            foreach ($orderDetails as $detail) {
                $order->orderDetails()->create($detail);
            }

            return $order->load('orderDetails.product');
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