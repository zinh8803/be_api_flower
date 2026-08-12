<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductReport;
use App\Repositories\Contracts\ProductReportRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductReportRepository extends BaseRepository implements ProductReportRepositoryInterface
{
    public function __construct(ProductReport $model)
    {
        parent::__construct($model);
    }

    public function all($columns = ['*'], ...$args)
    {
        return $this->model->with(['order', 'orderDetail'])->get($columns);
    }
}
