@extends('layouts.dashboard')
@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <h2 class="mb-0">Sale Details</h2>
                        <span class="badge {{ $sale->refund_status ? 'bg-warning' : 'bg-success' }} fs-6">
                            {{ $sale->refund_status ? ucfirst(str_replace('_', ' ', $sale->refund_status)) : 'Completed' }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mb-3">
                            <h3 class="card-title text-secondary mb-0">Receipt No: {{ $sale->id }}</h3>
                            <h3 class="card-title text-secondary mb-0">Date: {{ $sale->sale_date->format('d/m/Y') }}</h3>
                        </div>

                        <div class="table-responsive mb-4">
                            <h3 class="card-title text-secondary mb-3">Items Sold</h3>
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Item Name</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end px-4">Total</th>
                                        <th class="text-center">Status</th>
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
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if($sale->refund_status === 'partial_refund' || $sale->refund_status === 'full_refund')
                            <div class="table-responsive mb-4">
                                <h3 class="card-title text-secondary mb-3">Refunded Items</h3>
                                <table class="table table-hover align-middle table-danger">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4">Item Name</th>
                                            <th class="text-center">Quantity Refunded</th>
                                            <th class="text-end">Refund Amount</th>
                                            <th class="px-4">Reason</th>
                                            <th class="text-end px-4">Refund Date</th>
                                            <th class="text-end">Status</th>
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
                        @endif

                        <div class="mb-4">
                            <h3 class="card-title text-secondary mb-3">Customer Information</h3>
                            <div class="card bg-light">
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
                        </div>

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

                        <div class="row justify-content-end mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
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
                                            <span class="text-muted">Payment Method:</span>
                                            <span class="text-muted">{{ ucwords(str_replace('_', ' ', $sale->payment_method)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Sales
                            </a>
                            <div class="btn-group">
                                <form action="{{ route('sales.thermalReceipt', $sale->id) }}" method="POST" class="me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-print me-2"></i>Print Receipt
                                    </button>
                                </form>
                                <form action="{{ route('sales.invoice', $sale->id) }}" method="GET" class="me-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-file-invoice me-2"></i>Print Invoice
                                    </button>
                                </form>
                                <a href="{{ route('sales.showExchangeForm', $sale->id) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-exchange-alt me-2"></i>Exchange Item
                                </a>
                                <a href="{{ route('refund.create', $sale->id) }}" class="btn btn-danger">
                                    <i class="fas fa-undo-alt me-2"></i>Refund
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
