<?php

namespace App\Services;

use App\Repositories\Contracts\FlowerRepositoryInterface;

class FlowerService extends BaseService
{
    protected FlowerRepositoryInterface $flowerRepository;

    public function __construct(FlowerRepositoryInterface $flowerRepository)
    {
        $this->flowerRepository = $flowerRepository;
    }

    public function getFlowers()
    {
        return $this->flowerRepository->all();
    }

    public function getFlowerById($id)
    {
        return $this->flowerRepository->find($id);
    }

    public function createFlower(array $data)
    {
        $this->logInfo('Creating new flower', ['name' => $data['name'] ?? null]);
        return $this->flowerRepository->create($data);
    }

    public function updateFlower($id, array $data)
    {
        $this->logInfo('Updating flower', ['flower_id' => $id]);
        return $this->flowerRepository->update($id, $data);
    }

    public function deleteFlower($id)
    {
        $this->logInfo('Deleting flower', ['flower_id' => $id]);
        return $this->flowerRepository->delete($id);
    }
}
