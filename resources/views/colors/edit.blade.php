@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Edit Color</h1>
    <form action="{{ route('colors.update', $color) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Color Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $color->name }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update Color</button>
    </form>
</div>
@endsection
