<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        // Get the top 5 most selling brands
        $topSellingBrands = SaleItem::select('items.brand_id', DB::raw('SUM(sale_items.quantity) as total_sales'))
        ->join('items', 'sale_items.item_id', '=', 'items.id')
        ->groupBy('items.brand_id')
        ->orderByDesc('total_sales')
        ->take(3)
        ->get();

        // Now, fetch the brand details for each of the top 5 brands
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
        $lowStockItems = Item::where('quantity', '<=', 5)->get();

        // Sales Metrics
        $currentMonth = Carbon::now();
        $monthlySales = Sale::whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_amount');

        $previousMonthSales = Sale::whereMonth('created_at', $currentMonth->subMonth()->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_amount');

        $salesGrowthPercentage = $previousMonthSales > 0
            ? (($monthlySales - $previousMonthSales) / $previousMonthSales) * 100
            : 0;

        $cashPaymentsMonthly = Sale::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->where('payment_method', 'cash')
                            ->sum('total_amount');
        $creditPaymentsMonthly = Sale::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->where('payment_method', 'visa')
                                      ->sum('total_amount');

        $mobilePaymentsMonthly = Sale::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->where('payment_method', 'mobile_pay')
                                  ->sum('total_amount');

        // Inventory Metrics
        $totalItems = Item::count();
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

        // Stock Level Summary
        $stockLevels = [
                'critical' => Item::where('quantity', '<=', 5)->count(),
                'low' => Item::where('quantity', '>', 5)->where('quantity', '<=', 20)->count(),
                'healthy' => Item::where('quantity', '>', 20)->count()
            ];

        $topPaymentMethod = Sale::select('payment_method', DB::raw('count(*) as count'))
                            ->groupBy('payment_method')
                            ->orderByDesc('count')
                            ->first()->payment_method;
        $topPaymentMethodPercentage = (Sale::where('payment_method', $topPaymentMethod)->count() / Sale::count()) * 100;
        $topPaymentMethodCount = (Sale::where('payment_method', $topPaymentMethod)->count());
        $AllSalesCount = Sale::count();

        $recentItems = Item::latest()->take(5)->get();

        return view('layouts.dashboard', [
            'lowStockItems' => $lowStockItems,
            'monthlySales' => $monthlySales,
            'salesGrowthPercentage' => $salesGrowthPercentage,
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
            'cashPaymentsMonthly' => $cashPaymentsMonthly,
            'creditPaymentsMonthly' => $creditPaymentsMonthly,
            'mobilePaymentsMonthly' => $mobilePaymentsMonthly,
            'topSellingBrandDetails' => $topSellingBrandDetails,
            ]);
    }
}
