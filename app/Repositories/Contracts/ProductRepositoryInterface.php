<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function allTrash();
    public function restoreTrash($id);
    public function filterTypeColor($filters = []);
    public function findBySlug(string $slug);
    public function hide($id);
    public function getAllStock();
    public function getStockById($id);
    public function searchStockWarning($query = '', $date = null, $page = 1, $perPage = 10);
    public function stockWarning($page = 1, $perPage = 10);
    public function search($params);
    public function getProductsByCategoryId($categoryId);
    public function getProductsByCategory($categoryId);
    public function createWithRecipes(array $data);
    public function updateWithRecipes($id, array $data);
}
