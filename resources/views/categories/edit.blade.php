@extends('layouts.dashboard')
@section('title', 'Edit Category')

@section('content')
    <div class="container">
        <h2>Edit Category</h2>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Category Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $category->name }}" required>
            </div>

            <div class="form-group">
                <label for="brand_ids">Brands:</label>
                <select name="brand_ids[]" id="brand_ids" class="form-control" multiple required>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ $category->brands->contains($brand->id) ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="picture" class="mt-3">Category Picture:</label>
                <input type="file" name="picture" id="picture" class="form-control-file">
                @if($category->picture)
                    <img src="{{ asset('storage/'.$category->picture) }}" alt="{{ $category->name }}" class="img-thumbnail" width="500">
                @endif
            </div>

            <button type="submit" class="btn btn-primary mt-2">Update Category</button>
        </form>
    </div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#brand_ids').select2({
        placeholder: 'Select brands',
        allowClear: true
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
