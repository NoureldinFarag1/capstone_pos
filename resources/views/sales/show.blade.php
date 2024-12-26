@extends('layouts.dashboard')

@section('content')
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">

                    <h2>Sale Details</h2>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3 class="card-title">Items Sold</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->saleItems as $saleItem)
                                <tr>
                                    <td>{{ $saleItem->item->name }}</td>
                                    <td>{{ $saleItem->quantity }}</td>
                                    <td>${{ number_format($saleItem->price, 2) }}</td>
                                    <td>${{ number_format($saleItem->price * $saleItem->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h5 class="card-title text-danger">Total Discount: ${{ number_format($sale->discount, 2) }}</h5>
                    <h3 class="card-title">Total Amount: ${{ number_format($sale->total_amount, 2) }}</h3>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sales.index') }}" class="btn btn-warning">Back to Sales</a>
                        <div>
                            <form action="{{ route('sales.thermalReceipt', $sale->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-primary">Print Receipt</button>
                            </form>
                            <form action="{{ route('sales.invoice', $sale->id) }}" method="GET" style="display:inline;">
                                <button type="submit" class="btn btn-primary">Print Invoice</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
