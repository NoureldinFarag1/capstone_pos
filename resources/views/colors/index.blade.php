@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Colors</h1>
    <a href="{{ route('colors.create') }}" class="btn btn-primary">Add Color</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colors as $color)
                <tr>
                    <td>{{ $color->id }}</td>
                    <td>{{ $color->name }}</td>
                    <td>
                        <a href="{{ route('colors.edit', $color) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('colors.destroy', $color) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
