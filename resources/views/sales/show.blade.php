@extends('layouts.dashboard')
@section('content')
    <div class="sale-show container py-4" aria-labelledby="saleSummaryHeading">
        <!-- Top summary header -->
        <div class="sale-summary-bar d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 rounded-3">
            <div class="d-flex flex-column">
                <h1 id="saleSummaryHeading" class="h4 mb-1 fw-bold">Sale #{{ $sale->id }}</h1>
                <div class="d-flex align-items-center flex-wrap gap-3 small text-muted">
                    <span class="d-inline-flex align-items-center gap-1"><i class="fas fa-calendar-alt"></i>{{ $sale->sale_date->format('d/m/Y') }}</span>
                    <span class="d-inline-flex align-items-center gap-1"><i class="fas fa-clock"></i>{{ $sale->sale_date->format('H:i') }}</span>
                    <span class="d-inline-flex align-items-center gap-1"><i class="fas fa-credit-card"></i>{{ ucwords(str_replace('_',' ',$sale->payment_method)) }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="totals-chip text-end pe-3">
                    <div class="small text-muted">Total</div>
                    <div class="h5 mb-0 fw-semibold">EGP {{ number_format($sale->total_amount,2) }}</div>
                </div>
                <span class="status-badge badge {{ $sale->refund_status ? 'bg-warning' : 'bg-success' }} fw-semibold">
                    {{ $sale->refund_status ? ucfirst(str_replace('_', ' ', $sale->refund_status)) : 'Completed' }}
                </span>
                <div class="action-buttons-bar ms-2">
                    <form action="{{ route('sales.thermalReceipt', $sale->id) }}" method="POST" class="d-inline d-none" aria-hidden="true" id="thermalReceiptForm">
                        @csrf
                    </form>
                    <button type="button" id="printThermalBtn" class="btn btn-primary btn-sm" aria-label="Print thermal receipt"><i class="fas fa-print"></i><span class="d-none d-md-inline ms-1">Receipt</span></button>
                    <form action="{{ route('sales.invoice', $sale->id) }}" method="GET" class="d-inline" aria-label="Print invoice">
                        <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-invoice"></i><span class="d-none d-md-inline ms-1">Invoice</span></button>
                    </form>
                    <a href="{{ route('sales.showExchangeForm', $sale->id) }}" class="btn btn-outline-warning btn-sm" aria-label="Exchange item"><i class="fas fa-exchange-alt"></i><span class="d-none d-md-inline ms-1">Exchange</span></a>
                    <a href="{{ route('refund.create', $sale->id) }}" class="btn btn-outline-danger btn-sm" aria-label="Refund sale"><i class="fas fa-undo-alt"></i><span class="d-none d-md-inline ms-1">Refund</span></a>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-xl-8 col-lg-9">
                <div class="card shadow-sm mb-4" aria-labelledby="itemsSoldHeading">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <h2 id="itemsSoldHeading" class="h5 mb-0">Items Sold</h2>
                        <small class="text-muted">{{ $sale->saleItems->count() }} line items</small>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="table-responsive" role="region" aria-labelledby="itemsSoldHeading">
                            <table class="table table-striped table-hover align-middle" aria-describedby="itemsSoldCaption">
                                <caption id="itemsSoldCaption" class="text-muted">List of items included in this sale.</caption>
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="px-4">Item</th>
                                        <th scope="col" class="text-center">Qty</th>
                                        <th scope="col" class="text-end">Unit Price</th>
                                        <th scope="col" class="text-end px-4">Line Total</th>
                                        <th scope="col" class="text-center">Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->saleItems as $saleItem)
                                        <tr>
                                            <td class="px-4">
                                                <strong>{{ $saleItem->item->brand->name ?? 'No Brand' }}</strong> -
                                                {{ $saleItem->item->name }}
                                                @if($saleItem->as_gift)
                                                    <span class="badge bg-info ms-2">Gift</span>
                                                @endif
                                                @if($saleItem->special_discount > 0)
                                                    <span class="badge bg-danger ms-2">{{ $saleItem->special_discount }}% off</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $saleItem->quantity }}</td>
                                            <td class="text-end">EGP {{ number_format($saleItem->price, 2) }}</td>
                                            <td class="text-end px-4">EGP
                                                {{ number_format($saleItem->subtotal, 2) }}
                                            </td>
                                            <td class="text-center">
                                                @if($saleItem->is_exchanged)
                                                    <span class="badge bg-info text-white">Exchanged</span>
                                                @else
                                                    <span class="badge bg-success text-white">Original</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                        <td class="text-end px-4 fw-bold">EGP {{ number_format($sale->subtotal, 2) }}</td>
                                        <td aria-hidden="true"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @if($sale->refund_status === 'partial_refund' || $sale->refund_status === 'full_refund')
                            <div class="refunded-section mb-4" aria-labelledby="refundedItemsHeading">
                                <button class="btn btn-outline-secondary btn-sm mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#refundedItemsCollapse" aria-expanded="false" aria-controls="refundedItemsCollapse">
                                    <i class="fas fa-undo-alt me-1"></i> Show Refunded Items
                                </button>
                                <div id="refundedItemsCollapse" class="collapse">
                                <div class="table-responsive">
                                    <h2 id="refundedItemsHeading" class="h6 text-secondary mb-3">Refunded Items</h2>
                                    <table class="table table-hover align-middle table-danger" aria-describedby="refundedItemsCaption">
                                        <caption id="refundedItemsCaption" class="text-muted">Items that were refunded in this sale.</caption>
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="px-4">Item</th>
                                            <th scope="col" class="text-center">Qty Refunded</th>
                                            <th scope="col" class="text-end">Amount</th>
                                            <th scope="col" class="px-4">Reason</th>
                                            <th scope="col" class="text-end px-4">Date</th>
                                            <th scope="col" class="text-end">State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale->refunds as $refund)
                                            <tr>
                                                <td class="px-4">
                                                    <strong>{{ $refund->item->brand->name ?? 'No Brand' }}</strong> -
                                                    {{ $refund->item->name }}
                                                </td>
                                                <td class="text-center">{{ $refund->quantity_refunded }}</td>
                                                <td class="text-end">EGP {{ number_format($refund->refund_amount, 2) }}</td>
                                                <td class="px-4">{{ $refund->reason ?? 'No reason provided' }}</td>
                                                <td class="text-end px-4">{{ $refund->created_at->format('Y-m-d H:i') }}</td>
                                                <td class="text-end">
                                                    @if($refund->is_exchanged)
                                                        <span class="badge bg-info text-white">Exchanged</span>
                                                    @else
                                                        <span class="badge bg-success text-white">Original</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light fw-bold">
                                            <td colspan="2" class="text-end">Total Refunded:</td>
                                            <td class="text-end">EGP {{ number_format($sale->refunds->sum('refund_amount'), 2) }}</td>
                                            <td colspan="2"></td>
                                            <td class="text-end">
                                                @if($sale->refunds->count() > 0)
                                                    <span class="badge bg-danger text-white">Refunded</span>
                                                @else
                                                    <span class="badge bg-secondary text-white">No Refunds</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-4" aria-labelledby="customerInfoHeading">
                            <h2 id="customerInfoHeading" class="h6 text-secondary mb-3">Customer</h2>
                            @if($sale->customer_name || $sale->customer_phone)
                            <div class="card bg-light customer-card">
                                <div class="card-body p-3">
                                    @if($sale->customer_name)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Customer Name:</span>
                                            <span>{{ $sale->customer_name }}</span>
                                        </div>
                                    @endif
                                    @if($sale->customer_phone)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Customer Phone:</span>
                                            <span>{{ $sale->customer_phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @else
                                <div class="card bg-light customer-card">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-center flex-column text-muted py-2">
                                            <i class="fas fa-user me-2 fa-2x"></i>
                                            <div class="fw-semibold">Walk-in Customer</div>
                                        </div>
                                    </div>
                                </div>
                            @endif


                        @if($sale->notes)
                            <div class="mb-4">
                                <h3 class="card-title text-secondary mb-3">Notes</h3>
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <p class="mb-0">{{ $sale->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row justify-content-end mb-4" aria-labelledby="totalsHeading">
                            <div class="col-md-6">
                                <div class="card bg-light totals-card" aria-describedby="totalsCaption">
                                    <div class="card-body p-3">
                                        <h2 id="totalsHeading" class="visually-hidden">Totals</h2>
                                        @if($sale->address)
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Address:</span>
                                                <span>{{ $sale->address }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Shipping Fees:</span>
                                                <span>+ EGP{{ number_format($sale->shipping_fees, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0">Subtotal:</h6>
                                            <h6 class="mb-0">EGP {{ number_format($sale->subtotal, 2) }}</h6>
                                        </div>
                                        @if ($sale->discount_type !== 'none')
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-0">Discount type:</h6>
                                                <h6 class="mb-0">{{ ucfirst($sale->discount_type) }}</h6>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-0">Additional Discount:</h6>
                                                <h6 class="mb-0 text-danger">- EGP
                                                    {{ $sale->discount_type === 'percentage' ? number_format($sale->discount, 2)
                                        . " (" . $sale->discount_value . "%)" : number_format($sale->discount_value, 2) }}
                                                </h6>
                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-between mt-2">
                                            <h4 class="mb-0">Total Amount:</h4>
                                            <h4 class="mb-0">EGP {{ number_format($sale->total_amount, 2) }}</h4>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Payment:</span>
                                            <span class="text-muted">{{ ucwords(str_replace('_', ' ', $sale->payment_method)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm" aria-label="Back to sales list"><i class="fas fa-arrow-left me-1"></i> Back to Sales</a>
                        </div>
                        @push('scripts')
                        <script>
                        (function(){
                            const btn = document.getElementById('printThermalBtn');
                            const form = document.getElementById('thermalReceiptForm');
                            if(!btn || !form) return;
                            const endpoint = form.getAttribute('action');
                            btn.addEventListener('click', function(){
                                btn.disabled = true;
                                const originalHtml = btn.innerHTML;
                                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Printing';
                                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                fetch(endpoint,{
                                    method:'POST',
                                    headers:{'X-CSRF-TOKEN':token,'Accept':'application/json'},
                                })
                                .then(r=>{
                                    // Attempt JSON first; if not JSON still treat as success
                                    const ct = r.headers.get('content-type')||'';
                                    if(ct.includes('application/json')) return r.json();
                                    return {success:r.ok};
                                })
                                .then(data=>{
                                    const ok = data && (data.success === true || data.status === 'ok' || data.printed === true);
                                    showInlineToast(ok? 'Thermal receipt sent to printer.' : 'Receipt request processed.');
                                })
                                .catch(err=>{
                                    showInlineToast('Print failed: '+ err.message, true);
                                })
                                .finally(()=>{
                                    btn.disabled = false;
                                    btn.innerHTML = originalHtml;
                                });
                            });
                            function showInlineToast(message, error){
                                let tc = document.querySelector('.toast-container');
                                if(!tc){ tc = document.createElement('div'); tc.className='toast-container position-fixed bottom-0 end-0 p-3'; document.body.appendChild(tc); }
                                const t = document.createElement('div');
                                t.className = 'toast align-items-center text-white bg-'+(error?'danger':'success')+' border-0';
                                t.setAttribute('role','alert'); t.setAttribute('aria-live','assertive'); t.setAttribute('aria-atomic','true');
                                t.innerHTML = '<div class="d-flex"><div class="toast-body">'+message+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';
                                tc.appendChild(t);
                                const toast = new bootstrap.Toast(t,{delay:3000}); toast.show();
                                t.addEventListener('hidden.bs.toast',()=>t.remove());
                            }
                        })();
                        </script>
                        @endpush
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
