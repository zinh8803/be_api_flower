<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
/**
 * @OA\Tag(name="Products", description="Quản lý sản phẩm hoa")
 */
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected ProductRepositoryInterface $products;

    public function __construct(ProductRepositoryInterface $products)
    {
        $this->products = $products;
    }
    
    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Lấy danh sách sản phẩm",
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách sản phẩm",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *     )
     * )
     */
    public function index()
    {
    
        $products = $this->products->all()->load('category', 'recipes', 'recipes.flower', 'recipes.flower.importReceiptDetails');
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Tạo sản phẩm mới",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "category_id", "recipes[0][flower_id]", "recipes[0][quantity]"},
     *                 @OA\Property(property="name", type="string", example="Bó hoa cưới đỏ"),
     *                 @OA\Property(property="description", type="string", example="Bó hoa cưới rực rỡ"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Ảnh sản phẩm"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="size", type="string", example="Lớn"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 
     *                 @OA\Property(property="recipes[0][flower_id]", type="integer", example=1),
     *                 @OA\Property(property="recipes[0][quantity]", type="integer", example=10),
     *                 @OA\Property(property="recipes[1][flower_id]", type="integer", example=2),
     *                 @OA\Property(property="recipes[1][quantity]", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     )
     * )
     */
    public function store(StoreProductRequest $request)
    {
         $product = $this->products->createWithRecipes($request->validated());
        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->products->find($id);
        if (!$product) {
            return response()->json(["message" => "Product not found"], 404);
        }
        return new ProductResource($product);
    }
/**
     * @OA\Get(
     *     path="/api/products/category/{categoryId}",
     *     tags={"Products"},
     *     summary="Lấy danh sách sản phẩm theo danh mục",
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách sản phẩm theo danh mục",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *     ),
     *     @OA\Response(response=404, description="Danh mục không tồn tại")
     * )
     */
    public function getProductsByCategory($categoryId)
    {
        try {
            $products = $this->products->getProductsByCategory($categoryId)->load('recipes', 'recipes.flower', 'recipes.flower.importReceiptDetails');
            return ProductResource::collection($products);
        } catch (\RuntimeException $e) {
            return response()->json(["message" => $e->getMessage()], 404);
        }
    }
    /**
     * Update the specified resource in storage.
     */


   /**
 * @OA\Post(
 *     path="/api/products/{id}",
 *     tags={"Products"},
 *     summary="Cập nhật sản phẩm",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "category_id", "recipes[0][flower_id]", "recipes[0][quantity]"},
 *                 @OA\Property(property="name", type="string", example="Bó hoa cưới đỏ"),
 *                 @OA\Property(property="description", type="string", example="Bó hoa cưới rực rỡ"),
 *                 @OA\Property(property="image", type="string", format="binary", description="Ảnh sản phẩm"),
 *                 @OA\Property(property="status", type="integer", example=1),
 *                 @OA\Property(property="size", type="string", example="Lớn"),
 *                 @OA\Property(property="category_id", type="integer", example=1),
 *                 
 *                 @OA\Property(property="recipes[0][flower_id]", type="integer", example=1),
 *                 @OA\Property(property="recipes[0][quantity]", type="integer", example=10),
 *                 @OA\Property(property="recipes[1][flower_id]", type="integer", example=2),
 *                 @OA\Property(property="recipes[1][quantity]", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated",
 *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
 *     )
 * )
 */

    public function update(UpdateProductRequest $request, $id)
    {
        //dd($request->all());
        Log::info('Update product request data:', [
            'id' => $id,
            'validated' => $request->validated()
        ]);

        $product = $this->products->find($id);
        if (!$product) {
            Log::info('Product not found for update', ['id' => $id]);
            return response()->json(["message" => "Product not found"], 404);
        }

        $product = $this->products->updateWithRecipes($id, $request->validated());

        Log::info('Product updated successfully', ['id' => $id, 'product' => $product]);

        return (new ProductResource($product))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Xóa sản phẩm",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy($id)
    {
        $product = $this->products->find($id);
        if (!$product) {
            return response()->json(["message" => "Product not found"], 404);
        }

        $this->products->delete($id);
        return response()->json(["message" => "Product deleted successfully"], 200);
    }
    /**
     * @OA\Put(
     *     path="/api/products/{id}/hide",
     *     tags={"Products"},
     *     summary="Ẩn sản phẩm",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product hidden successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function hide($id)
    {
        try {
            $this->products->hide($id);
            return response()->json(['message' => 'Product hidden successfully'], 200);
        } catch (\RuntimeException $e) {
            return response()->json(["message" => $e->getMessage()], 404);
        }
    }
}
