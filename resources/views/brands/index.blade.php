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
                        <div class="discount-controls mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input brand-discount-toggle enhanced-toggle"
                                       type="checkbox"
                                       id="brandDiscountToggle{{ $brand->id }}"
                                       data-brand-id="{{ $brand->id }}"
                                       {{ $brand->has_discount ? 'checked' : '' }}>
                                <label class="form-check-label" for="brandDiscountToggle{{ $brand->id }}">
                                    Apply Brand Discount
                                </label>
                            </div>
                            <div class="brand-discount-inputs {{ $brand->has_discount ? '' : 'd-none' }}">
                                <select class="form-select form-select-sm mb-2 brand-discount-type"
                                        data-brand-id="{{ $brand->id }}">
                                    @php
                                        $firstItem = $brand->items()->first();
                                        $discountType = $firstItem ? $firstItem->discount_type : 'percentage';
                                        $discountValue = $firstItem ? $firstItem->discount_value : 0;
                                    @endphp
                                    <option value="percentage" {{ $discountType === 'percentage' ? 'selected' : '' }}>
                                        Percentage (%)
                                    </option>
                                    <option value="fixed" {{ $discountType === 'fixed' ? 'selected' : '' }}>
                                        Fixed Amount (EGP)
                                    </option>
                                </select>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           class="form-control brand-discount-value"
                                           data-brand-id="{{ $brand->id }}"
                                           placeholder="Enter discount"
                                           value="{{ $discountValue }}"
                                           min="0"
                                           step="0.01"
                                           required>
                                    <span class="input-group-text discount-symbol">
                                        {{ $discountType === 'percentage' ? '%' : 'EGP' }}
                                    </span>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a valid discount value
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            @if(!auth()->user()->hasRole('Trainee'))
                                <div>
                                    <a href="{{ route('brands.edit', $brand->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>
                                    <form action="{{ route('brands.destroy', $brand->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-search').select2({
            placeholder: 'Search brands...',
            allowClear: true,
            width: '100%'
        });
    });

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

    document.addEventListener('DOMContentLoaded', function () {
        // Handle brand discount toggle
        document.querySelectorAll('.brand-discount-toggle').forEach(toggle => {
            const brandId = toggle.dataset.brandId;
            const container = toggle.closest('.discount-controls');
            const inputsContainer = container.querySelector('.brand-discount-inputs');
            const typeSelect = container.querySelector('.brand-discount-type');
            const valueInput = container.querySelector('.brand-discount-value');
            const symbolSpan = container.querySelector('.discount-symbol');

            toggle.addEventListener('change', function() {
                const applyDiscount = this.checked;
                inputsContainer.classList.toggle('d-none', !applyDiscount);
                if (applyDiscount) {
                    valueInput.focus();
                } else {
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

            valueInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });

            valueInput.addEventListener('change', function() {
                if (!this.value) {
                    this.classList.add('is-invalid');
                    return;
                }

                if (toggle.checked) {
                    updateBrandDiscount(brandId, true);
                }
            });
        });

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
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    throw new Error(data.error || 'Failed to toggle brand discount');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error'
                });
            });
        }
    });
</script>
@endpush
