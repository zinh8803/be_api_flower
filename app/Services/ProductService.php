<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductService extends BaseService
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProducts(array $filters = [], int $perPage = 10)
    {
        return $this->productRepository->all($filters, $perPage);
    }

    public function getTrashProducts()
    {
        return $this->productRepository->allTrash();
    }

    public function restoreTrashProduct($id)
    {
        return $this->productRepository->restoreTrash($id);
    }

    public function filterTypeColor(array $filters = [])
    {
        return $this->productRepository->filterTypeColor($filters);
    }

    public function getProductByIdOrSlug($idOrSlug)
    {
        return $this->productRepository->find($idOrSlug);
    }

    public function createProductWithRecipes(array $data)
    {
        $this->logInfo('Creating product with recipes/sizes', ['name' => $data['name'] ?? null]);
        return $this->productRepository->createWithRecipes($data);
    }

    public function updateProductWithRecipes($id, array $data)
    {
        $this->logInfo('Updating product with recipes/sizes', ['product_id' => $id]);
        return $this->productRepository->updateWithRecipes($id, $data);
    }

    public function hideProduct($id)
    {
        return $this->productRepository->hide($id);
    }

    public function getAllStock()
    {
        return $this->productRepository->getAllStock();
    }

    public function getStockById($id)
    {
        return $this->productRepository->getStockById($id);
    }

    public function searchStockWarning($query = '', $date = null, $page = 1, $perPage = 10)
    {
        return $this->productRepository->searchStockWarning($query, $date, $page, $perPage);
    }

    public function stockWarning($page = 1, $perPage = 10)
    {
        return $this->productRepository->stockWarning($page, $perPage);
    }

    public function searchProducts($params)
    {
        return $this->productRepository->search($params);
    }

    public function getProductsByCategory($categoryIdentifier)
    {
        if (is_numeric($categoryIdentifier)) {
            return $this->productRepository->getProductsByCategoryId($categoryIdentifier);
        }
        return $this->productRepository->getProductsByCategory($categoryIdentifier);
    }
}
