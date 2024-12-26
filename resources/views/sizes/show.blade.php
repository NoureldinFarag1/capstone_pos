@extends('layouts.dashboard')

@section('content')
    <h1>Size Details</h1>
    <p><strong>Name:</strong> {{ $size->name }}</p>
    <p><strong>Type:</strong> {{ ucfirst($size->type) }}</p>
    <a href="{{ route('sizes.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
