<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\Request;

class OrderService extends BaseService
{
    protected OrderRepositoryInterface $orderRepository;
    protected CartService $cartService;

    public function __construct(OrderRepositoryInterface $orderRepository, CartService $cartService)
    {
        $this->orderRepository = $orderRepository;
        $this->cartService = $cartService;
    }

    public function createOrder(array $data, ?Request $request = null)
    {
        if ($request && empty($data['user_id'])) {
            $user = $this->cartService->resolveUserFromAccessToken($request);
            if ($user) {
                $data['user_id'] = $user->id;
            }
        }

        $this->logInfo('Creating new order', ['user_id' => $data['user_id'] ?? auth()->id()]);
        $order = $this->orderRepository->createOrder($data);

        if ($order && $request) {
            try {
                $this->cartService->clearCart($request);
                $this->logInfo('Cart successfully cleared after order placement', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                $this->logError('Failed to clear cart after order placement', ['error' => $e->getMessage()]);
            }
        }

        return $order;
    }

    public function getOrders(array $filters = [])
    {
        return $this->orderRepository->all($filters);
    }

    public function getOrderDetails(int $id)
    {
        return $this->orderRepository->show($id);
    }

    public function getUserOrders()
    {
        return $this->orderRepository->OrderByUser();
    }

    public function getUserOrderDetail(int $id)
    {
        return $this->orderRepository->OrderDetailById($id);
    }

    public function updateOrderStatus(int $id, array $data)
    {
        $this->logInfo('Updating order status', ['order_id' => $id, 'data' => $data]);
        return $this->orderRepository->update($id, $data);
    }

    public function cancelOrder(int $id)
    {
        $this->logInfo('Canceling order', ['order_id' => $id]);
        return $this->orderRepository->cancelOrderByUser($id);
    }

    public function createProductReport(array $data)
    {
        $this->logInfo('Creating product report', ['data' => $data]);
        return $this->orderRepository->createReport($data);
    }

    public function handleProductReport(array $data)
    {
        $this->logInfo('Resolving product report', ['data' => $data]);
        return $this->orderRepository->handleProductReport($data);
    }

    public function updateStatusOrderReturn($orderId, string $status)
    {
        return $this->orderRepository->updateStatusOrderReturn($orderId, $status);
    }

    public function deleteReport($orderId)
    {
        return $this->orderRepository->deleteReport($orderId);
    }
}
