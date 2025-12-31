@extends('layouts.dashboard')
@section('title', 'Add Color')

@section('content')
<div class="container">
    <h1>Add Color</h1>
    <form action="{{ route('colors.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Color Name</label>
            <input type="text" name="name" id="name" class="form-control" required autofocus>
        </div>
        <button type="submit" class="btn btn-success mt-3">Add Color</button>
    </form>
</div>
@endsection
