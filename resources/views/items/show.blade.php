@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <img src="{{ asset('storage/' . $item->picture) }}" alt="{{ $item->name }}" class="card-img-top img-fluid item-image">
                    <h5 class="card-title">{{ $item->name }}</h5>
                    @if($item->barcode)
                        <h3>Barcode:</h3>
                        <img src="{{  asset('storage/' .$item->barcode) }}">
                    @else
                        <p>No barcode available.</p>
                    @endif  
                    <p class="card-text"><strong>Brand:</strong> {{ $item->brand->name }}</p>
                    <p class="card-text"><strong>Category:</strong> {{ $item->category->name }}</p>
                    <p class="card-text"><strong>Price:</strong> ${{ $item->selling_price }}</p>
                    <p class="card-text"><strong>Total Sale:</strong> {{ $item->applied_sale }}%</p>
                    <p class="card-text"><strong>Quantity:</strong> {{ $item->quantity }}</p>                  
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('items.index') }}" class="btn btn-secondary">Back to Items</a>
</div>
@endsection
