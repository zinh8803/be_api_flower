<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function createOrder(array $data);
    public function cancelOrderByUser(int $id);
    public function all($filters = []);
    public function createReport(array $data);
    public function findByUserId(int $userId);
    public function OrderByUser();
    public function OrderDetailById(int $id);
    public function show(int $id);
    public function handleProductReport(array $data);
    public function updateStatusOrderReturn($orderId, string $status);
    public function updateReport(int $reportId, array $data);
    public function deleteReport($orderId): bool;
    public function processOrdersForToday();
    public function deductStock($flowerId, $neededQty);
}
