<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ImportReceipt;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashBoardController extends Controller
{
    public function statistics(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        if (!$start && !$end) {
            $orders = Order::with('orderDetails.product', 'orderDetails.productSize')
                ->where('status', '!=', 'đã hủy')
                ->get();
            $receipts = ImportReceipt::with('details.flower')->get();
            Log::info("No date filter - getting all data");
        } else {
            $startDateTime = Carbon::parse($start)->startOfDay();
            $endDateTime = Carbon::parse($end)->endOfDay();

            Log::info("DateTime range - Start: {$startDateTime}, End: {$endDateTime}");

            $orders = Order::where(function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('buy_at', [$startDateTime, $endDateTime])
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        $q->whereNull('buy_at')
                            ->whereBetween('created_at', [$startDateTime, $endDateTime]);
                    });
            })
                ->where('status', '!=', 'đã hủy')
                ->with('orderDetails.productSize.product', 'orderDetails.productSize')
                ->get();

            $receipts = ImportReceipt::where(function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('import_date', [$startDateTime, $endDateTime])
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        $q->whereNull('import_date')
                            ->whereBetween('created_at', [$startDateTime, $endDateTime]);
                    });
            })->with('details.flower')->get();
        }

        $sampleOrders = Order::select('id', 'order_code', 'buy_at', 'created_at')
            ->take(3)
            ->get();
        Log::info("Sample orders dates:", $sampleOrders->toArray());

        Log::info("Filtered orders count: " . $orders->count());
        Log::info("Filtered receipts count: " . $receipts->count());

        // Trước khi tính tổng doanh thu, lọc ra các đơn hàng đã hoàn thành
        $completedOrders = $orders->where('status', 'hoàn thành');

        // Thay thế các biến liên quan đến doanh thu
        $totalOrders = $orders->count(); // Giữ nguyên tổng số đơn
        $totalRevenue = $completedOrders->sum('total_price'); // Chỉ tính doanh thu từ đơn hoàn thành
        $totalCustomers = $orders->groupBy('email')->count(); // Giữ nguyên tổng khách
        $totalReceipts = $receipts->count();
        $totalImport = $receipts->sum('total_price');

        $topCustomers = $completedOrders->groupBy('email')->map(function ($customerOrders, $email) {
            $first = $customerOrders->first();
            return [
                'name' => $first->name ?? 'N/A',
                'email' => $email,
                'phone' => $first->phone ?? 'N/A',
                'total' => $customerOrders->count(),
                'spent' => $customerOrders->sum('total_price'),
            ];
        })->sortByDesc('spent')->take(5)->values();

        $productStats = [];
        foreach ($completedOrders as $order) {
            foreach ($order->orderDetails as $detail) {
                $pid = $detail->productSize->product_id;
                if (!isset($productStats[$pid])) {
                    $productStats[$pid] = [
                        'name' => $detail->productSize->product->name ?? 'Sản phẩm',
                        'image' => $detail->productSize->product->image_url ?? null,
                        'sold' => 0,
                        'revenue' => 0
                    ];
                }
                $productStats[$pid]['sold'] += $detail->quantity;
                $productStats[$pid]['revenue'] += $detail->subtotal ?? ($detail->quantity * ($detail->price ?? 0));
            }
        }
        $topProducts = collect($productStats)->sortByDesc('sold')->take(5)->values();

        $recentOrders = $orders->sortByDesc(function ($order) {
            return $order->buy_at ?? $order->created_at;
        })->take(10)->map(function ($o) {
            $date = $o->buy_at ?? $o->created_at;
            return [
                'code' => $o->order_code ?? 'N/A',
                'name' => $o->name ?? 'N/A',
                'date' => $date ? Carbon::parse($date)->format('Y-m-d H:i') : 'N/A',
                'status' => $o->status ?? 'unknown',
                'total' => $o->total_price ?? 0
            ];
        })->values();

        $orderStatusStats = $orders->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => $status ?? 'unknown',
                'count' => $group->count()
            ];
        })->values();

        if ($start && $end) {
            $startDateTime = Carbon::parse($start)->startOfDay();
            $endDateTime = Carbon::parse($end)->endOfDay();
            $period = new \DatePeriod(
                $startDateTime,
                new \DateInterval('P1D'),
                $endDateTime->copy()->addDay()
            );
        } else {
            $startDateTime = Carbon::now()->subDays(6)->startOfDay();
            $endDateTime = Carbon::now()->endOfDay();
            $period = new \DatePeriod(
                $startDateTime,
                new \DateInterval('P1D'),
                $endDateTime->copy()->addDay()
            );
        }

        $labels = [];
        $revenueByDate = [];
        $orderCountByDate = [];
        $importByDate = [];

        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $labels[] = $d;

            $dayRevenue = $orders->filter(function ($order) use ($d) {
                $orderDate = $order->buy_at ?? $order->created_at;
                return $order->status === 'hoàn thành' && $orderDate &&
                    Carbon::parse($orderDate)->format('Y-m-d') === $d;
            })->sum('total_price');
            $revenueByDate[] = (int)$dayRevenue;

            $dayOrderCount = $orders->filter(function ($order) use ($d) {
                $orderDate = $order->buy_at ?? $order->created_at;
                return $orderDate && Carbon::parse($orderDate)->format('Y-m-d') === $d;
            })->count();
            $orderCountByDate[] = $dayOrderCount;

            $dayImport = $receipts->filter(function ($receipt) use ($d) {
                $receiptDate = $receipt->import_date ?? $receipt->created_at;
                return $receiptDate && Carbon::parse($receiptDate)->format('Y-m-d') === $d;
            })->sum('total_price');
            $importByDate[] = (int)$dayImport;
        }

        // Thêm thống kê doanh thu theo trạng thái đơn hàng
        $revenueByStatus = $orders->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => $status ?? 'unknown',
                'count' => $group->count(),
                'revenue' => $group->sum('total_price')
            ];
        })->values();

        return response()->json([
            'success' => true,
            'stats' => [
                'totalOrders' => $totalOrders,
                'totalRevenue' => (int)$totalRevenue,
                'totalCustomers' => $totalCustomers,
                'totalReceipts' => $totalReceipts,
                'totalImport' => (int)$totalImport,
                'topCustomers' => $topCustomers,
                'topProducts' => $topProducts,
                'recentOrders' => $recentOrders,
                'orderStatusStats' => $orderStatusStats,
                'revenueByDate' => $revenueByDate,
                'orderCountByDate' => $orderCountByDate,
                'importByDate' => $importByDate,
                'labels' => $labels,
                'revenueByStatus' => $revenueByStatus,
            ],
            'period' => [
                'start_date' => $start,
                'end_date' => $end
            ],
            'debug' => [
                'total_orders_in_db' => Order::count(),
                'filtered_orders' => $totalOrders,
                'sample_order_dates' => $sampleOrders->pluck('buy_at', 'created_at')
            ]
        ]);
    }

    public function testData(Request $request)
    {
        $orders = Order::select('id', 'order_code', 'buy_at', 'created_at', 'total_price', 'status')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $receipts = ImportReceipt::select('id', 'import_date', 'created_at', 'total_price')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'orders' => $orders,
            'receipts' => $receipts
        ]);
    }

    public function monthlyStats(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $newRequest = new Request([
            'start_date' => $startOfMonth->toDateString(),
            'end_date' => $endOfMonth->toDateString()
        ]);

        return $this->statistics($newRequest);
    }

    public function yearlyStats(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

        $newRequest = new Request([
            'start_date' => $startOfYear->toDateString(),
            'end_date' => $endOfYear->toDateString()
        ]);

        return $this->statistics($newRequest);
    }

    public function weeklyStats()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        $newRequest = new Request([
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString()
        ]);

        return $this->statistics($newRequest);
    }
    public function exportToExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        if (!$start || !$end) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp ngày bắt đầu và ngày kết thúc'
            ], 400);
        }

        $response = $this->statistics($request);
        $stats = json_decode($response->getContent(), true)['stats'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'BÁO CÁO THỐNG KÊ');
        $sheet->setCellValue('A2', 'Từ ngày: ' . Carbon::parse($start)->format('d/m/Y') . ' đến ngày: ' . Carbon::parse($end)->format('d/m/Y'));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'THÔNG TIN TỔNG QUAN');
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'Tổng số đơn hàng:');
        $sheet->setCellValue('B5', $stats['totalOrders']);

        $sheet->setCellValue('A6', 'Tổng doanh thu:');
        $sheet->setCellValue('B6', number_format($stats['totalRevenue']) . ' đ');

        $sheet->setCellValue('A7', 'Tổng số khách hàng:');
        $sheet->setCellValue('B7', $stats['totalCustomers']);

        $sheet->setCellValue('A8', 'Tổng số phiếu nhập:');
        $sheet->setCellValue('B8', $stats['totalReceipts']);

        $sheet->setCellValue('A9', 'Tổng chi phí nhập:');
        $sheet->setCellValue('B9', number_format($stats['totalImport']) . ' đ');

        $sheet->setCellValue('A11', 'TOP 5 KHÁCH HÀNG');
        $sheet->getStyle('A11')->getFont()->setBold(true);

        $sheet->setCellValue('A12', 'Tên');
        $sheet->setCellValue('B12', 'Email');
        $sheet->setCellValue('C12', 'Số điện thoại');
        $sheet->setCellValue('D12', 'Số đơn');
        $sheet->setCellValue('E12', 'Tổng chi tiêu');

        $sheet->getStyle('A12:E12')->getFont()->setBold(true);

        $row = 13;
        foreach ($stats['topCustomers'] as $customer) {
            $sheet->setCellValue('A' . $row, $customer['name']);
            $sheet->setCellValue('B' . $row, $customer['email']);
            $sheet->setCellValue('C' . $row, $customer['phone']);
            $sheet->setCellValue('D' . $row, $customer['total']);
            $sheet->setCellValue('E' . $row, number_format($customer['spent']) . ' đ');
            $row++;
        }

        $sheet->setCellValue('A' . ($row + 1), 'TOP 5 SẢN PHẨM BÁN CHẠY');
        $sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Sản phẩm');
        $sheet->setCellValue('B' . $row, 'Số lượng đã bán');
        $sheet->setCellValue('C' . $row, 'Doanh thu');

        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);

        $row++;
        foreach ($stats['topProducts'] as $product) {
            $sheet->setCellValue('A' . $row, $product['name']);
            $sheet->setCellValue('B' . $row, $product['sold']);
            $sheet->setCellValue('C' . $row, number_format($product['revenue']) . ' đ');
            $row++;
        }

        $sheet->setCellValue('A' . ($row + 1), 'ĐƠN HÀNG GẦN ĐÂY');
        $sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Mã đơn');
        $sheet->setCellValue('B' . $row, 'Khách hàng');
        $sheet->setCellValue('C' . $row, 'Ngày đặt');
        $sheet->setCellValue('D' . $row, 'Trạng thái');
        $sheet->setCellValue('E' . $row, 'Tổng tiền');

        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);

        $row++;
        foreach ($stats['recentOrders'] as $order) {
            $sheet->setCellValue('A' . $row, $order['code']);
            $sheet->setCellValue('B' . $row, $order['name']);
            $sheet->setCellValue('C' . $row, $order['date']);
            $sheet->setCellValue('D' . $row, $order['status']);
            $sheet->setCellValue('E' . $row, number_format($order['total']) . ' đ');
            $row++;
        }

        $sheet->setCellValue('A' . ($row + 1), 'THỐNG KÊ THEO TRẠNG THÁI ĐƠN HÀNG');
        $sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Trạng thái');
        $sheet->setCellValue('B' . $row, 'Số lượng');

        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);

        $row++;
        foreach ($stats['orderStatusStats'] as $statusStat) {
            $sheet->setCellValue('A' . $row, $statusStat['status']);
            $sheet->setCellValue('B' . $row, $statusStat['count']);
            $row++;
        }

        $sheet->setCellValue('A' . ($row + 1), 'THỐNG KÊ THEO NGÀY');
        $sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Ngày');
        $sheet->setCellValue('B' . $row, 'Doanh thu');
        $sheet->setCellValue('C' . $row, 'Số đơn');
        $sheet->setCellValue('D' . $row, 'Chi phí nhập');

        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);

        $row++;
        foreach ($stats['labels'] as $index => $date) {
            $sheet->setCellValue('A' . $row, $date);
            $sheet->setCellValue('B' . $row, number_format($stats['revenueByDate'][$index]) . ' đ');
            $sheet->setCellValue('C' . $row, $stats['orderCountByDate'][$index]);
            $sheet->setCellValue('D' . $row, number_format($stats['importByDate'][$index]) . ' đ');
            $row++;
        }

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = 'bao_cao_' . Carbon::parse($start)->format('d_m_Y') . '_den_' . Carbon::parse($end)->format('d_m_Y') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return response($excelOutput)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Cache-Control', 'max-age=0')
            ->header('Cache-Control', 'max-age=1')
            ->header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT')
            ->header('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
            ->header('Cache-Control', 'cache, must-revalidate')
            ->header('Pragma', 'public');
    }
}
