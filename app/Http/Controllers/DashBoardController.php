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

class DashBoardController extends Controller
{
    public function statistics(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        if (!$start && !$end) {
            $orders = Order::with('orderDetails.product', 'orderDetails.productSize')->get();
            $receipts = ImportReceipt::with('details.flower')->get();
            Log::info("No date filter - getting all data");
        } else {
            $startDateTime = Carbon::parse($start)->startOfDay();
            $endDateTime = Carbon::parse($end)->endOfDay();

            Log::info("DateTime range - Start: {$startDateTime}, End: {$endDateTime}");

            $orders = Order::where(function($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('buy_at', [$startDateTime, $endDateTime])
                      ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                          $q->whereNull('buy_at')
                            ->whereBetween('created_at', [$startDateTime, $endDateTime]);
                      });
            })->with('orderDetails.product', 'orderDetails.productSize')->get();

            $receipts = ImportReceipt::where(function($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('import_date', [$startDateTime, $endDateTime])
                      ->orWhere(function($q) use ($startDateTime, $endDateTime) {
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

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        $totalCustomers = $orders->groupBy('email')->count();
        $totalReceipts = $receipts->count();
        $totalImport = $receipts->sum('total_price');

        $topCustomers = $orders->groupBy('email')->map(function ($customerOrders, $email) {
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
        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $pid = $detail->product_id;
                if (!isset($productStats[$pid])) {
                    $productStats[$pid] = [
                        'name' => $detail->product->name ?? 'Sản phẩm',
                        'image' => $detail->product->image_url ?? null,
                        'sold' => 0,
                        'revenue' => 0
                    ];
                }
                $productStats[$pid]['sold'] += $detail->quantity;
                $productStats[$pid]['revenue'] += $detail->subtotal ?? ($detail->quantity * ($detail->price ?? 0));
            }
        }
        $topProducts = collect($productStats)->sortByDesc('sold')->take(5)->values();

        $recentOrders = $orders->sortByDesc(function($order) {
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
            
            $dayRevenue = $orders->filter(function($order) use ($d) {
                $orderDate = $order->buy_at ?? $order->created_at;
                return $orderDate && Carbon::parse($orderDate)->format('Y-m-d') === $d;
            })->sum('total_price');
            $revenueByDate[] = (int)$dayRevenue;
            
            $dayOrderCount = $orders->filter(function($order) use ($d) {
                $orderDate = $order->buy_at ?? $order->created_at;
                return $orderDate && Carbon::parse($orderDate)->format('Y-m-d') === $d;
            })->count();
            $orderCountByDate[] = $dayOrderCount;
            
            $dayImport = $receipts->filter(function($receipt) use ($d) {
                $receiptDate = $receipt->import_date ?? $receipt->created_at;
                return $receiptDate && Carbon::parse($receiptDate)->format('Y-m-d') === $d;
            })->sum('total_price');
            $importByDate[] = (int)$dayImport;
        }

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
                'labels' => $labels
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
}
