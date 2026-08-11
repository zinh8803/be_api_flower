<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Category",
 *     description="Quản lý danh mục hoa"
 * )
 * @OA\PathItem(path="/categories")
 */
class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Category"},
     *     summary="Lấy danh sách danh mục hoa",
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách danh mục hoa",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Category"))
     *     )
     * )
     */
    public function index()
    {
        $categories = $this->categoryService->getCategories();
        return CategoryResource::collection($categories);
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     tags={"Category"},
     *     summary="Tạo mới danh mục hoa",
     *     @OA\RequestBody(required=true, @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(ref="#/components/schemas/CategoryStoreRequest")
     *     )),
     *     @OA\Response(
     *         response=201,
     *         description="Tạo thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     )
     * )
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());
        return new CategoryResource($category);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     summary="Lấy thông tin danh mục hoa",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin danh mục hoa",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(response=404, description="Danh mục không tồn tại")
     * )
     */
    public function show($slug)
    {
        $category = is_numeric($slug) 
            ? $this->categoryService->getCategoryById($slug)
            : $this->categoryService->getCategoryBySlug($slug);

        if (!$category) {
            return response()->json(['message' => 'Không có danh mục'], 404);
        }
        return new CategoryResource($category);
    }

    /**
     * @OA\Post(
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     summary="Cập nhật danh mục hoa",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(ref="#/components/schemas/CategoryUpdateRequest")
     *     )),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(response=404, description="Danh mục không tồn tại")
     * )
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->getCategoryById($id);
        if (!$category) {
            return response()->json(['message' => 'Không có danh mục'], 404);
        }
        $updatedCategory = $this->categoryService->updateCategory($category->id, $request->validated());
        return new CategoryResource($updatedCategory);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     summary="Xóa danh mục hoa",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Xóa thành công"),
     *     @OA\Response(response=404, description="Danh mục không tồn tại")
     * )
     */
    public function destroy($slug)
    {
        $category = is_numeric($slug)
            ? $this->categoryService->getCategoryById($slug)
            : $this->categoryService->getCategoryBySlug($slug);

        if (!$category) {
            return response()->json(['message' => 'Không có danh mục'], 404);
        }
        $this->categoryService->deleteCategory($category->id);
        return response()->json(['message' => 'Xóa danh mục thành công']);
    }
}
