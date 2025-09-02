<?php
namespace App\Http\Controllers;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RefundController extends Controller
{
    /**
     * Display a listing of refunds with comprehensive KPIs.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Date filtering
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $status = $request->get('status', 'all');

        // Base query for refunds
        $refundsQuery = Refund::with(['sale', 'item.brand'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by status if needed
        if ($status !== 'all') {
            $refundsQuery->whereHas('sale', function($query) use ($status) {
                $query->where('refund_status', $status);
            });
        }

        $refunds = $refundsQuery->latest()->paginate(15);

        // KPI Calculations
        $kpis = $this->calculateRefundKPIs($startDate, $endDate, $status);

        return view('refund.index', compact('refunds', 'kpis', 'startDate', 'endDate', 'status'));
    }

    private function calculateRefundKPIs($startDate, $endDate, $status = 'all')
    {
        $baseQuery = Refund::whereBetween('created_at', [$startDate, $endDate]);

        // Apply status filter if needed
        if ($status !== 'all') {
            $baseQuery->whereHas('sale', function($query) use ($status) {
                $query->where('refund_status', $status);
            });
        }

        return [
            'total_refunds' => $baseQuery->count(),
            'total_refund_amount' => $baseQuery->sum('refund_amount'),
            'average_refund_amount' => $baseQuery->avg('refund_amount') ?? 0,
            'partial_refunds' => Sale::whereBetween('created_at', [$startDate, $endDate])
                ->where('refund_status', 'partial_refund')
                ->count(),
            'full_refunds' => Sale::whereBetween('created_at', [$startDate, $endDate])
                ->where('refund_status', 'full_refund')
                ->count(),
            'refund_rate' => $this->calculateRefundRateForPeriod($startDate, $endDate, $status),
            'top_refund_reasons' => $this->getTopRefundReasons($startDate, $endDate, $status),
            'top_refunded_items' => $this->getTopRefundedItems($startDate, $endDate, $status),
            'daily_refund_trend' => $this->getDailyRefundTrend($startDate, $endDate, $status),
            'refunds_by_payment_method' => $this->getRefundsByPaymentMethod($startDate, $endDate, $status)
        ];
    }

    private function calculateRefundRateForPeriod($startDate, $endDate, $status = 'all')
    {
        $totalSalesQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);
        $refundedSalesQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $refundedSalesQuery->where('refund_status', $status);
        } else {
            $refundedSalesQuery->whereIn('refund_status', ['partial_refund', 'full_refund']);
        }

        $totalSales = $totalSalesQuery->count();
        $refundedSales = $refundedSalesQuery->count();

        return $totalSales > 0 ? ($refundedSales / $totalSales) * 100 : 0;
    }

    private function getTopRefundReasons($startDate, $endDate, $status = 'all')
    {
        $query = Refund::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('reason');

        if ($status !== 'all') {
            $query->whereHas('sale', function($q) use ($status) {
                $q->where('refund_status', $status);
            });
        }

        return $query->select('reason', DB::raw('COUNT(*) as count'), DB::raw('SUM(refund_amount) as total_amount'))
            ->groupBy('reason')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    private function getTopRefundedItems($startDate, $endDate, $status = 'all')
    {
        $query = Refund::with('item')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->whereHas('sale', function($q) use ($status) {
                $q->where('refund_status', $status);
            });
        }

        return $query->select('item_id', DB::raw('SUM(quantity_refunded) as total_quantity'), DB::raw('SUM(refund_amount) as total_amount'))
            ->groupBy('item_id')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();
    }

    private function getDailyRefundTrend($startDate, $endDate, $status = 'all')
    {
        $query = Refund::whereBetween('created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->whereHas('sale', function($q) use ($status) {
                $q->where('refund_status', $status);
            });
        }

        return $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(refund_amount) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRefundsByPaymentMethod($startDate, $endDate, $status = 'all')
    {
        $query = Refund::with('sale')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status !== 'all') {
            $query->whereHas('sale', function($q) use ($status) {
                $q->where('refund_status', $status);
            });
        }

        return $query->get()
            ->groupBy('sale.payment_method')
            ->map(function ($refunds, $paymentMethod) {
                return [
                    'payment_method' => $paymentMethod,
                    'count' => $refunds->count(),
                    'total_amount' => $refunds->sum('refund_amount')
                ];
            })
            ->values();
    }
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
                $unitPrice = $item->priceAfterSale();
                $refundAmount = $unitPrice * $quantityToRefund;
                $totalRefundAmount += $refundAmount;

                Log::info('Processing refund', [
                    'item' => $item->name,
                    'quantity_to_refund' => $quantityToRefund,
                    'unit_price' => $unitPrice,
                    'refund_amount' => $refundAmount
                ]);

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
                    // Update the remaining quantity and subtotal
                    $remainingSubtotal = $remainingQuantity * $saleItem->price;
                    $saleItem->update([
                        'quantity' => $remainingQuantity,
                        'subtotal' => $remainingSubtotal
                    ]);

                    Log::info('Updated sale item', [
                        'item_id' => $saleItem->id,
                        'remaining_quantity' => $remainingQuantity,
                        'price' => $saleItem->price,
                        'new_subtotal' => $remainingSubtotal
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

            // Recalculate the sale's subtotal from the remaining sale items
            $sale->subtotal = $sale->saleItems()->sum('subtotal');

            $sale->save();

            Log::info('Refund processed', [
                'sale_id' => $sale->id,
                'total_refund_amount' => $totalRefundAmount,
                'new_sale_total' => $sale->total_amount,
                'new_sale_subtotal' => $sale->subtotal,
                'refund_status' => $sale->refund_status
            ]);

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Refund processed successfully.');
        });
    }
}
