<?php

namespace App\Repositories\Contracts;

interface DiscountRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll($filters = []);
    public function checkCodeValidity($code, $userId = null);
    public function sentDiscountEmail($discount);
    public function getDiscountStats();
    public function updateExpiredStatus();
}
