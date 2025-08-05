<?php

namespace App\Repositories\Eloquent;

use App\Helpers\ImageHelper;
use App\Models\ImportReceiptDetail;
use App\Models\Product;
use App\Models\ProductSize;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class ProductRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function all($filters = [])
    {
        $query = $this->model->with(['category', 'productSizes.recipes.flower.flowerType'])->orderBy('created_at', 'desc');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->paginate(10);
    }

    public function allTrash()
    {
        return $this->model->onlyTrashed()->with(['category', 'productSizes.recipes.flower'])->paginate(10);
    }

    public function restoreTrash($id)
    {
        $product = $this->model->withTrashed()->find($id);
        if (!$product) {
            throw new RuntimeException('Không có sản phẩm.');
        }
        $product->restore();
        return $product->load(['category', 'productSizes.recipes.flower']);
    }

    public function deleteForce($id)
    {
        $product = $this->model->withTrashed()->find($id);
        if (!$product) {
            throw new RuntimeException('Không có sản phẩm.');
        }

        ImportReceiptDetail::where('product_id', $id)->delete();
        $product->forceDelete();
        return true;
    }

    public function filterTypeColor($filters = [])
    {
        $query = $this->model->with(['category', 'productSizes.recipes.flower.flowerType', 'productSizes.recipes.flower.color']);

        if (!empty($filters['color_id'])) {
            $colors = is_array($filters['color_id']) ? $filters['color_id'] : [$filters['color_id']];
            $query->whereHas('productSizes.recipes.flower.color', function ($q) use ($colors) {
                $q->whereIn('id', $colors);
            });
        }

        if (!empty($filters['flower_type_id'])) {
            $flowerTypeIds = is_array($filters['flower_type_id']) ? $filters['flower_type_id'] : [$filters['flower_type_id']];
            $query->whereHas('productSizes.recipes.flower.flowerType', function ($q) use ($flowerTypeIds) {
                $q->whereIn('id', $flowerTypeIds);
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['price'])) {
            $minPrice = $filters['price'][0];
            $maxPrice = $filters['price'][1];
            $query->whereHas('productSizes', function ($q) use ($minPrice, $maxPrice) {
                $q->where('size', 'Nhỏ')
                    ->whereBetween('price', [$minPrice, $maxPrice]);
            });
        }
        Log::info('Product filter applied', ['filters' => $filters]);

        return $query->paginate(10);
    }

    public function find($slug)
    {
        return $this->model->with(['category', 'productSizes.recipes.flower'])->where('slug', $slug)->first();
    }

    public function findById($id)
    {
        return $this->model->with(['category', 'productSizes.recipes.flower'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function getProductsByCategory($categorySlug)
    {
        $products = $this->model
            ->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            })
            ->get();

        if ($products->isEmpty()) {
            throw new \RuntimeException('No products found for the given category.');
        }

        return $products->load(['category', 'productSizes.recipes.flower']);
    }

    public function getProductsByCategoryId($categoryId)
    {
        $products = $this->model
            ->where('category_id', $categoryId)
            ->get();

        if ($products->isEmpty()) {
            throw new \RuntimeException('No products found for the given category ID.');
        }

        return $products->load(['category', 'productSizes.recipes.flower']);
    }

    public function update($id, array $data)
    {
        $record = $this->findById($id);
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
            if (!empty($data['description'])) {
                $data['description'] = $this->handleDescriptionImages($data['description']);
            }

            $sizes = $data['sizes'] ?? $data['productSizes'] ?? [];
            unset($data['sizes'], $data['productSizes']);

            $product = $this->model->create($data);

            foreach ($sizes as $sizeData) {
                $price = $sizeData['price'];

                $productSize = $product->productSizes()->create([
                    'size' => $sizeData['size'],
                    'price' => $price,
                ]);

                foreach ($sizeData['recipes'] as $recipe) {
                    $productSize->recipes()->create([
                        'flower_id' => $recipe['flower_id'],
                        'quantity' => $recipe['quantity'],
                        'product_size_id' => $productSize->id,
                        // 'product_id' => $product->id,
                    ]);
                }
            }

            Log::info('Product with sizes and recipes created', ['product_id' => $product->id]);

            return $product->load('productSizes.recipes.flower');
        });
    }
    protected function handleDescriptionImages($html)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();

        $imgs = $dom->getElementsByTagName('img');
        foreach ($imgs as $img) {
            $src = $img->getAttribute('src');
            if (strpos($src, 'data:image') === 0) {
                preg_match('/data:image\/(\w+);base64,(.*)/', $src, $matches);
                $ext = $matches[1] ?? 'png';
                $base64 = $matches[2] ?? '';

                if ($base64) {
                    $imageData = base64_decode($base64);
                    $tmpFile = tmpfile();
                    $tmpFilePath = stream_get_meta_data($tmpFile)['uri'];
                    file_put_contents($tmpFilePath, $imageData);

                    $uploadedFile = new UploadedFile(
                        $tmpFilePath,
                        'desc_' . uniqid() . '.' . $ext,
                        'image/' . $ext,
                        null,
                        true
                    );

                    try {
                        $imageUrl = ImageHelper::uploadImage($uploadedFile, 'products');
                        if ($imageUrl) {
                            $img->setAttribute('src', $imageUrl);
                        }
                    } catch (\Exception $e) {
                        Log::error('Description image upload failed', ['error' => $e->getMessage()]);
                    }

                    fclose($tmpFile);
                }
            }
        }
        return $dom->saveHTML();
    }
    public function updateWithRecipes($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $product = $this->findById($id);

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
            if (!empty($data['description'])) {
                $data['description'] = $this->handleDescriptionImages($data['description']);
            }

            if (isset($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            foreach ($product->productSizes as $size) {
                $size->recipes()->delete();
                $size->delete();
            }

            $sizes = $data['sizes'] ?? $data['productSizes'] ?? [];
            unset($data['sizes'], $data['productSizes']);

            $product->update($data);

            foreach ($sizes as $sizeData) {
                $price = $sizeData['price'];

                $productSize = $product->productSizes()->create([
                    'size' => $sizeData['size'],
                    'price' => $price,
                ]);

                foreach ($sizeData['recipes'] as $recipe) {
                    $productSize->recipes()->create([
                        'flower_id' => $recipe['flower_id'],
                        'quantity' => $recipe['quantity'],
                        'product_size_id' => $productSize->id,
                        // 'product_id' => $product->id,
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
        $today = Carbon::now()->format('Y-m-d');
        Log::info('Checking stock for product today', ['product_id' => $id, 'date' => $today]);

        $product = $this->model->with(['productSizes.recipes.flower'])->find($id);
        if (!$product) {
            return null;
        }

        $stockStatus = [];
        $canBeMade = true;

        foreach ($product->productSizes as $size) {
            $sizeStatus = [
                'size_id' => $size->id,
                'size_name' => $size->size,
                'price' => $size->price,
                'in_stock' => true,
                'flower_details' => []
            ];

            foreach ($size->recipes as $recipe) {
                $flowerStock = ImportReceiptDetail::where('flower_id', $recipe->flower_id)
                    ->whereDate('import_date', $today)
                    ->select(DB::raw('SUM(quantity - used_quantity) as remaining'))
                    ->value('remaining') ?? 0;

                $neededQuantity = $recipe->quantity;

                if ($flowerStock < $neededQuantity) {
                    $sizeStatus['in_stock'] = false;
                    $canBeMade = false;
                }

                $sizeStatus['flower_details'][] = [
                    'flower_id' => $recipe->flower_id,
                    'flower_name' => $recipe->flower->name,
                    'needed_quantity' => $neededQuantity,
                    'available_quantity' => $flowerStock,
                    'sufficient' => $flowerStock >= $neededQuantity
                ];
            }

            $stockStatus[] = $sizeStatus;
        }

        Log::info('Stock check completed for product', [
            'product_id' => $id,
            'can_be_made' => $canBeMade,
            'details' => $stockStatus
        ]);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'can_be_made' => $canBeMade,
            'stock_details' => $stockStatus,
            'checked_date' => $today
        ];
    }

    public function checkStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_size_id' => 'required|exists:product_sizes,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $productSize = ProductSize::with('recipes.flower')->findOrFail($request->product_size_id);
            $quantity = $request->quantity;
            $inStock = true;
            $missingFlowers = [];

            foreach ($productSize->recipes as $recipe) {
                $need = $recipe->quantity * $quantity;

                // kiểm tra tồn kho ngày hôm nay
                $today = now()->format('Y-m-d');
                $stock = ImportReceiptDetail::where('flower_id', $recipe->flower_id)
                    ->whereDate('import_date', $today)
                    ->sum(DB::raw('quantity - used_quantity'));

                if ($stock < $need) {
                    $inStock = false;
                    $missingFlowers[] = [
                        'flower' => $recipe->flower->name,
                        'needed' => $need,
                        'available' => $stock
                    ];
                }
            }

            return response()->json([
                'in_stock' => $inStock,
                'missing_flowers' => $missingFlowers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'in_stock' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function search($params)
    {
        $query = $this->model->query();

        if (!empty($params['product'])) {
            $products = $this->model->where('name', 'like', '%' . $params['product'] . '%')->get();
            if ($products->count() > 0) {
                return $products->load('category', 'productSizes.recipes.flower');
            }

            $categoryProducts = $this->model->whereHas('category', function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['product'] . '%');
            })->get();
            if ($categoryProducts->count() > 0) {
                return $categoryProducts->load('category', 'productSizes.recipes.flower');
            }

            $flowerProducts = $this->model->whereHas('productSizes.recipes.flower', function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['product'] . '%');
            })->get();
            return $flowerProducts->load('category', 'productSizes.recipes.flower');
        }

        // if (!empty($params['name'])) {
        //     $query->where('name', 'like', '%' . $params['name'] . '%');
        // }
        // if (!empty($params['category_id'])) {
        //     $query->where('category_id', $params['category_id']);
        // }
        // if (!empty($params['flower_name'])) {
        //     $query->whereHas('recipes.flower', function ($q) use ($params) {
        //         $q->where('name', 'like', '%' . $params['flower_name'] . '%');
        //     });
        // }

        return $query->with([
            'category',
            'productSizes.recipes.flower.flowerType'
        ])->paginate(10);
    }
}
