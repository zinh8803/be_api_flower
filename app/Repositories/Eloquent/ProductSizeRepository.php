<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductSize;
use App\Repositories\Contracts\ProductSizeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductSizeRepository extends BaseRepository implements ProductSizeRepositoryInterface
{
    public function __construct(ProductSize $productSize)
    {
        parent::__construct($productSize);
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->with(['product', 'recipes'])->get($columns);
    }

    public function find($id, array $columns = ['*']): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->model->with(['product', 'recipes'])->find($id, $columns);
    }
}
