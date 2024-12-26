@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h2>Edit Category</h2>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Category Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}" required>
            </div>

            <div class="form-group">
                <label for="brand_id">Brand:</label>
                <select name="brand_id" id="brand_id" class="form-control" required>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ $brand->id == $category->brand_id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="picture">Category Picture:</label>
                <input type="file" name="picture" id="picture" class="form-control-file">
                @if($category->picture)
                    <img src="{{ asset('storage/'.$category->picture) }}" alt="{{ $category->name }}" class="img-thumbnail" width="500">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
    </div>
@endsection
