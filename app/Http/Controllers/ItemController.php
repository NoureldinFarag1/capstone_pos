<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Item;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ItemController extends BaseController
{
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all(); // Fetch all brands
        return view('items.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'applied_sale' => 'nullable|numeric|min:0|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|integer',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);
        $category = Category::findOrFail($request->input('category_id'));
        $brand = Brand::findOrFail($request->input('brand_id'));

        // Store the item picture if uploaded
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('items', 'public');
        } else {
            $picturePath = null;
        }

        // Create the item in the database
        $item = Item::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'brand_id' => $request->input('brand_id'),
            //'price' => $request->input('price'),
            'picture' => $picturePath,
            'quantity' => $request->input('quantity'),
            'buying_price' => $request->input('buying_price'),
            'selling_price' => $request->input('selling_price'),
            'tax' => $request->input('tax'),
            'applied_sale' => $request->input('applied_sale', 0), // default to 0 if not provided
        ]);

        // Create a unique barcode based on the item ID
        $barcode = str_pad($brand->id . $category->id . $item->id, 8, '0', STR_PAD_LEFT); // Example of padded barcode

        $barcodeGenerator = new BarcodeGeneratorPNG();
        $barcodePath = 'barcodes/' . $barcode . '.png'; // Define barcode file path

        // Save barcode image using Storage facade
        file_put_contents(storage_path('app/public/' . $barcodePath), $barcodeGenerator->getBarcode($barcode, $barcodeGenerator::TYPE_CODE_128));

        // Update the item with the barcode path
        $item->barcode = $barcodePath;
        $item->save(); // Save the changes to include the barcode

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
        }
    
    public function index(Request $request)
    {
        $brands = Brand::all(); // Fetch all brands
        $categories = Category::all(); // Fetch all categories if needed

        $items = Item::when($request->brand, function ($query) use ($request) {
            return $query->where('brand_id', $request->brand);
        })
        ->when($request->category, function ($query) use ($request) {
            return $query->where('category_id', $request->category);
        })
        ->get();
        $lowStockItems = $this->getLowStockItems();
        $categories = Category::all();
        $brands = Brand::all();
        return view('items.index', compact('items', 'categories', 'brands', 'lowStockItems'));
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $categories = Category::all(); // To show all categories for selection
        $brands = Brand::all(); // To show all brands for selection
        return view('items.edit', compact('item', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'applied_sale' => 'nullable|numeric|min:0|max:100',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'required|integer|min:0',
        ]);

        $item = Item::findOrFail($id);

        // If a new picture is uploaded, store it
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('items', 'public');
            $item->picture = $picturePath;
        }

        // Update other item details
        $item->name = $request->input('name');
        $item->category_id = $request->input('category_id');
        $item->brand_id = $request->input('brand_id');
        $item->buying_price = $request->input('buying_price');
        $item->selling_price = $request->input('selling_price');
        $item->tax = $request->input('tax');
        $item->applied_sale = $request->input('applied_sale', 0);
        $item->quantity = $request->input('quantity');
        $item->save(); // Save the changes to the database

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete(); // Delete the item from the database

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }

    public function show($id)
    {
        $item = Item::with(['category', 'brand'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    public function getItemsByCategory($category_id)
    {
        $items = Item::where('category_id', $category_id)->get();
        return response()->json($items);
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Fetch the item
        $item = Item::findOrFail($request->input('item_id'));

        // Calculate total price
        $totalPrice = $item->sellingPriceWithTax() * $request->input('quantity');

        // Create the sale, including the item's barcode
        Sale::create([
            'item_id' => $item->id,
            'quantity' => $request->input('quantity'),
            'total_price' => $totalPrice,
            'barcode' => $item->barcode, // Store the item's barcode in the sale
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function filter(Request $request)
    {
        $brandId = $request->input('brand_id');
        $categoryId = $request->input('category_id');
        $itemName = $request->input('item_name');

        $query = Item::query();

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($itemName) {
            $query->where('name', 'like', '%' . $itemName . '%');
        }

        $items = $query->get();

        return response()->json($items);
    }

    public function searchByBarcode(Request $request)
    {   
        
        $item = Item::where('barcode', $request->barcode)->first();

        if ($item) {
            return response()->json([
                'id' => $item->id,
                'name' => $item->name,
                'brand_id' => $item->brand_id,
                'category_id' => $item->category_id,
                'category_name' => $item->category->name
            ]);
        } else {
            return response()->json(null);
        }
    }
    public function filterItems(Request $request)
    {
        $brandId = $request->input('brand_id');
        $categoryId = $request->input('category_id');

        $items = Item::where('brand_id', $brandId)
            ->where('category_id', $categoryId)
            ->get();

        return response()->json($items);
    }

    public function getItemsByBrandAndCategory(Request $request)
    {
        $items = Item::where('brand_id', $request->brand_id)
                    ->where('category_id', $request->category_id)
                    ->get();
        return response()->json($items);
    }

}
