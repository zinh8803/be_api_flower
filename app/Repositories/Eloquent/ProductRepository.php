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
        $this->model = $product->with(['category', 'recipes', 'recipes.flower','productSizes']);
    }

    public function all()
    {
        return $this->model->paginate(10);
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
        // Xử lý ảnh như cũ...
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

        // Lấy ra size và recipes
        $sizes = $data['sizes'] ?? $data['productSizes'] ?? [];
        unset($data['sizes'], $data['productSizes']);

        // Tạo sản phẩm
        $product = $this->model->create($data);

        foreach ($sizes as $sizeData) {
            // Tính giá cho từng size
            $totalPrice = 0;
            foreach ($sizeData['recipes'] as $recipe) {
                $importPrice = ImportReceiptDetail::where('flower_id', $recipe['flower_id'])
                    ->orderByDesc('import_date')
                    ->value('import_price') ?? 0;
                $totalPrice += $recipe['quantity'] * $importPrice;
            }
            $finalPrice = $totalPrice * 1.5;

            // Tạo product_size với giá đã tính
            $productSize = $product->productSizes()->create([
                'size' => $sizeData['size'],
                'price' => $finalPrice,
            ]);

            // Tạo recipes cho từng size
            foreach ($sizeData['recipes'] as $recipe) {
                $productSize->recipes()->create([

                    'flower_id' => $recipe['flower_id'],
                    'quantity' => $recipe['quantity'],
                    'product_size_id' => $productSize->id,
                    'product_id' => $product->id,
                ]);
            }
        }

        Log::info('Product with sizes and recipes created', ['product_id' => $product->id]);

        return $product->load('productSizes.recipes.flower');
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

            // Xóa toàn bộ productSizes và recipes cũ
            foreach ($product->productSizes as $size) {
                $size->recipes()->delete();
                $size->delete();
            }

            $sizes = $data['sizes'] ?? $data['productSizes'] ?? [];
            unset($data['sizes'], $data['productSizes']);

            $product->update($data);

            foreach ($sizes as $sizeData) {
                // Tính giá cho từng size
                $totalPrice = 0;
                foreach ($sizeData['recipes'] as $recipe) {
                    $importPrice = ImportReceiptDetail::where('flower_id', $recipe['flower_id'])
                        ->orderByDesc('import_date')
                        ->value('import_price') ?? 0;
                    $totalPrice += $recipe['quantity'] * $importPrice;
                }
                $finalPrice = $totalPrice * 1.5;

                // Tạo product_size với giá đã tính
                $productSize = $product->productSizes()->create([
                    'size' => $sizeData['size'],
                    'price' => $finalPrice,

                ]);

                // Tạo recipes cho từng size
                foreach ($sizeData['recipes'] as $recipe) {
                    $productSize->recipes()->create([
                        'flower_id' => $recipe['flower_id'],
                        'quantity' => $recipe['quantity'],
                        'product_size_id' => $productSize->id,
                        'product_id' => $product->id,
                    ]);
                }
            }

            Log::info('Product updated with sizes and recipes', ['product_id' => $product->id]);

            return $product->load('productSizes.recipes.flower');
        });
    }
    public function getAllStock()
    {
        $products = $this->model->all()->map(function ($product) {
            $stock = ImportReceiptDetail::where('flower_id', $product->id)
                ->select(DB::raw('SUM(quantity - used_quantity) as remaining'))
                ->value('remaining') ?? 0;
            Log::info('Stock for product', ['product_id' => $product->id, 'remaining' => $stock]);
            return [
                'id' => $product->id,
                'name' => $product->name,
                'remaining_quantity' => (int) $stock,
            ];
        });

        return $products;
    }
    public function getStockById($id)
    {
        $product = $this->model->find($id);
        if (!$product) {
            return null;
        }

        $stock = ImportReceiptDetail::where('flower_id', $id)
            ->select(DB::raw('SUM(quantity - used_quantity) as remaining'))
            ->value('remaining') ?? 0;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'remaining_quantity' => (int) $stock,
        ];
    }
    public function search($params)
    {
        $query = $this->model->query();

        if (!empty($params['product'])) {
            $products = $this->model->where('name', 'like', '%' . $params['product'] . '%')->get();
            if ($products->count() > 0) {
                return $products->load('category', 'recipes', 'recipes.flower');
            }

            $categoryProducts = $this->model->whereHas('category', function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['product'] . '%');
            })->get();
            if ($categoryProducts->count() > 0) {
                return $categoryProducts->load('category', 'recipes', 'recipes.flower');
            }

            $flowerProducts = $this->model->whereHas('recipes.flower', function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['product'] . '%');
            })->get();
            return $flowerProducts->load('category', 'recipes', 'recipes.flower');
        }

        if (!empty($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }
        if (!empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }
        if (!empty($params['flower_name'])) {
            $query->whereHas('recipes.flower', function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['flower_name'] . '%');
            });
        }

        return $query->with('category', 'recipes', 'recipes.flower')->get();
    }
}
