@extends('layouts.dashboard')
@section('title', 'Create Size')

@section('content')
    <h1>Create New Size</h1>
    <form action="{{ route('sizes.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Size Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="type">Size Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="clothes">Clothes</option>
                <option value="shoes">Shoes</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success mt-2">Create</button>
    </form>
@endsection
