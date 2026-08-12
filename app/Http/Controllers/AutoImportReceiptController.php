<?php

namespace App\Http\Controllers;

use App\Http\Requests\AutoImportReceipt\StoreAutoImportReceipt;
use App\Http\Requests\AutoImportReceipt\UpdateAutoImportReceipt;
use App\Http\Resources\AutoImportReceiptResource;
use App\Services\AutoImportReceiptService;
use Illuminate\Http\Request;

class AutoImportReceiptController extends Controller
{
    protected AutoImportReceiptService $autoImportReceiptService;

    public function __construct(AutoImportReceiptService $autoImportReceiptService)
    {
        $this->autoImportReceiptService = $autoImportReceiptService;
    }

    /**
     * @OA\Get(
     *     path="/api/auto-import-receipts",
     *     tags={"Auto Import Receipts"},
     *     summary="Get all auto import receipts",
     *     @OA\Response(
     *         response=200,
     *         description="List of auto import receipts",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AutoImportReceipt"))
     *     )
     * )
     */
    public function index()
    {
        $receipts = $this->autoImportReceiptService->getAutoImportReceipts();
        return new AutoImportReceiptResource($receipts);
    }

    /**
     * @OA\Post(
     *     path="/api/auto-import-receipts",
     *     tags={"Auto Import Receipts"},
     *     summary="Create a new auto import receipt config",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AutoImportReceipt")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Auto import receipt created",
     *         @OA\JsonContent(ref="#/components/schemas/AutoImportReceipt")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function store(StoreAutoImportReceipt $request)
    {
        $data = $request->validated();

        if (empty($data['details'])) {
            return response()->json(['message' => 'Phải có ít nhất một sản phẩm để nhập'], 422);
        }

        $autoImport = $this->autoImportReceiptService->createAutoImportReceipt($data);
        return new AutoImportReceiptResource($autoImport);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $receipt = $this->autoImportReceiptService->getAutoImportReceiptById($id);
        return new AutoImportReceiptResource($receipt);
    }

    /**
     * @OA\Put(
     *     path="/api/auto-import-receipts/{id}",
     *     tags={"Auto Import Receipts"},
     *     summary="Update an auto import receipt config",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AutoImportReceipt")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Auto import receipt updated",
     *         @OA\JsonContent(ref="#/components/schemas/AutoImportReceipt")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Auto import receipt not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function update(UpdateAutoImportReceipt $request, $id)
    {
        $data = $request->validated();

        $autoImport = $this->autoImportReceiptService->getAutoImportReceiptById($id);
        if (!$autoImport) {
            return response()->json(['message' => 'Cấu hình tự động nhập không tồn tại'], 404);
        }

        $updated = $this->autoImportReceiptService->updateAutoImportReceipt($id, $data);
        return new AutoImportReceiptResource($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $autoImport = $this->autoImportReceiptService->getAutoImportReceiptById($id);
        if (!$autoImport) {
            return response()->json(['message' => 'Cấu hình tự động nhập không tồn tại'], 404);
        }

        $this->autoImportReceiptService->deleteAutoImportReceipt($id);
        return response()->json(['message' => 'Xóa thành công']);
    }
}
