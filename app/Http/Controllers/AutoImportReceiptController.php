<?php

namespace App\Http\Controllers;

use App\Http\Resources\AutoImportReceiptResource;
use Illuminate\Http\Request;
use App\Repositories\Contracts\AutoImportReceiptInterface;
use App\Http\Requests\AutoImportReceipt\StoreAutoImportReceipt;
use App\Http\Requests\AutoImportReceipt\UpdateAutoImportReceipt;

class AutoImportReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $autoImportReceiptRepository;

    public function __construct(AutoImportReceiptInterface $autoImportReceiptRepository)
    {
        $this->autoImportReceiptRepository = $autoImportReceiptRepository;
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
        $receipts = $this->autoImportReceiptRepository->all();
        return new AutoImportReceiptResource($receipts);
    }

    /**
     * Store a newly created resource in storage.
     */
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
        $data['import_date'] = $data['import_date'] ?? now()->format('Y-m-d');
        $data['run_time'] = $data['run_time'] ?? now()->format('H:i');

        if (empty($data['details'])) {
            return response()->json(['message' => 'Phải có ít nhất một sản phẩm để nhập'], 422);
        }

        $autoImport = $this->autoImportReceiptRepository->create($data);
        return new AutoImportReceiptResource($autoImport);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $receipt = $this->autoImportReceiptRepository->find($id);
        return new AutoImportReceiptResource($receipt);
    }

    /**
     * Update the specified resource in storage.
     */
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

        $autoImport = $this->autoImportReceiptRepository->find($id);
        if (!$autoImport) {
            return response()->json(['message' => 'Cấu hình tự động nhập không tồn tại'], 404);
        }

        $autoImport = $this->autoImportReceiptRepository->update($id, $data);
        return new AutoImportReceiptResource($autoImport);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/auto-import-receipts/{id}",
     *     tags={"Auto Import Receipts"},
     *     summary="Delete an auto import receipt config",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Auto import receipt deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Auto import receipt not found"
     *     )
     * )
     */
    public function destroy($id) {}
}
