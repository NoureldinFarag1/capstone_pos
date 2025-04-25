<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandTraceController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('brands.trace', compact('brands'));
    }

    public function trace(Request $request)
    {
        if (!$request->brand_name) {
            return redirect()->route('brands.trace')
                ->with('error', 'Please select a brand');
        }

        $brand = Brand::where('name', $request->brand_name)->first();
        if (!$brand) {
            return redirect()->route('brands.trace')
                ->with('error', 'Brand not found');
        }

        $selectedBrand = $brand;
        $query = Sale::whereHas('items', function ($query) use ($brand) {
            $query->where('brand_id', $brand->id);
        });

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // OPTIMIZED: Eager load with pivot data to avoid N+1 queries
        $sales = $query->with([
            'items' => function ($query) use ($brand) {
                $query->where('brand_id', $brand->id)
                      ->withPivot(['quantity', 'price']);
            }
        ])->orderBy('created_at', 'desc')->get();

        // OPTIMIZED: Calculate totals in the database instead of PHP loops
        $saleIds = $sales->pluck('id')->toArray();

        $salesData = DB::table('sale_items')
            ->join('items', 'sale_items.item_id', '=', 'items.id')
            ->where('items.brand_id', $brand->id)
            ->whereIn('sale_items.sale_id', $saleIds)
            ->select(
                DB::raw('SUM(sale_items.quantity * sale_items.price) as total_revenue'),
                DB::raw('SUM(sale_items.quantity) as total_units_sold')
            )
            ->first();

        $totalRevenue = $salesData->total_revenue ?? 0;
        $totalUnitsSold = $salesData->total_units_sold ?? 0;

        $brands = Brand::all();
        return view('brands.trace', compact('sales', 'selectedBrand', 'brands', 'totalRevenue', 'totalUnitsSold'));
    }
}
