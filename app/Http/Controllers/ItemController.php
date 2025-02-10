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
use Symfony\Component\Process\Process;

class ItemController extends BaseController
{
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all(); // Fetch all brands
        $sizes = Size::all(); // Fetch all sizes, make sure the Size model exists
        $colors = Color::all();
        return view('items.create', compact('categories', 'brands', 'sizes', 'colors'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate incoming request
            $validated = $request->validate([
                'name' => 'required',
                'buying_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'sizes' => 'required|array',
                'colors' => 'array',
                'colors.*' => 'exists:colors,id',
                'variant_quantities' => 'required|array'
            ]);

            $category = Category::findOrFail($request->input('category_id'));
            $brand = Brand::findOrFail($request->input('brand_id'));

            // Store the item picture if uploaded
            $picturePath = null;
            if ($request->hasFile('picture')) {
                $picturePath = $request->file('picture')->store('items', 'public');
            }

            // Create parent item
            $parentItem = Item::create([
                'name' => $request->input('name'),
                'category_id' => $request->input('category_id'),
                'brand_id' => $request->input('brand_id'),
                'picture' => $picturePath,
                'quantity' => 0,
                'buying_price' => $request->input('buying_price'),
                'selling_price' => $request->input('selling_price'),
                'tax' => $request->input('tax'),
                'discount_type' => $request->input('discount_type'),
                'discount_value' => $request->input('discount_value'),
                'is_parent' => true,
            ]);

            // Generate parent barcode
            $parentBarcode = Str::padLeft($brand->id, 3, '0') .
                Str::padLeft($category->id, 3, '0') .
                Str::padLeft($parentItem->id, 4, '0');

            $parentItem->code = $parentBarcode;
            $parentItem->save();

            // Get the sizes and colors
            $sizes = Size::whereIn('id', $request->input('sizes'))->get();
            $colors = Color::whereIn('id', $request->input('colors', []))->get();

            $totalQuantity = 0;

            // Create variants
            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    $variantQuantity = $request->input("variant_quantities.{$size->id}.{$color->id}", 0);

                    if ($variantQuantity == 0) {
                        continue;
                    }

                    $totalQuantity += $variantQuantity;

                    // Enhanced if conditions for 'N/A'
                    if ($size->name == 'N/A' && $color->name == 'N/A') {
                        $variantName = $request->input('name');
                    } elseif ($size->name == 'N/A') {
                        $variantName = $request->input('name') . ' - ' . $color->name;
                    } elseif ($color->name == 'N/A') {
                        $variantName = $request->input('name') . ' - ' . $size->name;
                    } else {
                        $variantName = $request->input('name') . ' - ' . $size->name . ' - ' . $color->name;
                    }

                    // Create the variant
                    $variant = Item::create([
                        'name' => $variantName,
                        'category_id' => $request->input('category_id'),
                        'brand_id' => $request->input('brand_id'),
                        'picture' => $picturePath,
                        'quantity' => $variantQuantity,
                        'buying_price' => $request->input('buying_price'),
                        'selling_price' => $request->input('selling_price'),
                        'tax' => $request->input('tax'),
                        'discount_type' => $request->input('discount_type'),
                        'discount_value' => $request->input('discount_value'),
                        'parent_id' => $parentItem->id,
                        'is_parent' => false,
                    ]);

                    // Generate variant barcode
                    $variantBarcode = $parentBarcode .
                        Str::padLeft($color->id, 2, '0') .
                        Str::padLeft($size->id, 2, '0');

                    // Generate barcode image
                    $barcodeGenerator = new BarcodeGeneratorPNG();
                    $barcodePath = 'barcodes/' . $variantBarcode . '.png';
                    $barcodeStorage = storage_path('app/public/' . $barcodePath);

                    // Ensure directory exists
                    if (!file_exists(dirname($barcodeStorage))) {
                        mkdir(dirname($barcodeStorage), 0755, true);
                    }

                    file_put_contents(
                        $barcodeStorage,
                        $barcodeGenerator->getBarcode($variantBarcode, $barcodeGenerator::TYPE_CODE_128)
                    );

                    $variant->barcode = $barcodePath;
                    $variant->code = $variantBarcode;
                    $variant->sizes()->attach([$size->id]);
                    $variant->colors()->attach([$color->id]);
                    $variant->save();
                }
            }

            $parentItem->quantity = $totalQuantity;
            $parentItem->save();

