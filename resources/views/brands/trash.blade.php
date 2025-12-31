@extends('layouts.dashboard')
@section('title', 'Trash - Brands')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-dark mb-1">Trash - Brands</h1>
        <p class="text-muted">Restore brands deleted within the last 30 days or delete permanently</p>
    </div>
    <a href="{{ route('brands.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Brands
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        @if($trashed->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-trash fa-2x mb-3"></i>
                <p>No trashed brands in the last 30 days.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trashed as $brand)
                            <tr>
                                <td>{{ $brand->name }}</td>
                                <td>{{ $brand->deleted_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <form action="{{ route('brands.restore', $brand->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm" type="submit">
                                            <i class="fas fa-undo me-1"></i>Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('brands.forceDelete', $brand->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Permanently delete this brand? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="fas fa-times me-1"></i>Delete Permanently
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
