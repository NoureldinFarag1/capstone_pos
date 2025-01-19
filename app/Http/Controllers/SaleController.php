<?php

namespace App\Http\Controllers;

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
            'discount_type' => 'nullable|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
            'shipping_fees' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
        ]);

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

        // Calculate the total amount including shipping fees if applicable
        $totalAmount = $request->total;
        if ($request->shipping_fees) {
            $totalAmount += $request->shipping_fees;
        }

        // Create sale with pre-calculated total
        $sale = Sale::create([
            'user_id' => \Illuminate\Support\Facades\Auth::user()->id, // Attach the currently authenticated user
            'total_amount' => $totalAmount, // Use the calculated total including shipping fees
            'subtotal' => $request->subtotal,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'shipping_fees' => $request->shipping_fees,
            'address' => $request->address,
        ]);

        foreach ($request->items as $itemData) {
            $item = Item::find($itemData['item_id']);

            if ($item->quantity < $itemData['quantity']) {
                return redirect()->back()->with('error', 'Insufficient stock for ' . $item->name);
            }

            // Create sale item with the price from the form
            $sale->saleItems()->create([
                'item_id' => $item->id,
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'subtotal' => $itemData['price'] * $itemData['quantity'],
            ]);

            // Update inventory
            $item->decrement('quantity', $itemData['quantity']);
        }

        // Calculate and store the actual discount amount
        if ($request->discount_type !== 'none' && $request->discount_value > 0) {
            $discountAmount = $request->subtotal - $request->total;
            $sale->update(['discount' => $discountAmount]);
        }

        // Open the cash drawer and Print the thermal receipt
        $this->printThermalReceipt($sale->id);
        $this->openCashDrawer();

        return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
    }

    private function getPrinterConnector($printerName)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return new WindowsPrintConnector($printerName);
        } else {
            return new CupsPrintConnector($printerName);
        }
    }

    public function printGiftReceipt(Request $request)
    {
        try {
            Log::info('Starting gift receipt print', ['request' => $request->all()]);

            // Create a temporary receipt structure
            $items = collect($request->input('saleItems', []));

            $printerName = 'Xprinter_XP_T361U';
            $connector = $this->getPrinterConnector($printerName);
            $printer = new Printer($connector);

            // Initialize printer
            $printer->initialize();

            // Print logo and header
            $logoPath = public_path('images/RECEIPTLOGO.png');
            if (file_exists($logoPath)) {
                try {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
                    $printer->text("LOCAL HUB\n");
                    $printer->selectPrintMode();

                    $logo = EscposImage::load($logoPath);
                    $printer->bitImage($logo);

                    $printer->feed(1);
                    $printer->text("It's not just a Showroom\n");
                    $printer->feed(1);
                } catch (\Exception $e) {
                    Log::error("Logo error: " . $e->getMessage());
                }
            }

            // Gift Receipt Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->setEmphasis(true);
            $printer->text("GIFT RECEIPT\n");
            $printer->setEmphasis(false);
            $printer->selectPrintMode();
            $printer->feed(1);

            // Receipt Info
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(str_repeat("-", 48) . "\n");
            $printer->text("Date: " . now()->format('d M Y') . "\n");
            $printer->text("Time: " . now()->format('H:i:s') . "\n");
            $printer->text(str_repeat("-", 48) . "\n");

            // Items
            $printer->setEmphasis(true);
            $printer->text("ITEMS\n");
            $printer->setEmphasis(false);

            foreach ($items as $item) {
                if (isset($item['item']['name']) && isset($item['quantity'])) {
                    $name = $item['item']['name'];
                    $qty = $item['quantity'];

                    // Print item details without price
                    $printer->text(str_pad(substr($name, 0, 35), 35));
                    $printer->text(str_pad("Qty: " . $qty, 13, ' ', STR_PAD_LEFT) . "\n");
                }
            }

            // Footer
            $printer->feed(1);
            $printer->text(str_repeat("-", 48) . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            $printer->setEmphasis(true);
            $printer->text("Exchange & Return Policy\n");
            $printer->setEmphasis(false);
            $printer->text("Items may be exchanged within 14 days\n");
            $printer->text("Refunds are only with the original receipt\n");

            $printer->feed(1);
            $printer->setEmphasis(true);
            $printer->text("Thank You For Shopping With Us!\n");
            $printer->setEmphasis(false);

            $printer->feed(1);
            $printer->text("Follow us on Instagram\n");
            $printer->text("@localhub_egy\n");

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
            $printerName = 'Xprinter_XP_T361U';

            Log::info('Connecting to printer: ' . $printerName);
            $connector = $this->getPrinterConnector($printerName);
            //$connector = new WindowsPrintConnector($printerName);
            $printer = new Printer($connector);

            // Initialize printer
            $printer->initialize();
            $logoPath = public_path('images/RECEIPTLOGO.png');
            if (file_exists($logoPath)) {
                try {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
                    $printer->setEmphasis(true);
                    $printer->text("LOCAL HUB\n");

                    $printer->feed(1);
                    $printer->setEmphasis(false);
                    $logo = EscposImage::load($logoPath);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->bitImage($logo); // Print the logo
                    $printer->selectPrintMode();
                    $printer->feed(1);
                    $printer->setEmphasis(true);
                    $printer->text("It's not just a Showroom\n");
                    $printer->setEmphasis(false);
                    $printer->feed(1);
                } catch (\Exception $e) {
                    Log::error("Error loading logo: " . $e->getMessage());
                    // Handle the error gracefully (e.g., print a message instead)
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text("Logo could not be printed\n");

                }
            }
            // Receipt Info with modern spacing
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(str_repeat("·", 48) . "\n");
            $printer->setEmphasis(true);
            $printer->text("RECEIPT #" . str_pad($sale->id, 4, '0', STR_PAD_LEFT) . "\n");
            $printer->setEmphasis(false);
            $printer->text("Date: " . $sale->created_at->format('d M Y') . "\n");
            $printer->text("Time: " . $sale->created_at->format('H:i:s') . "\n");
            $printer->text(str_repeat("·", 48) . "\n");
            $printer->feed(1);

            // Modern Items Header
            $printer->setEmphasis(true);
            $printer->text("PURCHASE DETAILS\n");
            $printer->setEmphasis(false);
            $printer->text(str_repeat("-", 48) . "\n");

            // Table Header with clean spacing
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(
                str_pad("Item", 22) .
                str_pad("Qty", 6, ' ', STR_PAD_LEFT) .
                str_pad("Price", 10, ' ', STR_PAD_LEFT) .
                str_pad("Total", 10, ' ', STR_PAD_LEFT) . "\n"
            );
            $printer->text(str_repeat("-", 48) . "\n");

            // Print items with improved spacing
            $subtotal = 0;
            $totalDiscount = 0;

            foreach ($sale->saleItems as $saleItem) {
                $itemName = $saleItem->item->name;
                $quantity = $saleItem->quantity;
                $price = $saleItem->item->selling_price;
                $itemDiscount = $saleItem->item->discount_value;

                $lineTotal = $quantity * $price - ($itemDiscount * $quantity);
                $subtotal += $lineTotal;

                // Item details with better formatting
                $printer->text(str_pad(substr($itemName, 0, 21), 22));
                $printer->text(
                    str_pad($quantity, 6, ' ', STR_PAD_LEFT) .
                    str_pad(number_format($price, 2), 10, ' ', STR_PAD_LEFT) .
                    str_pad(number_format($lineTotal, 2), 10, ' ', STR_PAD_LEFT) . "\n"
                );

                // Discount display with modern styling
                if ($itemDiscount > 0) {
                    $printer->text("|_");
                    $printer->setEmphasis(true);
                    $printer->text("SAVE ");
                    $printer->setEmphasis(false);
                    $printer->text(str_pad(number_format($itemDiscount * $quantity, 2), 8, ' ', STR_PAD_LEFT) . "\n");
                    $totalDiscount += ($itemDiscount * $quantity);
                }
            }

            // Summary section with modern separators
            $printer->feed(1);
            $printer->text(str_repeat("-", 48) . "\n");

            // Amount Summary with right alignment
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text(
                str_pad("Subtotal:", 37, ' ', STR_PAD_LEFT) .
                str_pad(number_format($subtotal, 2), 11, ' ', STR_PAD_LEFT) . "\n"
            );

            if ($totalDiscount > 0) {
                $printer->setEmphasis(true);
                $printer->text(
                    str_pad("Total Savings:", 37, ' ', STR_PAD_LEFT) .
                    str_pad("-" . number_format($totalDiscount, 2), 11, ' ', STR_PAD_LEFT) . "\n"
                );
                $printer->setEmphasis(false);
            }

            if ($sale->discount > 0) {
                $printer->text(
                    str_pad("Total Discounts:", 37, ' ', STR_PAD_LEFT) .
                    str_pad("-" . number_format($sale->discount, 2), 11, ' ', STR_PAD_LEFT) . "\n"
                );
            }

            // Add shipping fees and address if COD
            if ($sale->payment_method === 'cod') {
                $printer->text(
                    str_pad("Shipping Fees:", 37, ' ', STR_PAD_LEFT) .
                    str_pad(number_format($sale->shipping_fees, 2), 11, ' ', STR_PAD_LEFT) . "\n"
                );
                $printer->text(
                    str_pad("Address:", 37, ' ', STR_PAD_LEFT) .
                    str_pad($sale->address, 11, ' ', STR_PAD_LEFT) . "\n"
                );
            }

            // Final total with bold border
            $printer->text(str_repeat("=", 48) . "\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->setEmphasis(true);
            $printer->text(
                str_pad("TOTAL ",8, ' ', STR_PAD_LEFT) .
                str_pad(number_format($sale->total_amount, 2), 5, ' ', STR_PAD_LEFT) . "\n"
            );
            $printer->setEmphasis(false);
            $printer->selectPrintMode();
            $printer->text(str_repeat("=", 48) . "\n");

            // Modern footer with social media
            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("طلب استبدال أو استرداد قيمة المنتج خلال ١٤ يوم\n");
            $printer->text("Exchanges and Refunds are only applicable for 14 days\n");
            $printer->text("Thank You For Shopping With Us!\n");
            $printer->setEmphasis(false);
            $printer->feed(1);
            $printer->text("Find us on Instagram\n");
            $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
            $printer->text("@localhub_egy\n");
            $printer->selectPrintMode();
            $printer->feed(1);
            $printer->text(str_repeat(".", 48) . "\n");

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

    private function openCashDrawer()
    {
        try {
            Log::info('Attempting to open cash drawer');

            $printerName = 'Xprinter_XP_T361U';
            Log::info('Using printer: ' . $printerName);

            $connector = $this->getPrinterConnector($printerName);
            $printer = new Printer($connector);

            // Send raw bytes directly, bypassing text encoding
            $printer->getPrintConnector()->write("\x1B\x70\x00\x19\xFA");

            $printer->close();
            Log::info('Cash drawer operation completed');

            return true;
        } catch (\Exception $e) {
            Log::error('Cash drawer error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }


    public function index(Request $request)
    {
        $sort = $request->get('sort', 'desc'); // Default to 'desc' if no sort parameter is provided
        $search = $request->query('search'); // Get the search query

        $sales = Sale::when($search, function ($query, $search) {
            $query->where('id', $search); // Search by transaction ID
        })
        ->orderBy('created_at', $sort)
        ->paginate(15);
        return view('sales.index', compact('sales', 'sort','search'));
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
}
