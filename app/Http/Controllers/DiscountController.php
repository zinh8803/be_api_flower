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
     * @OA\Post(
     *     path="/api/discounts/check-code",
     *     tags={"Discount"},
     *     summary="Kiểm tra mã giảm giá",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", description="Mã giảm giá")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mã giảm giá hợp lệ",
     *         @OA\JsonContent(ref="#/components/schemas/Discount")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mã giảm giá không hợp lệ"
     *     )
     * )
     */
    public function checkCode(Request $request)
    {
        $code = $request->input('name');
        $userId = $request->input('user_id');
        $result = $this->discountRepository->checkCodeValidity($code, $userId);

        if (!$result['status']) {
            switch ($result['reason']) {
                case 'not_found':
                    return response()->json(['message' => 'Mã giảm giá không đúng hoặc không tồn tại'], 404);
                case 'expired':
                    return response()->json(['message' => 'Mã giảm giá đã hết hạn'], 400);
                case 'usage_limit':
                    return response()->json(['message' => 'Mã giảm giá đã hết lượt sử dụng'], 400);
                case 'not_allowed':
                    return response()->json(['message' => 'Bạn không được phép sử dụng mã giảm giá này'], 403);
                default:
                    return response()->json(['message' => 'Mã giảm giá không hợp lệ'], 400);
            }
        }

        return new DiscountResource($result['discount']);
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
        if (!$discount) {
            return response()->json([
                'message' => 'không tìm thấy mã giảm giá'
            ], 404);
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
        if (!$discount) {
            return response()->json(['message' => 'Không tìm thấy mã giảm giá'], 404);
        }
        $this->discountRepository->delete($id);
        return response()->json([
            'message' => 'Xóa mã giảm giá thành công'
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/discounts/send-discount",
     *     tags={"Discount"},
     *     summary="Gửi mã giảm giá cho người dùng đăng ký nhận email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"discount_ids"},
     *             @OA\Property(property="discount_ids", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Gửi thành công"
     *     )
     * )
     */
    public function sendDiscountToSubscribers(Request $request)
    {
        $discountIds = $request->input('discount_ids', []);
        $discounts = Discount::whereIn('id', $discountIds)->get();
        $this->discountRepository->sentDiscountEmail($discounts);

        return response()->json(['message' => 'Đã gửi mã giảm giá cho các user đăng ký nhận mail!']);
    }
}
