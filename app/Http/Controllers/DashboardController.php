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

        // Count pending COD orders for notification badge - make it null-safe
        $pendingCodCount = Sale::where('payment_method', 'cod')
                              ->where(function($query) {
                                  $query->where('is_arrived', 'pending')
                                        ->orWhereNull('is_arrived');
                              })
                              ->count();

        // Get brands with sales - OPTIMIZED to avoid N+1 query by including brands table directly
        $topSellingBrands = SaleItem::select(
            'items.brand_id',
            'brands.name',
            'brands.picture',
            DB::raw('SUM(sale_items.quantity) as total_sales')
        )
        ->join('items', 'sale_items.item_id', '=', 'items.id')
        ->join('brands', 'items.brand_id', '=', 'brands.id')
        ->groupBy('items.brand_id', 'brands.name', 'brands.picture')
        ->having('total_sales', '>', 0)
        ->orderByDesc('total_sales')
        ->get();

        // Convert result to needed format without additional queries
        $topSellingBrandDetails = $topSellingBrands->map(function($brand) {
            return [
                'name' => $brand->name,
                'image' => $brand->picture,
                'total_sales' => $brand->total_sales
            ];
        })->toArray();

        // Top Selling Brands Monthly
        $topSellingBrandsMonthly = SaleItem::select(
            'items.brand_id',
            'brands.name',
            'brands.picture',
            DB::raw('SUM(sale_items.quantity) as total_sales')
        )
        ->join('items', 'sale_items.item_id', '=', 'items.id')
        ->join('brands', 'items.brand_id', '=', 'brands.id')
        ->whereMonth('sale_items.created_at', $selectedMonth)
        ->whereYear('sale_items.created_at', $selectedYear)
        ->groupBy('items.brand_id', 'brands.name', 'brands.picture')
        ->having('total_sales', '>', 0)
        ->orderByDesc('total_sales')
        ->get();

        // Low Stock Items
        $lowStockItems = Item::where('quantity', '<=', 2)
            ->where('is_parent', false)
            ->orderBy('quantity', 'asc')
            ->orderBy('name', 'asc')  // Secondary sort by name
            ->get();

        // Sales Metrics
        $monthlySales = $this->getMonthlySalesData($selectedMonth, $selectedYear);

        // OPTIMIZED: Get monthly payment methods statistics in a single query
        $monthlyPaymentStats = Sale::whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $cashPaymentsMonthly = $monthlyPaymentStats['cash'] ?? 0;
        $creditPaymentsMonthly = $monthlyPaymentStats['credit_card'] ?? 0;
        $mobilePaymentsMonthly = $monthlyPaymentStats['mobile_pay'] ?? 0;
        $codPaymentsMonthly = $monthlyPaymentStats['cod'] ?? 0;

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

        // OPTIMIZED: Get today's payment methods statistics in a single query
        $todayPaymentStats = Sale::whereDate('created_at', Carbon::today())
            ->select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();

        $cashPayments = $todayPaymentStats['cash'] ?? 0;
        $creditPayments = $todayPaymentStats['credit_card'] ?? 0;
        $mobilePayments = $todayPaymentStats['mobile_pay'] ?? 0;
        $codPayments = $todayPaymentStats['cod'] ?? 0;

        // OPTIMIZED: Get stock levels in a single query with CASE expressions
        $stockLevelStats = Item::where('is_parent', false)
            ->select([
                DB::raw('COUNT(CASE WHEN quantity <= 2 THEN 1 END) as critical'),
                DB::raw('COUNT(CASE WHEN quantity > 2 AND quantity <= 5 THEN 1 END) as low'),
                DB::raw('COUNT(CASE WHEN quantity > 5 THEN 1 END) as healthy')
            ])
            ->first();

        $stockLevels = [
            'critical' => $stockLevelStats->critical,
            'low' => $stockLevelStats->low,
            'healthy' => $stockLevelStats->healthy
        ];

        // OPTIMIZED: Get payment method statistics in a single query
        $paymentMethodStats = Sale::select('payment_method', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        $allSalesCount = $paymentMethodStats->sum('count');
        $topPaymentMethodStat = $paymentMethodStats->first();
        $topPaymentMethod = $topPaymentMethodStat ? $topPaymentMethodStat->payment_method : null;
        $topPaymentMethodCount = $topPaymentMethodStat ? $topPaymentMethodStat->count : 0;
        $topPaymentMethodPercentage = $allSalesCount > 0 ? ($topPaymentMethodCount / $allSalesCount * 100) : 0;

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
            'average_refund_amount' => Refund::avg('refund_amount') ?? 0,
            'total_refunds_count' => Refund::count(),
            'refunds_by_reason' => Refund::select('reason', DB::raw('COUNT(*) as count'), DB::raw('SUM(refund_amount) as total_amount'))
                ->whereNotNull('reason')
                ->groupBy('reason')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'top_refunded_items' => Refund::with('item')
                ->select('item_id', DB::raw('SUM(quantity_refunded) as total_quantity'), DB::raw('SUM(refund_amount) as total_amount'))
                ->groupBy('item_id')
                ->orderBy('total_amount', 'desc')
                ->limit(5)
                ->get(),
            'recent_refunds' => Refund::with(['sale', 'item'])
                ->latest()
                ->take(5)
                ->get(),
            'weekly_refund_trend' => $this->getWeeklyRefundTrend(),
            'monthly_refund_comparison' => $this->getMonthlyRefundComparison()
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
            'AllSalesCount' => $allSalesCount,
            'recentItems' => $recentItems,
            'todayRevenue' => Sale::whereDate('created_at', today())->sum('total_amount'),
            'todayOrders' => Sale::whereDate('created_at', today())->count(),
            'cashPayments'=>$cashPayments,
            'creditPayments' =>$creditPayments,
            'mobilePayments' => $mobilePayments,
            'cashPaymentsMonthly' => $cashPaymentsMonthly,
            'creditPaymentsMonthly' => $creditPaymentsMonthly,
            'mobilePaymentsMonthly' => $mobilePaymentsMonthly,
            'codPayments' => $codPayments,
            'codPaymentsMonthly' => $codPaymentsMonthly,
            'topSellingBrands' => $topSellingBrandDetails,
            'topSellingBrandsMonthly' => $topSellingBrandsMonthly,
            'recentRefunds' => $refundMetrics['recent_refunds'],
            'todayRefunds' => $refundMetrics['today_refunds'],
            'monthRefunds' => $refundMetrics['month_refunds'],
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
            'pendingCodCount' => $pendingCodCount,
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
        // OPTIMIZED: Get monthly sales data in fewer queries
        $currentMonthSales = Sale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('SUM(CASE WHEN payment_method = "cash" THEN total_amount ELSE 0 END) as cash_payments'),
                DB::raw('SUM(CASE WHEN payment_method = "credit_card" THEN total_amount ELSE 0 END) as credit_payments'),
                DB::raw('SUM(CASE WHEN payment_method = "mobile_pay" THEN total_amount ELSE 0 END) as mobile_payments'),
                DB::raw('SUM(CASE WHEN payment_method = "cod" THEN total_amount ELSE 0 END) as cod_payments')
            )
            ->first();

        $previousMonthSales = Sale::whereMonth('created_at', Carbon::create($year, $month)->subMonth()->month)
            ->whereYear('created_at', Carbon::create($year, $month)->subMonth()->year)
            ->sum('total_amount');

        $salesGrowthPercentage = $previousMonthSales > 0
            ? (($currentMonthSales->total_sales - $previousMonthSales) / $previousMonthSales) * 100
            : 0;

        return [
            'total_sales' => $currentMonthSales->total_sales ?? 0,
            'salesGrowthPercentage' => $salesGrowthPercentage,
            'cashPaymentsMonthly' => $currentMonthSales->cash_payments ?? 0,
            'creditPaymentsMonthly' => $currentMonthSales->credit_payments ?? 0,
            'mobilePaymentsMonthly' => $currentMonthSales->mobile_payments ?? 0,
            'codPaymentsMonthly' => $currentMonthSales->cod_payments ?? 0,
        ];
    }

    private function getWeeklyRefundTrend()
    {
        return Refund::select(
            DB::raw('WEEK(created_at) as week'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(refund_amount) as total_amount')
        )
        ->where('created_at', '>=', Carbon::now()->subWeeks(4))
        ->groupBy('week')
        ->orderBy('week')
        ->get();
    }

    private function getMonthlyRefundComparison()
    {
        $currentMonth = Refund::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('refund_amount');

        $previousMonth = Refund::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('refund_amount');

        $growthPercentage = $previousMonth > 0
            ? (($currentMonth - $previousMonth) / $previousMonth) * 100
            : 0;

        return [
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth,
            'growth_percentage' => $growthPercentage
        ];
    }
}
