@extends('layouts.dashboard')

@section('content')
<div class="row mb-4 ms-5">
    <div class="col-md-12 ms-2">
        <h1>Categories</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Add New Category</a>
    </div>
</div>

<div class="container">
    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text">Brand: {{ $category->brand->name }}</p>
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary">Edit</a>
                        @can('admin')
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
