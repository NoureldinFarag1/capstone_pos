@extends('layouts.dashboard')

@section('content')
<h1>Create New Item</h1>

<form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="name">Item Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="category_id">Category</label>
        <select name="category_id" class="form-control" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="brand_id">Brand</label>
        <select name="brand_id" class="form-control" required>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
    <label for="buying_price">Buying Price</label>
    <input type="number" class="form-control" name="buying_price" required>
    </div>
    <div class="form-group">
        <label for="selling_price">Selling Price</label>
        <input type="number" class="form-control" name="selling_price" required>
    </div>
    <div class="form-group">
        <label for="tax">Tax (%)</label>
        <input type="number" class="form-control" name="tax" required>
    </div>
    <div class="form-group">
        <label for="applied_sale">Applied Sale (%)</label>
        <input type="number" class="form-control" name="applied_sale" min="0" max="100">
    </div>
    <div class="form-group">
        <label for="picture">Item Picture</label>
        <input type="file" name="picture" class="form-control" accept="image/*" required>
    </div>
    <br>
    <div class="form-group">
         <label for="quantity">Quantity</label>
         <input type="number" name="quantity" class="form-control" id="quantity" required min="0" value="{{ old('quantity', isset($item) ? $item->quantity : '') }}">
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Add Item</button>
</form>
@endsection
