<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\Eloquent\CategoryRepository;
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
    /**
     * Display a listing of the resource.
     */
    protected $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
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
        $categories = $this->categoryRepository->all();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
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
        // $category = $this->categoryRepository->create($request->validated());
        // return (new CategoryResource($category));

        $category = $this->categoryRepository->create($request->validated());
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
        $category = $this->categoryRepository->find($slug);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
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
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category = $this->categoryRepository->update($category->id, $request->validated());
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
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
        $category = $this->categoryRepository->find($slug);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $this->categoryRepository->delete($category->id);
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
