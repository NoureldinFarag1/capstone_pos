<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Refund;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // Get selected month and year from request
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Get brands with sales
        $topSellingBrands = SaleItem::select('items.brand_id', DB::raw('SUM(sale_items.quantity) as total_sales'))
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->groupBy('items.brand_id')
            ->having('total_sales', '>', 0)
            ->orderByDesc('total_sales')
            ->get();

        // Now, fetch the brand details for each of the brands with sales
        $topSellingBrandDetails = [];
        foreach ($topSellingBrands as $brand) {
            $brandDetails = Brand::find($brand->brand_id);
            if ($brandDetails) {
                $topSellingBrandDetails[] = [
                    'name' => $brandDetails->name,
                    'image' => $brandDetails->picture,
                    'total_sales' => $brand->total_sales
                ];
            }
        }

        // Low Stock Items
        $lowStockItems = Item::where('quantity', '<=', 2)
            ->where('is_parent', false)
            ->orderBy('quantity', 'asc')
            ->orderBy('name', 'asc')  // Secondary sort by name
            ->get();

        // Sales Metrics
        $monthlySales = $this->getMonthlySalesData($selectedMonth, $selectedYear);

        $cashPaymentsMonthly = Sale::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->where('payment_method', 'cash')
                            ->sum('total_amount');
        $creditPaymentsMonthly = Sale::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->where('payment_method', 'credit_card')
                                      ->sum('total_amount');

        $mobilePaymentsMonthly = Sale::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->where('payment_method', 'mobile_pay')
                                  ->sum('total_amount');

        $codPaymentsMonthly = Sale::whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->where('payment_method', 'cod')
                          ->sum('total_amount');

        // Inventory Metrics
        $totalItems = Item::where('is_parent', false)->count();
        $totalBrands = Brand::count();
        $totalCategories = Category::count();

        // Top Selling Items (Last 30 Days)
        $topSellingItems = DB::table('sale_items')
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->where('sale_items.created_at', '>=', now()->subDays(30))
            ->select(
                'items.id',
                'items.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity')
            )
            ->groupBy('items.id', 'items.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // Daily Sales Trend
        $dailySales = Sale::where('created_at', '>=', now()->subDays(7))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as total_sales')
                )
                ->get();
        $cashPayments = Sale::whereDate('created_at', Carbon::today())->where('payment_method', 'cash')->sum('total_amount');
        $creditPayments = Sale::whereDate('created_at', Carbon::today())->where('payment_method', 'credit_card')->sum('total_amount');
        $mobilePayments = Sale::whereDate('created_at', Carbon::today())->where('payment_method', 'mobile_pay')->sum('total_amount');
        $codPayments = Sale::whereDate('created_at', Carbon::today())->where('payment_method', 'cod')->sum('total_amount');

        // Stock Level Summary
        $stockLevels = [
                'critical' => Item::where('quantity', '<=', 2)
                     ->where('is_parent', false)
                     ->count(),

    'low' => Item::where('quantity', '>', 2)
                 ->where('quantity', '<=', 5)
                 ->where('is_parent', false)
                 ->count(),

    'healthy' => Item::where('quantity', '>', 5)
                     ->where('is_parent', false)
                     ->count()
            ];

        $topPaymentMethod = Sale::select('payment_method', DB::raw('count(*) as count'))
                            ->groupBy('payment_method')
                            ->orderByDesc('count')
                            ->first()->payment_method;
        $topPaymentMethodPercentage = (Sale::where('payment_method', $topPaymentMethod)->count() / Sale::count()) * 100;
        $topPaymentMethodCount = (Sale::where('payment_method', $topPaymentMethod)->count());
        $AllSalesCount = Sale::count();

        $recentItems = Item::where('is_parent', false)->latest()->take(5)->get();

        // Add new metrics
        $salesAnalytics = [
            'hourly' => $this->getHourlySales(),
            'weekly' => $this->getWeeklySales(),
            'yearly' => $this->getYearlySales(),
        ];

        $inventoryMetrics = [
            'total_value' => Item::where('is_parent', false)->sum(DB::raw('quantity * selling_price')),
            'avg_item_price' => Item::where('is_parent', false)->avg('selling_price'),
            'out_of_stock' => Item::where('quantity', 0)->where('is_parent', false)->count(),
            'inventory_turnover' => $this->calculateInventoryTurnover()
        ];

        // Category Performance
        $categoryPerformance = Category::with(['items' => function ($query) {
            $query->where('is_parent', false);
        }])
        ->withCount(['items' => function ($query) {
            $query->where('is_parent', false);
        }])
        ->withSum(['items' => function ($query) {
            $query->where('is_parent', false);
        }], 'quantity')
        ->withAvg(['items' => function ($query) {
            $query->where('is_parent', false);
        }], 'selling_price')
        ->get();

        $salesForecasting = $this->calculateSalesForecasting();

        // Add refund metrics
        $refundMetrics = [
            'today_refunds' => Refund::whereDate('created_at', Carbon::today())->sum('refund_amount'),
            'month_refunds' => Refund::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('refund_amount'),
            'refund_rate' => $this->calculateRefundRate(),
            'recent_refunds' => Refund::with(['sale', 'item'])
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('layouts.dashboard', [
            'lowStockItems' => $lowStockItems,
            'monthlySales' => $monthlySales['total_sales'],
            'salesGrowthPercentage' => $monthlySales['salesGrowthPercentage'],
            'totalItems' => $totalItems,
            'totalBrands' => $totalBrands,
            'totalCategories' => $totalCategories,
            'topSellingItems' => $topSellingItems,
            'dailySales' => $dailySales,
            'stockLevels' => $stockLevels,
            'topPaymentMethod' =>$topPaymentMethod,
            'topPaymentMethodPercentage' =>$topPaymentMethodPercentage,
            'topPaymentMethodCount' =>$topPaymentMethodCount,
            'AllSalesCount' => $AllSalesCount,
            'recentItems' => $recentItems,
            'todayRevenue' => Sale::whereDate('created_at', today())->sum('total_amount'),
            'todayOrders' => Sale::whereDate('created_at', today())->count(),
            'cashPayments'=>$cashPayments,
            'creditPayments' =>$creditPayments,
            'mobilePayments' => $mobilePayments,
            'cashPaymentsMonthly' => $monthlySales['cashPaymentsMonthly'],
            'creditPaymentsMonthly' => $monthlySales['creditPaymentsMonthly'],
            'mobilePaymentsMonthly' => $monthlySales['mobilePaymentsMonthly'],
            'codPayments' => $codPayments,
            'codPaymentsMonthly' => $monthlySales['codPaymentsMonthly'],
            'topSellingBrandDetails' => $topSellingBrandDetails,
            'salesAnalytics' => $salesAnalytics,
            'inventoryMetrics' => $inventoryMetrics,
            'categoryPerformance' => $categoryPerformance,
            'salesForecasting' => $salesForecasting,
            'bestSellingDays' => $this->getBestSellingDays(),
            'peakHours' => $this->getPeakHours(),
            'customerMetrics' => $this->getCustomerMetrics(),
            'refundMetrics' => $refundMetrics,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
        ]);
    }

    private function getYearlySales()
    {
        return Sale::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )
        ->groupBy('year')
        ->get();
    }

    private function getWeeklySales()
    {
        return Sale::select(
            DB::raw('WEEK(created_at) as week'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )
        ->groupBy('week')
        ->get();
    }

    private function getHourlySales()
    {
        return Sale::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )
        ->whereDate('created_at', Carbon::today())
        ->groupBy('hour')
        ->get();
    }

    private function calculateInventoryTurnover()
    {
        $averageInventory = Item::avg('quantity');
        $costOfGoodsSold = SaleItem::sum(DB::raw('quantity * price'));

        return $averageInventory > 0 ? $costOfGoodsSold / $averageInventory : 0;
    }

    private function getBestSellingDays()
    {
        return Sale::select(
            DB::raw('DAYOFWEEK(created_at) as day_of_week'),
            DB::raw('DAYNAME(created_at) as day'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total')
        )
        ->groupBy('day_of_week', 'day')
        ->orderByRaw('FIELD(DAYOFWEEK(created_at), 7, 1, 2, 3, 4, 5, 6)')
        ->get();
    }

    private function getPeakHours()
    {
        return Sale::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('hour')
        ->orderBy('count', 'desc')
        ->limit(5)
        ->get();
    }

    private function getCustomerMetrics()
    {
        return [
            'repeat_customers' => Sale::select('customer_phone')
                ->whereNotNull('customer_phone')
                ->groupBy('customer_phone')
                ->havingRaw('COUNT(*) > 1')
                ->count(),
            'avg_transaction' => Sale::avg('total_amount'),
            'max_transaction' => Sale::max('total_amount'),
            'total_customers' => Sale::distinct('customer_phone')->count('customer_phone'),
        ];
    }

    private function calculateSalesForecasting()
    {
        // Simple moving average forecasting
        $historicalSales = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->limit(30)
        ->get();

        // Calculate 7-day moving average
        $movingAverage = collect($historicalSales)
            ->take(7)
            ->avg('total');

        return [
            'next_day_forecast' => $movingAverage,
            'historical_trend' => $historicalSales
        ];
    }

    private function calculateRefundRate()
    {
        $totalSales = Sale::whereMonth('created_at', Carbon::now()->month)->count();
        $refundedSales = Sale::whereMonth('created_at', Carbon::now()->month)
            ->whereIn('refund_status', ['partial_refund', 'full_refund'])
            ->count();

        return $totalSales > 0 ? ($refundedSales / $totalSales) * 100 : 0;
    }

    private function getMonthlySalesData($month, $year)
    {
        $monthlySales = Sale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('total_amount');

        $previousMonthSales = Sale::whereMonth('created_at', Carbon::create($year, $month)->subMonth()->month)
            ->whereYear('created_at', $year)
            ->sum('total_amount');

        $salesGrowthPercentage = $previousMonthSales > 0
            ? (($monthlySales - $previousMonthSales) / $previousMonthSales) * 100
            : 0;

        $cashPaymentsMonthly = Sale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $creditPaymentsMonthly = Sale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_method', 'credit_card')
            ->sum('total_amount');

        $mobilePaymentsMonthly = Sale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_method', 'mobile_pay')
            ->sum('total_amount');

        $codPaymentsMonthly = Sale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_method', 'cod')
            ->sum('total_amount');

        return [
            'total_sales' => $monthlySales,
            'salesGrowthPercentage' => $salesGrowthPercentage,
            'cashPaymentsMonthly' => $cashPaymentsMonthly,
            'creditPaymentsMonthly' => $creditPaymentsMonthly,
            'mobilePaymentsMonthly' => $mobilePaymentsMonthly,
            'codPaymentsMonthly' => $codPaymentsMonthly,
        ];
    }
}
