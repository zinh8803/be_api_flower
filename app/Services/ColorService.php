<?php

namespace App\Services;

use App\Repositories\Contracts\ColorRepositoryInterface;

class ColorService extends BaseService
{
    protected ColorRepositoryInterface $colorRepository;

    public function __construct(ColorRepositoryInterface $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function getColors()
    {
        return $this->colorRepository->getAll();
    }

    public function getColorById($id)
    {
        return $this->colorRepository->findById($id);
    }

    public function createColor(array $data)
    {
        $this->logInfo('Creating color', ['name' => $data['name'] ?? null]);
        return $this->colorRepository->create($data);
    }

    public function updateColor($id, array $data)
    {
        $this->logInfo('Updating color', ['color_id' => $id]);
        return $this->colorRepository->update($id, $data);
    }

    public function deleteColor($id)
    {
        $this->logInfo('Deleting color', ['color_id' => $id]);
        return $this->colorRepository->delete($id);
    }
}
