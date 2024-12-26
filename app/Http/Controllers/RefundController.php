<?php
namespace App\Http\Controllers;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    /**
     * Display the refund form for a specific sale.
     *
     * @param int $sale_id
     * @return \Illuminate\View\View
     */
    public function create($sale_id)
    {
        // Retrieve sale with related sale items and their associated items
        $sale = Sale::with('saleItems.item')->findOrFail($sale_id);
        $saleItems = $sale->saleItems;
        return view('refund.create', compact('sale', 'saleItems'));
    }

    /**
     * Process the refund for a sale.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            // Validate the request data
            $validated = $request->validate([
                'sale_id' => 'required|exists:sales,id',
                'refund.*.quantity' => 'nullable|numeric|min:0',
                'refund.*.reason' => 'nullable|string|max:255',
            ]);

            $sale = Sale::findOrFail($validated['sale_id']);
            $refunds = $validated['refund'] ?? [];
            $totalRefundAmount = 0;

            foreach ($refunds as $saleItemId => $refundData) {
                $quantityToRefund = $refundData['quantity'] ?? 0;
                $reason = $refundData['reason'] ?? null;

                // Skip if refund quantity is 0
                if ($quantityToRefund <= 0) {
                    continue;
                }

                $saleItem = SaleItem::findOrFail($saleItemId);

                // Validate refund quantity does not exceed sold quantity
                if ($quantityToRefund > $saleItem->quantity) {
                    return redirect()->back()->withErrors([
                        "Refund quantity for item '{$saleItem->item->name}' exceeds the sold quantity.",
                    ]);
                }

                $item = $saleItem->item;

                // Calculate refund amount
                $refundAmount = $item->priceAfterSale() * $quantityToRefund;
                $totalRefundAmount += $refundAmount;

                // Update item stock
                $item->increment('quantity', $quantityToRefund);

                // Create refund record
                Refund::create([
                    'sale_id' => $sale->id,
                    'sale_item_id' => $saleItemId,
                    'item_id' => $item->id,
                    'quantity_refunded' => $quantityToRefund,
                    'refund_amount' => $refundAmount,
                    'reason' => $reason,
                ]);

                // Update or delete the sale item based on refund quantity
                $remainingQuantity = $saleItem->quantity - $quantityToRefund;
                if ($remainingQuantity <= 0) {
                    // If all items are refunded, delete the sale item
                    $saleItem->delete();
                } else {
                    // Update the remaining quantity
                    $saleItem->update([
                        'quantity' => $remainingQuantity,
                        'total_amount' => $remainingQuantity * $item->priceAfterSale()
                    ]);
                }
            }

            // Update sale total amount
            $sale->total_amount -= $totalRefundAmount;

            // Update sale refund status
            if ($sale->saleItems()->count() === 0) {
                $sale->refund_status = 'full_refund';
            } elseif ($totalRefundAmount > 0) {
                $sale->refund_status = 'partial_refund';
            }

            $sale->save();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Refund processed successfully.');
        });
    }
}
