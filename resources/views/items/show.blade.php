@extends('layouts.dashboard')
@section('content')
<br>
<div class="container">
    <div class="row">
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
                            @else
                                <p class="card-text">No barcode available.</p>
                            @endif
                        </div>
                        <div class="col-6">
                            <p class="card-text"><strong>Brand:</strong> {{ $item->brand->name }}</p>
                            <p class="card-text"><strong>Category:</strong> {{ $item->category->name }}</p>
                            <p class="card-text"><strong>Price:</strong> ${{ $item->priceAfterSale() }}</p>
                            @if($item->discount_type === 'percentage')
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">{{ $item->discount_value }}%</span></p>
                            @else
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">${{ $item->discount_value }}</span></p>
                            @endif
                            <p class="card-text"><strong>Quantity:</strong> {{ $item->quantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="printLabelBtn" class="btn btn-warning btn-sm">Print Label</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary btn-sm">Back to Items</a>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the button and modal
    const printBtn = document.getElementById('printLabelBtn');
    const modal = document.getElementById('printQuantityModal');

    // Add click event to print button
    printBtn.addEventListener('click', function() {
        // Show the modal using Bootstrap's modal
        $('#printQuantityModal').modal('show');
    });

    // Handle the confirm print button
    document.getElementById('confirmPrint').addEventListener('click', function() {
        const quantity = document.getElementById('labelQuantity').value;

        // Show loading state
        this.disabled = true;
        this.textContent = 'Printing...';

        // Make the AJAX request
        fetch(`/items/{{ $item->id }}/print-label`, {
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
                alert('Labels sent to printer successfully!');
            } else {
                alert('Failed to print labels: ' + data.error);
            }
            $('#printQuantityModal').modal('hide');
        })
        .catch(error => {
            alert('Error printing labels: ' + error);
        })
        .finally(() => {
            // Reset button state
            this.disabled = false;
            this.textContent = 'Print';
        });
    });
});
</script>
@endpush
@endsection
