@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Sales</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('sales.create') }}" class="btn btn-primary">Create New Sale</a>
        <a href="{{ route('sales.export') }}" class="btn btn-success">Export Sales</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Total Amount</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>${{ number_format($sale->total_amount, 2) }}</td>
                    <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-info btn-sm">Show</a>
                        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this sale?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
