<?php

namespace App\Repositories\Eloquent;

use App\Models\FlowerType;
use App\Repositories\Contracts\FlowerTypeRepositoryInterface;
class FlowerTypeRepository implements FlowerTypeRepositoryInterface
{
    protected $model;

    public function __construct(FlowerType $flowerType)
    {
        $this->model = $flowerType;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}

