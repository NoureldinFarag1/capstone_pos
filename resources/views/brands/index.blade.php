@extends('layouts.dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="ms-3">Brands</h1>
        <a href="{{ route('brands.create') }}" class="btn btn-primary ms-3">Add New Brand</a>
    </div>
</div>

<div class="container">
    <div class="row">
        @foreach($brands as $brand)
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="{{ asset('storage/' . $brand->picture) }}" alt="{{ $brand->name }}" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">{{ $brand->name }}</h5>
                        <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-primary">Edit</a>
                        @can('admin')
                        <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
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
