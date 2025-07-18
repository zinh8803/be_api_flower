<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Helpers\ImageHelper;
use Illuminate\Support\Str;

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

    public function find($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile && $data['image']->isValid()) {
            try {
                $imageUrl = ImageHelper::uploadImage($data['image'], 'categories');

                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                }

                unset($data['image']);
            } catch (\Exception $e) {
                Log::error('Image upload failed', ['error' => $e->getMessage()]);
            }
        }

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->findById($id);

        // if (isset($data['image']) && $data['image'] instanceof UploadedFile && $data['image']->isValid()) {
        //     try {
        //         $imageUrl = ImageHelper::uploadImage($data['image'], 'categories');

        //         if ($imageUrl) {
        //             $data['image_url'] = $imageUrl;
        //         }

        //         unset($data['image']);
        //     } catch (\Exception $e) {
        //         Log::error('Image upload failed during update', ['error' => $e->getMessage()]);
        //     }
        // }
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
