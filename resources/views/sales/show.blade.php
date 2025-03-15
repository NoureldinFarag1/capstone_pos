@extends('layouts.dashboard')
@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h2 class="mb-0">Sale Details</h2>
                    </div>

                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive mb-4">
                            <h3 class="card-title text-secondary mb-3">Items Sold</h3>
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Item Name</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end px-4">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->saleItems as $saleItem)
                                        <tr>
                                            <td class="px-4">{{ $saleItem->item->brand->name ?? 'No Brand' }} -
                                                {{ $saleItem->item->name }}
                                            </td>
                                            <td class="text-center">{{ $saleItem->quantity }}</td>
                                            <td class="text-end">EGP {{ number_format($saleItem->price, 2) }}</td>
                                            <td class="text-end px-4">EGP
                                                {{ number_format($saleItem->price * $saleItem->quantity, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

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
                                        @if($sale->shipping_fees)
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Shipping Fees:</span>
                                                <span>+ EGP{{ number_format($sale->shipping_fees, 2) }}</span>
                                            </div>
                                        @endif
                                        @if($sale->address)
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Address:</span>
                                                <span>{{ $sale->address }}</span>
                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-danger">Discount:</span>
                                            <span class="text-danger">-
                                                EGP{{ number_format($sale->discount_value, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h4 class="mb-0">Total Amount:</h4>
                                            <h4 class="mb-0">EGP {{ number_format($sale->total_amount, 2) }}</h4>
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
                                <form action="{{ route('sales.invoice', $sale->id) }}" method="GET">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-file-invoice me-2"></i>Print Invoice
                                    </button>
                                </form>
                                <a href="{{ route('sales.showExchangeForm', $sale->id) }}" class="btn btn-warning ms-2">
                                    <i class="fas fa-exchange-alt me-2"></i>Exchange Item
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection