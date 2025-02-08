@extends('layouts.dashboard')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="fw-bold">Items</h1>
        <div>
            <a href="{{ route('items.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add New Item
            </a>
            <button id="generateBarcodes" class="btn btn-secondary">
                <i class="fas fa-barcode"></i> Generate Barcodes
            </button>
        </div>
    </div>

    <!-- Filter and Export Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <!-- Search Bar -->
        <div class="input-group mb-3 me-2" style="max-width: 300px;">
            <span class="input-group-text bg-gradient-primary">
                <i class="fas fa-search text-gray-600"></i>
            </span>
            <form action="{{ route('items.index') }}" method="GET" class="d-flex flex-grow-1">
                <input type="text"
                       class="form-control"
                       id="itemSearch"
                       name="search"
                       placeholder="Search items..."
                       value="{{ request('search') }}">
                <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            </form>
        </div>

        <!-- Export Dropdown -->
        <div class="dropdown me-3">
            <button class="btn btn-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-file-export"></i> Export Items
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                <li><a class="dropdown-item" href="{{ route('items.export') }}">All Brands</a></li>
                @foreach($brands as $brand)
                    <li><a class="dropdown-item"
                            href="{{ route('items.export', ['brand_id' => $brand->id]) }}">{{ $brand->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('items.index') }}" method="GET" class="d-flex">
            <div class="input-group">
                <select name="brand_id" class="form-select">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Items Grid -->
    <div class="row g-4">
        @foreach($items as $item)
            @if($item->is_parent)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <a href="{{ route('items.show', $item->id) }}" class="text-decoration-none text-reset">
                                    <h5 class="card-title fw-bold m-0">{{ $item->name }} - {{ $item->brand->name }}</h5>
                                </a>
                                @if($item->quantity <= 0)
                                    <div class="stock-badge">
                                        <span class="badge bg-danger">Out of Stock</span>
                                    </div>
                                @endif
                            </div>

                            <div class="item-details">
                                <div class="price-info mb-3">
                                    <p class="mb-1 d-flex justify-content-between">
                                        <span class="text-muted">Base Price:</span>
                                        <span class="fw-bold">EGP{{ number_format($item->selling_price, 2) }}</span>
                                    </p>
                                    @if($item->discount_type === 'percentage')
                                        <p class="mb-1 d-flex justify-content-between">
                                            <span class="text-muted">Discount:</span>
                                            <span class='text-danger'>{{ $item->discount_value }}% OFF</span>
                                        </p>
                                    @elseif($item->discount_type === 'fixed')
                                        <p class="mb-1 d-flex justify-content-between">
                                            <span class="text-muted">Discount:</span>
                                            <span class='text-danger'>EGP{{ number_format($item->discount_value, 2) }} OFF</span>
                                    @endif
                                    <p class="mb-1 d-flex justify-content-between">
                                        <span class="text-muted">Final Price:</span>
                                        <span class="fw-bold">EGP{{ number_format($item->priceAfterSale(), 2) }}</span>
                                    </p>
                                </div>
                                <p class="mb-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Stock:</span>
                                    <span class="fw-medium">{{ $item->quantity }} units</span>
                                </p>
                            </div>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between">
                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @can('admin')
                                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="delete-item-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $items->links() }}
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Update search functionality to submit form on input
            const searchInput = document.getElementById('itemSearch');
            let timeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                });
            }

            // Existing delete confirmation
            document.querySelectorAll('.delete-item-form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will delete the item and all its variants. You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Generate barcodes
            document.getElementById('generateBarcodes').addEventListener('click', function() {
                fetch('{{ route('items.generateBarcodes') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire({
                              title: 'Success!',
                              text: 'Barcodes generated successfully!',
                              icon: 'success',
                              confirmButtonText: 'OK'
                          });
                      } else {
                          Swal.fire({
                              title: 'Error!',
                              text: 'Failed to generate barcodes: ' + data.error,
                              icon: 'error',
                              confirmButtonText: 'OK'
                          });
                      }
                  });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .color-preview {
            display: inline-block;
            border: 1px solid #dee2e6;
        }
    </style>
@endpush
@endsection
