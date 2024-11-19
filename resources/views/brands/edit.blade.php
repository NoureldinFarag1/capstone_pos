@extends('layouts.dashboard')

@section('content')
<h2>Edit Brand</h2>

<form action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div>
        <label for="name">Brand Name:</label>
        <input type="text" name="name" id="name" value="{{ $brand->name }}" required>
    </div>
    <br>
    <div>
        <label for="picture">Brand Picture:</label>
        <input type="file" name="picture" id="picture">
        @if($brand->picture)
        <img src="{{ asset('storage/'.$brand->picture) }}" alt="{{ $brand->name }}" width="100">
        @endif
    </div>
    <br>
    <button type="submit" class="btn btn-success" >Update Brand</button>
</form>
@endsection
