<?php

namespace App\Repositories\Eloquent;

use App\Models\Flower;
use App\Repositories\Contracts\FlowerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FlowerRepository extends BaseRepository implements FlowerRepositoryInterface
{
    public function __construct(Flower $flower)
    {
        parent::__construct($flower);
    }

    public function all($columns = ['*'], ...$args)
    {
        return $this->model->with(['flowerType', 'color'])->get($columns);
    }
}
