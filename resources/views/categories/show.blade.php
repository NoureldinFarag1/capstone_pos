@extends('layouts.dashboard')
@section('title', 'Category Details')

@section('content')
<div class="container">
    <h1>{{ $category->name }}</h1>
    @if($category->picture)
        <img src="{{ asset('storage/'.$category->picture) }}" alt="{{ $category->name }}" class="img-thumbnail" width="500">
    @endif
    <h3>Brands</h3>
    <ul>
        @foreach($category->brands as $brand)
            <li>{{ $brand->name }}</li>
        @endforeach
    </ul>
    <h3>Items</h3>
    <ul>
        @foreach($category->items as $item)
            <li>{{ $item->name }}</li>
        @endforeach
    </ul>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back to Categories</a>
</div>
@endsection
