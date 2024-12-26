@extends('layouts.dashboard')

@section('content')
    <h1>Edit Size</h1>
    <form action="{{ route('sizes.update', $size->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Size Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $size->name }}" required>
        </div>
        <div class="form-group">
            <label for="type">Size Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="clothes" {{ $size->type == 'clothes' ? 'selected' : '' }}>Clothes</option>
                <option value="shoes" {{ $size->type == 'shoes' ? 'selected' : '' }}>Shoes</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection
