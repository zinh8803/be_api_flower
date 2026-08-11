<?php

namespace App\Services;

use App\Models\ImportReceipt;
use App\Models\Order;
use Carbon\Carbon;

class DashBoardService extends BaseService
{
    public function getStatistics(?string $start, ?string $end): array
    {
        if (!$start && !$end) {
            $orders = Order::with('orderDetails.product', 'orderDetails.productSize')
                ->where('status', '!=', 'đã hủy')
                ->get();
            $receipts = ImportReceipt::with('details.flower')->get();
        } else {
            $startDateTime = Carbon::parse($start)->startOfDay();
            $endDateTime = Carbon::parse($end)->endOfDay();

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

        $completedOrders = $orders;
        $totalOrders = $orders->count();
        $totalRevenue = $completedOrders->sum('total_price');
        $totalCustomers = $orders->groupBy('email')->count();
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

        $revenueByStatus = $orders->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => $status ?? 'unknown',
                'count' => $group->count(),
                'revenue' => $group->sum('total_price')
            ];
        })->values();

        return [
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
        ];
    }
}
