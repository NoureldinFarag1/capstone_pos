<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // OPTIMIZED: Eager load categories and items count to avoid N+1 queries in the view
        $brands = Brand::with('categories')
            ->withCount('items')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->get();

        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $picturePath = $request->file('picture')->store('brands', 'public');
        Brand::create([
            'name' => $request->name,
            'picture' => $picturePath,
        ]);
        return redirect()->route('brands.index');
    }

        public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $brand = Brand::findOrFail($id);

        // If a new picture is uploaded, store it
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('brands', 'public');
            $brand->picture = $picturePath;
        }

        $brand->name = $request->input('name');
        $brand->save();

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }
    public function brandCount()
    {
        $count = Brand::count();
        return response()->json(['count' => $count]);
    }

    public function toggleDiscount(Request $request, $id)
    {
        try {
            // Validate request
            if ($request->input('apply_discount') && !$request->filled('discount_value')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please enter a discount value'
                ], 400);
            }

            DB::beginTransaction();

            $brand = Brand::findOrFail($id);
            $applyDiscount = $request->input('apply_discount');
            $discountType = $request->input('discount_type', 'percentage');

            // Get existing discount value from first item of this brand
            $existingDiscount = Item::where('brand_id', $brand->id)
                ->where('discount_value', '>', 0)
                ->value('discount_value') ?? 0;

            $discountValue = $applyDiscount ?
                ($request->input('discount_value') ?: $existingDiscount) : 0;

            // Validate discount value based on type
            if ($applyDiscount) {
                if ($discountType === 'percentage' && ($discountValue <= 0 || $discountValue > 100)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Percentage discount must be between 1 and 100'
                    ], 400);
                }
                if ($discountType === 'fixed' && $discountValue <= 0) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Fixed discount must be greater than 0'
                    ], 400);
                }
            }

            // Update all items belonging to this brand
            Item::where('brand_id', $brand->id)->update([
                'discount_type' => $discountType,
                'discount_value' => $discountValue
            ]);

            // Update brand's discount status
            $brand->has_discount = $applyDiscount;
            $brand->save();

            DB::commit();

            $discountDisplay = $discountType === 'percentage' ?
                $discountValue . '%' :
                'EGP ' . number_format($discountValue, 2);

            return response()->json([
                'success' => true,
                'message' => $applyDiscount ?
                    "Discount of {$discountDisplay} applied to all items in {$brand->name}" :
                    "Discount removed from all items in {$brand->name}"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to toggle brand discount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and print PDF labels for all items in a brand with quantity > 0
     * Uses the batch printing method from ItemController for a single print job
     */
    public function printLabels(Request $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            // Get all variants for this brand with quantity > 0
            $items = Item::where('brand_id', $brand->id)
                ->where('quantity', '>', 0)
                ->with(['sizes', 'colors', 'category', 'brand'])
                ->get();

            if ($items->isEmpty()) {
                return redirect()->back()->with('warning', 'No items with quantity found for this brand.');
            }

            // Initialize data collection for batch printing
            $itemsData = [];
            $totalItems = 0;

            foreach ($items as $item) {
                // For parent items, include all variants
                if ($item->is_parent) {
                    $variants = Item::where('parent_id', $item->id)
                        ->where('quantity', '>', 0)
                        ->with(['sizes', 'colors', 'brand'])
                        ->get();

                    foreach ($variants as $variant) {
                        // Ensure variant has its own barcode, not parent's
                        if (!$variant->barcode || $variant->barcode === $item->barcode) {
                            $this->regenerateBarcode($variant);
                            $variant->refresh();
                        }

                        // Add to batch print with quantity
                        if ($variant->quantity > 0) {
                            $itemsData[$variant->id] = $variant->quantity;
                            $totalItems++;
                        }
                    }
                }
                // For regular items (not parents and not variants)
                else if (!$item->parent_id) {
                    // Ensure item has a barcode
                    if (!$item->barcode || !file_exists(public_path('storage/' . $item->barcode))) {
                        $this->regenerateBarcode($item);
                        $item->refresh();
                    }

                    // Add to batch print with quantity
                    if ($item->quantity > 0) {
                        $itemsData[$item->id] = $item->quantity;
                        $totalItems++;
                    }
                }
            }

            if (empty($itemsData)) {
                return redirect()->back()->with('warning', 'No printable items found for this brand.');
            }

            // Use the ItemController's static batchPrintLabels method
            $result = \App\Http\Controllers\ItemController::batchPrintLabels($itemsData);

            if ($result['success']) {
                $message = "{$result['quantity']} labels for {$totalItems} items sent to printer successfully using {$result['method']} printing.";
                if ($result['method'] === 'individual') {
                    $message .= " (PDF merging was not available on your system)";
                }
                return redirect()->back()->with('success', $message);
            } else {
                $errorMsg = isset($result['error']) ? ': ' . $result['error'] : '.';
                return redirect()->back()->with('error', 'Failed to print labels' . $errorMsg);
            }

        } catch (\Exception $e) {
            Log::error('Brand label printing error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate labels: ' . $e->getMessage());
        }
    }

    private function generateItemLabelPDF($item)
    {
        // First check if the barcode field exists at all
        if (!$item->barcode) {
            $this->regenerateBarcode($item);
            // Refresh the item to get the updated barcode path
            $item->refresh();
        }

        // Then check if the file exists on disk
        $barcodePath = public_path('storage/' . $item->barcode);
        if (!file_exists($barcodePath)) {
            $this->regenerateBarcode($item);
            // Refresh the item to get the updated barcode path
            $item->refresh();
        }

        // Convert the barcode to base64 data URL for PDF generation
        $barcodeFilePath = public_path('storage/' . $item->barcode);
        $barcodeData = 'data:image/png;base64,' . base64_encode(file_get_contents($barcodeFilePath));

        return [
            'item' => $item,
            'barcodePath' => $barcodeData,
        ];
    }

    private function generateCombinedLabelsPDF($labelData, $filename)
    {
        // Create PDF with multiple labels using the existing template
        $pdf = PDF::loadView('pdf.labels-sheet', [
            'labels' => $labelData,
        ]);

        // Set paper size for multiple labels (A4)
        $pdf->setPaper('A4', 'portrait');

        // Configure DomPDF
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->getDomPDF()->set_option('enable_javascript', false);
        $pdf->getDomPDF()->set_option('enable_remote', false);

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Print labels directly to the printer instead of downloading them
     * This prints each label separately with the correct label size
     */
    private function printLabelsDirectly($labelData, $filename)
    {
        try {
            // Create temp directory if it doesn't exist
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Get printer name from config
            $printerName = config('printer.name');
            if (!$printerName) {
                // Try to get from printer_name.txt
                if (file_exists(base_path('printer_name.txt'))) {
                    $printerName = trim(file_get_contents(base_path('printer_name.txt')));
                } else {
                    $printerName = 'Microsoft Print to PDF';  // Default fallback
                }
            }

            // Counter for success
            $successCount = 0;
            $totalLabels = count($labelData);

            // Print each label individually with correct size
            foreach ($labelData as $index => $data) {
                // Create PDF with the single label template
                $pdf = PDF::loadView('pdf.label', [
                    'item' => $data['item'],
                    'barcodePath' => $data['barcodePath'],
                ]);

                // Set exact label dimensions (36.5mm x 25mm)
                $width = 36.5 * 2.83465;  // 103.46 points
                $height = 25 * 2.83465;   // 70.87 points
                $pdf->setPaper([0, 0, $width, $height], 'landscape');

                // Configure DomPDF
                $pdf->getDomPDF()->set_option('enable_php', true);
                $pdf->getDomPDF()->set_option('enable_javascript', false);
                $pdf->getDomPDF()->set_option('enable_remote', true);
                $pdf->getDomPDF()->set_option('font_height_ratio', 1.0);
                $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

                // Generate a unique temporary file name
                $tempPath = $tempDir . '/label_' . $data['item']->id . '_' . time() . '.pdf';

                // Save PDF temporarily
                $pdf->save($tempPath);

                // Print based on OS - use item quantity as the number of copies
                $quantity = $data['item']->quantity;
                if ($this->isWindows()) {
                    $this->printLabelWindows($tempPath, $printerName, $quantity);
                } else {
                    $this->printLabelMac($tempPath, $printerName, $quantity);
                }

                // Increment success counter by the quantity
                $successCount += $quantity;

                // Delete temp file
                @unlink($tempPath);
            }

            return redirect()->back()->with('success', "$successCount of $totalLabels labels sent to printer successfully.");
        } catch (\Exception $e) {
            Log::error('Label printing error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to print labels: ' . $e->getMessage());
        }
    }

    /**
     * Check if the current OS is Windows
     */
    private function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Windows-specific printing implementation
     */
    private function printLabelWindows($tempPath, $printerName, $quantity)
    {
        // Path to SumatraPDF
        $sumatraPath = '"C:\Program Files\SumatraPDF\SumatraPDF.exe"';

        // Define custom paper size in mm
        $printSettings = "-print-settings \"$quantity"
            . ",paper=Custom.36.5x25mm"
            . ",fit=NoScaling"
            . ",offset-x=0,offset-y=0\"";

        // Build and execute the print command
        $command = "$sumatraPath $printSettings -print-to \"$printerName\" \"$tempPath\"";

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Windows print error: ' . $process->getErrorOutput());
            throw new \Exception('Printing failed on Windows: ' . $process->getErrorOutput());
        }

        return true;
    }

    /**
     * Mac/Linux-specific printing implementation
     */
    private function printLabelMac($tempPath, $printerName, $quantity)
    {
        // Mac/Linux printing using lp command with proper options for small labels
        $command = "lp -d \"$printerName\" -n $quantity -o fit-to-page -o media=Custom.36.5x25mm \"$tempPath\"";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('Mac print error: ' . implode("\n", $output));
            throw new \Exception('Printing failed on Mac: ' . implode("\n", $output));
        }

        return true;
    }

    private function regenerateBarcode($item)
    {
        // Use the same barcode generation logic as ItemController
        $barcodeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodePath = 'barcodes/' . $item->code . '.png';
        $storagePath = storage_path('app/public/' . $barcodePath);

        // Ensure directory exists
        if (!file_exists(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0755, true);
        }

        $barcodeImage = $barcodeGenerator->getBarcode(
            $item->code,
            $barcodeGenerator::TYPE_CODE_128,
            3,
            50
        );

        if (file_put_contents($storagePath, $barcodeImage)) {
            $item->barcode = $barcodePath;
            $item->save();
        }
    }
}
