@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Sale Details</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h3>Items Sold</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Barcode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $saleItem)
                <tr>
                    <td>{{ $saleItem->item->name }}</td>
                    <td>{{ $saleItem->quantity }}</td>
                    <td>${{ number_format($saleItem->price, 2) }}</td>
                    <td>${{ number_format($saleItem->price * $saleItem->quantity, 2) }}</td>
                    <td><img src="{{ asset('storage/' . $saleItem->barcode) }}" alt="Barcode"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Total Amount: ${{ number_format($sale->total_amount, 2) }}</h4>

    <a href="{{ route('sales.index') }}" class="btn btn-warning">Back to Sales</a>

    <!-- Button to print the thermal receipt -->
    <form action="{{ route('sales.thermalReceipt', $sale->id) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-primary">Print Receipt</button>
    </form>

    <!-- Button to print the invoice -->
    <form action="{{ route('sales.invoice', $sale->id) }}" method="GET" style="display:inline;">
        <button type="submit" class="btn btn-primary">Print Invoice</button>
    </form>

</div>
@endsection
