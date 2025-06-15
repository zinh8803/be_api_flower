<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function hide($id);
    public function getAllStock();
    public function getStockById($id);
    public function getProductsByCategory($categoryId);
    public function createWithRecipes(array $data);
    public function updateWithRecipes($id, array $data);
}
