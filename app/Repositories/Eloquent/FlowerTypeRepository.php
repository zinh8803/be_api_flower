<?php

namespace App\Repositories\Eloquent;

use App\Models\FlowerType;
use App\Repositories\Contracts\FlowerTypeRepositoryInterface;

class FlowerTypeRepository extends BaseRepository implements FlowerTypeRepositoryInterface
{
    public function __construct(FlowerType $flowerType)
    {
        parent::__construct($flowerType);
    }
}
