<?php

namespace App\Http\Controllers;

use App\Exports\ItemsExport;
use App\Exports\SalesExport;
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
use Illuminate\Support\Facades\Storage;
use App\Exports\SalesPerBrandExport;

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
                'buying_price' => 'nullable|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'nullable|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'sizes' => 'required|array',
                'colors' => 'array',
                'colors.*' => 'exists:colors,id',
                'variant_quantities' => 'required|array'
            ]);

            // Set default values for nullable fields
            $buyingPrice = $request->input('buying_price') ?? 0;
            $tax = $request->input('tax') ?? 0;
            $discountValue = $request->input('discount_value') ?? 0;

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
                'buying_price' => $buyingPrice,
                'selling_price' => $request->input('selling_price'),
                'tax' => $tax,
                'discount_type' => $request->input('discount_type'),
                'discount_value' => $discountValue,
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
                        'buying_price' => $buyingPrice,
                        'selling_price' => $request->input('selling_price'),
                        'tax' => $tax,
                        'discount_type' => $request->input('discount_type'),
                        'discount_value' => $discountValue,
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
                    $storagePath = storage_path('app/public/' . $barcodePath);

                    // Ensure directory exists
                    if (!file_exists(dirname($storagePath))) {
                        mkdir(dirname($storagePath), 0755, true);
                    }

                    // Generate barcode with larger dimensions and higher quality
                    $barcodeImage = $barcodeGenerator->getBarcode(
                        $variantBarcode,
                        $barcodeGenerator::TYPE_CODE_128,
                        3,  // Width factor (larger number = wider bars)
                        50  // Height in pixels
                    );

                    // Save the barcode image
                    if (file_put_contents($storagePath, $barcodeImage)) {
                        $variant->barcode = $barcodePath;
                        $variant->code = $variantBarcode;
                        $variant->save();
                    } else {
                        throw new \Exception('Failed to save barcode image');
                    }

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
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        $query = Item::with(['brand', 'category', 'sizes', 'colors'])
                    ->where('is_parent', true)
                    ->select([
                        'items.*',
                        DB::raw('CAST(items.selling_price AS DECIMAL(10,2)) as selling_price'),
                        DB::raw('CAST(items.discount_value AS DECIMAL(10,2)) as discount_value'),
                        DB::raw('CAST(items.quantity AS SIGNED) as quantity')
                    ]);

        // Apply filters
        if (!$showAll) {
            if ($search || $brandId) {
                $query->when($brandId, function ($query) use ($brandId) {
                    return $query->where('brand_id', $brandId);
                })
                ->when($search, function ($query) use ($search) {
                    return $query->where(function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhere('code', $search)
                          ->orWhereHas('variants', function($vq) use ($search) {
                              $vq->where('code', $search);
                          })
                          ->orWhereHas('brand', function($bq) use ($search) {
                              $bq->where('name', 'like', '%' . $search . '%');
                          });
                    });
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Apply sorting with proper number handling
        switch ($sortBy) {
            case 'price':
                $query->orderByRaw('CAST(selling_price AS DECIMAL(10,2)) ' . $sortOrder);
                break;
            case 'quantity':
                $query->orderByRaw('CAST(quantity AS SIGNED) ' . $sortOrder);
                break;
            case 'name':
            default:
                $query->orderBy('name', $sortOrder);
                break;
        }

        $items = $query->paginate(12)->withQueryString();

        // Format numeric values
        $items->transform(function ($item) {
            $item->selling_price = number_format($item->selling_price, 2, '.', '');
            $item->discount_value = number_format($item->discount_value, 2, '.', '');
            $item->quantity = (int)$item->quantity;
            return $item;
        });

        return view('items.index', compact('items', 'brands', 'showAll', 'sortBy', 'sortOrder'));
    }

    public function export($brandId = null)
    {
        $query = Item::with(['brand', 'category'])
                    ->where('is_parent', true);

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        $items = $query->get();

        $filename = $brandId ? Brand::find($brandId)->name . '_items.xlsx' : 'all_items.xlsx';

        return Excel::download(new ItemsExport($items), $filename);
    }

    public function edit($id)
    {
        $item = Item::with(['variants', 'sizes', 'colors'])->findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();
        $sizes = Size::all();
        $colors = Color::all();

        // Check if item is a variant with N/A size or color
        $hasNASize = $item->sizes()->where('name', 'N/A')->exists();
        $hasNAColor = $item->colors()->where('name', 'N/A')->exists();

        if (!$item->is_parent) {
            $parentItems = Item::where('is_parent', true)->get();
            return view('items.edit', compact('item', 'categories', 'brands', 'sizes', 'colors', 'parentItems', 'hasNASize', 'hasNAColor'));
        }

        return view('items.edit', compact('item', 'categories', 'brands', 'sizes', 'colors', 'hasNASize', 'hasNAColor'));
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

                // Update all variants with new shared properties and names
                foreach ($item->variants as $variant) {
                    $size = $variant->sizes->first();
                    $color = $variant->colors->first();

                    if ($size && $color) {
                        $variantName = $request->name . ' - ' . $size->name . ' - ' . $color->name;
                    } elseif ($size) {
                        $variantName = $request->name . ' - ' . $size->name;
                    } elseif ($color) {
                        $variantName = $request->name . ' - ' . $color->name;
                    } else {
                        $variantName = $request->name;
                    }

                    $variant->update([
                        'name' => $variantName,
                        'selling_price' => $request->selling_price,
                        'tax' => $request->tax,
                        'discount_type' => $request->discount_type,
                        'discount_value' => $request->discount_value,
                    ]);
                }
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

        // Update the item's stock
        $item->quantity -= $request->input('quantity');
        $item->save();

        // Update the parent item's stock if the item is a variant
        if ($item->parent_id) {
            $parentItem = Item::findOrFail($item->parent_id);
            $parentItem->quantity = $parentItem->variants()->sum('quantity');
            $parentItem->save();
        }

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
            $printerName = 'Xprinter_XP_T361U';

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

    public function exportCSV($brandId = null)
    {
        // Query builder for items
        $query = DB::table('items')
            ->join('brands', 'items.brand_id', '=', 'brands.id')
            ->select(
                'brands.name as brand_name',
                'items.name as item_name',
                'items.quantity as stock_quantity',
                'items.selling_price',
                'items.discount_type',
                'items.discount_value'
            )
            ->where('items.is_parent', false);

        if ($brandId) {
            $query->where('items.brand_id', $brandId);
        }

        $items = $query->get();

        // Calculate totals
        $totalStock = 0;
        $totalValue = 0;

        // Create CSV data with sections
        $csvData = "INVENTORY REPORT\n";
        $csvData .= "Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";

        // Add detailed items section
        $csvData .= "DETAILED INVENTORY LIST\n";
        $csvData .= "Brand,Item Name,Stock,Regular Price,Discount,Sale Price,Stock Value\n";

        foreach ($items as $item) {
            // Calculate values
            $salePrice = $item->discount_type === 'percentage'
                ? $item->selling_price * (1 - ($item->discount_value / 100))
                : $item->selling_price - $item->discount_value;

            $stockValue = $item->stock_quantity * $salePrice;

            // Update totals
            $totalStock += $item->stock_quantity;
            $totalValue += $stockValue;

            // Format discount for display (remove EGP from fixed discount)
            $discountDisplay = $item->discount_type === 'percentage'
                ? "{$item->discount_value}%"
                : $item->discount_value;

            $csvData .= sprintf(
                "%s,%s,%d,%.2f,%s,%.2f,%.2f\n",
                $item->brand_name,
                $item->item_name,
                $item->stock_quantity,
                $item->selling_price,
                $discountDisplay,
                $salePrice,
                $stockValue
            );
        }

        // Add summary section (removed EGP from values)
        $csvData .= "\nINVENTORY SUMMARY\n";
        $csvData .= "Total Items in Stock," . $totalStock . "\n";
        $csvData .= "Total Stock Value," . number_format($totalValue, 2) . "\n";

        // Add brand-specific statistics if filtering by brand (removed EGP)
        if ($brandId) {
            $brand = Brand::find($brandId);
            $csvData .= "\nBRAND STATISTICS: {$brand->name}\n";
            $csvData .= "Total SKUs," . $items->count() . "\n";
            $csvData .= "Average Item Price," . number_format($items->avg('selling_price'), 2) . "\n";
            $csvData .= "Highest Priced Item," . number_format($items->max('selling_price'), 2) . "\n";
            $csvData .= "Lowest Priced Item," . number_format($items->min('selling_price'), 2) . "\n";
        }

        // Generate filename
        $fileName = $brandId
            ? str_replace(' ', '_', strtolower(Brand::find($brandId)->name)) . '_inventory_' . now()->format('Y-m-d') . '.csv'
            : 'full_inventory_' . now()->format('Y-m-d') . '.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
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
            $processed = 0;
            $errors = [];

            // Get all parent items
            $parentItems = Item::where('is_parent', true)->get();

            if ($parentItems->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No parent items found.',
                    'processed' => 0
                ]);
            }

            // Ensure the barcodes directory exists
            $barcodesPath = storage_path('app/public/barcodes');
            if (!file_exists($barcodesPath)) {
                mkdir($barcodesPath, 0755, true);
            }

            $barcodeGenerator = new BarcodeGeneratorPNG();

            foreach ($parentItems as $parentItem) {
                try {
                    // Generate parent barcode if it doesn't exist
                    if (empty($parentItem->code)) {
                        $parentBarcode = Str::padLeft($parentItem->brand_id, 3, '0') .
                            Str::padLeft($parentItem->category_id, 3, '0') .
                            Str::padLeft($parentItem->id, 4, '0');

                        $parentItem->code = $parentBarcode;
                        $parentItem->save();
                    } else {
                        $parentBarcode = $parentItem->code;
                    }

                    // Generate barcodes for all variants of this parent
                    $variants = Item::where('parent_id', $parentItem->id)->get();

                    foreach ($variants as $variant) {
                        try {
                            // Get the first size and color for the variant
                            $size = $variant->sizes->first();
                            $color = $variant->colors->first();

                            if ($size && $color) {
                                $variantBarcode = $parentBarcode .
                                    Str::padLeft($color->id, 2, '0') .
                                    Str::padLeft($size->id, 2, '0');

                                $barcodePath = 'barcodes/' . $variantBarcode . '.png';
                                $storagePath = storage_path('app/public/' . $barcodePath);

                                $barcodeImage = $barcodeGenerator->getBarcode(
                                    $variantBarcode,
                                    $barcodeGenerator::TYPE_CODE_128,
                                    3,
                                    50
                                );

                                if (file_put_contents($storagePath, $barcodeImage)) {
                                    $variant->barcode = $barcodePath;
                                    $variant->code = $variantBarcode;
                                    $variant->save();
                                    $processed++;
                                    Log::info("Generated barcode for variant {$variant->id}: {$variantBarcode}");
                                } else {
                                    $errors[] = "Failed to save barcode image for variant {$variant->id}";
                                    Log::error("Failed to save barcode image for variant {$variant->id}");
                                }
                            } else {
                                $errors[] = "Size or color missing for variant {$variant->id}";
                                Log::error("Size or color missing for variant {$variant->id}");
                            }
                        } catch (\Exception $e) {
                            $errors[] = "Error processing variant {$variant->id}: " . $e->getMessage();
                            Log::error("Barcode generation failed for variant {$variant->id}: " . $e->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error processing parent item {$parentItem->id}: " . $e->getMessage();
                    Log::error("Barcode generation failed for parent item {$parentItem->id}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => $processed > 0 ? 'Barcodes generated successfully' : 'No barcodes generated',
                'processed' => $processed,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Barcode generation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate barcodes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportBrandSales(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        return Excel::download(new SalesPerBrandExport(), 'brand-sales-' . $date . '.xlsx');
    }

    public function exportInventoryCSV($brandId = null)
    {
        // Query builder for items
        $query = DB::table('items')
            ->join('brands', 'items.brand_id', '=', 'brands.id')
            ->select(
                'brands.name as brand_name',
                'items.name as item_name',
                'items.quantity as stock_quantity',
                'items.selling_price',
                'items.discount_type',
                'items.discount_value'
            )
            ->where('items.is_parent', false);

        if ($brandId) {
            $query->where('items.brand_id', $brandId);
        }

        $items = $query->get();

        // Create CSV data
        $csvData = "Brand,Item,Stock Quantity,Regular Price,Discount,Final Price\n";

        foreach ($items as $item) {
            $finalPrice = $item->discount_type === 'percentage'
                ? $item->selling_price * (1 - ($item->discount_value / 100))
                : $item->selling_price - $item->discount_value;

            $discountDisplay = $item->discount_type === 'percentage'
                ? "{$item->discount_value}%" : $item->discount_value;

            $csvData .= sprintf(
                "%s,%s,%d,%.2f,%s,%.2f\n",
                str_replace(',', ' ', $item->brand_name),
                str_replace(',', ' ', $item->item_name),
                $item->stock_quantity,
                $item->selling_price,
                $discountDisplay,
                $finalPrice
            );
        }

        $fileName = $brandId
            ? str_replace(' ', '_', strtolower(Brand::find($brandId)->name)) . '_inventory.csv'
            : 'full_inventory.csv';

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    public function exportSalesCSV(Request $request)
    {
        $startDate = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $brandId = $request->brand_id;

        $reportData = $this->generateSalesReport($startDate, $endDate, $brandId);

        $brandName = $brandId ? str_replace(' ', '_', strtolower(Brand::find($brandId)->name)) . '_' : '';
        $fileName = $brandName . 'sales-report_' . $startDate . '_to_' . $endDate . '.xlsx';

        return Excel::download(
            new SalesExport($startDate, $endDate, $brandId),
            $fileName
        );
    }

    private function generateSalesReport($startDate, $endDate, $brandId = null)
    {
        $sales = Sale::query()
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->when($brandId, function ($query) use ($brandId) {
                return $query->whereHas('item', function ($q) use ($brandId) {
                    $q->where('brand_id', $brandId);
                });
            })
            ->with(['item' => function ($query) {
                $query->select('sale_items.sale_id', 'items.name', 'sale_items.quantity', 'sale_items.price')
                    ->join('sale_items', 'items.id', '=', 'sale_items.item_id');
            }])
            ->get();

        return ['sales' => $sales];
    }

    public function toggleDiscount(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $item = Item::findOrFail($id);
            $applyDiscount = $request->input('apply_discount');
            $discountValue = $request->input('discount_value', $item->discount_value ?: 0);

            // Update item
            $item->discount_value = $applyDiscount ? $discountValue : 0;
            $item->save();

            // Update all variants if this is a parent item
            if ($item->is_parent) {
                Item::where('parent_id', $item->id)->update([
                    'discount_value' => $item->discount_value
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $applyDiscount ?
                    "Discount of {$discountValue}% applied successfully" :
                    'Discount removed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Discount update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update discount: ' . $e->getMessage()
            ], 500);
        }
    }
}
