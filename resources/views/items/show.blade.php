@extends('layouts.dashboard')
@section('content')
<div class="container">
    <div class="row">
        <!-- Parent Item Details -->
        <div class="col-md-4">
            <div class="card mb-3">
                <img src="{{ asset('storage/' . $item->picture) }}" alt="{{ $item->name }}" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title">{{ $item->name }}</h5>
                    <div class="row">
                        <div class="col-6">
                            @if($item->barcode)
                                <img src="{{ asset('storage/' .$item->barcode) }}" alt="Barcode" class="img-fluid">
                                <p class="card-text">Barcode: {{ $item->code }}</p>
                            @endif
                        </div>
                        <div class="col-6">
                            <p class="card-text"><strong>Brand:</strong> {{ $item->brand->name }}</p>
                            <p class="card-text"><strong>Category:</strong> {{ $item->category->name }}</p>
                            <p class="card-text"><strong>Base Price:</strong> EGP{{ $item->priceAfterSale() }}</p>
                            @if($item->discount_type === 'percentage')
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">{{ $item->discount_value }}%</span></p>
                            @else
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">EGP{{ $item->discount_value }}</span></p>
                            @endif
                            <p class="card-text"><strong>Total Stock:</strong> {{ $item->quantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variants Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Item Variants</h5>
                    <button type="button" class="btn btn-primary btn-sm" id="saveAllQuantities">
                        Save All Changes
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Variant</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Stock</th>
                                    <th>Barcode</th>
                                    <th>Actions</th>
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
                                                    <span class="color-preview rounded-circle"
                                                          style="width: 15px; height: 15px; background-color: {{ $variant->colors->first()->hex_code }};"></span>
                                                    {{ $variant->colors->first()->name }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number"
                                                   class="form-control form-control-sm quantity-input"
                                                   value="{{ $variant->quantity }}"
                                                   min="0"
                                                   style="width: 80px;">
                                        </td>
                                        <td>
                                            @if($variant->barcode)
                                                <img src="{{ asset('storage/' .$variant->barcode) }}"
                                                     alt="Barcode" class="img-fluid" style="max-height: 30px">
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-warning btn-sm print-label"
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

<!-- Print Quantity Modal -->
<div class="modal" id="printQuantityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print Labels</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="labelQuantity">Number of Labels to Print:</label>
                    <input type="number" class="form-control" id="labelQuantity" min="1" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmPrint">Print</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all print buttons
    const printBtns = document.querySelectorAll('.print-label');
    const modal = document.getElementById('printQuantityModal');

    // Add click event to all print buttons
    printBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const variantId = this.dataset.variantId;
            const confirmPrint = document.getElementById('confirmPrint');

            // Update the confirm button to know which variant to print
            confirmPrint.dataset.variantId = variantId;

            // Show the modal
            $('#printQuantityModal').modal('show');
        });
    });

    // Handle the confirm print button
    document.getElementById('confirmPrint').addEventListener('click', function() {
        const quantity = document.getElementById('labelQuantity').value;
        const variantId = this.dataset.variantId;

        // Show loading state
        this.disabled = true;
        this.textContent = 'Printing...';

        // Make the AJAX request
        fetch(`/items/${variantId}/print-label`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Labels sent to printer successfully!'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to print labels: ' + data.error
                });
            }
            $('#printQuantityModal').modal('hide');
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error printing labels: ' + error
            });
        })
        .finally(() => {
            // Reset button state
            this.disabled = false;
            this.textContent = 'Print';
        });
    });

    // Handle quantity updates
    const saveAllBtn = document.getElementById('saveAllQuantities');

    saveAllBtn.addEventListener('click', function() {
        // Show loading state
        saveAllBtn.disabled = true;
        saveAllBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

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
            // Reset button state
            saveAllBtn.disabled = false;
            saveAllBtn.textContent = 'Save All Changes';
        });
    });
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
