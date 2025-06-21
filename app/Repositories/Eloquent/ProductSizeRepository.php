<?php
namespace App\Repositories\Eloquent;

use App\Models\ProductSize;
use App\Repositories\Contracts\ProductSizeRepositoryInterface;

class ProductSizeRepository implements ProductSizeRepositoryInterface
{
    public function all() {
        return ProductSize::all();
    }

    public function find($id) {
        return ProductSize::findOrFail($id);
    }

    public function create(array $data) {
        return ProductSize::create($data);
    }

    public function update($id, array $data) {
        $item = ProductSize::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id) {
        return ProductSize::destroy($id);
    }
}
