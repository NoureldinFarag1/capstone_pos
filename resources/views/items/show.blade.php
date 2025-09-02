@extends('layouts.dashboard')
@section('content')
<div class="container py-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Items
        </a>
    </div>

    <div class="row">
        <!-- Parent Item Details -->
        <div class="col-md-4">
            <div class="card mb-3 shadow-sm">
                <div class="d-flex justify-content-between p-3 border-bottom">
                    <h5 class="card-header-title mb-0">Item Details</h5>
                    @role('admin|moderator')
                    <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endrole
                </div>
                <div class="item-image-container">
                    <img src="{{ asset('storage/' . $item->picture) }}" alt="{{ $item->name }}"
                        class="card-img-top item-image">
                </div>
                <div class="card-body">
                    <h4 class="card-title text-center mb-3">{{ $item->name }}</h4>
                    <div class="item-metadata mb-3">
                        <span class="badge bg-secondary">ID: {{ $item->id }}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            @if($item->barcode)
                                <img src="{{ asset('storage/' . $item->barcode) }}" alt="Barcode" class="img-fluid">
                                <p class="card-text">Barcode: {{ $item->code }}</p>
                            @endif
                        </div>
                        <div class="col-6">
                            <p class="card-text"><strong>Brand:</strong> {{ optional($item->brand)->name ?? '-' }}</p>
                            <p class="card-text"><strong>Category:</strong> {{ optional($item->category)->name ?? '-' }}</p>
                            <p class="card-text"><strong>Base Price:</strong> EGP{{ $item->selling_price }}</p>
                            @if($item->discount_type === 'percentage')
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">{{ $item->discount_value }}%</span>
                                </p>
                            @else
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">EGP{{ $item->discount_value }}</span>
                                </p>
                            @endif
                            <p class="card-text"><strong>Selling Price:</strong> EGP{{ $item->priceAfterSale() }}</p>
                            <p class="card-text"><strong>Total Stock:</strong> {{ $item->quantity }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge {{ $item->quantity > 0 ? 'bg-success' : 'bg-danger' }} badge-lg">
                            {{ $item->quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span>
                        <span class="text-muted small">Last updated: {{ $item->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variants Table -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Item Variants</h5>
                        @role('admin|moderator')
                        <button type="button" class="btn btn-primary btn-sm" id="saveAllQuantities">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        @endrole
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Variant</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th class="text-center">Stock</th>
                                    <th>Barcode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->variants as $variant)
                                    <tr data-variant-id="{{ $variant->id }}" class="variant-row">
                                        <td>{{ $variant->name }}</td>
                                        <td>{{ $variant->sizes->first()->name ?? '-' }}</td>
                                        <td>
                                            @if($variant->colors->first())
                                                <span class="d-flex align-items-center gap-2">
                                                    <span class="color-preview rounded-circle"
                                                        style="width: 15px; height: 15px; background-color: {{ $variant->colors->first()->hex_code }};"></span>
                                                    {{ $variant->colors->first()->name }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                @role('admin|moderator')
                                                <input type="number" class="form-control form-control-sm quantity-input"
                                                    value="{{ $variant->quantity }}" min="0" style="width: 80px;">
                                                @else
                                                <input type="number" class="form-control form-control-sm quantity-input"
                                                    value="{{ $variant->quantity }}" min="0" style="width: 80px;" disabled>
                                                @endrole
                                                <span class="stock-status">
                                                    @if($variant->quantity == 0)
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    @elseif($variant->quantity <= 5) <span class="badge bg-warning">Low
                                                        Stock</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($variant->barcode)
                                                <img src="{{ asset('storage/' . $variant->barcode) }}" alt="Barcode"
                                                    class="img-fluid" style="max-height: 30px">
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm print-label"
                                                data-variant-id="{{ $variant->id }}">
                                                Print Label
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .item-metadata {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get all print buttons
            const printBtns = document.querySelectorAll('.print-label');

            // Add click event to all print buttons
            printBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const variantId = this.dataset.variantId;

                    // Show SweetAlert input for quantity
                    Swal.fire({
                        title: 'Print Labels',
                        input: 'number',
                        inputLabel: 'Number of Labels to Print:',
                        inputAttributes: {
                            min: 1,
                            value: 1
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Print',
                        showLoaderOnConfirm: true,
                        preConfirm: (quantity) => {
                            return fetch(`/items/${variantId}/print-label`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    quantity: quantity
                                })
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(response.statusText);
                                    }
                                    return response.json();
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(
                                        `Request failed: ${error}`);
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (result.value.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Labels sent to printer successfully!'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to print labels: ' + result.value
                                        .error
                                });
                            }
                        }
                    });
                });
            });

            // Handle quantity updates
            const saveAllBtn = document.getElementById('saveAllQuantities');

            saveAllBtn.addEventListener('click', function () {
                // Show loading state
                saveAllBtn.disabled = true;
                saveAllBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                const updates = [];
                document.querySelectorAll('tr[data-variant-id]').forEach(row => {
                    updates.push({
                        id: row.dataset.variantId,
                        quantity: parseInt(row.querySelector('.quantity-input').value) || 0
                    });
                });

                // Send updates to server
                fetch('/items/update-variants-quantity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        updates: updates
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Quantities updated successfully!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            throw new Error(data.error || 'Failed to update quantities');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error updating quantities: ' + error.message
                        });
                    })
                    .finally(() => {
                        // Reset button state
                        saveAllBtn.disabled = false;
                        saveAllBtn.textContent = 'Save All Changes';
                    });
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });

        function exportVariants() {
            // Add export functionality here
            alert('Export feature coming soon!');
        }
    </script>
@endpush

<style>
    .color-preview {
        display: inline-block;
        border: 1px solid #dee2e6;
    }

    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .quantity-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        border-color: #80bdff;
    }

    @media print {

        .btn-group,
        .print-label {
            display: none;
        }
    }
</style>
@endsection
