@extends('layouts.dashboard')

@section('content')
<div class="container">
    @php($returnUrl = session('items.return_url') ?? route('items.index'))
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                @if($item->picture)
                    <img src="{{ asset('storage/' . $item->picture) }}" alt="Thumbnail" class="rounded border border-light" style="width:48px;height:48px;object-fit:cover;">
                @else
                    <div class="rounded bg-light text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-box-seam" style="font-size:1.25rem"></i>
                    </div>
                @endif
                <div>
                    <h2 class="mb-1 h4">Edit {{ $item->is_parent ? 'Item' : 'Variant' }}: <span class="fw-semibold">{{ $item->name }}</span></h2>
                    <div class="d-flex flex-wrap align-items-center gap-2 small">
                        <span class="badge bg-light text-dark">Brand: {{ $brands->firstWhere('id', $item->brand_id)->name ?? '-' }}</span>
                        <span class="badge bg-light text-dark">Category: {{ $categories->firstWhere('id', $item->category_id)->name ?? '-' }}</span>
                        @if(!empty($item->code))
                            <span class="badge bg-light text-dark">Code: {{ $item->code }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ $returnUrl }}" class="btn btn-light">← Back</a>
        </div>
        @if($item->is_parent)
            <div class="bg-light border-bottom py-2 px-3 d-flex flex-wrap align-items-center gap-3">
                <span class="text-muted small">Variants: <strong>{{ $item->variants->count() }}</strong></span>
                <span class="text-muted small">Total Qty: <strong>{{ $item->variants->sum('quantity') }}</strong></span>
                <span class="text-muted small">Price: <strong>{{ number_format($item->selling_price, 2) }}</strong></span>
            </div>
        @endif
        <div class="card-body">
            <!-- Common Fields -->
            <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data" id="editItemForm">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header text-white">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Item Name</label>
                     <input type="text" name="name" class="form-control" value="{{ $item->name }}" {{ !$item->is_parent ? 'readonly' : '' }}
                         @if(!$item->is_parent) data-bs-toggle="tooltip" title="Edit name on the parent item only" @endif>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                     <select name="category_id" class="form-select" {{ !$item->is_parent ? 'disabled' : '' }}
                          @if(!$item->is_parent) data-bs-toggle="tooltip" title="Change category on the parent item only" @endif>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Brand</label>
                    <select name="brand_id" class="form-select" {{ !$item->is_parent ? 'disabled' : '' }}
                        @if(!$item->is_parent) data-bs-toggle="tooltip" title="Change brand on the parent item only" @endif>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Picture -->
                        <div class="mb-3">
                            <label class="form-label">Item Picture</label>
                <input type="file" name="picture" class="form-control" {{ !$item->is_parent ? 'disabled' : '' }}
                    @if(!$item->is_parent) data-bs-toggle="tooltip" title="Update picture on the parent item only" @endif>
                            @if($item->picture)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $item->picture) }}" alt="Current Image" class="img-thumbnail" style="max-height: 100px">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pricing Details -->
                <div class="card mb-4">
                    <div class="card-header text-white">
                        <h5 class="mb-0">Pricing Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Selling Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">EGP</span>
                         <input type="number" name="selling_price" class="form-control" value="{{ $item->selling_price }}" {{ !$item->is_parent ? 'readonly' : '' }}
                             @if(!$item->is_parent) data-bs-toggle="tooltip" title="Price is inherited from parent item" @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tax Rate</label>
                                    <div class="input-group">
                         <input type="number" name="tax" class="form-control" value="{{ $item->tax }}" {{ !$item->is_parent ? 'readonly' : '' }}
                             @if(!$item->is_parent) data-bs-toggle="tooltip" title="Tax is inherited from parent item" @endif>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type</label>
                     <select name="discount_type" class="form-select" {{ !$item->is_parent ? 'disabled' : '' }}
                          @if(!$item->is_parent) data-bs-toggle="tooltip" title="Discount type is inherited from parent item" @endif>
                                        <option value="percentage" {{ $item->discount_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ $item->discount_type == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Discount Value</label>
                     <input type="number" name="discount_value" class="form-control" value="{{ $item->discount_value }}" {{ !$item->is_parent ? 'readonly' : '' }}
                         @if(!$item->is_parent) data-bs-toggle="tooltip" title="Discount value is inherited from parent item" @endif>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($item->is_parent)
                    <!-- Variants Table -->
                    <div class="card mb-4">
                        <div class="card-header text-white d-flex justify-content-between align-items-center sticky-top" style="top: 0; z-index: 1;">
                            <h5 class="mb-0">Variants <span class="badge bg-secondary">{{ $item->variants->count() }}</span></h5>
                            <button type="button" class="btn btn-success btn-sm" id="saveAllQuantities">
                                Save All Changes
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 420px; overflow: auto;">
                                <table class="table table-sm table-striped table-hover align-middle">
                                    <thead>
                                        <tr class="table-light" style="position: sticky; top: 0; z-index: 2;">
                                            <th>Variant</th>
                                            <th>Size</th>
                                            <th>Color</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($item->variants as $variant)
                                            <tr data-variant-id="{{ $variant->id }}">
                                                <td>{{ $variant->name }}</td>
                                                <td>{{ $variant->sizes->first()->name ?? '-' }}</td>
                                                <td>
                                                    @if($variant->colors->first())
                                                        <span class="d-flex align-items-center gap-2">
                                                            @if($variant->colors->first()->name != 'N/A')
                                                                <span class="color-preview rounded-circle"
                                                                    style="width: 15px; height: 15px; background-color: {{ $variant->colors->first()->hex_code }};"></span>
                                                            @endif
                                                            {{ $variant->colors->first()->name }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm quantity-input"
                                                           value="{{ $variant->quantity }}" min="0" style="width: 100px">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if(!$hasNASize || !$hasNAColor)
                    <!-- Add New Variant Section -->
                    <div class="card mb-4">
                        <div class="card-header text-white">
                            <h5 class="mb-0">Add New Variant</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if(!$hasNASize)
                                <div class="col-md-4 mb-3">
                                    <label for="new_variant_size" class="form-label">Size</label>
                                    <select id="new_variant_size" class="form-select">
                                        <option value="" selected disabled>Select Size</option>
                                        @foreach($sizes->where('name', '!=', 'N/A') as $size)
                                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                @if(!$hasNAColor)
                                <div class="col-md-4 mb-3">
                                    <label for="new_variant_color" class="form-label">Color</label>
                                    <select id="new_variant_color" class="form-select">
                                        <option value="" selected disabled>Select Color</option>
                                        @foreach($colors->where('name', '!=', 'N/A') as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-md-4 mb-3">
                                    <label for="new_variant_quantity" class="form-label">Quantity</label>
                                    <input type="number" id="new_variant_quantity" class="form-control" min="0">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" id="addVariantBtn">Add Variant</button>
                        </div>
                    </div>
                    @endif
                @else
                    <!-- Single Variant Quantity -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Stock Quantity</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" value="{{ $item->quantity }}" min="0">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Sticky Action Bar -->
                <div class="d-flex justify-content-end gap-2 p-3 bg-white border-top position-sticky" style="bottom: 0; z-index: 5;">
                    <a href="{{ $returnUrl }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update {{ $item->is_parent ? 'Item' : 'Variant' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        if (window.bootstrap && window.bootstrap.Tooltip) {
            new window.bootstrap.Tooltip(tooltipTriggerEl);
        }
    });

    const saveAllBtn = document.getElementById('saveAllQuantities');
    if (saveAllBtn) {
        saveAllBtn.addEventListener('click', function() {
            saveAllBtn.disabled = true;
            saveAllBtn.textContent = 'Saving...';

            const updates = [];
            document.querySelectorAll('tr[data-variant-id]').forEach(row => {
                updates.push({
                    id: row.dataset.variantId,
                    quantity: parseInt(row.querySelector('.quantity-input').value) || 0
                });
            });

            fetch('/items/update-variants-quantity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ updates: updates })
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
                saveAllBtn.disabled = false;
                saveAllBtn.textContent = 'Save All Changes';
            });
        });
    }

    const addVariantBtn = document.getElementById('addVariantBtn');
    addVariantBtn.addEventListener('click', function() {
        let sizeId = document.getElementById('new_variant_size').value;
        let colorId = document.getElementById('new_variant_color').value;
        const quantity = document.getElementById('new_variant_quantity').value;

        // Auto-select "N/A" if size or color is not chosen
        if (!sizeId) {
            sizeId = "{{ $sizes->where('name', 'N/A')->first()->id }}";
        }
        if (!colorId) {
            colorId = "{{ $colors->where('name', 'N/A')->first()->id }}";
        }

        if (quantity <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid quantity.'
            });
            return;
        }

        // Make AJAX request to add the new variant
        fetch('/items/add-variant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                parent_id: {{ $item->id }},
                size_id: sizeId,
                color_id: colorId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Variant added successfully!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to add variant: ' + data.error
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error adding variant: ' + error
            });
        });
    });

    const form = document.getElementById('editItemForm');
    if (form) {
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
    }
});
</script>
@endpush

<style>
.color-preview {
    display: inline-block;
    border: 1px solid #dee2e6;
}
</style>
@endsection
