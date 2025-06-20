<?php
namespace App\Repositories\Contracts;
interface OrderRepositoryInterface
{
    public function createOrder(array $data);
    public function deductStock($flowerId, $neededQty);
    public function findById(int $id);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function all();

    public function findByUserId(int $userId);
    public function OrderByUser();

}