<?php

namespace App\Http\Controllers;

use App\Exports\BestSellingReportExport;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Exports\SalesPerBrandExport;
use Maatwebsite\Excel\Facades\Excel;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use App\Exports\DailySalesExport;
use App\Exports\HourlySalesReportExport;
use App\Exports\PaymentMethodReportExport;
use App\Exports\RefundsReportExport;

class SaleController extends Controller
{
    public function create()
    {
        $items = Item::all();
        return view('sales.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.special_discount' => 'nullable|numeric|min:0|max:100', // Add this line
            'items.*.as_gift' => 'required|boolean', // Updated this rule
            'discount_type' => 'nullable|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'shipping_fees' => 'nullable|numeric|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            // Remove validation for subtotal and total as we'll calculate these
        ]);

        $userRole = $request->user()->role; // Assuming role is available in the user object

        // Log the user role and discount details for debugging
        Log::info('User Role: ' . $userRole);
        Log::info('Discount Type: ' . $request->discount_type);
        Log::info('Discount Value: ' . $request->discount_value);

        // Validate discount value based on user role
        if ($userRole === 'cashier') {
            if ($request->discount_type === 'percentage' && $request->discount_value > 20) {
                Log::warning('Cashier attempted to apply a percentage discount greater than 20%.');
                return redirect()->back()->with('error', 'As a cashier, percentage discount cannot exceed 20%.')->withInput();
            }

            if ($request->discount_type === 'fixed' && $request->discount_value > 100) {
                Log::warning('Cashier attempted to apply a fixed discount greater than 100 EGP.');
                return redirect()->back()->with('error', 'As a cashier, fixed amount discount cannot exceed 100 EGP.')->withInput();
            }
        }

        // Ensure the discount values are set correctly in the validated data
        $validated['discount_type'] = $request->discount_type;
        $validated['discount_value'] = $request->discount_value;

        // Validate stock availability for all items before proceeding
        foreach ($request->items as $itemData) {
            $item = Item::find($itemData['item_id']);

            if (!$item) {
                return redirect()->back()
                    ->with('error', 'Item not found.')
                    ->withInput();
            }

            if ($item->quantity <= 0) {
                return redirect()->back()
                    ->with('error', "'{$item->name}' is out of stock.")
                    ->withInput();
            }

            if ($item->quantity < $itemData['quantity']) {
                return redirect()->back()
                    ->with('error', "Insufficient stock for '{$item->name}'. Available: {$item->quantity}")
                    ->withInput();
            }
        }

        // Get today's date
        $today = now()->toDateString();

        // Get the last display_id for today
        $lastSale = Sale::where('sale_date', $today)->orderBy('display_id', 'desc')->first();
        $displayId = $lastSale ? $lastSale->display_id + 1 : 1;

        // Create sale record
        $sale = Sale::create([
            'user_id' => \Illuminate\Support\Facades\Auth::user()->id,
            'total_amount' => 0,  // Set initial value
            'subtotal' => 0,      // Set initial value
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'shipping_fees' => $request->shipping_fees ?? 0,
            'address' => $request->address,
            'notes' => $request->notes,
            'display_id' => $displayId,
            'sale_date' => $today,
        ]);

        // Track the computed subtotal from sale items
        $computedSubtotal = 0;

