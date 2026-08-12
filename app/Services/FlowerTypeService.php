<?php

namespace App\Services;

use App\Repositories\Contracts\FlowerTypeRepositoryInterface;

class FlowerTypeService extends BaseService
{
    protected FlowerTypeRepositoryInterface $flowerTypeRepository;

    public function __construct(FlowerTypeRepositoryInterface $flowerTypeRepository)
    {
        $this->flowerTypeRepository = $flowerTypeRepository;
    }

    public function getFlowerTypes()
    {
        return $this->flowerTypeRepository->all();
    }

    public function getFlowerTypeById($id)
    {
        return $this->flowerTypeRepository->find($id);
    }

    public function createFlowerType(array $data)
    {
        $this->logInfo('Creating new flower type', ['name' => $data['name'] ?? null]);
        return $this->flowerTypeRepository->create($data);
    }

    public function updateFlowerType($id, array $data)
    {
        $this->logInfo('Updating flower type', ['flower_type_id' => $id]);
        return $this->flowerTypeRepository->update($id, $data);
    }

    public function deleteFlowerType($id)
    {
        $this->logInfo('Deleting flower type', ['flower_type_id' => $id]);
        return $this->flowerTypeRepository->delete($id);
    }
}
