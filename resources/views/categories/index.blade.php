@extends('layouts.dashboard')
@section('title', 'Categories')

@section('content')
<div class="container">
    <h1>Categories</h1>
    <a href="{{ route('categories.create') }}" class="btn btn-success mb-3">Create New Category</a>
    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card">
                    @if($category->picture)
                        <img src="{{ asset('storage/'.$category->picture) }}" class="card-img-top" alt="{{ $category->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->name ?? 'N/A' }}</h5>
                        <p class="card-text">
                            <strong>Brands:</strong>
                            @foreach($category->brands as $brand)
                                {{ $brand->name ?? 'N/A' }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                        <a href="{{ route('categories.show', $category->id) }}" class="btn btn-primary">View</a>
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
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