        // Add sale items and update inventory
        foreach ($request->items as $itemData) {
            $item = Item::find($itemData['item_id']);

            // Set price to 0 if item is marked as gift
            $basePrice = $itemData['as_gift'] ? 0 : $itemData['price'];

            // Calculate special discount
            $specialDiscount = isset($itemData['special_discount']) ? floatval($itemData['special_discount']) : 0;
            $specialDiscountAmount = $basePrice * ($specialDiscount / 100);
            $finalPrice = $basePrice - $specialDiscountAmount;

            // Calculate item subtotal after special discount
            $itemSubtotal = $finalPrice * $itemData['quantity'];
            $computedSubtotal += $itemSubtotal;

            // Create sale item with the price, special discount and gift status
            $sale->saleItems()->create([
                'item_id' => $item->id,
                'quantity' => $itemData['quantity'],
                'price' => $basePrice,
                'special_discount' => $specialDiscount,
                'subtotal' => $itemSubtotal,
                'as_gift' => $itemData['as_gift'],
            ]);

            // Update inventory
            $item->decrement('quantity', $itemData['quantity']);

            // If item is a variant, update the parent item's stock.
            if ($item->parent_id) {
                $parentItem = Item::findOrFail($item->parent_id);
                $parentItem->quantity = $parentItem->variants()->sum('quantity');
                $parentItem->save();
            }
        }

        // Log the computed subtotal for debugging
        Log::info('Computed Subtotal: ' . $computedSubtotal);

        // Calculate actual discount amount
        $discountAmount = 0;
        if ($request->discount_type !== 'none' && $request->discount_value > 0) {
            if ($request->discount_type === 'percentage') {
                $discountAmount = $computedSubtotal * ($request->discount_value / 100);
            } else if ($request->discount_type === 'fixed') {
                $discountAmount = min($request->discount_value, $computedSubtotal);
            }
        }

        // Log the discount calculation for debugging
        Log::info('Discount Calculation: ', [
            'type' => $request->discount_type,
            'value' => $request->discount_value,
            'amount' => $discountAmount
        ]);

        // Calculate the total amount including shipping fees and discount
        $shippingFees = $request->shipping_fees ?? 0;
        $totalAmount = $computedSubtotal - $discountAmount + $shippingFees;

        // Ensure total is not negative
        $totalAmount = max($totalAmount, 0);

        // Log the final calculated amounts
        Log::info('Final Calculations: ', [
            'subtotal' => $computedSubtotal,
            'discount' => $discountAmount,
            'shipping' => $shippingFees,
            'total' => $totalAmount
        ]);

        // Update the sale with the correct calculated values
        $sale->update([
            'subtotal' => $computedSubtotal,
            'total_amount' => $totalAmount,
            'discount' => $discountAmount
        ]);

        // Log the final sale record for debugging
        Log::info('Final Sale Record: ', $sale->toArray());

        // Print the thermal receipt and open cash drawer
        $this->printThermalReceipt($sale->id);

