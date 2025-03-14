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

        $sales = $query->with([
            'items' => function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            }
        ])->orderBy('created_at', 'desc')->get();

        $totalRevenue = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                return $item->pivot->quantity * $item->pivot->price;
            });
        });

        $totalUnitsSold = $sales->sum(function ($sale) {
            return $sale->items->sum('pivot.quantity');
        });

        $brands = Brand::all();
        return view('brands.trace', compact('sales', 'selectedBrand', 'brands', 'totalRevenue', 'totalUnitsSold'));
    }
}