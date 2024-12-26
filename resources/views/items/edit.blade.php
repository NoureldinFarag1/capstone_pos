@extends('layouts.dashboard')

@section('content')
<br>
<div class="container">
    <h2 class="mb-4 text-center">Edit Item</h2>

    <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <div class="form-group mb-4">
                    <label for="name" class="form-label">Item Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $item->name }}" required>
                </div>

                <div class="form-group mb-4">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select name="brand_id" id="brand_id" class="form-select">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <div class="form-group mb-4">
                    <label for="selling_price" class="form-label">Price</label>
                    <input type="number" name="selling_price" id="selling_price" class="form-control" value="{{ $item->selling_price }}" required>
                </div>

                <div class="form-group mb-4">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $item->quantity }}" required>
                </div>

                <!-- Discount Type and Value -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="discount_type" class="form-label">Discount Type</label>
                        <select id="discount_type" name="discount_type" class="form-select">
                            <option value="percentage" {{ old('discount_type', $item->discount_type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ old('discount_type', $item->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="discount_value" class="form-label">Discount Value</label>
                        <input type="number" id="discount_value" name="discount_value" class="form-control" value="{{ old('discount_value', $item->discount_value) }}" min="0" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Picture Upload -->
        <div class="form-group mb-4">
            <label for="picture" class="form-label">Item Picture (optional)</label>
            <input type="file" name="picture" id="picture" class="form-control">
            @if($item->picture)
                <div class="mt-3">
                    <img src="{{ asset('storage/' . $item->picture) }}" alt="{{ $item->name }}" class="img-thumbnail" style="max-width: 200px;">
                </div>
            @endif
        </div>

        <!-- Sizes -->
        <div class="form-group mb-4">
            <label class="form-label">Sizes</label>
            <div class="row">
                @foreach($sizes as $size)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sizes[]" id="size-{{ $size->id }}" value="{{ $size->id }}"
                                {{ $item->sizes->contains($size->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="size-{{ $size->id }}">
                                {{ $size->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Update Item
            </button>
        </div>
    </form>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
