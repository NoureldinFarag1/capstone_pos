@extends('layouts.dashboard')
@section('title', 'Create Category')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2 ms-3">
        <h1>Create New Category</h1>

        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="brand_ids">Brands</label>
                <select name="brand_ids[]" id="brand_ids" class="form-control" multiple required>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="picture">Category Picture</label>
                <input type="file" name="picture" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add Category</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#brand_ids').select2({
        placeholder: 'Select brands',
        allowClear: true
    });
});
</script>
@endpush
