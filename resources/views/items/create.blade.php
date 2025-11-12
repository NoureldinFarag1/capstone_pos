@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Add Item</h2>
            <a href="{{ route('items.index') }}" class="btn btn-light">← Back to All Items</a>
        </div>
        <div class="card-body">
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" id="createItemForm">
                @csrf

                <!-- Basic Information Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Item Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                                <div class="form-text">Enter a descriptive name for the item</div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="category_id" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" selected disabled>Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="brand_id" class="form-label">Brand <span
                                        class="text-danger">*</span></label>
                                <select name="brand_id" class="form-select" required>
                                    <option value="" selected disabled>Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="picture" class="form-label">Item Picture</label>
                            <div class="input-group">
                                <input type="file" name="picture" class="form-control" accept="image/*"
                                    id="pictureInput">
                                <label class="input-group-text" for="pictureInput">Browse</label>
                            </div>
                            <div id="imagePreview" class="mt-2 d-none">
                                <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Pricing Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="buying_price" class="form-label">Buying Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">EGP</span>
                                    <input type="number" name="buying_price" class="form-control" min="0" step="0.01"
                                        placeholder="0">
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="selling_price" class="form-label">Selling Price <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">EGP</span>
                                    <input type="number" name="selling_price" class="form-control" min="0" step="0.01"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="tax" class="form-label">Tax Rate</label>
                                <div class="input-group">
                                    <input type="number" name="tax" class="form-control" min="0" max="100" placeholder="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="quantity" class="form-label">Initial Stock <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="discount_type" class="form-label">Discount Type</label>
                                <select id="discount_type" name="discount_type" class="form-select">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount (EGP)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="discount_value" class="form-label">Discount Value</label>
                                <input type="number" id="discount_value" name="discount_value" class="form-control"
                                    min="0" placeholder="0">
                                <div id="discountHelp" class="form-text"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variants Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Item Variants</h5>
                    </div>
                    <div class="card-body">
                        <!-- Sizes -->
                        <div class="mb-4">
                            <label class="form-label">Available Sizes <span class="text-danger">*</span></label>

                            <!-- N/A Size Option -->
                            <div class="mb-3">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($sizes->where('name', 'N/A') as $size)
                                    <div class="form-check">
                                        <input type="checkbox" name="sizes[]" value="{{ $size->id }}"
                                            class="form-check-input" id="size{{ $size->id }}">
                                        <label class="form-check-label px-3 py-2 border rounded-3"
                                            for="size{{ $size->id }}">
                                            {{ $size->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Clothes Sizes -->
                            <div class="mb-3">
                                <h6>Clothes Sizes</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($sizes->where('type', 'clothes')->where('name', '!=', 'N/A')->sortBy('name') as $size)
                                    <div class="form-check">
                                        <input type="checkbox" name="sizes[]" value="{{ $size->id }}"
                                            class="form-check-input" id="size{{ $size->id }}">
                                        <label class="form-check-label px-3 py-2 border rounded-3"
                                            for="size{{ $size->id }}">
                                            {{ $size->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Shoes Sizes -->
                            <div class="mb-3">
                                <h6>Shoes Sizes</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($sizes->where('type', 'shoes')->where('name', '!=', 'N/A')->sortBy('name') as $size)
                                    <div class="form-check">
                                        <input type="checkbox" name="sizes[]" value="{{ $size->id }}"
                                            class="form-check-input" id="size{{ $size->id }}">
                                        <label class="form-check-label px-3 py-2 border rounded-3"
                                            for="size{{ $size->id }}">
                                            {{ $size->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Colors -->
                        <div class="mb-3">
                            <label class="form-label">Available Colors</label>
                            <!-- N/A Color Option -->
                            <div class="mb-2">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($colors->where('name', 'N/A') as $color)
                                    <div class="form-check">
                                        <input type="checkbox" name="colors[]" value="{{ $color->id }}"
                                            class="form-check-input" id="color{{ $color->id }}">
                                        <label class="form-check-label d-flex align-items-center gap-2"
                                            for="color{{ $color->id }}">
                                            <span class="color-preview rounded-circle border"
                                                style="width: 20px; height: 20px; background-color: {{ $color->hex_code }};"></span>
                                            {{ $color->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Other Colors -->
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($colors->where('name', '!=', 'N/A')->sortBy('name') as $color)
                                <div class="form-check">
                                    <input type="checkbox" name="colors[]" value="{{ $color->id }}"
                                        class="form-check-input" id="color{{ $color->id }}">
                                    <label class="form-check-label d-flex align-items-center gap-2"
                                        for="color{{ $color->id }}">
                                        <span class="color-preview rounded-circle border"
                                            style="width: 20px; height: 20px; background-color: {{ $color->hex_code }};"></span>
                                        {{ $color->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Variant Quantities Preview -->
                        <div id="variantQuantities" class="mt-4 d-none">
                            <h6>Set Quantities for Each Variant</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Variant</th>
                                            <th>Size</th>
                                            <th>Color</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variantQuantitiesBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('items.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pictureInput = document.getElementById('pictureInput');
    const imagePreview = document.getElementById('imagePreview');

    pictureInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.querySelector('img').src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    const discountType = document.getElementById('discount_type');
    const discountValue = document.getElementById('discount_value');
    const discountHelp = document.getElementById('discountHelp');

    function updateDiscountHelp() {
        const type = discountType.value;
        const value = discountValue.value;
        if (type === 'percentage') {
            discountHelp.textContent = `${value}% will be deducted from the selling price`;
        } else {
            discountHelp.textContent = `EGP ${value} will be deducted from the selling price`;
        }
    }

    discountType.addEventListener('change', updateDiscountHelp);
    discountValue.addEventListener('input', updateDiscountHelp);

    const form = document.getElementById('createItemForm');
    form.addEventListener('submit', function(e) {
        const sizes = document.querySelectorAll('input[name="sizes[]"]:checked');
        if (sizes.length === 0) {
            e.preventDefault();
            alert('Please select at least one size');
        }
    });

    // Handle variant quantity preview
    function updateVariantQuantities() {
        const selectedSizes = Array.from(document.querySelectorAll('input[name="sizes[]"]:checked')).map(
            input => ({
                id: input.value,
                name: input.nextElementSibling.textContent.trim()
            }));

        const selectedColors = Array.from(document.querySelectorAll('input[name="colors[]"]:checked')).map(
            input => ({
                id: input.value,
                name: input.nextElementSibling.textContent.trim(),
                hex: input.nextElementSibling.querySelector('.color-preview').style.backgroundColor
            }));

        const variantQuantitiesDiv = document.getElementById('variantQuantities');
        const tbody = document.getElementById('variantQuantitiesBody');
        tbody.innerHTML = '';

        if (selectedSizes.length && selectedColors.length) {
            variantQuantitiesDiv.classList.remove('d-none');

            selectedColors.forEach(color => {
                selectedSizes.forEach(size => {
                    const tr = document.createElement('tr');
                    const variantName = document.querySelector('input[name="name"]').value;

                    tr.innerHTML = `
                            <td>${variantName}</td>
                            <td>${size.name}</td>
                            <td>
                                <span class="d-flex align-items-center gap-2">
                                    <span class="color-preview rounded-circle" style="width: 15px; height: 15px; background-color: ${color.hex}"></span>
                                    ${color.name}
                                </span>
                            </td>
                            <td>
                                <input type="number"
                                       name="variant_quantities[${size.id}][${color.id}]"
                                       class="form-control form-control-sm variant-quantity"
                                       style="width: 100px"
                                       min="0"
                                       value="0">
                            </td>
                        `;
                    tbody.appendChild(tr);
                });
            });
        } else {
            variantQuantitiesDiv.classList.add('d-none');
        }
    }

    // Add event listeners for size and color checkboxes
    document.querySelectorAll('input[name="sizes[]"], input[name="colors[]"]').forEach(input => {
        input.addEventListener('change', updateVariantQuantities);
    });

    // Remove the original quantity input field since we're using variant quantities
    document.querySelector('input[name="quantity"]').closest('.col-md-3').remove();
});
</script>
@endpush

<style>
.form-check-label {
    cursor: pointer;
}

.form-check-input:checked+.form-check-label {
    background-color: #e9ecef;
    border-color: #0d6efd;
}

.color-preview {
    display: inline-block;
    border: 1px solid #dee2e6;
}
</style>
@endsection
