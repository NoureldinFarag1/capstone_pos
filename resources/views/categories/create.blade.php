@extends('layouts.dashboard')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2 ms-3">
        <h1>Create New Category</h1>

        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="brand_id">Brand Name</label>
                <select name="brand_id" class="form-control" required>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="picture">Category Picture</label>
                <input type="file" name="picture" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
    </div>
</div>
@endsection
