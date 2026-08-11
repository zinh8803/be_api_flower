<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlowerType\StoreFlowerTypeRequest;
use App\Http\Requests\FlowerType\UpdateFlowerTypeRequest;
use App\Http\Resources\FlowerTypeResource;
use App\Services\FlowerTypeService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Flower Types",
 *     description="Quản lý loại hoa"
 * )
 * @OA\PathItem(path="/flower-types")
 */
class FlowerTypeController extends Controller
{
    protected FlowerTypeService $flowerTypeService;

    public function __construct(FlowerTypeService $flowerTypeService)
    {
        $this->flowerTypeService = $flowerTypeService;
    }

    /**
     * @OA\Get(
     *     path="/api/flower-types",
     *     tags={"Flower Types"},
     *     summary="Lấy danh sách loại hoa",
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách loại hoa",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/FlowerType"))
     *     )
     * )
     */
    public function index()
    {
        $flowerTypes = $this->flowerTypeService->getFlowerTypes();
        return FlowerTypeResource::collection($flowerTypes);
    }

    /**
     * @OA\Post(
     *     path="/api/flower-types",
     *     tags={"Flower Types"},
     *     summary="Tạo mới loại hoa",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FlowerTypeStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tạo thành công",
     *         @OA\JsonContent(ref="#/components/schemas/FlowerType")
     *     )
     * )
     */
    public function store(StoreFlowerTypeRequest $request)
    {
        $flowerType = $this->flowerTypeService->createFlowerType($request->validated());
        return new FlowerTypeResource($flowerType);
    }

    /**
     * @OA\Get(
     *     path="/api/flower-types/{id}",
     *     tags={"Flower Types"},
     *     summary="Lấy chi tiết loại hoa",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID loại hoa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin loại hoa",
     *         @OA\JsonContent(ref="#/components/schemas/FlowerType")
     *     )
     * )
     */
    public function show($id)
    {
        $flowerType = $this->flowerTypeService->getFlowerTypeById($id);
        if (!$flowerType) {
            return response()->json(['message' => 'Không tìm thấy loại hoa'], 404);
        }
        return new FlowerTypeResource($flowerType);
    }

    /**
     * @OA\Put(
     *     path="/api/flower-types/{id}",
     *     tags={"Flower Types"},
     *     summary="Cập nhật loại hoa",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID loại hoa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FlowerTypeUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công",
     *         @OA\JsonContent(ref="#/components/schemas/FlowerType")
     *     )
     * )
     */
    public function update(UpdateFlowerTypeRequest $request, $id)
    {
        $flowerType = $this->flowerTypeService->getFlowerTypeById($id);
        if (!$flowerType) {
            return response()->json(['message' => 'Không tìm thấy loại hoa'], 404);
        }
        $updatedFlowerType = $this->flowerTypeService->updateFlowerType($id, $request->validated());
        return new FlowerTypeResource($updatedFlowerType);
    }

    /**
     * @OA\Delete(
     *     path="/api/flower-types/{id}",
     *     tags={"Flower Types"},
     *     summary="Xóa loại hoa",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID loại hoa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Xóa thành công"
     *     )
     * )
     */
    public function destroy($id)
    {
        $flowerType = $this->flowerTypeService->getFlowerTypeById($id);
        if (!$flowerType) {
            return response()->json(['message' => 'Không tìm thấy loại hoa'], 404);
        }
        $this->flowerTypeService->deleteFlowerType($id);
        return response()->json(['message' => 'Xóa loại hoa thành công'], 200);
    }
}
