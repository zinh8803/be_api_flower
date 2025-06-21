<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRecipt\StoreImportReceiptRequest;
use App\Http\Requests\ImportRecipt\UpdateImportReceiptRequest;
use App\Http\Resources\ImportReceiptResource;
use App\Models\ImportReceipt;
use App\Repositories\Contracts\ImportReceiptRepositoryInterface;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="Import Receipts",
 *     description="Quản lý phiếu nhập hoa"
 * )
 */
class ImportReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected ImportReceiptRepositoryInterface $receipts;

    public function __construct(ImportReceiptRepositoryInterface $receipts)
    {
        $this->receipts = $receipts;
    }
  /**
     * @OA\Get(
     *     path="/api/import-receipts",
     *     tags={"Import Receipts"},
     *     summary="Danh sách phiếu nhập",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ImportReceiptResource"))
     *     )
     * )
     */
    public function index()
    {
        $data = $this->receipts->all();
        return ImportReceiptResource::collection($data);
    }


   /**
     * @OA\Post(
     *     path="/api/import-receipts",
     *     tags={"Import Receipts"},
     *     summary="Tạo mới phiếu nhập",
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ImportReceiptStoreRequest")),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/ImportReceiptResource"))
     * )
     */
    public function store(StoreImportReceiptRequest $request)
    {
        $receipt = $this->receipts->createWithDetails($request->validated());
        return (new ImportReceiptResource($receipt->load('details.flower')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/import-receipts/{id}",
     *     tags={"Import Receipts"},
     *     summary="Chi tiết phiếu nhập",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/ImportReceiptResource"))
     * )
     */
    public function show(int $id)
    {
        $receipt = $this->receipts->find($id);
        return new ImportReceiptResource($receipt->load('details.flower'));
    }

    /**
     * @OA\Put(
     *     path="/api/import-receipts/{id}",
     *     tags={"Import Receipts"},
     *     summary="Cập nhật phiếu nhập (ghi chú hoặc thay đổi toàn bộ chi tiết)",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ImportReceiptUpdateRequest")),
     *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/ImportReceiptResource"))
     * )
     */
    public function update(UpdateImportReceiptRequest $request, int $id)
    {
        $receipt = $this->receipts->updateWithDetails($id, $request->validated());
        return new ImportReceiptResource($receipt->load('details.flower'));
    }

    /**
     * @OA\Delete(
     *     path="/api/import-receipts/{id}",
     *     tags={"Import Receipts"},
     *     summary="Xóa phiếu nhập",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy(int $id)
    {
        $receipt = $this->receipts->find($id);
        if (!$receipt) {
            return response()->json(['message' => 'Không tìm thấy phiếu nhập'], 404);
        }
        $this->receipts->delete($id);

        return response()->json(['message'=> 'xoá thành công'], 200);
    }
}
