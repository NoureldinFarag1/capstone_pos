@extends('layouts.dashboard')

        @section('content')
            <div class="container">
                <h1>Items</h1>
                <a href="{{ route('items.create') }}" class="btn btn-primary     mb-3">Add New Item</a>
                <div class="container">
    <div class="row">
        @foreach($items as $item)
            <div class="col-md-4">
                <div class="card mb-4">
                <a href="{{ route('items.show', $item->id) }}" class="card mb-1 text-decoration-none text-dark">
                    <img src="{{ asset('storage/' . $item->picture) }}" alt="{{ $item->name }}" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->name }}</h5>
                        <p class="card-text">Price: ${{ $item->selling_price }}</p>
                        <p class="card-text">Sale: {{ $item->applied_sale }}%</p>
                        <p class="card-text">Total Amount: ${{ $item->PriceAfterSale() }}</p>
                        <p class="card-text">Quantity: {{ $item->quantity }}</p>
                        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-primary">Edit</a>
                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection