<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Exports\SalesPerBrandExport;
use Maatwebsite\Excel\Facades\Excel;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector; // Use appropriate connector for your environment
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class SaleController extends Controller
{
    public function create()
    {
        $items = Item::all();
        $sales = Sale::create(['total_amount' => 0]);
        return view('sales.create', compact('items', 'sales'));
    }

    public function store(Request $request)
    {
        // Validate input for multiple items
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Create a sale
        $sale = Sale::create(['total_amount' => 0]);

        // Initialize total amount
        $totalAmount = 0;

        foreach ($validated['items'] as $itemData) {
            $item = Item::find($itemData['item_id']);

            if ($item && $item->quantity >= $itemData['quantity']) {
                // Create SaleItem
                $saleItem = new SaleItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->item_id = $item->id;
                $saleItem->quantity = $itemData['quantity'];
                $saleItem->price = $item->selling_price;
                $saleItem->barcode = $item->barcode;
                $saleItem->save();

                // Update total amount
                $totalAmount += $item->selling_price * $itemData['quantity'];

                // Decrement item quantity
                $item->decrement('quantity', $itemData['quantity']);
            } else {
                return redirect()->back()->withErrors(['insufficient_stock' => 'Insufficient stock for item: ' . $item->name]);
            }
        }

        // Update total amount of the sale
        $sale->update(['total_amount' => $totalAmount]);

        return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
    }


    public function index()
    {
        $sales = Sale::with('saleItems.item')->get();
        return view('sales.index', compact('sales'));
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
        $shopName = 'Your Shop Name'; // Replace with your actual shop name
        $shopAddress = 'Your Shop Address'; // Replace with your actual shop address
        $shopPhone = 'Your Shop Phone'; // Replace with your actual shop phone
        $shopEmail = 'yourshop@example.com'; // Replace with your actual shop email
        $paymentTerms = 'Payment due within 30 days'; // Replace with your actual payment terms

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

    public function printThermalReceipt($id)
    {
        $sale = Sale::with('saleItems.item')->findOrFail($id);

        // Change 'YourPrinterName' to your thermal printer's name
        $connector = new CupsPrintConnector('YourPrinterName'); // For Linux
        // $connector = new WindowsPrintConnector('YourPrinterName'); // For Windows
        $printer = new Printer($connector);

        // Print receipt details
        $printer->setEmphasis(true);
        $printer->text("Receipt #{$sale->id}\n");
        $printer->setEmphasis(false);
        $printer->text("Date: {$sale->created_at}\n");
        $printer->text("--------------------------------\n");

        foreach ($sale->saleItems as $saleItem) {
            $printer->text("Item: {$saleItem->item->name}\n");
            $printer->text("Qty: {$saleItem->quantity}\n");
            $printer->text("Price: {$saleItem->price}\n");
            $printer->text("--------------------------------\n");
        }

        $printer->text("Total: {$sale->total_amount}\n");
        $printer->cut();
        $printer->close();

        return redirect()->route('sales.index')->with('success', 'Receipt printed successfully.');
    }

    public function printInvoice($id)
    {
        $sale = Sale::with('saleItems.item')->findOrFail($id);
        $pdf = PDF::loadView('invoices.invoice', compact('sale'));
        return $pdf->download('invoice_' . $sale->id . '.pdf');
    }
    
}
