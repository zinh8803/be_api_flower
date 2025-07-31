<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function createOrder(array $data);
    public function findById(int $id);

    public function update(int $id, array $data);

    public function cancelOrderByUser(int $id);
    public function delete(int $id);

    public function all();
    public function createReport(array $data);
    public function findByUserId(int $userId);
    public function OrderByUser();

    public function updateStatusOrderReturn($orderId, string $status);
}
