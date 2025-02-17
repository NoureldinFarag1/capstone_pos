@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="row g-4">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 sidebar-container">
            <div class="sidebar-wrapper">
                <div class="card shadow-sm border-0 position-sticky" style="top: 1rem;">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('items.index') }}" method="GET" id="filterForm">
                            <!-- Add hidden input for show_all parameter -->
                            <input type="hidden" name="show_all" value="{{ request('show_all') }}">
                            <!-- Search -->
                            <div class="mb-4">
                                <label class="form-label text-muted small text-uppercase">Search Items</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0 bg-transparent">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control border-start-0"
                                           id="itemSearch"
                                           name="search"
                                           placeholder="Type to search..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Brands Filter -->
                            <div class="mb-4">
                                <label class="form-label text-muted small text-uppercase">Select Brand</label>
                                <div class="brands-list bg-light rounded p-3">
                                    <!-- Export All Brands button at the top -->
                                    <div class="mb-3">
                                        <a href="{{ route('items.exportCSV') }}" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-file-export me-1"></i> Export All Brands
                                        </a>
                                    </div>

                                    <!-- Individual Brands -->
                                    @foreach($brands as $brand)
                                        <div class="brand-item d-flex align-items-center mb-2 p-2 rounded hover-bg-light">
                                            <div class="form-check flex-grow-1">
                                                <input type="radio"
                                                       class="form-check-input"
                                                       name="brand_id"
                                                       id="brand_{{ $brand->id }}"
                                                       value="{{ $brand->id }}"
                                                       {{ request('brand_id') == $brand->id ? 'checked' : '' }}
                                                       onchange="document.getElementById('filterForm').submit()">
                                                <label class="form-check-label d-flex align-items-center cursor-pointer"
                                                       for="brand_{{ $brand->id }}">
                                                    <div class="brand-logo-wrapper">
                                                        @if($brand->picture)
                                                            <img src="{{ asset('storage/' . $brand->picture) }}"
                                                                 alt="{{ $brand->name }}"
                                                                 class="brand-logo">
                                                        @else
                                                            <i class="fas fa-building text-secondary"></i>
                                                        @endif
                                                    </div>
                                                    <span class="brand-name">{{ $brand->name }}</span>
                                                </label>
                                            </div>
                                            <a href="{{ route('items.exportCSV', ['brand_id' => $brand->id]) }}"
                                               class="btn btn-outline-success btn-sm ms-2"
                                               title="Export {{ $brand->name }}">
                                                <i class="fas fa-file-export"></i>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9 ps-lg-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold">Items</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('items.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add New Item
                    </a>
                    <button id="generateBarcodeBtn" class="btn btn-secondary">
                        <i class="fas fa-barcode"></i> Generate Barcodes
                    </button>
                </div>
            </div>

            <!-- Clear Filters and Show All Items Buttons -->
            <div class="d-flex justify-content-start gap-2 mb-4">
                <a href="{{ route('items.index') }}"
                   class="btn btn-outline-secondary {{ !request()->has('search') && !request()->has('brand_id') && !request()->has('show_all') ? 'disabled' : '' }}">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </a>
                <a href="{{ route('items.index', ['show_all' => 1]) }}"
                   class="btn btn-outline-primary">
                    <i class="fas fa-list me-2"></i>Show All Items
                </a>
            </div>

            <!-- Items Grid -->
            @if($items->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No items found matching your criteria.
                </div>
            @else
                <div class="row g-4">
                    @foreach($items as $item)
                        @if($item->is_parent)
                            <div class="col-md-6 col-xl-4">
                                <div class="card shadow-sm border-0 h-100 hover-shadow transition">
                                    @if($item->brand->logo)
                                        <div class="card-header bg-light border-0 py-2">
                                            <img src="{{ asset('storage/' . $item->brand->logo) }}"
                                                 alt="{{ $item->brand->name }}"
                                                 class="brand-logo-sm"
                                                 style="height: 30px; object-fit: contain;">
                                        </div>
                                    @endif
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
            @endif

            <!-- Pagination and Results -->
            <div class="d-flex justify-content-center mt-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Search input functionality
    const searchInput = document.getElementById('itemSearch');
    if (searchInput) {
        searchInput.focus();
        let timeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    }

    // Delete confirmation
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

    // Barcode generation
    const generateBarcodesBtn = document.getElementById('generateBarcodeBtn');
    if (generateBarcodesBtn) {
        generateBarcodesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

            fetch('{{ route("items.generate-barcodes") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error('Server response: ' + text);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: `Generated ${data.processed} barcodes successfully!`,
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.error || 'Failed to generate barcodes');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error'
                });
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-barcode"></i> Generate Barcodes';
            });
        });
    }
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
