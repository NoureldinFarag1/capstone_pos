@extends('layouts.dashboard')
@section('title', 'Exchange Items')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h2 class="mb-0">Exchange Items</h2>
                    <span class="badge {{ $sale->refund_status ? 'bg-warning' : 'bg-success' }} fs-6">
                        Sale #{{ $sale->id }}
                    </span>
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

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Exchange Instructions</h5>
                                    <ul class="mb-0">
                                        <li>Select the item from the sale you want to exchange</li>
                                        <li>Choose a replacement item with sufficient stock</li>
                                        <li>Items with zero quantity cannot be exchanged</li>
                                        <li>You can add multiple exchanges in one transaction</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('sales.exchange', $sale->id) }}" method="POST">
                        @csrf
                        <div id="exchangeItemsContainer">
                            <div class="exchange-item mb-4">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Exchange Item #1</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="sale_item_id" class="form-label fw-bold">Item to Exchange:</label>
                                                    <select name="exchange_items[0][sale_item_id]" class="form-control sale-item-select" required>
                                                        <option value="">-- Select Item --</option>
                                                        @foreach($sale->saleItems as $saleItem)
                                                            @if($saleItem->quantity > 0)
                                                                <option value="{{ $saleItem->id }}" data-quantity="{{ $saleItem->quantity }}">
                                                                    {{ $saleItem->item->brand->name ?? 'No Brand' }} -
                                                                    {{ $saleItem->item->name }}
                                                                    (Qty: {{ $saleItem->quantity }},
                                                                    Price: {{ number_format($saleItem->price, 2) }})
                                                                </option>
                                                            @else
                                                                <option value="{{ $saleItem->id }}" disabled>
                                                                    {{ $saleItem->item->brand->name ?? 'No Brand' }} -
                                                                    {{ $saleItem->item->name }}
                                                                    (No quantity available)
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="new_item_id" class="form-label fw-bold">Replacement Item:</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control item-search" placeholder="Search for items...">
                                                        <button class="btn btn-outline-secondary reset-search" type="button">Clear</button>
                                                    </div>
                                                    <select name="exchange_items[0][new_item_id]" class="form-control new-item-select" required>
                                                        <option value="">-- Select Replacement --</option>
                                                        @foreach($items as $item)
                                                            @if(!$item->is_parent)
                                                                @if($item->quantity > 0)
                                                                    <option value="{{ $item->id }}" data-stock="{{ $item->quantity }}" data-brand="{{ $item->brand->name ?? 'No Brand' }}">
                                                                        {{ $item->brand->name ?? 'No Brand' }} -
                                                                        {{ $item->name }}
                                                                        (Stock: {{ $item->quantity }},
                                                                        Price: {{ number_format($item->selling_price, 2) }})
                                                                    </option>
                                                                @else
                                                                    <option value="{{ $item->id }}" disabled>
                                                                        {{ $item->brand->name ?? 'No Brand' }} -
                                                                        {{ $item->name }}
                                                                        (Out of Stock)
                                                                    </option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <div class="stock-warning mt-2 text-danger d-none">
                                                        <small><i class="fas fa-exclamation-triangle"></i> Warning: Selected item has insufficient stock!</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="exchange-summary mt-3 d-none">
                                            <div class="alert alert-info">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Original Item:</strong> <span class="original-item-name"></span><br>
                                                        <strong>Quantity:</strong> <span class="original-item-quantity"></span><br>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Replacement Item:</strong> <span class="replacement-item-name"></span><br>
                                                        <strong>Available Stock:</strong> <span class="replacement-item-stock"></span><br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-danger remove-exchange-item">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="addExchangeItemButton" class="btn btn-secondary">
                                <i class="fas fa-plus me-2"></i>Add Another Exchange
                            </button>
                            <div>
                                <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-exchange-alt me-2"></i>Complete Exchange
                                </button>
                            </div>
                        </div>
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
    // Initialize Select2
    function initializeSelect2() {
        $('.sale-item-select, .new-item-select').select2({
            placeholder: 'Select an item',
            allowClear: true,
            width: '100%'
        });
    }

    initializeSelect2();

    // Exchange item counter
    let exchangeItemIndex = 1;

    // Add new exchange item form
    $('#addExchangeItemButton').click(function() {
        exchangeItemIndex++;

        const newExchangeItem = `
            <div class="exchange-item mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Exchange Item #${exchangeItemIndex}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sale_item_id" class="form-label fw-bold">Item to Exchange:</label>
                                    <select name="exchange_items[${exchangeItemIndex-1}][sale_item_id]" class="form-control sale-item-select" required>
                                        <option value="">-- Select Item --</option>
                                        @foreach($sale->saleItems as $saleItem)
                                            @if($saleItem->quantity > 0)
                                                <option value="{{ $saleItem->id }}" data-quantity="{{ $saleItem->quantity }}">
                                                    {{ $saleItem->item->brand->name ?? 'No Brand' }} -
                                                    {{ $saleItem->item->name }}
                                                    (Qty: {{ $saleItem->quantity }},
                                                    Price: {{ number_format($saleItem->price, 2) }})
                                                </option>
                                            @else
                                                <option value="{{ $saleItem->id }}" disabled>
                                                    {{ $saleItem->item->brand->name ?? 'No Brand' }} -
                                                    {{ $saleItem->item->name }}
                                                    (No quantity available)
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_item_id" class="form-label fw-bold">Replacement Item:</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control item-search" placeholder="Search for items...">
                                        <button class="btn btn-outline-secondary reset-search" type="button">Clear</button>
                                    </div>
                                    <select name="exchange_items[${exchangeItemIndex-1}][new_item_id]" class="form-control new-item-select" required>
                                        <option value="">-- Select Replacement --</option>
                                        @foreach($items as $item)
                                            @if(!$item->is_parent)
                                                @if($item->quantity > 0)
                                                    <option value="{{ $item->id }}" data-stock="{{ $item->quantity }}" data-brand="{{ $item->brand->name ?? 'No Brand' }}">
                                                        {{ $item->brand->name ?? 'No Brand' }} -
                                                        {{ $item->name }}
                                                        (Stock: {{ $item->quantity }},
                                                        Price: {{ number_format($item->selling_price, 2) }})
                                                    </option>
                                                @else
                                                    <option value="{{ $item->id }}" disabled>
                                                        {{ $item->brand->name ?? 'No Brand' }} -
                                                        {{ $item->name }}
                                                        (Out of Stock)
                                                    </option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="stock-warning mt-2 text-danger d-none">
                                        <small><i class="fas fa-exclamation-triangle"></i> Warning: Selected item has insufficient stock!</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="exchange-summary mt-3 d-none">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Original Item:</strong> <span class="original-item-name"></span><br>
                                        <strong>Quantity:</strong> <span class="original-item-quantity"></span><br>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Replacement Item:</strong> <span class="replacement-item-name"></span><br>
                                        <strong>Available Stock:</strong> <span class="replacement-item-stock"></span><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-danger remove-exchange-item">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#exchangeItemsContainer').append(newExchangeItem);
        initializeSelect2();
        bindEventHandlers();
    });

    // Bind event handlers for dynamically added elements
    function bindEventHandlers() {
        // Remove exchange item handler
        $('.remove-exchange-item').off('click').on('click', function() {
            $(this).closest('.exchange-item').remove();
        });

        // Sale item select handler
        $('.sale-item-select').off('change').on('change', function() {
            const exchangeItem = $(this).closest('.exchange-item');
            updateExchangeSummary(exchangeItem);
            checkStockCompatibility(exchangeItem);
        });

        // New item select handler
        $('.new-item-select').off('change').on('change', function() {
            const exchangeItem = $(this).closest('.exchange-item');
            updateExchangeSummary(exchangeItem);
            checkStockCompatibility(exchangeItem);
        });

        // Item search handler
        $('.item-search').off('input').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            const select = $(this).closest('.form-group').find('.new-item-select');

            select.find('option').each(function() {
                const text = $(this).text().toLowerCase();
                if (text.includes(searchTerm) || searchTerm === '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Reset search handler
        $('.reset-search').off('click').on('click', function() {
            const input = $(this).closest('.input-group').find('.item-search');
            const select = $(this).closest('.form-group').find('.new-item-select');

            input.val('');
            select.find('option').show();
        });
    }

    // Update exchange summary
    function updateExchangeSummary(exchangeItem) {
        const saleItemSelect = exchangeItem.find('.sale-item-select');
        const newItemSelect = exchangeItem.find('.new-item-select');
        const summary = exchangeItem.find('.exchange-summary');

        if (saleItemSelect.val() && newItemSelect.val()) {
            const saleItemText = saleItemSelect.find('option:selected').text();
            const newItemText = newItemSelect.find('option:selected').text();
            const originalQuantity = saleItemSelect.find('option:selected').data('quantity');
            const replacementStock = newItemSelect.find('option:selected').data('stock');

            summary.find('.original-item-name').text(saleItemText);
            summary.find('.original-item-quantity').text(originalQuantity);
            summary.find('.replacement-item-name').text(newItemText);
            summary.find('.replacement-item-stock').text(replacementStock);

            summary.removeClass('d-none');
        } else {
            summary.addClass('d-none');
        }
    }

    // Check if replacement item has enough stock
    function checkStockCompatibility(exchangeItem) {
        const saleItemSelect = exchangeItem.find('.sale-item-select');
        const newItemSelect = exchangeItem.find('.new-item-select');
        const warning = exchangeItem.find('.stock-warning');

        if (saleItemSelect.val() && newItemSelect.val()) {
            const originalQuantity = saleItemSelect.find('option:selected').data('quantity');
            const replacementStock = newItemSelect.find('option:selected').data('stock');

            if (replacementStock < originalQuantity) {
                warning.removeClass('d-none');
            } else {
                warning.addClass('d-none');
            }
        } else {
            warning.addClass('d-none');
        }
    }

    // Initialize event handlers
    bindEventHandlers();
});
</script>
@endpush
