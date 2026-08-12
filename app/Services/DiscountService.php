<?php

namespace App\Services;

use App\Repositories\Contracts\DiscountRepositoryInterface;

class DiscountService extends BaseService
{
    protected DiscountRepositoryInterface $discountRepository;

    public function __construct(DiscountRepositoryInterface $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    public function getDiscounts(array $filters = [])
    {
        return $this->discountRepository->getAll($filters);
    }

    public function getDiscountById($id)
    {
        return $this->discountRepository->findById($id);
    }

    public function createDiscount(array $data)
    {
        $this->logInfo('Creating discount code', ['name' => $data['name'] ?? null]);
        return $this->discountRepository->create($data);
    }

    public function updateDiscount($id, array $data)
    {
        $this->logInfo('Updating discount code', ['discount_id' => $id]);
        return $this->discountRepository->update($id, $data);
    }

    public function deleteDiscount($id)
    {
        return $this->discountRepository->delete($id);
    }

    public function checkCodeValidity($code, $userId = null)
    {
        return $this->discountRepository->checkCodeValidity($code, $userId);
    }

    public function sendDiscountEmail($discounts)
    {
        $this->logInfo('Dispatching discount email notification');
        return $this->discountRepository->sentDiscountEmail($discounts);
    }

    public function getDiscountStats()
    {
        return $this->discountRepository->getDiscountStats();
    }
}
