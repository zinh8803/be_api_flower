<?php

namespace App\Repositories\Contracts;

interface ImportReceiptRepositoryInterface extends BaseRepositoryInterface
{
    public function all($filters = [], ...$args);
    public function createWithDetails(array $data);
    public function updateWithDetails($id, array $data);
}