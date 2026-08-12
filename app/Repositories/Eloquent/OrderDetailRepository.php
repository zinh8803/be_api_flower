<?php

namespace App\Repositories\Eloquent;

use App\Models\OrderDetail;
use App\Repositories\Contracts\OrderDetailRepositoryInterface;

class OrderDetailRepository extends BaseRepository implements OrderDetailRepositoryInterface
{
    public function __construct(OrderDetail $model)
    {
        parent::__construct($model);
    }
}
