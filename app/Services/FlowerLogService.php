<?php

namespace App\Services;

use App\Repositories\Contracts\FlowerLogRepositoryInterface;

class FlowerLogService extends BaseService
{
    protected FlowerLogRepositoryInterface $flowerLogRepository;

    public function __construct(FlowerLogRepositoryInterface $flowerLogRepository)
    {
        $this->flowerLogRepository = $flowerLogRepository;
    }

    public function getLogs(array $filters = [])
    {
        return $this->flowerLogRepository->all($filters);
    }

    public function getLogsByFlowerId(int $flowerId, array $filters = [])
    {
        return $this->flowerLogRepository->getByFlowerId($flowerId, $filters);
    }

    public function createLog(array $data)
    {
        $this->logInfo('Creating flower stock log', $data);
        return $this->flowerLogRepository->log($data);
    }
}
