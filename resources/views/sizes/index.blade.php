@extends('layouts.dashboard')
@section('title', 'Product Sizes')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Product Sizes</h1>
        <a href="{{ route('sizes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Create New Size
        </a>
    </div>

    <!-- No Sizes Available Message -->
    @if($sizes->isEmpty())
        <div class="alert alert-info">
            No sizes have been created yet.
            <a href="{{ route('sizes.create') }}" class="alert-link">Create your first size</a>.
        </div>
    @else
        <!-- Product Sizes Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sizes->sortBy('type') as $size)
                                <tr>
                                    <td>{{ $size->name }}</td>
                                    <td>{{ ucfirst($size->type) }}</td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <!-- Edit Button -->
                                            <a href="{{ route('sizes.edit', $size->id) }}" class="btn btn-warning btn-sm ml-2" title="Edit Size">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <!-- Delete Button -->
                                            <form action="{{ route('sizes.destroy', $size->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this size?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm ml-2" title="Delete Size">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        };
    });
</script>
@endpush
