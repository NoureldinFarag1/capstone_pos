@extends('layouts.dashboard')
@section('title', 'Brand Management')

@push('styles')
    @vite('resources/css/brands.css')
@endpush

@section('content')
<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-dark mb-1">Brand Management</h1>
        <p class="text-muted">Manage your product brands and discount settings</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('brands.trash') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 shadow-sm">
            <i class="fas fa-trash-alt me-2"></i>Trash
        </a>
        <a href="{{ route('brands.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
            <i class="fas fa-plus me-2"></i>Add New Brand
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary text-white rounded-4 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="fw-bold mb-0">{{ $brands->count() }}</h3>
                        <p class="mb-0 opacity-75">Total Brands</p>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-tags fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-success text-white rounded-4 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="fw-bold mb-0">{{ $brands->where('has_discount', true)->count() }}</h3>
                        <p class="mb-0 opacity-75">With Discounts</p>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-percentage fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-info text-white rounded-4 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="fw-bold mb-0">{{ $brands->sum(function($brand) { return $brand->items()->where('quantity', '>', 0)->count(); }) }}</h3>
                        <p class="mb-0 opacity-75">Items Available</p>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-boxes fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-warning text-white rounded-4 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="fw-bold mb-0">{{ $brands->filter(function($brand) { return $brand->items()->count() > 0; })->count() }}</h3>
                        <p class="mb-0 opacity-75">Active Brands</p>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center search-controls">
            <div class="col-md-6">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" class="form-control ps-5 rounded-3 border-0 bg-light"
                           id="brandSearch" placeholder="Search brands...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select rounded-3 border-0 bg-light" id="discountFilter">
                    <option value="">All Brands</option>
                    <option value="with-discount">With Discount</option>
                    <option value="without-discount">Without Discount</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select rounded-3 border-0 bg-light" id="sortBy">
                    <option value="name">Sort by Name</option>
                    <option value="items">Sort by Items Count</option>
                    <option value="discount">Sort by Discount</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Brands Grid -->
<div class="row" id="brandsContainer">
    @foreach($brands->sortBy('name') as $brand)
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4 brand-card"
             data-name="{{ strtolower($brand->name) }}"
             data-has-discount="{{ $brand->has_discount ? 'true' : 'false' }}"
             data-items-count="{{ $brand->items()->where('quantity', '>', 0)->count() }}">

            <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift transition-all">
                <!-- Brand Header -->
                <div class="card-header border-0 bg-transparent p-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="brand-avatar me-3">
                            <img src="{{ asset('storage/' . $brand->picture) }}"
                                 alt="{{ $brand->name }}"
                                 class="rounded-circle border border-3 border-white shadow-sm"
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $brand->name }}</h5>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-box me-1"></i>
                                {{ $brand->items()->count() }} items
                                @if($brand->items()->where('quantity', '>', 0)->count() > 0)
                                    <span class="badge bg-success-subtle text-success ms-2 rounded-pill">
                                        {{ $brand->items()->where('quantity', '>', 0)->count() }} in stock
                                    </span>
                                @endif
                            </p>
                        </div>
                        @if($brand->has_discount)
                            <div class="discount-badge">
                                <span class="badge bg-danger-subtle text-danger rounded-pill">
                                    <i class="fas fa-tag me-1"></i>Discount
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4">
                    <!-- Discount Controls -->
                    <div class="discount-controls mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label fw-semibold mb-0">Brand Discount</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input brand-discount-toggle modern-toggle"
                                       type="checkbox"
                                       id="brandDiscountToggle{{ $brand->id }}"
                                       data-brand-id="{{ $brand->id }}"
                                       {{ $brand->has_discount ? 'checked' : '' }}>
                                <label class="form-check-label visually-hidden" for="brandDiscountToggle{{ $brand->id }}">
                                    Toggle discount
                                </label>
                            </div>
                        </div>

                        <div class="brand-discount-inputs {{ $brand->has_discount ? '' : 'd-none' }}">
                            <div class="row g-2">
                                <div class="col-6">
                                    <select class="form-select form-select-sm rounded-3 border-light brand-discount-type"
                                            data-brand-id="{{ $brand->id }}">
                                        @php
                                            $firstItem = $brand->items()->first();
                                            $discountType = $firstItem ? $firstItem->discount_type : 'percentage';
                                            $discountValue = $firstItem ? $firstItem->discount_value : 0;
                                        @endphp
                                        <option value="percentage" {{ $discountType === 'percentage' ? 'selected' : '' }}>
                                            Percentage
                                        </option>
                                        <option value="fixed" {{ $discountType === 'fixed' ? 'selected' : '' }}>
                                            Fixed Amount
                                        </option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                               class="form-control rounded-start-3 border-light brand-discount-value"
                                               data-brand-id="{{ $brand->id }}"
                                               placeholder="0"
                                               value="{{ $discountValue }}"
                                               min="0"
                                               step="0.01"
                                               required>
                                        <span class="input-group-text bg-light border-light rounded-end-3 discount-symbol">
                                            {{ $discountType === 'percentage' ? '%' : 'EGP' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                Please enter a valid discount value
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="card-footer border-0 bg-transparent p-4 pt-0">
                    <div class="d-flex justify-content-between align-items-center">
                        @if(!auth()->user()->hasRole('Trainee'))
                            <div class="btn-group" role="group">
                                <a href="{{ route('brands.edit', $brand->id) }}"
                                   class="btn btn-outline-primary btn-sm rounded-start-3">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <form action="{{ route('brands.destroy', $brand->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-end-3 delete-btn">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        @endif

                        <a href="{{ route('brands.printLabels', $brand->id) }}"
                           class="btn btn-success btn-sm rounded-3"
                           title="Print labels for all items in this brand with quantity > 0">
                            <i class="fas fa-print me-1"></i>Print Labels
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Empty State -->
<div id="emptyState" class="empty-state d-none">
    <i class="fas fa-search"></i>
    <h4>No brands found</h4>
    <p>Try adjusting your search criteria</p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search and filter functionality
    initializeFilters();
    initializeDeleteConfirmation();
    initializeDiscountToggle();
});

