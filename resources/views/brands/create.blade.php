@extends('layouts.dashboard')
@section('title', 'Add Brand')

@section('content')
<form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="name">Brand Name</label>
        <input type="text" name="name" class="form-control" required autofocus>
    </div>
    <div class="form-group">
        <label for="picture">Brand Picture</label>
        <input type="file" name="picture" class="form-control" accept="image/*" required>
    </div>
    <button type="submit" class="btn btn-primary mt-2">Add Brand</button>
</form>
@endsection
