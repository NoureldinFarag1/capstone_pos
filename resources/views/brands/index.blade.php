@extends('layouts.dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="ms-3">Brands</h1>
        <a href="{{ route('brands.create') }}" class="btn btn-primary ms-3">Add New Brand</a>
    </div>
</div>

<div class="container">
    <div class="row">
        @foreach($brands->sortBy('name') as $brand)
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="{{ asset('storage/' . $brand->picture) }}" alt="{{ $brand->name }}" class="card-img-top rounded-circle mx-auto d-block mt-2" style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $brand->name }}</h5>
                        <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-primary">Edit</a>
                        @can('admin')
                        <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
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
