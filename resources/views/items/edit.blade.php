@extends('layouts.dashboard')

@section('content')
<h2>Edit Item</h2>

<form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Item Name -->
    <div class="form-group">
        <label for="name">Item Name</label>
        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
    </div>

    <!-- Category -->
    <div class="form-group">
        <label for="category_id">Category</label>
        <select name="category_id" class="form-control">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Brand -->
    <div class="form-group">
        <label for="brand_id">Brand</label>
        <select name="brand_id" class="form-control">
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Price -->
    <div class="form-group">
        <label for="price">Price</label>
        <input type="number" name="price" class="form-control" value="{{ $item->price }}" required>
    </div>

    <!-- Quantity -->
    <div class="form-group">
        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" class="form-control" value="{{ $item->quantity }}" required>
    </div>

    <!-- Optional: Picture Upload -->
    <div class="form-group">
        <label for="picture">Item Picture (optional)</label>
        <input type="file" name="picture" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Update Item</button>
</form>
@endsection