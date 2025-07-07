<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\Eloquent\OrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    //swagger
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Lấy danh sách đơn hàng",
     *     @OA\Response(response=200, description="Danh sách đơn hàng", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))),
     * )
     */

    public function index(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'status']);
        return OrderResource::collection($this->orderRepository->all($filters));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Tạo đơn hàng mới",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrderStoreRequest")
     *     ),
     *     @OA\Response(response=200, description="Đơn hàng đã được tạo thành công", @OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response(response=422, description="Lỗi xác thực")
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderRepository->createOrder($request->validated());
        return new OrderResource($order);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/details",
     *     tags={"Orders"},
     *     summary="Lấy danh sách đơn hàng của người dùng",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Danh sách đơn hàng của người dùng", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))),
     * )
     */
    public function OrderDetailUser()
    {
        $orders = $this->orderRepository->OrderByUser();
        Log::info('orders', ['orders' => $orders->toArray()]);
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Bạn chưa có đơn hàng nào.'], 404);
        }
        return OrderResource::collection($orders);
    }
    /**
     * @OA\Get(
     *     path="/api/orders/user/{id}",
     *     tags={"Orders"},
     *     summary="Lấy chi tiết đơn hàng theo ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Chi tiết đơn hàng", @OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response(response=404, description="Đơn hàng không tồn tại")
     * )
     */
    public function OrderDetailById(int $id)
    {
        $order = $this->orderRepository->OrderDetailById($id);

        if ($order instanceof JsonResponse) {
            return $order;
        }

        return new OrderResource($order);
    }


    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/admin/orders/details/{id}",
     *     tags={"Orders"},
     *     summary="Lấy chi tiết đơn hàng theo ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Chi tiết đơn hàng", @OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response(response=404, description="Đơn hàng không tồn tại")
     * )
     */
    public function show($id)
    {
        $order = $this->orderRepository->show($id);
        if ($order instanceof JsonResponse) {
            return $order;
        }
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Cập nhật đơn hàng",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateOrderRequest")
     *     ),
     *     @OA\Response(response=200, description="Đơn hàng đã được cập nhật thành công", @OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response(response=404, description="Đơn hàng không tồn tại"),
     *     @OA\Response(response=422, description="Lỗi xác thực")
     * )
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order = $this->orderRepository->update($id, $request->validated());
        Log::info('Order updated successfully', ['id' => $id, 'data' => $request->all()]);
        return response()->json([
            'message' => 'Order updated successfully',
        ], 200);
    }
    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Xóa đơn hàng",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Đơn hàng đã được xóa thành công"),
     *     @OA\Response(response=404, description="Đơn hàng không tồn tại")
     * )
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