            DB::commit();
            return redirect()->route('items.index')->with('success', 'Item and variants created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Item creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create item: ' . $e->getMessage());
        }
    }

    public function addVariant(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:items,id',
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $parentItem = Item::findOrFail($request->input('parent_id'));
        $size = Size::findOrFail($request->input('size_id'));
        $color = Color::findOrFail($request->input('color_id'));

        // Check if the variant already exists
        $existingVariant = Item::where('parent_id', $parentItem->id)
            ->whereHas('sizes', function ($query) use ($size) {
                $query->where('sizes.id', $size->id);
            })
            ->whereHas('colors', function ($query) use ($color) {
                $query->where('colors.id', $color->id);
            })
            ->first();

        if ($existingVariant) {
            // Update the existing variant's quantity
            $existingVariant->quantity += $request->input('quantity');
            $existingVariant->save();

            // Update parent item's total quantity
            $parentItem->quantity += $request->input('quantity');
            $parentItem->save();

            return response()->json(['success' => true, 'message' => 'Variant updated successfully']);
        } else {
            // Create variant name
            $variantName = $parentItem->name . ' - ' . $size->name . ' - ' . $color->name;

            // Create the variant
            $variant = Item::create([
                'name' => $variantName,
                'category_id' => $parentItem->category_id,
                'brand_id' => $parentItem->brand_id,
                'picture' => $parentItem->picture,
                'quantity' => $request->input('quantity'),
                'buying_price' => $parentItem->buying_price,
                'selling_price' => $parentItem->selling_price,
                'tax' => $parentItem->tax,
                'discount_type' => $parentItem->discount_type,
                'discount_value' => $parentItem->discount_value,
                'parent_id' => $parentItem->id,
                'is_parent' => false,
            ]);

            // Generate variant barcode
            $variantBarcode = $parentItem->code .
                Str::padLeft($color->id, 2, '0') .
                Str::padLeft($size->id, 2, '0');

            // Generate barcode image
            $barcodeGenerator = new BarcodeGeneratorPNG();
            $barcodePath = 'barcodes/' . $variantBarcode . '.png';
            file_put_contents(
                storage_path('app/public/' . $barcodePath),
                $barcodeGenerator->getBarcode($variantBarcode, $barcodeGenerator::TYPE_CODE_128)
            );

            // Update variant with barcode
            $variant->barcode = $barcodePath;
            $variant->code = $variantBarcode;

            // Attach size and color
            $variant->sizes()->attach([$size->id]);
            $variant->colors()->attach([$color->id]);

            $variant->save();

            // Update parent item's total quantity
            $parentItem->quantity += $request->input('quantity');
            $parentItem->save();

            return response()->json(['success' => true, 'message' => 'Variant added successfully']);
        }
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
        $brands = Brand::all();
        $search = $request->input('search');
        $brandId = $request->input('brand_id');
        $showAll = $request->input('show_all');

        // Initialize query with pagination
        $query = Item::with('brand')->where('is_parent', true);

        if ($showAll) {
            // Show all items when show_all is true
            $items = $query->orderBy('id', 'desc')
                          ->paginate(12)
                          ->withQueryString();
        } else {
            // Apply filters if search or specific brand is selected
            if ($search || $brandId) {
                $query->when($brandId, function ($query) use ($brandId) {
                    return $query->where('brand_id', $brandId);
                })
                ->when($search, function ($query) use ($search) {
                    return $query->where(function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhere('code', 'like', '%' . $search . '%')
                          ->orWhereHas('brand', function($q) use ($search) {
                              $q->where('name', 'like', '%' . $search . '%');
                          });
                    });
                });
            } else {
                // If no filters are applied and not showing all, return empty result set
                $query->whereRaw('1 = 0');
            }

            $items = $query->orderBy('id', 'desc')
                          ->paginate(12)
                          ->withQueryString();
        }

        $lowStockItems = $this->getLowStockItems();
        return view('items.index', compact('items', 'brands', 'lowStockItems', 'showAll'));
    }


    public function edit($id)
    {
        $item = Item::with(['variants', 'sizes', 'colors'])->findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        $sizes = Size::all();
        $colors = Color::all();

        if (!$item->is_parent) {
            $parentItems = Item::where('is_parent', true)->get();
            return view('items.edit', compact('item', 'categories', 'brands', 'sizes', 'colors', 'parentItems'));
        }

        return view('items.edit', compact('item', 'categories', 'brands', 'sizes', 'colors'));
    }

    public function update(Request $request, Item $item)
    {
        DB::beginTransaction();
        try {
            if ($item->is_parent) {
                // Update parent item
                $item->update([
                    'name' => $request->name,
                    'category_id' => $request->category_id,
                    'brand_id' => $request->brand_id,
                    'selling_price' => $request->selling_price,
                    'tax' => $request->tax,
                    'discount_type' => $request->discount_type,
                    'discount_value' => $request->discount_value,
                ]);

                // Handle picture upload
                if ($request->hasFile('picture')) {
                    $picturePath = $request->file('picture')->store('items', 'public');
                    $item->update(['picture' => $picturePath]);
                }

                // Update all variants with new shared properties
                $item->variants()->update([
                    'selling_price' => $request->selling_price,
                    'tax' => $request->tax,
                    'discount_type' => $request->discount_type,
                    'discount_value' => $request->discount_value,
                ]);
            } else {
                // Update variant
                $item->update([
                    'quantity' => $request->quantity
                ]);

                // Update parent's total quantity
                if ($item->parent) {
                    $item->parent->update([
                        'quantity' => $item->parent->variants()->sum('quantity')
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('items.index')->with('success', 'Item updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update error: ' . $e->getMessage());
            return back()->with('error', 'Error updating item: ' . $e->getMessage());
        }
    }

    public function destroy(Item $item)
    {
        try {
            $item->delete();
            return redirect()->route('items.index')
                ->with('success', 'Item deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('items.index')
                ->with('error', 'Failed to delete item. ' . $e->getMessage());
        }
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

    public function itemsExport(Request $request)
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
            $printerName = 'Xprinter_XP-T361U';

            // Generate PDF in memory
            $barcodePath = public_path('storage/' . $item->barcode);
            $pdf = PDF::loadView('pdf.label', [
                'item' => $item,
                'barcodePath' => $barcodePath,
            ]);

            // Convert mm to points (1mm = 2.83465 points)
            $width = 36.5 * 2.83465;  // 103.46 points
            $height = 25 * 2.83465;   // 70.87 points
            $pdf->setPaper([0, 0, $width, $height], 'landscape');

            // Save PDF temporarily
            $tempPath = storage_path('app/temp/label_' . uniqid() . '.pdf');
            $pdf->save($tempPath);

            if ($this->isWindows()) {
                // Path to SumatraPDF
                $sumatraPath = '"C:\Program Files\SumatraPDF\SumatraPDF.exe"';

                // Define custom paper size in mm
                $printSettings = "-print-settings \"$quantity"
                    . ",paper=Custom.36.5x25mm"  // Exact dimensions in mm
                    . ",fit=NoScaling"           // Prevent auto-scaling
                    . ",offset-x=0,offset-y=0\""; // No margin offset

                // Build and execute the print command
                $command = "$sumatraPath $printSettings -print-to \"$printerName\" \"$tempPath\"";

                $process = Process::fromShellCommandline($command);
                $process->setTimeout(60);
                $process->run();

                if (!$process->isSuccessful()) {
                    Log::error('Windows print error: ' . $process->getErrorOutput());
                    throw new \Exception('Printing failed on Windows: ' . $process->getErrorOutput());
                }
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

        $query = DB::table('items')
            ->join('brands', 'items.brand_id', '=', 'brands.id')
            ->leftJoin('sale_items', function ($join) use ($startDate, $endDate) {
                $join->on('items.id', '=', 'sale_items.item_id');
                if ($startDate && $endDate) {
                    $join->whereBetween('sale_items.created_at', [$startDate, date('Y-m-d 23:59:59', strtotime($endDate))]);
                }
            })
            ->select(
                'items.id',
                'brands.name as brand_name',
                'items.name as item_name',
                'items.quantity as stock_quantity',
                'items.selling_price',
                DB::raw('COALESCE(SUM(sale_items.quantity), 0) as quantity_sold')
            )
            ->where('items.is_parent', false); // Exclude parent items

        // Only apply brand filter if a specific brand is selected
        if ($brandId) {
            $query->where('items.brand_id', $brandId);
            $fileName = str_replace(' ', '_', strtolower(DB::table('brands')->where('id', $brandId)->value('name')));
        } else {
            $fileName = 'all_brands';
        }

        $items = $query->groupBy('items.id', 'brands.name', 'items.name', 'items.quantity', 'items.selling_price')
            ->get();

        // Including sale price and selling price
        $items = $items->map(function ($itemData) {
            $item = Item::find($itemData->id);
            $itemData->sale_price = $item->priceAfterSale();
            $itemData->selling_price = $item->selling_price;
            return $itemData;
        });

        // Create CSV data
        $csvData = "Brand,Item,Quantity Sold,Stock Quantity,Price Before Sale,Price After Sale\n";
        foreach ($items as $item) {
            $csvData .= "{$item->brand_name},{$item->item_name},{$item->quantity_sold},{$item->stock_quantity},{$item->selling_price}EGP,{$item->sale_price}EGP\n";
        }

        // Dynamic file name with date range if provided
        $fileName .= '_items_report';
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

    // Add this new method to ItemController
    public function updateVariantsQuantity(Request $request)
    {
        try {
            DB::beginTransaction();

            $totalQuantity = 0;
            $parentId = null;

            foreach ($request->updates as $update) {
                $variant = Item::findOrFail($update['id']);
                $variant->quantity = (int) $update['quantity'];
                $variant->save();

                $parentId = $variant->parent_id;
                $totalQuantity += $variant->quantity;
            }

            // Update parent's quantity
            if ($parentId) {
                $parent = Item::findOrFail($parentId);
                $parent->quantity = $totalQuantity;
                $parent->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'totalQuantity' => $totalQuantity,
                'message' => 'Quantities updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Quantity update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateBarcodes()
    {
        try {
            $items = Item::all();
            $barcodeGenerator = new BarcodeGeneratorPNG();

            foreach ($items as $item) {
                $barcodePath = 'barcodes/' . $item->code . '.png';
                $barcodeStorage = storage_path('app/public/' . $barcodePath);

                // Ensure directory exists
                if (!file_exists(dirname($barcodeStorage))) {
                    mkdir(dirname($barcodeStorage), 0755, true);
                }

                file_put_contents(
                    $barcodeStorage,
                    $barcodeGenerator->getBarcode($item->code, $barcodeGenerator::TYPE_CODE_128)
                );

                $item->barcode = $barcodePath;
                $item->save();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Barcode generation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
