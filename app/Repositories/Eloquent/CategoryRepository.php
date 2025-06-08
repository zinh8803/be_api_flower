<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
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
// {
//     protected $model;

//     public function __construct(Product $product)
//     {
//         $this->model = $product;
//     }

//     public function all()
//     {
//         return $this->model->all();
//     }

//     public function find($id)
//     {
//         return $this->model->findOrFail($id);
//     }

//     public function create(array $data)
//     {
//         return $this->model->create($data);
//     }

//     public function update($id, array $data)
//     {
//         $record = $this->find($id);
//         $record->update($data);
//         return $record;
//     }

//     public function delete($id)
//     {
//         return $this->model->destroy($id);
//     }
// }
