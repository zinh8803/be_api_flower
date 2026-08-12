<?php

namespace App\Http\Controllers;

use App\Http\Resources\FlowerLogResource;
use App\Services\FlowerLogService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Flower Logs",
 *     description="Quản lý log biến động tồn kho hoa (cộng / trừ hoa)"
 * )
 */
class FlowerLogController extends Controller
{
    protected FlowerLogService $flowerLogService;

    public function __construct(FlowerLogService $flowerLogService)
    {
        $this->flowerLogService = $flowerLogService;
    }

    /**
     * @OA\Get(
     *     path="/api/flower-logs",
     *     tags={"Flower Logs"},
     *     summary="Danh sách log biến động tồn kho hoa",
     *     @OA\Parameter(name="flower_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="type", in="query", required=false, description="import, sale, cancel, adjustment", @OA\Schema(type="string")),
     *     @OA\Parameter(name="change_type", in="query", required=false, description="plus hoặc minus", @OA\Schema(type="string")),
     *     @OA\Parameter(name="from_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/FlowerLog"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->only(['flower_id', 'type', 'change_type', 'from_date', 'to_date', 'per_page']);
        $logs = $this->flowerLogService->getLogs($filters);
        return FlowerLogResource::collection($logs);
    }

    /**
     * @OA\Get(
     *     path="/api/flower/{id}/logs",
     *     tags={"Flower Logs"},
     *     summary="Danh sách log biến động tồn kho của 1 loại hoa",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="type", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="change_type", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/FlowerLog"))
     *     )
     * )
     */
    public function getLogsByFlower($id, Request $request)
    {
        $filters = $request->only(['type', 'change_type', 'from_date', 'to_date', 'per_page']);
        $logs = $this->flowerLogService->getLogsByFlowerId((int)$id, $filters);
        return FlowerLogResource::collection($logs);
    }
}