function initializeFilters() {
    const searchInput = document.getElementById('brandSearch');
    const discountFilter = document.getElementById('discountFilter');
    const sortBy = document.getElementById('sortBy');

    [searchInput, discountFilter, sortBy].forEach(element => {
        if (element) {
            element.addEventListener(element.type === 'text' ? 'input' : 'change', filterBrands);
        }
    });

    function filterBrands() {
        const searchTerm = searchInput.value.toLowerCase();
        const discountFilter = document.getElementById('discountFilter').value;
        const sortBy = document.getElementById('sortBy').value;
        const brandCards = document.querySelectorAll('.brand-card');
        const container = document.getElementById('brandsContainer');
        const emptyState = document.getElementById('emptyState');

        let visibleCards = [];

        brandCards.forEach(card => {
            const name = card.dataset.name;
            const hasDiscount = card.dataset.hasDiscount === 'true';
            const itemsCount = parseInt(card.dataset.itemsCount);
            let show = true;

            // Apply filters
            if (searchTerm && !name.includes(searchTerm)) show = false;
            if (discountFilter === 'with-discount' && !hasDiscount) show = false;
            if (discountFilter === 'without-discount' && hasDiscount) show = false;

            if (show) {
                card.style.display = 'block';
                visibleCards.push({ element: card, name, itemsCount, hasDiscount });
            } else {
                card.style.display = 'none';
            }
        });

        // Sort visible cards
        visibleCards.sort((a, b) => {
            switch (sortBy) {
                case 'items': return b.itemsCount - a.itemsCount;
                case 'discount': return b.hasDiscount - a.hasDiscount;
                default: return a.name.localeCompare(b.name);
            }
        });

        // Reorder elements
        visibleCards.forEach(card => container.appendChild(card.element));

        // Toggle empty state
        emptyState.classList.toggle('d-none', visibleCards.length > 0);
    }
}

function initializeDeleteConfirmation() {
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');

            Swal.fire({
                title: 'Delete Brand?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'rounded-3',
                    cancelButton: 'rounded-3'
                }
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
}

function initializeDiscountToggle() {
    document.querySelectorAll('.brand-discount-toggle').forEach(toggle => {
        const brandId = toggle.dataset.brandId;
        const container = toggle.closest('.discount-controls');
        const inputsContainer = container.querySelector('.brand-discount-inputs');
        const typeSelect = container.querySelector('.brand-discount-type');
        const valueInput = container.querySelector('.brand-discount-value');
        const symbolSpan = container.querySelector('.discount-symbol');

        toggle.addEventListener('change', function() {
            const applyDiscount = this.checked;

            if (applyDiscount) {
                inputsContainer.classList.remove('d-none');
                inputsContainer.classList.add('slide-down');
                setTimeout(() => valueInput.focus(), 300);
            } else {
                inputsContainer.classList.add('slide-up');
                setTimeout(() => {
                    inputsContainer.classList.add('d-none');
                    inputsContainer.classList.remove('slide-up');
                }, 300);
                updateBrandDiscount(brandId, false);
            }
        });

        typeSelect.addEventListener('change', function() {
            const isPercentage = this.value === 'percentage';
            symbolSpan.textContent = isPercentage ? '%' : 'EGP';
            valueInput.max = isPercentage ? '100' : '';
            valueInput.step = isPercentage ? '1' : '0.01';

            if (toggle.checked && valueInput.value) {
                updateBrandDiscount(brandId, true);
            }
        });

        valueInput.addEventListener('input', () => {
            valueInput.classList.remove('is-invalid');
        });

        valueInput.addEventListener('change', function() {
            if (!this.value) {
                this.classList.add('is-invalid');
                return;
            }
            if (toggle.checked) updateBrandDiscount(brandId, true);
        });
    });
}

function updateBrandDiscount(brandId, applyDiscount) {
    const container = document.querySelector(`[data-brand-id="${brandId}"]`).closest('.discount-controls');
    const discountType = container.querySelector('.brand-discount-type').value;
    const discountValue = container.querySelector('.brand-discount-value').value;

    fetch(`/brands/${brandId}/toggle-discount`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            apply_discount: applyDiscount,
            discount_type: discountType,
            discount_value: discountValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDiscountBadge(container, applyDiscount);
            showSuccessToast(data.message);
        } else {
            throw new Error(data.error || 'Failed to toggle brand discount');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert(error.message);
    });
}

function updateDiscountBadge(container, applyDiscount) {
    const card = container.closest('.card');
    const badge = card.querySelector('.discount-badge');

    if (applyDiscount && !badge) {
        const newBadge = document.createElement('div');
        newBadge.className = 'discount-badge';
        newBadge.innerHTML = '<span class="badge bg-danger-subtle text-danger rounded-pill"><i class="fas fa-tag me-1"></i>Discount</span>';
        card.querySelector('.card-header .d-flex').appendChild(newBadge);
    } else if (!applyDiscount && badge) {
        badge.remove();
    }
}

function showSuccessToast(message) {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        customClass: { popup: 'rounded-4' }
    });
}

function showErrorAlert(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        customClass: { popup: 'rounded-4' }
    });
}
</script>
@endpush
