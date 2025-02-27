@extends('layouts.dashboard')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h2 class="mb-0">Exchange Items</h2>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('sales.exchange', $sale->id) }}" method="POST">
                        @csrf
                        <div id="exchangeItemsContainer">
                            <div class="exchange-item mb-3">
                                <div class="form-group">
                                    <label for="sale_item_id">Sale Item</label>
                                    <select name="exchange_items[0][sale_item_id]" class="form-control sale-item-select" required>
                                        @foreach($sale->saleItems as $saleItem)
                                            <option value="{{ $saleItem->id }}">{{ $saleItem->item->brand->name ?? 'No Brand' }} - {{ $saleItem->item->name }} (Quantity: {{ $saleItem->quantity }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="new_item_id">New Item</label>
                                    <select name="exchange_items[0][new_item_id]" class="form-control new-item-select" required>
                                        @foreach($items as $item)
                                            @if(!$item->is_parent)
                                                <option value="{{ $item->id }}">{{ $item->brand->name ?? 'No Brand' }} - {{ $item->name }} (Stock: {{ $item->quantity }})</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-danger remove-exchange-item mt-2">Remove</button>
                            </div>
                        </div>
                        <button type="button" id="addExchangeItemButton" class="btn btn-secondary mt-2">Add Another Exchange</button>
                        <button type="submit" class="btn btn-warning mt-2">Submit Exchanges</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    function initializeSelect2() {
        $('.sale-item-select, .new-item-select').select2({
            placeholder: 'Select an item',
            allowClear: true
        });
    }

    initializeSelect2();

    let exchangeItemIndex = 1;

    $('#addExchangeItemButton').click(function() {
        const newExchangeItem = `
            <div class="exchange-item mb-3">
                <div class="form-group">
                    <label for="sale_item_id">Sale Item</label>
                    <select name="exchange_items[${exchangeItemIndex}][sale_item_id]" class="form-control sale-item-select" required>
                        @foreach($sale->saleItems as $saleItem)
                            <option value="{{ $saleItem->id }}">{{ $saleItem->item->brand->name ?? 'No Brand' }} - {{ $saleItem->item->name }} (Quantity: {{ $saleItem->quantity }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_item_id">New Item</label>
                    <select name="exchange_items[${exchangeItemIndex}][new_item_id]" class="form-control new-item-select" required>
                        @foreach($items as $item)
                            @if(!$item->is_parent)
                                <option value="{{ $item->id }}">{{ $item->brand->name ?? 'No Brand' }} - {{ $item->name }} (Stock: {{ $item->quantity }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-danger remove-exchange-item mt-2">Remove</button>
            </div>
        `;
        $('#exchangeItemsContainer').append(newExchangeItem);
        initializeSelect2();

        // Set the new item select box to the same as the selected sale item
        const saleItemSelect = $(`select[name="exchange_items[${exchangeItemIndex}][sale_item_id]"]`);
        const newItemSelect = $(`select[name="exchange_items[${exchangeItemIndex}][new_item_id]"]`);
        saleItemSelect.change(function() {
            const selectedSaleItemId = $(this).val();
            newItemSelect.val(selectedSaleItemId).trigger('change');
        });

        exchangeItemIndex++;
    });

    $(document).on('click', '.remove-exchange-item', function() {
        $(this).closest('.exchange-item').remove();
    });

    // Set the initial new item select box to the same as the selected sale item
    $('.sale-item-select').change(function() {
        const selectedSaleItemId = $(this).val();
        const newItemSelect = $(this).closest('.exchange-item').find('.new-item-select');
        newItemSelect.val(selectedSaleItemId).trigger('change');
    });
});
</script>
@endpush
