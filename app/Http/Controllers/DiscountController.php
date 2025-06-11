<?php

namespace App\Http\Controllers;

use App\Http\Requests\Discount\StoreDiscountRequest;
use App\Http\Requests\Discount\UpdateDiscountRequest;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use App\Repositories\Eloquent\DiscountRepository;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Discount",
 *     description="Quản lý mã giảm giá"
 * )
 * @OA\PathItem(path="/discounts")
 */
class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $discountRepository;
    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }
       /**
     * @OA\Get(
     *     path="/api/discounts",
     *     tags={"Discount"},
     *     summary="Lấy danh sách mã giảm giá",
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách mã giảm giá",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Discount"))
     *     )
     * )
     */
    public function index()
    {
        $discount = $this->discountRepository->getAll();
        return DiscountResource::collection($discount);
    }

    /**
     * Store a newly created resource in storage.
     */
       /**
     * @OA\Post(
     *     path="/api/discounts",
     *     tags={"Discount"},
     *     summary="Tạo mới mã giảm giá",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DiscountStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tạo thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Discount")
     *     )
     * )
     */
    public function store(StoreDiscountRequest $request)
    {
        $discount = $this->discountRepository->create($request->validated());
        return new DiscountResource($discount);
    }

    /**
     * Display the specified resource.
     */
          /**
     * @OA\Get(
     *     path="/api/discounts/{id}",
     *     tags={"Discount"},
     *     summary="Lấy chi tiết mã giảm giá",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID mã giảm giá",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin mã giảm giá",
     *         @OA\JsonContent(ref="#/components/schemas/Discount")
     *     )
     * )
     */
    public function show($id)
    {
        $discount = $this->discountRepository->findById($id);
        if(!$discount){
            return response()->json([
                'message'=>'không tìm thấy mã giảm giá'
            ],404);
        }
        return new DiscountResource($discount);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/discounts/{id}",
     *     tags={"Discount"},
     *     summary="Cập nhật mã giảm giá",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID mã giảm giá",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DiscountUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Discount")
     *     )
     * )
     */
    public function update(UpdateDiscountRequest $request, $id)
    {
        $discount = $this->discountRepository->findById($id);
        if (!$discount) {
            return response()->json(['message' => 'Không tìm thấy mã giảm giá'], 404);
        }

        $discount->update($request->validated());
        return new DiscountResource($discount);
    }

    /**
     * Remove the specified resource from storage.
     */
       /**
     * @OA\Delete(
     *     path="/api/discounts/{id}",
     *     tags={"Discount"},
     *     summary="Xóa mã giảm giá",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID mã giảm giá",
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
        $discount = $this->discountRepository->findById($id);
        if(!$discount){
             return response()->json(['message' => 'Không tìm thấy mã giảm giá'], 404);
        }
        $this->discountRepository->delete($id);
        return response()->json([
            'message'=>'Xóa mã giảm giá thành công'
        ],200);
    }
}
