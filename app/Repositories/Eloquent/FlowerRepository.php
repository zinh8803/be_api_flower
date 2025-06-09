<?php

namespace App\Repositories\Eloquent;

use App\Models\Flower;
use App\Repositories\Contracts\FlowerRepositoryInterface;
class FlowerRepository implements FlowerRepositoryInterface
{
    protected $model;

    public function __construct(Flower $flowerType)
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

