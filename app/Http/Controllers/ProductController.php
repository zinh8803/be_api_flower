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
    protected $products;

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

        $products = $this->products->all();
        return ProductResource::collection($products);
    }

        /**
     * @OA\Get(
     *     path="/api/products/search",
     *     tags={"Products"},
     *     summary="Tìm kiếm sản phẩm theo tên sản phẩm, tên danh mục, hoặc tên hoa (ưu tiên theo thứ tự này)",
     *     @OA\Parameter(
     *         name="product",
     *         in="query",
     *         description="Từ khóa tìm kiếm (tên sản phẩm, tên danh mục hoặc tên hoa)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Tìm theo tên sản phẩm (nếu muốn lọc riêng)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Tìm theo ID danh mục (nếu muốn lọc riêng)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="flower_name",
     *         in="query",
     *         description="Tìm theo tên hoa (nếu muốn lọc riêng)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách sản phẩm tìm kiếm",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *     )
     * )
     */
    public function search(Request $request)
    {
        $products = $this->products->search($request->all());
        return ProductResource::collection($products);
    }



    /**
     * Store a newly created resource in storage.
     */
    /**
 * @OA\Post(
 *     path="/api/products",
 *     tags={"Products"},
 *     summary="Tạo sản phẩm mới với nhiều size và công thức hoa",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "category_id", "status", "sizes[0][size]", "sizes[0][recipes][0][flower_id]", "sizes[0][recipes][0][quantity]"},
 *                 
 *                 @OA\Property(property="name", type="string", example="Bó hoa cưới đẹp", description="Tên sản phẩm"),
 *                 @OA\Property(property="description", type="string", example="Mẫu bó hoa cưới với nhiều hoa baby", description="Mô tả sản phẩm"),
 *                 @OA\Property(property="category_id", type="integer", example=1, description="ID danh mục"),
 *                 @OA\Property(property="status", type="integer", example=1, description="Trạng thái sản phẩm"),
 *                 @OA\Property(property="image", type="string", format="binary", description="Ảnh sản phẩm"),

 *                 @OA\Property(property="sizes[0][size]", type="string", example="Nhỏ", description="Tên size 1"),
 *                 @OA\Property(property="sizes[0][recipes][0][flower_id]", type="integer", example=1, description="ID hoa thứ 1 cho size Nhỏ"),
 *                 @OA\Property(property="sizes[0][recipes][0][quantity]", type="integer", example=10, description="Số lượng hoa thứ 1 cho size Nhỏ"),
 *                 @OA\Property(property="sizes[0][recipes][1][flower_id]", type="integer", example=2, description="ID hoa thứ 2 cho size Nhỏ"),
 *                 @OA\Property(property="sizes[0][recipes][1][quantity]", type="integer", example=5, description="Số lượng hoa thứ 2 cho size Nhỏ"),

 *                 @OA\Property(property="sizes[1][size]", type="string", example="Lớn", description="Tên size 2"),
 *                 @OA\Property(property="sizes[1][recipes][0][flower_id]", type="integer", example=1, description="ID hoa cho size Lớn"),
 *                 @OA\Property(property="sizes[1][recipes][0][quantity]", type="integer", example=20, description="Số lượng hoa cho size Lớn")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tạo sản phẩm thành công",
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

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Lấy thông tin sản phẩm theo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin sản phẩm",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(response=404, description="Product not found")
     * )
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
 *     summary="Cập nhật sản phẩm với nhiều size và công thức hoa",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID sản phẩm cần cập nhật",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"_method", "name", "category_id", "status", "sizes[0][size]", "sizes[0][recipes][0][flower_id]", "sizes[0][recipes][0][quantity]"},
 *                 @OA\Property(property="_method", type="string", example="PUT", description="Phương thức HTTP (đặt là PUT để cập nhật)"),
 *                 @OA\Property(property="name", type="string", example="Bó hoa cưới đỏ"),
 *                 @OA\Property(property="description", type="string", example="Bó hoa cưới rực rỡ"),
 *                 @OA\Property(property="image", type="string", format="binary", description="Ảnh sản phẩm (tùy chọn)"),
 *                 @OA\Property(property="status", type="integer", example=1, description="Trạng thái sản phẩm"),
 *                 @OA\Property(property="category_id", type="integer", example=1, description="ID danh mục sản phẩm"),

 *                 @OA\Property(property="sizes[0][size]", type="string", example="Nhỏ"),
 *                 @OA\Property(property="sizes[0][recipes][0][flower_id]", type="integer", example=1),
 *                 @OA\Property(property="sizes[0][recipes][0][quantity]", type="integer", example=10),
 *                 @OA\Property(property="sizes[0][recipes][1][flower_id]", type="integer", example=2),
 *                 @OA\Property(property="sizes[0][recipes][1][quantity]", type="integer", example=5),

 *                 @OA\Property(property="sizes[1][size]", type="string", example="Lớn"),
 *                 @OA\Property(property="sizes[1][recipes][0][flower_id]", type="integer", example=1),
 *                 @OA\Property(property="sizes[1][recipes][0][quantity]", type="integer", example=20)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cập nhật sản phẩm thành công",
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
    /**
     * @OA\Get(
     *     path="/api/products/stock",
     *     tags={"Products"},
     *     summary="Kiểm tra tồn kho tất cả sản phẩm",
     *     @OA\Response(
     *         response=200,
     *         description="Tồn kho của tất cả sản phẩm",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *     )
     * )
     */
    public function checkAllStock()
    {
        $data = $this->products->getAllStock();
        Log::info('Check all stock data:', ['data' => $data]);
        return response()->json(['data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}/stock",
     *     tags={"Products"},
     *     summary="Kiểm tra tồn kho của sản phẩm theo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tồn kho của sản phẩm",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */

    public function checkStock($id)
    {
        $data = $this->products->getStockById($id);
        if (!$data) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($data);
    }
}