        return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
    }
    private function getPrinterConnector()
    {
        $configPath = base_path('printer_config.json');
        $config = json_decode(File::get($configPath), true);

        if (PHP_OS_FAMILY === 'Windows') {
            $printerName = $config['windows'];
            return new WindowsPrintConnector($printerName);
        } else {
            $printerName = $config['mac'];
            return new CupsPrintConnector($printerName);
        }
    }

    public function printGiftReceipt(Request $request)
    {
        try {
            // Add detailed logging
            Log::info('Full request data:', $request->all());
            Log::info('Sale items data:', ['items' => $request->input('saleItems')]);

            // Load gift receipt template from config
            $templatePath = Config::get('receipt.gift_receipt_template');
            if (!File::exists($templatePath)) {
                Log::error("Gift receipt template file not found: $templatePath");
                return response()->json(['success' => false, 'message' => 'Gift receipt template not found.'], 500);
            }
            $template = File::get($templatePath);

            // Load logo path from config
            $logoPath = Config::get('receipt.logo_path');

            // Load store information from config
            $storeName = Config::get('receipt.store_name');
            $storeInstagram = Config::get('receipt.store_instagram');

            // Debug the incoming data
            Log::info('Received sale items:', ['items' => $request->input('saleItems')]);

            // Initialize printer early
            $connector = $this->getPrinterConnector();
            $printer = new Printer($connector);
            $printer->initialize();

            // Print logo
            if (file_exists($logoPath)) {
                try {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $logo = EscposImage::load($logoPath);
                    $printer->bitImage($logo);
                    $printer->feed(1);
                } catch (\Exception $e) {
                    Log::error("Logo error: " . $e->getMessage());
                }
            }

            // Create a temporary receipt structure with explicit formatting
            $formattedItems = collect($request->input('items', []))->map(function ($item) {
                Log::info('Processing item:', ['item' => $item]);

                // Get item details from database including brand
                if (!empty($item['item_id'])) {
                    $dbItem = Item::with('brand')->find($item['item_id']);
                    $name = $dbItem ? $dbItem->name : ($item['name'] ?? 'Unknown Item');
                    $brandName = $dbItem && $dbItem->brand ? $dbItem->brand->name : '';
                    // Combine item name and brand
                    $fullName = $name . ($brandName ? " - $brandName" : '');
                } else {
                    $fullName = $item['name'] ?? 'Unknown Item';
                }

                $qty = $item['quantity'] ?? 0;

                // Format with fixed widths - allowing more space for the combined name
                $formattedName = str_pad(substr($fullName, 0, 30), 30);
                $formattedQty = str_pad((string) $qty, 8, ' ', STR_PAD_LEFT);

                $line = $formattedName . $formattedQty;
                Log::info('Formatted receipt line:', ['line' => $line]);

                return $line;
            })->join("\n");

            // Prepare data for template
            $data = [
                'store_name' => $storeName,
                'store_instagram' => $storeInstagram,
                'date' => now()->format('d M Y'),
                'time' => now()->format('H:i:s'),
                'items' => $formattedItems ?: 'No items' // Provide fallback for empty items
            ];

            // Replace placeholders in the template
            foreach ($data as $key => $value) {
                $template = str_replace("%$key%", $value, $template);
            }

            // Print the template with explicit justification for items
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $lines = explode("\n", $template);
            foreach ($lines as $line) {
                // Center align everything except items section
                if (strpos($line, 'Item') !== false || strpos($line, $formattedItems) !== false) {
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                } else {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                }
                $printer->text($line . "\n");
            }

            // Cut receipt
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Gift receipt error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function printThermalReceipt($id)
    {
        try {
            Log::info('Starting thermal receipt print for sale #' . $id);

            $sale = Sale::with('saleItems.item')->findOrFail($id);

            // Load thermal receipt template from config
            $templatePath = Config::get('receipt.thermal_receipt_template');
            if (!File::exists($templatePath)) {
                Log::error("Thermal receipt template file not found: $templatePath");
                return redirect()->route('sales.index')->with('error', 'Thermal receipt template not found.');
            }
            $template = File::get($templatePath);

            // Load logo path from config
            $logoPath = Config::get('receipt.logo_path');

            // Load store information from config
            $storeName = Config::get('receipt.store_name');
            $storeSlogan = Config::get('receipt.store_slogan');
            $storeInstagram = Config::get('receipt.store_instagram');

            Log::info('Connecting to printer');
            $connector = $this->getPrinterConnector();
            $printer = new Printer($connector);

            // Initialize printer
            $printer->initialize();

            // Print store name (larger and centered)
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2); // Double width and height
            $printer->text($storeName . "\n");
            $printer->setTextSize(1, 1); // Reset text size

            // Print logo
            if (file_exists($logoPath)) {
                try {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $logo = EscposImage::load($logoPath);
                    $printer->bitImage($logo); // Print the logo
                    $printer->feed(1);
                } catch (\Exception $e) {
                    Log::error("Error loading logo: " . $e->getMessage());
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text("Logo could not be printed\n");
                }
            }

            // Print slogan (centered below the logo)
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($storeSlogan . "\n");
            $printer->feed(1);

            $printer->feed(1);

            // Prepare data for template
            $items = $sale->saleItems->map(function ($saleItem) {
                $itemName = $saleItem->item->brand->name . " - " . $saleItem->item->name;
                $quantity = $saleItem->quantity;
                $basePrice = $saleItem->item->selling_price;

                // Calculate special discount first
                $specialDiscountAmount = 0;
                if ($saleItem->special_discount > 0) {
                    $specialDiscountAmount = ($saleItem->special_discount / 100) * $basePrice;
                }

                // Then calculate regular item discount
                $regularDiscount = 0;
                $item = $saleItem->item;
                if ($item->discount_type === 'percentage') {
                    $regularDiscount = ($item->discount_value / 100) * ($basePrice - $specialDiscountAmount);
                } else if ($item->discount_type === 'fixed') {
                    $regularDiscount = min($item->discount_value, $basePrice - $specialDiscountAmount);
                }

                $finalPrice = $basePrice - $specialDiscountAmount - $regularDiscount;
                $lineTotal = $quantity * $finalPrice;

                // Format the output with both special and regular discounts
                if (strlen($itemName) > 20) {
                    $itemNamePart1 = substr($itemName, 0, 20);
                    $itemNamePart2 = substr($itemName, 20);
                    $output = str_pad($itemNamePart1, 20) . "\n" . str_pad($itemNamePart2, 20) .
                        str_pad($quantity, 5, ' ', STR_PAD_LEFT) .
                        str_pad(number_format($basePrice, 2), 12, ' ', STR_PAD_LEFT) .
                        str_pad(number_format($lineTotal, 2), 11, ' ', STR_PAD_LEFT) . "\n";
                } else {
                    $output = str_pad($itemName, 20) .
                        str_pad($quantity, 5, ' ', STR_PAD_LEFT) .
                        str_pad(number_format($basePrice, 2), 12, ' ', STR_PAD_LEFT) .
                        str_pad(number_format($lineTotal, 2), 11, ' ', STR_PAD_LEFT) . "\n";
                }

                if ($saleItem->special_discount > 0) {
                    $output .= str_pad("S Discount ({$saleItem->special_discount}%): -" .
                        number_format($specialDiscountAmount * $quantity, 2), 48, ' ', STR_PAD_LEFT) . "\n";
                }

                if ($regularDiscount > 0) {
                    $output .= str_pad("Discount: -" .
                        number_format($regularDiscount * $quantity, 2), 48, ' ', STR_PAD_LEFT);
                }

                return $output;
            })->implode("\n");

            // Calculate total discounts with correct discount type handling
            $totalDiscount = $sale->saleItems->sum(function ($saleItem) {
                $item = $saleItem->item;
                $price = $item->selling_price;
                if ($item->discount_type === 'percentage') {
                    return ($item->discount_value / 100) * $price * $saleItem->quantity;
                } else if ($item->discount_type === 'fixed') {
                    return min($item->discount_value, $price) * $saleItem->quantity;
                }
                return 0;
            });

            $subtotalBeforeDiscount = $sale->saleItems->sum(function ($item) {
                return $item->quantity * $item->item->selling_price;
            });

            $subtotal = $sale->saleItems->sum(function ($item) {
                $price = $item->selling_price;
                $discount = 0;
                if ($item->item->discount_type === 'percentage') {
                    // Ensure percentage discount is between 0 and 100
                    $discountPercentage = max(0, min(100, $item->item->discount_value));
                    $discount = ($discountPercentage / 100) * $price;
                } else if ($item->item->discount_type === 'fixed') {
                    // Ensure fixed discount doesn't exceed item price
                    $discount = max(0, min($price, $item->item->discount_value));
                }
                return $item->quantity * ($price - $discount);
            });

            // Calculate additional discount from the sale level
            $additionalDiscount = 0;
            if ($sale->discount_type === 'percentage') {
                // Ensure percentage discount is between 0 and 100
                $discountPercentage = max(0, min(100, $sale->discount_value));
                $additionalDiscount = $subtotal * ($discountPercentage / 100);
            } elseif ($sale->discount_type === 'fixed') {
                // Ensure fixed discount doesn't exceed subtotal
                $additionalDiscount = max(0, min($subtotal, $sale->discount_value));
            }

            // Log discount values for debugging
            Log::info('Discount values:', [
                'total_discount' => $totalDiscount,
                'additional_discount' => $additionalDiscount,
                'stored_discount' => $sale->discount,
                'discount_type' => $sale->discount_type,
                'discount_value' => $sale->discount_value
            ]);

            // Prepare base data array
            $data = [
                'store_name' => $storeName,
                'store_slogan' => $storeSlogan,
                'store_instagram' => $storeInstagram,
                'sale_id' => $sale->id,
                'sale_date' => $sale->sale_date->format('d/m'),
                'display_id' => str_pad($sale->display_id, 4, '0', STR_PAD_LEFT),
                'created_at' => $sale->created_at->format('d M Y'),
                'created_at_time' => $sale->created_at->format('H:i:s'),
                'payment_method' => $sale->payment_method,
                'items' => $items,
                'separator' => '――――――――――――――――――――――――――――――――――――――――――――――――',
                'subtotal_before_discount' => number_format($subtotalBeforeDiscount, 2),
                'subtotal' => number_format($subtotal, 2),
                'total_discount' => number_format($totalDiscount, 2),
                'additional_discount' => number_format($sale->discount ?? 0, 2),
                'sale_discount_type' => $sale->discount_type,
                'sale_discount_value' => $sale->discount_value,
                'total_amount' => number_format($sale->total_amount, 2),
                'shipping_fees' => $sale->shipping_fees ? number_format($sale->shipping_fees, 2) : '0.00',
            ];

            // Add customer details if shipping exists
            if ($sale->shipping_fees > 0) {
                $data['customer_name'] = $sale->customer_name ?? 'N/A';
                $data['customer_phone'] = $sale->customer_phone ?? 'N/A';
                $data['customer_address'] = $sale->address ?? 'N/A';
                // Remove the conditional sections markers
                $template = str_replace(['%if_shipping_start%', '%if_shipping_end%'], '', $template);
            } else {
                // Remove the entire shipping and customer details section
                $template = preg_replace('/%if_shipping_start%.*?%if_shipping_end%\n?/s', '', $template);
                // Make sure these fields are empty to avoid undefined placeholder errors
                $data['customer_name'] = '';
                $data['customer_phone'] = '';
                $data['customer_address'] = '';
            }

            // Add discount details if applicable
            if ($sale->discount_type !== 'none' && $sale->discount_value > 0) {
                $discountLine = $sale->discount_type === 'percentage'
                    ? "Additional Discount: EGP " . number_format($sale->discount, 2) . " ({$sale->discount_value}%)"
                    : "Additional Discount: EGP " . number_format($sale->discount, 2) . " (Fixed)";
                $data['discount_line'] = $discountLine;
                $template = str_replace(['%if_discount_start%', '%if_discount_end%'], '', $template);
            } else {
                $template = preg_replace('/%if_discount_start%.*?%if_discount_end%\n?/s', '', $template);
            }

            // Format gift items separately
            $giftItems = $sale->saleItems->where('as_gift', true)->map(function ($saleItem) {
                $itemName = $saleItem->item->brand->name . " - " . $saleItem->item->name;
                $quantity = $saleItem->quantity;

                // Split item name into two lines if it's too long
                if (strlen($itemName) > 20) {
                    $itemNamePart1 = substr($itemName, 0, 20);
                    $itemNamePart2 = substr($itemName, 20);
                    return str_pad($itemNamePart1, 20) . "\n" . str_pad($itemNamePart2, 20) .
                        str_pad("Qty: " . $quantity, 8, ' ', STR_PAD_LEFT);
                } else {
                    return str_pad($itemName, 20) .
                        str_pad("Qty: " . $quantity, 8, ' ', STR_PAD_LEFT);
                }
            })->implode("\n");

            // Add gift items data to template data
            $data['gift_items'] = $giftItems;

            // Handle gift items section visibility
            if ($sale->saleItems->where('as_gift', true)->count() > 0) {
                // Remove the conditional sections markers for gifts
                $template = str_replace(['%if_gifts_start%', '%if_gifts_end%'], '', $template);
            } else {
                // Remove the entire gifts section if there are no gift items
                $template = preg_replace('/%if_gifts_start%.*?%if_gifts_end%\n?/s', '', $template);
            }

            // Replace placeholders in the template
            foreach ($data as $key => $value) {
                $template = str_replace("%$key%", $value, $template);
            }

            // Print the template
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $lines = explode("\n", $template);
            foreach ($lines as $line) {
                $printer->text($line . "\n");
            }

            // Open cash drawer
            $printer->getPrintConnector()->write("\x1B\x70\x00\x19\xFA");

            // Cut receipt
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            Log::info('Receipt printed successfully');
            return redirect()->route('sales.index')->with('success', 'Receipt printed successfully');
        } catch (\Exception $e) {
            Log::error('Receipt printing error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('sales.index')
                ->with('error', 'Printer error: ' . $e->getMessage());
        }
    }


    public function index(Request $request)
    {
        $sort = $request->get('sort', 'desc');
        $search = $request->query('search');

        $sales = Sale::when($search, function ($query, $search) {
            if (is_numeric($search)) {
                $query->where('id', $search)
                    ->orWhere('display_id', $search);
            } else {
                if (preg_match('/(\d{2})\/(\d{2})\s*-\s*#(\d+)/', $search, $matches)) {
                    $day = $matches[1];
                    $month = $matches[2];
                    $displayId = ltrim($matches[3], '0');

                    $query->whereDay('sale_date', $day)
                        ->whereMonth('sale_date', $month)
                        ->where('display_id', $displayId);
                }
            }
        })
            ->orderBy('sale_date', 'desc')
            ->orderBy('display_id', 'desc')
            ->paginate(15);

        return view('sales.index', compact('sales', 'sort', 'search'));
    }

    public function deleteAllSales()
    {
        Sale::truncate(); // Delete all sales
        return redirect()->route('sales.index')->with('success', 'All sales deleted successfully.');
    }

    public function show($id)
    {
        $sale = Sale::with('saleItems.item')->findOrFail($id); // Load the sale and its items
        return view('sales.show', compact('sale')); // Pass the sale to the view
    }

    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }

    public function showInvoice($id)
    {
        $sale = Sale::with('saleItems.item')->findOrFail($id);

        // Define shop information
        $shopName = 'Local HUB';
        $shopAddress = 'Waslet-Dahshur Zayed';
        $shopPhone = '.';
        $shopEmail = '@localhub_egy';
        $paymentTerms = 'refunds are acceptable for 14 days';

        return view('invoices.show', compact('sale', 'shopName', 'shopAddress', 'shopPhone', 'shopEmail', 'paymentTerms'));
    }


    public function searchByBarcode(Request $request)
    {
        // Get barcode from the request
        $barcode = $request->input('barcode');

        // Check if the barcode was provided
        if (!$barcode) {
            return response()->json(['error' => 'Barcode is required'], 400);
        }

        // Remove the file extension from the barcode for the query
        $barcodeWithExtension = $barcode . '.png';

        // Find the item by its barcode
        $item = Item::where('barcode', $barcodeWithExtension)->first();

        // If item is not found, return an error
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // If found, return the item data to be added to the sale
        return response()->json(['item' => $item], 200);
    }


    public function exportSalesPerBrand()
    {
        return Excel::download(new SalesPerBrandExport, 'sales_per_brand.xlsx');
    }


    public function printInvoice($id)
    {
        $sale = Sale::with('saleItems.item')->findOrFail($id);
        $pdf = PDF::loadView('invoices.invoice', compact('sale'));
        return $pdf->download('invoice_' . $sale->id . '.pdf');
    }

    public function deleteAllItems(Sale $sale)
    {
        $sale->saleItems()->delete(); // Delete all sale items associated with the sale
        return response()->json(['success' => true]);
    }

    public function loyalCustomers()
    {
        $customers = Sale::select(
            'customer_name',
            'customer_phone',
            DB::raw('COUNT(*) as visit_count'),
            DB::raw('SUM(total_amount) as total_spent'),
            DB::raw('MAX(created_at) as last_visit')
        )
            ->whereNotNull('customer_phone')
            ->groupBy('customer_name', 'customer_phone')
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('visit_count')
            ->paginate(15);

        return view('sales.loyal-customers', compact('customers'));
    }

    public function paymentMethodSales(Request $request, $period, $method)
    {
        $query = Sale::query();

        if ($period === 'daily') {
            $query->whereDate('created_at', Carbon::today());
            $periodLabel = "Today's";
        } else {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
            $periodLabel = "This Month's";
        }

        $sales = $query->where('payment_method', $method)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $methodLabel = str_replace('_', ' ', ucfirst($method));

        return view('sales.payment-method', compact('sales', 'periodLabel', 'methodLabel'));
    }

    public function generateDailyReport(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $format = $request->get('format', 'excel');

        // Get sales with necessary relationships
        $sales = Sale::whereDate('sale_date', $date)
            ->with(['user', 'refunds', 'saleItems.item'])
            ->get();

        // Prepare report data
        $reportData = [
            'date' => $date,
            'totalSales' => $sales->sum('total_amount'),
            'numberOfSales' => $sales->count(),
            'sales' => $sales
        ];

        $fileName = 'daily_sales_' . $date;

        // Handle different export formats
        return match ($format) {
            'excel' => Excel::download(
                new DailySalesExport($reportData, $date),
                $fileName . '.xlsx'
            ),
            'csv' => Excel::download(
                new DailySalesExport($reportData, $date),
                $fileName . '.csv'
            ),
            default => response('Invalid format specified.', 400),
        };
    }

    public function generatePaymentMethodReport(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $format = $request->query('format', 'excel');

        $sales = Sale::where('sale_date', $date)
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->groupBy('payment_method')
            ->get();

        $reportData = [
            'date' => $date,
            'sales' => $sales
        ];

        $fileName = 'payment_method_report_' . $date;
        return $this->downloadReport($reportData, $fileName, $format, PaymentMethodReportExport::class);
    }

    public function generateHourlySalesReport(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $format = $request->query('format', 'excel');

        $sales = Sale::where('sale_date', $date)
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $reportData = [
            'date' => $date,
            'sales' => $sales
        ];

        $fileName = 'hourly_sales_report_' . $date;
        return $this->downloadReport($reportData, $fileName, $format, HourlySalesReportExport::class);
    }

    public function generateRefundsReport(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $format = $request->query('format', 'excel');

        // Changed to query refunds directly and include necessary relationships
        $refunds = Sale::where('sale_date', $date)
            ->whereIn('refund_status', ['partial_refund', 'full_refund'])
            ->with([
                'user:id,name',
                'refunds.item:id,name',
                'saleItems.item:id,name'
            ])
            ->get();

        $reportData = [
            'date' => $date,
            'refunds' => $refunds
        ];

        $fileName = 'refunds_report_' . $date;
        return $this->downloadReport($reportData, $fileName, $format, RefundsReportExport::class);
    }

    private function downloadReport($data, $fileName, $format, $exportClass)
    {
        if ($format === 'excel') {
            return Excel::download(new $exportClass($data), $fileName . '.xlsx');
        } elseif ($format === 'csv') {
            return Excel::download(new $exportClass($data), $fileName . '.csv');
        }
        return response('Invalid format specified.', 400);
    }

    public function exchange(Request $request, Sale $sale)
    {
        Log::info('Exchange request received', $request->all());

        $validated = $request->validate([
            'exchange_items' => 'required|array',
            'exchange_items.*.sale_item_id' => 'required|exists:sale_items,id',
            'exchange_items.*.new_item_id' => 'required|exists:items,id',
        ]);

        Log::info('Validated exchange data', $validated);

        if (empty($validated['exchange_items'])) {
            return redirect()->back()->with('error', 'No items provided for exchange.')->withInput();
        }

        foreach ($validated['exchange_items'] as $exchangeItem) {
            $saleItem = SaleItem::findOrFail($exchangeItem['sale_item_id']);
            $newItem = Item::findOrFail($exchangeItem['new_item_id']);

            // Check if the sale item has a quantity greater than 0
            if ($saleItem->quantity <= 0) {
                return redirect()->back()->with('error', "Cannot exchange '{$saleItem->item->name}' because it has zero quantity.")->withInput();
            }

            // Check if the new item has enough stock
            if ($newItem->quantity <= 0) {
                return redirect()->back()->with('error', "Cannot exchange with '{$newItem->name}' because it is out of stock.")->withInput();
            }

            if ($newItem->quantity < $saleItem->quantity) {
                return redirect()->back()->with('error', "Insufficient stock for '{$newItem->name}'. Available: {$newItem->quantity}, Required: {$saleItem->quantity}")->withInput();
            }

            // Update inventory for the old item
            $saleItem->item->increment('quantity', $saleItem->quantity);

            // Get the selling price of the new item
            $newItemPrice = $newItem->priceAfterSale();

            // Calculate subtotal
            $newSubtotal = $newItemPrice * $saleItem->quantity;

            // Update the sale item with new details and mark as exchanged
            $saleItem->update([
                'item_id' => $newItem->id,
                'price' => $newItemPrice,
                'subtotal' => $newSubtotal,
                'is_exchanged' => true // Mark the item as exchanged
            ]);

            // Update inventory for the new item
            $newItem->decrement('quantity', $saleItem->quantity);

            Log::info('Item exchanged successfully', [
                'sale_item_id' => $saleItem->id,
                'item_quantity' => $saleItem->quantity,
                'new_item_id' => $newItem->id,
                'new_price' => $newItemPrice,
                'new_subtotal' => $newSubtotal,
                'is_exchanged' => true
            ]);
        }

        // Recalculate the total amount for the sale
        $sale = Sale::with('saleItems')->find($sale->id);

        // Calculate new subtotal by summing all item subtotals
        $newSubtotalAmount = $sale->saleItems->sum('subtotal');
        Log::info('Recalculated sale subtotal', ['subtotal' => $newSubtotalAmount]);

        // Apply discount
        $discountAmount = 0;
        if ($sale->discount_type === 'percentage') {
            $discountAmount = $newSubtotalAmount * ($sale->discount_value / 100);
        } elseif ($sale->discount_type === 'fixed') {
            $discountAmount = min($sale->discount_value, $newSubtotalAmount);
        }

        Log::info('Applied discount', ['type' => $sale->discount_type, 'value' => $sale->discount_value, 'amount' => $discountAmount]);

        // Calculate the final total including shipping fees and discounts
        $newTotalAmount = $newSubtotalAmount - $discountAmount + $sale->shipping_fees;

        Log::info('New total amount calculated', [
            'subtotal' => $newSubtotalAmount,
            'discount' => $discountAmount,
            'shipping' => $sale->shipping_fees,
            'total' => $newTotalAmount
        ]);

        $sale->update([
            'total_amount' => $newTotalAmount,
            'subtotal' => $newSubtotalAmount,
            'discount' => $discountAmount
        ]);

        return redirect()->route('sales.show', $sale->id)->with('success', 'Items exchanged successfully.');
    }

    public function showExchangeForm(Sale $sale)
    {
        $items = Item::where('is_parent', false)->get();
        return view('sales.exchange', compact('sale', 'items'));
    }
}
