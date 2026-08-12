<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryService extends BaseService
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategories()
    {
        return $this->categoryRepository->all();
    }

    public function getCategoryBySlug(string $slug)
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function getCategoryById($id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function createCategory(array $data)
    {
        $this->logInfo('Creating new category', ['name' => $data['name'] ?? null]);
        return $this->categoryRepository->create($data);
    }

    public function updateCategory($id, array $data)
    {
        $this->logInfo('Updating category', ['category_id' => $id]);
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory($id)
    {
        $this->logInfo('Deleting category', ['category_id' => $id]);
        return $this->categoryRepository->delete($id);
    }
}
