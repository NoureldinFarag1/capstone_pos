<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Item;

class ItemTraceController extends Controller
{
    public function index()
    {
        $items = Item::all();
        $itemName = ''; // Initialize $itemName
        return view('items.trace', compact('items', 'itemName'));
    }

    public function trace(Request $request)
    {
        $searchTerm = $request->input('item_name');

        if (empty($searchTerm)) {
            return redirect()->route('items.trace')->with('error', 'Please select an item to trace.');
        }

        try {
            // Find the selected item
            $selectedItem = Item::where('name', $searchTerm)->first();

            if (!$selectedItem) {
                return view('items.trace', [
                    'items' => Item::all(),
                    'error' => 'Selected item not found.'
                ]);
            }

            $sales = Sale::select('sales.*')
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->join('items', 'items.id', '=', 'sale_items.item_id')
                ->leftJoin('brands', 'items.brand_id', '=', 'brands.id')
                ->where(function ($query) use ($searchTerm) {
                    $query->where('items.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('brands.name', 'like', '%' . $searchTerm . '%');
                })
                ->with([
                    'items' => function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%');
                    }
                ])
                ->distinct()
                ->get();

            $items = Item::all();

            // Even if sales is empty, we'll pass the selectedItem
            return view('items.trace', compact('sales', 'searchTerm', 'items', 'selectedItem'));

        } catch (\Exception $e) {
            \Log::error('Error tracing items: ' . $e->getMessage());

            return view('items.trace', [
                'items' => Item::all(),
                'error' => 'An error occurred while tracing items. Please try again.'
            ]);
        }
    }
}