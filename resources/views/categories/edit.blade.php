@extends('layouts.dashboard')

@section('content')
<h2>Edit Category</h2>

<form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div>
        <label for="name">Category Name:</label>
        <input type="text" name="name" id="name" value="{{ $category->name }}" required>
    </div>

    <div>
        <label for="brand_id">Brand:</label>
        <select name="brand_id" id="brand_id" required>
            @foreach($brands as $brand)
            <option value="{{ $brand->id }}" {{ $brand->id == $category->brand_id ? 'selected' : '' }}>
                {{ $brand->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="picture">Category Picture:</label>
        <input type="file" name="picture" id="picture">
        @if($category->picture)
        <img src="{{ asset('storage/'.$category->picture) }}" alt="{{ $category->name }}" width="100">
        @endif
    </div>

    <button type="submit">Update Category</button>
</form>
@endsection
