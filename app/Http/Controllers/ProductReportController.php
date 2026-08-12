<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductReport\StoreProductReportRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductReportController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreProductReportRequest $request)
    {
        Log::info('Creating product report', ['data' => $request->all()]);

        $data = $request->validated();

        try {
            $this->orderService->createProductReport($data);
            return response()->json(['message' => 'Báo cáo sản phẩm thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Error creating product report: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi tạo báo cáo sản phẩm'], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $this->orderService->handleProductReport($request->all());
            return response()->json(['message' => 'Cập nhật báo cáo thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Error updating product report: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi cập nhật báo cáo sản phẩm'], 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->orderService->deleteReport($id);
            return response()->json(['message' => 'Xóa báo cáo thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting product report: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa báo cáo sản phẩm'], 500);
        }
    }
}
