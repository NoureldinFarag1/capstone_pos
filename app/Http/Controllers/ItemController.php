<?php

namespace App\Http\Controllers;

use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Size;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ItemController extends BaseController
{
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all(); // Fetch all brands
        $sizes = Size::all(); // Fetch all sizes, make sure the Size model exists
        $colors = Color::all();
        return view('items.create', compact('categories', 'brands', 'sizes','colors'));
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|integer',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'quantity' => 'required|integer|min:0',
            'sizes' => 'required|array',
            'colors' => 'array',
            'colors.*' => 'exists:colors,id'
        ]);

        $category = Category::findOrFail($request->input('category_id'));
        $brand = Brand::findOrFail($request->input('brand_id'));

        // Store the item picture if uploaded
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('items', 'public');
        } else {
            $picturePath = null;
        }

        // Calculate the discounted price using your model's discount method
        $createdItems = [];

        // Get the size and color names for reference
        $sizes = Size::whereIn('id', $request->input('sizes'))->get();
        $colors = Color::whereIn('id', $request->input('colors'))->get();

        // Create an item for each size and color combination
        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                // Create a unique name for this variation
                $variantName = $request->input('name') . ' - ' . $color->name . ' - ' . $size->name;

                // Create the item variation
                $item = Item::create([
                    'name' => $variantName,
                    'category_id' => $request->input('category_id'),
                    'brand_id' => $request->input('brand_id'),
                    'picture' => $picturePath,
                    'quantity' => $request->input('quantity'),
                    'buying_price' => $request->input('buying_price'),
                    'selling_price' => $request->input('selling_price'),
                    'tax' => $request->input('tax'),
                    'discount_type' => $request->input('discount_type'),
                    'discount_value' => $request->input('discount_value'),
                ]);

                // Generate unique barcode
                $barcode = Str::padLeft($brand->id, 3, '0') .
                          Str::padLeft($category->id, 3, '0') .
                          Str::padLeft($item->id, 4, '0');

                // Generate barcode image
                $barcodeGenerator = new BarcodeGeneratorPNG();
                $barcodePath = 'barcodes/' . $barcode . '.png';
                file_put_contents(
                    storage_path('app/public/' . $barcodePath),
                    $barcodeGenerator->getBarcode($barcode, $barcodeGenerator::TYPE_CODE_128)
                );

                // Update the item with the barcode
                $item->barcode = $barcodePath;
                $item->code = $barcode;

                // Attach the specific size and color to this item
                $item->sizes()->attach([$size->id]);
                $item->colors()->attach([$color->id]);

                $item->save();

                $createdItems[] = $item;
            }
        }

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }
    public function getRelatedItems($name)
    {
        // Remove size and color information to get base name
        $baseName = explode(' - ', $name)[0];

        // Find all items that start with this base name
        return Item::where('name', 'like', $baseName . '%')->get();
    }

    public function index(Request $request)
    {
        $brands = Brand::all(); // Fetch all brands
        $categories = Category::all(); // Fetch all categories if needed

        $brandId = $request->input('brand_id'); // Capture the selected brand
        $categoryId = $request->input('category_id'); // Capture the selected category

        // Query items with optional brand and category filtering, and paginate
        $items = Item::when($brandId, function ($query) use ($brandId) {
                return $query->where('brand_id', $brandId);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->paginate(21); // Paginate n items per page

        $lowStockItems = $this->getLowStockItems();
        return view('items.index', compact('items', 'categories', 'brands', 'lowStockItems'));
    }


    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $categories = Category::all(); // To show all categories for selection
        $brands = Brand::all(); // To show all brands for selection
        $sizes = Size::all();
        return view('items.edit', compact('item', 'categories', 'brands', 'sizes'));
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'selling_price' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0|max:100',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'quantity' => 'required|integer|min:0',
            'sizes' => 'nullable'
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
        $item->selling_price = $request->input('selling_price');
        $item->tax = $request->input('tax');
        $item->discount_type = $request->discount_type;
        $item->discount_value = $request->discount_value;
        $item->quantity = $request->input('quantity');
        $item->sizes()->sync($request->sizes);
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

        $item = Item::where('code', $request->code)->first();

        if ($item) {
            return response()->json([
                'id' => $item->id,
                'name' => $item->name,
                'brand_id' => $item->brand_id,
                'category_id' => $item->category_id,
                'category_name' => $item->category->name,
                'price' => $item->selling_price,
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

    public function ItemsExport(Request $request)
    {
        $brandId = $request->input('brand_id');
        $items = Item::when($brandId, function ($query) use ($brandId) {
            return $query->where('brand_id', $brandId);
        })->get();

        // Use Excel or CSV package (like Laravel Excel) to export
        return Excel::download(new ItemsExport($items), 'items.xlsx');
    }

    private function isWindows()
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    public function printLabel($id, Request $request)
    {
        try {
            $item = Item::findOrFail($id);
            $quantity = $request->input('quantity', 1);
            $printerName = 'Xprinter_XP_T361U';

            // Generate PDF in memory
            $barcodePath = public_path('storage/' . $item->barcode);
            $pdf = PDF::loadView('pdf.label', [
                'item' => $item,
                'barcodePath' => $barcodePath,
            ]);

            // Save PDF temporarily
            $tempPath = storage_path('app/temp/label_' . uniqid() . '.pdf');
            $pdf->save($tempPath);

            if ($this->isWindows()) {
                // Windows printing using direct command with copies parameter
                shell_exec('print /d:"' . $printerName . '" /n:' . $quantity . ' "' . $tempPath . '"');
            } else {
                // Mac/Linux printing using lp command
                exec("lp -d $printerName -n $quantity $tempPath");
            }

            // Clean up temp file
            unlink($tempPath);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Label printing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportCSV(Request $request)
    {
        $brandId = $request->input('brand_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $brandName = DB::table('brands')->where('id', $brandId)->value('name');
        $brandNameSlug = str_replace(' ', '_', strtolower($brandName));

        $items = DB::table('items')
        ->join('brands', 'items.brand_id', '=', 'brands.id')
        ->leftJoin('sale_items', function ($join) use ($startDate, $endDate) {
            $join->on('items.id', '=', 'sale_items.item_id');
            if ($startDate && $endDate) {
                $join->whereBetween('sale_items.created_at', [$startDate, $endDate]);
            }
        })
        ->select(
            'items.id',
            'brands.name as brand_name',
            'items.name as item_name',
            'items.quantity as stock_quantity',
            'items.selling_price',
            DB::raw('SUM(sale_items.quantity) as quantity_sold'),
        )
        ->where('items.brand_id', $brandId)
        ->groupBy('items.id', 'brands.name', 'items.name', 'items.quantity', 'items.selling_price')
        ->get();

        // Add sale price
        $items = $items->map(function ($itemData) {
            $item = Item::find($itemData->id);
            $itemData->sale_price = $item->priceAfterSale();
            return $itemData;
        });

        // Create CSV data
        $csvData = "Brand,Item,Quantity Sold,Stock Quantity,Price\n";
        foreach ($items as $item) {
            $csvData .= "{$item->brand_name},{$item->item_name},{$item->quantity_sold},{$item->stock_quantity},{$item->sale_price}EGP\n";
        }

        // Dynamic file name with date range if provided
        $fileName = $brandNameSlug . '_items_report';
        if ($startDate && $endDate) {
            $fileName .= "_{$startDate}_to_{$endDate}";
        }
        $fileName .= '.csv';

        // Return CSV as downloadable response
        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new ItemsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Items imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'There was an error importing the file: ' . $e->getMessage());
        }
    }
}
