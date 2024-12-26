@extends('layouts.dashboard')
@section('content')
<div class="container">
    <h2>Edit Brand</h2>

    <form action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name">Brand Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $brand->name }}" required>
        </div>

        <div class="mb-3">
            <label for="picture">Brand Picture:</label>
            <input type="file" name="picture" id="picture" class="form-control">
            @if($brand->picture)
                <img src="{{ asset('storage/'.$brand->picture) }}" alt="{{ $brand->name }}" class="img-thumbnail mt-2" style="max-width: 200px;">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update Brand</button>
    </form>
</div>
@endsection
