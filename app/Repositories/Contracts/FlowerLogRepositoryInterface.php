<?php

namespace App\Repositories\Contracts;

interface FlowerLogRepositoryInterface extends BaseRepositoryInterface
{
    public function getByFlowerId(int $flowerId, array $filters = []);
    public function log(array $data);
}
