<?php

namespace App\Http\Controllers;

use App\Http\Resources\colorResource;
use App\Repositories\Contracts\ColorRepositoryInterface;
use Cloudinary\Transformation\Argument\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Color",
 *     description="Quản lý màu sắc"
 * )
 * @OA\PathItem(path="/colors")
 */
class ColorController extends Controller
{
    protected $colorRepository;

    public function __construct(ColorRepositoryInterface $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }


    /**
     * @OA\Get(
     *     path="/api/colors",
     *     tags={"Color"},
     *     summary="Lấy danh sách màu sắc",
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách màu sắc",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Color"))
     *     )
     * )
     */
    public function index()
    {
        // $start = microtime(true);
        // $colors = $this->colorRepository->getAll();
        // $afterRepo = microtime(true);
        // $response = colorResource::collection($colors);
        // $afterResource = microtime(true);

        // $repoMs = round(($afterRepo - $start) * 1000, 2);
        // $resourceMs = round(($afterResource - $afterRepo) * 1000, 2);
        // $totalMs = round(($afterResource - $start) * 1000, 2);

        // Log::info('ColorController@index: repo=' . $repoMs . 'ms, resource=' . $resourceMs . 'ms, total=' . $totalMs . 'ms');

        // // Thêm header để xem trực tiếp trên Postman/Network tab
        // return $response
        //     ->additional([]) // giữ nguyên payload
        //     ->response()
        //     ->withHeaders([
        //         'Server-Timing' => "repo;dur={$repoMs}, resource;dur={$resourceMs}, controller;dur={$totalMs}",
        //         'X-Server-Processing-Time' => "{$totalMs}ms",
        //     ]);
        $colors = $this->colorRepository->getAll();
        return response()->json([
            'status' => true,
            'data' => $colors,
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/colors/{id}",
     *     tags={"Color"},
     *     summary="Lấy thông tin màu sắc theo ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin màu sắc",
     *         @OA\JsonContent(ref="#/components/schemas/Color")
     *     )
     * )
     */
    public function show($id)
    {
        $color = $this->colorRepository->findById($id);
        return new colorResource($color);
    }
    /**
     * @OA\Post(
     *     path="/api/colors",
     *     tags={"Color"},
     *     summary="Tạo mới màu sắc",
     *     @OA\RequestBody(required=true, @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/Color")
     *     )),
     *     @OA\Response(
     *         response=201,
     *         description="Màu sắc được tạo thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Color")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'hex_code' => 'required|string|max:7',
        ]);

        $color = $this->colorRepository->create($data);
        return new colorResource($color);
    }
    /**
     * @OA\Put(
     *     path="/api/colors/{id}",
     *     tags={"Color"},
     *     summary="Cập nhật màu sắc",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(ref="#/components/schemas/Color")
     *     )),
     *     @OA\Response(
     *         response=200,
     *         description="Màu sắc được cập nhật thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Color")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'hex_code' => 'sometimes|required|string|max:7',
        ]);
        $color = $this->colorRepository->update($id, $data);
        return new colorResource($color);
    }
    /**
     * @OA\Delete(
     *     path="/api/colors/{id}",
     *     tags={"Color"},
     *     summary="Xóa màu sắc",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Màu sắc được xóa thành công",
     *         @OA\JsonContent(ref="#/components/schemas/Color")
     *     )
     * )
     */
    public function destroy($id)
    {
        $color = $this->colorRepository->delete($id);
        return response()->json(['message' => 'Color deleted successfully', 'data' => new colorResource($color)]);
    }
}
