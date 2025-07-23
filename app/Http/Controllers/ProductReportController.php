<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductReport\StoreProductReportRequest;
use App\Http\Resources\ProductReportResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductReport;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductReportRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductReportController extends Controller
{
    protected $model;
    public function __construct(OrderRepository $model)
    {
        $this->model = $model;
    }

    public function store(StoreProductReportRequest $request)
    {
        //dd(1);
        Log::info('Creating product report', ['data' => $request->all()]);

        $data = $request->validated();


        try {
            $this->model->createReport($data);
            return response()->json(['message' => 'Báo cáo sản phẩm thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Error creating product report: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi tạo báo cáo sản phẩm'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->model->handleProductReport($request->all());
            return response()->json(['message' => 'Cập nhật báo cáo thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Error updating product report: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi cập nhật báo cáo sản phẩm'], 500);
        }
    }

    public function delete($id)
    {
        // $report = ProductReport::find($id);
        // if (!$report) {
        //     return response()->json(['message' => 'Báo cáo không tồn tại'], 404);
        // }

        try {
            $this->model->deleteReport($id);
            return response()->json(['message' => 'Xóa báo cáo thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting product report: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa báo cáo sản phẩm'], 500);
        }
    }
}
