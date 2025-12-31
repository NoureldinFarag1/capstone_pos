@extends('layouts.dashboard')
@section('title', 'Items')

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
                                    @foreach($brands->sortBy('name') as $brand)
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
                                                                 class="brand-logo rounded-circle">
                                                        @else
                                                            <div class="brand-logo-placeholder rounded-circle d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <span class="brand-name">{{ $brand->name }}</span>
                                                </label>
                                            </div>
                                            <a href="{{ route('items.exportCSV', ['brand_id' => $brand->id]) }}"
                                               class="btn btn-outline-success btn-sm"
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
            @include('items.partials._breadcrumbs', ['current' => 'All Items'])
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-end gap-3">
                    <h1 class="fw-bold mb-0">All Items</h1>
                    <span class="text-muted small">Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() ?? 0 }} items</span>
                </div>
                @include('items.partials._header_actions')
            </div>

            <!-- Clear Filters and Show All Items Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                    <a href="{{ route('items.index') }}"
                       class="btn btn-outline-secondary {{ !request()->has('search') && !request()->has('brand_id') && !request()->has('show_all') ? 'disabled' : '' }}">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </a>
                    <a href="{{ route('items.index', ['show_all' => 1]) }}"
                       class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>Show All Items
                    </a>
                </div>
            </div>

            <!-- Bulk Selection Form -->
            <form id="bulkPrintForm" method="POST" action="{{ route('items.printLabels') }}" style="display: none;">
                @csrf
                <input type="hidden" name="item_ids" id="selectedItemIds">
            </form>

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
                                @include('items.partials._item-card', ['item' => $item])
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
                button.innerHTML = '<i class="fas fa-barcode"></i> Create Barcodes';
            });
        });
    }

    // Bulk print labels functionality
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkPrintBtn = document.getElementById('bulkPrintBtn');
    const bulkPrintForm = document.getElementById('bulkPrintForm');
    const selectedItemIds = document.getElementById('selectedItemIds');

    function updateBulkPrintButton() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        bulkPrintBtn.disabled = checkedBoxes.length === 0;

        if (checkedBoxes.length > 0) {
            bulkPrintBtn.innerHTML = `<i class="fas fa-print me-2"></i>Print ${checkedBoxes.length} Selected Label${checkedBoxes.length > 1 ? 's' : ''}`;
        } else {
            bulkPrintBtn.innerHTML = '<i class="fas fa-print me-2"></i>Print Selected Labels';
        }
    }

    // Handle checkbox changes
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkPrintButton);
    });

    // Handle bulk print button click
    if (bulkPrintBtn) {
        bulkPrintBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            const itemIds = Array.from(checkedBoxes).map(cb => cb.value);

            if (itemIds.length > 0) {
                selectedItemIds.value = JSON.stringify(itemIds);
                bulkPrintForm.submit();
            }
        });
    }

    // Initialize bulk print button state
    updateBulkPrintButton();
});
</script>
@endpush

@push('styles')
    <style>
        /* Header toolbar spacing */
        .btn-toolbar .btn { white-space: nowrap; }

        .color-preview {
            display: inline-block;
            border: 1px solid #dee2e6;
        }
        .brand-item {
            transition: all 0.3s ease;
            padding: 0.5rem 0.75rem !important; /* Reduced padding */
        }

        .brand-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px); /* Reduced transform */
        }

        .brand-logo-wrapper {
            width: 40px;           /* Adjusted width */
            height: 40px;          /* Adjusted height */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem; /* Adjusted margin */
        }

        .brand-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px solid #ced4da; /* Reduced border */
            box-shadow: 0 1px 3px rgba(0,0,0,0.08); /* Reduced shadow */
            transition: border-color 0.2s ease-in-out; /* Added transition */
        }

        .brand-logo-placeholder {
            width: 100%;
            height: 100%;
            background-color: #f8f9fa;
            border: 1px solid #ced4da; /* Reduced border */
            color: #6c757d;
            font-size: 1.1rem;       /* Adjusted font size */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-name {
            font-size: 0.9rem;       /* Adjusted font size */
            color: #495057;
            font-weight: 500;
        }

        .form-check-input:checked + .form-check-label .brand-logo {
            background-color: #e7f1ff;
        }

        /* Custom styles for print label functionality */
        .item-checkbox {
            transform: scale(1.2);
        }

        .bulk-actions {
            min-width: 200px;
        }

        /* Improve checkbox visibility on cards */
        .card .form-check { background: rgba(255, 255, 255, 0.92); border-radius: 6px; padding: 3px 4px; }
        .card:hover .form-check { background: rgba(255, 255, 255, 1); }

        /* Print labels button styling */
        .print-labels-section {
            text-align: center;
        }

        .print-labels-section .btn {
            margin-bottom: 0.25rem;
        }

        /* Card improvements */
        .card.hover-shadow { transition: box-shadow .2s, transform .2s; border: 1px solid #e9ecef; }
        .card.hover-shadow:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.12); }
        .card .badge { font-weight: 500; }
        .brand-logo-sm { filter: grayscale(15%); opacity: .9; }
    </style>
@endpush
@endsection
