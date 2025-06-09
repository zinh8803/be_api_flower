<?php

namespace App\Http\Controllers;

use App\Http\Requests\Flower\StoreFlowerRequest;
use App\Http\Requests\Flower\UpdateFlowerRequest;
use App\Http\Resources\FlowerResource;
use App\Models\Flower;
use App\Repositories\Eloquent\FlowerRepository;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Flower",
 *     description="Quản lý hoa"
 * )
 * @OA\PathItem(path="/flower")
 */
class FlowerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $flowerRepository;

    public function __construct(FlowerRepository $flowerRepository)
    {
        $this->flowerRepository = $flowerRepository;
    }
      /**
     * @OA\Get(
     *     path="/api/flower",
     *     tags={"Flower"},
     *     summary="Lấy danh sách hoa",
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách hoa",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Flower"))
     *     )
     * )
     */
    public function index()
    {
        $flowers = $this->flowerRepository->all();
        return FlowerResource::collection($flowers);
    }

    /**
     * Store a newly created resource in storage.
     */
       /**
     * @OA\Post(
     *     path="/api/flower",
     *     tags={"Flower"},
     *     summary="Tạo mới hoa",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FlowerStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tạo thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Flower")
     *     )
     * )
     */
    public function store(StoreFlowerRequest $request)
    {
        $flower = $this->flowerRepository->create($request->validated());
        return new FlowerResource($flower);
    }

    /**
     * Display the specified resource.
     */
        /**
     * @OA\Get(
     *     path="/api/flower/{id}",
     *     tags={"Flower"},
     *     summary="Lấy chi tiết hoa",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID hoa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin hoa",
     *         @OA\JsonContent(ref="#/components/schemas/Flower")
     *     )
     * )
     */
    public function show($id)
    {
        $flower = $this->flowerRepository->find($id);
        if (!$flower) {
            return response()->json(['message' => 'Không tìm thấy hoa'], 404);
        }
        return new FlowerResource($flower);
    }

   

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/flower/{id}",
     *     tags={"Flower"},
     *     summary="Cập nhật hoa",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID hoa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FlowerUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Flower")
     *     )
     * )
     */
    public function update(UpdateFlowerRequest $request, $id)
    {
        $flower = $this->flowerRepository->find($id);
        if (!$flower) {
            return response()->json(['message' => 'Không tìm thấy hoa'], 404);
        }

        $flower->update($request->validated());
        return new FlowerResource($flower);
    }

    /**
     * Remove the specified resource from storage.
     */
      /**
     * @OA\Delete(
     *     path="/api/flower/{id}",
     *     tags={"Flower"},
     *     summary="Xóa hoa",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID hoa",
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
        $flower = $this->flowerRepository->find($id);
        if (!$flower) {
            return response()->json(['message' => 'Không tìm thấy hoa'], 404);
        }

        $this->flowerRepository->delete($id);
        return response()->json(['message' => 'Xóa hoa thành công'], 200);
    }
    
}
