<?php

namespace App\Repositories\Eloquent;

use App\Helpers\ImageHelper;
use App\Models\ImportReceiptDetail;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
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

    public function getProductsByCategory($categoryId)
    {
        $products = $this->model->where('category_id', $categoryId)->get();
        if ($products->isEmpty()) {
            throw new RuntimeException('No products found for the given category.');
        }
        return $products;
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
    public function hide($id)
    {
        $product = $this->find($id);
        if (!$product) {
            throw new RuntimeException('Product not found.');
        }
        $product->status = false;
        $product->save();
        return $product;
    }
     public function createWithRecipes(array $data)
    {
        return DB::transaction(function () use ($data) {
              if (isset($data['image']) && $data['image'] instanceof UploadedFile && $data['image']->isValid()) {
            try {
                $imageUrl = ImageHelper::uploadImage($data['image'], 'products');
                Log::info('Image uploaded successfully', ['image_url' => $imageUrl]);
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                }
                
                unset($data['image']);
            } catch (\Exception $e) {
                Log::error('Image upload failed', ['error' => $e->getMessage()]);
            }
        }

            $recipes = $data['recipes'];
            unset($data['recipes']);

            $totalPrice = 0;
            foreach ($recipes as $recipe) {
                $importPrice = ImportReceiptDetail::where('flower_id', $recipe['flower_id'])->orderByDesc('import_date')->value('import_price') ?? 0;
                $totalPrice += $recipe['quantity'] * $importPrice;
            }
            $data['price'] = $totalPrice * 1.5;

            $product = $this->model->create($data);

            foreach ($recipes as $recipe) {
                $product->recipes()->create([
                    'product_id' => $product->id,
                    'flower_id' => $recipe['flower_id'],
                    'quantity' => $recipe['quantity'],
                ]);
            }
            Log::info(
                'Product created with recipes',
                ['product' => $product]
            );
            return $product;
        });
    }

    public function updateWithRecipes($id, array $data)
{
    return DB::transaction(function () use ($id, $data) {
        $product = $this->model->find($id);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile && $data['image']->isValid()) {
            try {
                $imageUrl = ImageHelper::uploadImage($data['image'], 'products');
                
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                }
                
                unset($data['image']);
            } catch (\Exception $e) {
                Log::error('Image upload failed during update', ['error' => $e->getMessage()]);
            }
        }

        $product->recipes()->delete();

        $recipes = $data['recipes'] ?? [];

        $totalPrice = 0;
        foreach ($recipes as $recipe) {
            $importPrice = ImportReceiptDetail::where('flower_id', $recipe['flower_id'])
                ->orderByDesc('import_date')
                ->value('import_price') ?? 0;
            $totalPrice += $recipe['quantity'] * $importPrice;
        }
        $data['price'] = $totalPrice * 1.5;

        $product->update($data);

        foreach ($recipes as $recipe) {
            $product->recipes()->create([
                'product_id' => $product->id,
                'flower_id' => $recipe['flower_id'],
                'quantity' => $recipe['quantity'],
            ]);
        }
        Log::info(
            'Product updated with recipes',
            ['product' => $product]
        );

        return $product;
    });
}

}
