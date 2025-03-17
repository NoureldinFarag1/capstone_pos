@extends('layouts.dashboard')

@section('content')
    @php
        use Illuminate\Support\Facades\Auth;
    @endphp
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        .sale-header {
            font-family: 'Roboto Mono', monospace;
            font-size: 1.5rem;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-size: 1.0rem;
            font-weight: 500;
            color: #34495e;
        }

        .form-control,
        .form-select {
            font-size: 1.0rem;
            padding: 0.75rem;
        }

        .btn-lg {
            font-size: 1.0rem;
            padding: 0.75rem 1.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #1a237e;
            padding: 1rem 1.5rem;
        }

        .card-header h5 {
            font-size: 1.3rem;
            font-weight: 500;
        }

        .price-display {
            font-family: 'Roboto Mono', monospace;
            font-size: 1.5rem;
        }

        .total-amount {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .discount-amount {
            color: #e74c3c;
        }

        .list-group-item {
            padding: 1rem;
            font-size: 1.1rem;
        }

        .badge {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }

        #itemList {
            max-height: 400px;
            overflow-y: auto;
        }

        .select2-container .select2-selection--single {
            height: 45px;
            font-size: 1.1rem;
        }
    </style>
    <div class="container">
        <h1 class="sale-header">Create New Sale</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
            @csrf

            <!-- Item Selection Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Add Items</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="barcode" class="form-label fw-bold">Scan Barcode</label>
                            <input type="text" id="barcode" class="form-control form-control-lg" placeholder="Scan barcode"
                                autofocus>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="itemSelect" class="form-label fw-bold">Select Item</label>
                            <select id="itemSelect" class="form-select">
                                <option value="">Select an item</option>
                                @php
                                    $sortedItems = $items->sortBy(function ($item) {
                                        return $item->brand->name ?? 'No Brand';
                                    });
                                @endphp
                                @foreach ($sortedItems as $item)
                                    @if (!$item->is_parent)
                                        <option value="{{ $item->id }}" data-price="{{ $item->priceAfterSale() }}"
                                            data-original-price="{{ $item->selling_price }}" data-code="{{ $item->code }}"
                                            data-stock="{{ $item->quantity }}" {{ $item->quantity <= 0 ? 'disabled' : '' }}>
                                            {{ $item->brand->name ?? 'No Brand' }} - {{ $item->name }} (Stock:
                                            {{ $item->quantity }})
                                            {{ $item->quantity <= 0 ? '- Out of Stock' : '' }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="quantity" class="form-label fw-bold">Quantity</label>
                            <input type="number" id="quantity" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="button" id="addItemButton" class="btn btn-success btn-lg w-100">Add</button>
                        </div>
                    </div>

                    <ul id="itemList" class="list-group"></ul>
                </div>
            </div>

            <!-- Customer Details Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="customerPhone" class="form-label fw-bold">Phone Number</label>
                            <input type="text" id="customerPhone" name="customer_phone" class="form-control form-control-lg"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="customerName" class="form-label fw-bold">Customer Name</label>
                            <input type="text" id="customerName" name="customer_name" class="form-control form-control-lg"
                                required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <!-- Discount Section -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Discount</label>
                            <div class="input-group">
                                <select id="discountType" class="form-select">
                                    <option value="none">No Discount</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                                <input type="number" id="discountValue" class="form-control" min="0" value="0">
                            </div>
                        </div>
                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <label for="paymentMethod" class="form-label fw-bold">Payment Method</label>
                            <select id="paymentMethod" name="payment_method" class="form-select form-select-lg" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Visa</option>
                                <option value="mobile_pay">Mobile Payment</option>
                                <option value="cod">Cash On Delivery (COD)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row" id="codDetails">
                        <div class="col-md-6" id="shippingFeesContainer" style="display: none;">
                            <label for="shippingFees" class="form-label">Shipping Fees</label>
                            <input type="number" id="shippingFees" name="shipping_fees" class="form-control" min="0"
                                value="0">
                        </div>
                        <div class="col-md-6" id="addressContainer" style="display: none;">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address" class="form-control">
                        </div>
                    </div>

                    <!-- Totals Section -->
                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">Subtotal:</p>
                                    <h4 id="subtotalAmount" class="price-display">EGP 0.00</h4>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">Discount:</p>
                                    <h4 id="discountAmount" class="price-display discount-amount">EGP 0.00</h4>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted">Total:</p>
                                    <h4 id="totalAmount" class="price-display total-amount">EGP 0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes and Submit -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="notes" class="form-label fw-bold">Notes</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Create Sale</button>
                        <button type="button" id="printGiftReceiptBtn" class="btn btn-secondary btn-lg">Print Gift
                            Receipt</button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="subtotal" id="hiddenSubtotal">
            <input type="hidden" name="total" id="hiddenTotal">
            <input type="hidden" name="discount_type" id="hiddenDiscountType">
            <input type="hidden" name="discount_value" id="hiddenDiscountValue">
        </form>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999;">
        <div class="position-absolute top-50 start-50 translate-middle text-white text-center">
            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h4 class="mt-3">Processing Sale...</h4>
        </div>
    </div>

    @push('scripts')
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#itemSelect').select2({
                    placeholder: 'Select an item',
                    allowClear: true
                });

                const itemSelect = document.getElementById('itemSelect');
                const quantityInput = document.getElementById('quantity');
                const addItemButton = document.getElementById('addItemButton');
                const itemList = document.getElementById('itemList');
                const barcodeInput = document.getElementById('barcode');
                const subtotalAmountDisplay = document.getElementById('subtotalAmount');
                const discountAmountDisplay = document.getElementById('discountAmount');
                const totalAmountDisplay = document.getElementById('totalAmount');
                const discountTypeSelect = document.getElementById('discountType');
                const discountValueInput = document.getElementById('discountValue');
                const saleForm = document.getElementById('saleForm');
                const printGiftReceiptBtn = document.getElementById('printGiftReceiptBtn');
                const customerPhoneInput = document.getElementById('customerPhone');
                const customerNameInput = document.getElementById('customerName');
                const paymentMethodSelect = document.getElementById('paymentMethod');
                const shippingFeesContainer = document.getElementById('shippingFeesContainer');
                const addressContainer = document.getElementById('addressContainer');

                customerPhoneInput.addEventListener('blur', function () {
                    const phoneNumber = customerPhoneInput.value.trim();
                    if (phoneNumber) {
                        fetch(`/customers/fetch-name?phone=${phoneNumber}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.name) {
                                    customerNameInput.value = data.name;
                                }
                            })
                            .catch(error => console.error('Error fetching customer name:', error));
                    }
                });

                // Store items in a map to consolidate quantities
                const addedItems = new Map();

                // Add item to list with quantity consolidation
                function addItemToList(item, quantity) {
                    // Ensure quantity is a number
                    quantity = parseInt(quantity) || 1;

                    // Get current stock quantity
                    const stockQuantity = parseInt(itemSelect.options[itemSelect.selectedIndex].getAttribute(
                        'data-stock'));

                    // Check if item is in stock
                    if (stockQuantity <= 0) {
                        alert('This item is out of stock.');
                        return false;
                    }

                    // Calculate total requested quantity (existing + new)
                    let totalRequestedQuantity = quantity;
                    if (addedItems.has(item.id)) {
                        totalRequestedQuantity += addedItems.get(item.id).quantity;
                    }

                    // Check if total quantity exceeds stock
                    if (totalRequestedQuantity > stockQuantity) {
                        alert(`Cannot add ${quantity} items. Only ${stockQuantity} available in stock.`);
                        return false;
                    }

                    // Check if item already exists in the list
                    if (addedItems.has(item.id)) {
                        // Update existing item's quantity
                        const existingEntry = addedItems.get(item.id);
                        existingEntry.quantity += quantity;
                        updateItemInList(item, existingEntry.quantity);
                    } else {
                        // Add new item to the list
                        const newEntry = {
                            id: item.id,
                            name: item.name,
                            price: item.price,
                            originalPrice: item.originalPrice,
                            quantity: quantity
                        };
                        addedItems.set(item.id, newEntry);
                        createNewItemListEntry(newEntry);
                    }

                    // Recalculate total
                    calculateTotal();
                    return true;
                }

                // Create a new list entry for an item
                function createNewItemListEntry(itemEntry) {
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    listItem.setAttribute('data-item-id', itemEntry.id);

                    listItem.innerHTML = `
                                                                                                                                                                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                                                                                                                                                                    <input type="checkbox" class="item-checkbox me-2" checked>
                                                                                                                                                                                                                                                                                    ${itemEntry.name} - EGP ${itemEntry.price.toFixed(2)}
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                                                                                                                                                                    <span class="bg-primary rounded-pill me-2">
                                                                                                                                                                                                                                                                                        Qty: ${itemEntry.quantity}
                                                                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                                                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                                                                                                                                                                                                                                                                        Remove
                                                                                                                                                                                                                                                                                    </button>
                                                                                                                                                                                                                                                                                    <input type="hidden" name="items[${itemEntry.id}][item_id]" value="${itemEntry.id}">
                                                                                                                                                                                                                                                                                    <input type="hidden" name="items[${itemEntry.id}][quantity]" value="${itemEntry.quantity}">
                                                                                                                                                                                                                                                                                    <input type="hidden" name="items[${itemEntry.id}][price]" value="${itemEntry.price}">
                                                                                                                                                                                                                                                                                    <input type="hidden" name="items[${itemEntry.id}][as_gift]" value="0">
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                            `;

                    itemList.appendChild(listItem);

                    // Add checkbox change event listener
                    const checkbox = listItem.querySelector('.item-checkbox');
                    checkbox.addEventListener('change', function () {
                        const itemId = listItem.getAttribute('data-item-id');
                        const asGiftInput = listItem.querySelector(`input[name="items[${itemId}][as_gift]"]`);

                        if (!this.checked) {
                            asGiftInput.value = "1"; // Mark as gift
                        } else {
                            asGiftInput.value = "0"; // Mark as regular item
                        }
                        calculateTotal(); // Just recalculate total without modifying discount
                        updateNotesWithGiftItems();
                    });

                    // Add remove item event listener
                    listItem.querySelector('.remove-item').addEventListener('click', function () {
                        const itemId = listItem.getAttribute('data-item-id');
                        addedItems.delete(itemId);
                        listItem.remove();
                        calculateTotal();
                    });
                }

                // Update an existing item in the list
                function updateItemInList(item, newQuantity) {
                    const existingListItem = itemList.querySelector(`[data-item-id="${item.id}"]`);
                    if (existingListItem) {
                        const badgeElement = existingListItem.querySelector('.badge');
                        const quantityInput = existingListItem.querySelector('input[name$="[quantity]"]');

                        if (badgeElement) badgeElement.textContent = `Qty: ${newQuantity}`;
                        if (quantityInput) quantityInput.value = newQuantity;
                    }
                }

                discountTypeSelect.addEventListener('change', function () {
                    // Preserve the current discount value when changing type
                    const currentValue = parseFloat(discountValueInput.value) || 0;
                    discountValueInput.value = currentValue;
                    calculateTotal();
                });

                discountValueInput.addEventListener('input', function () {
                    const discountType = discountTypeSelect.value;
                    const discountValue = parseFloat(discountValueInput.value) || 0;

                    if (discountType === 'percentage' && discountValue > 100) {
                        alert('Percentage discount cannot exceed 100%.');
                        discountValueInput.value = 100;
                    } else if (discountType === 'fixed' && discountValue > subtotal) {
                        alert('Fixed amount discount cannot exceed the subtotal.');
                        discountValueInput.value = subtotal;
                    }

                    calculateTotal();
                });


                // Calculate total price with discount
                function calculateTotal() {
                    const currentDiscountType = discountTypeSelect.value;
                    const currentDiscountValue = parseFloat(discountValueInput.value) || 0;

                    let subtotal = 0;
                    let giftTotal = 0;
                    let regularTotal = 0;
                    let shippingFees = parseFloat(document.getElementById('shippingFees').value) || 0;

                    // Calculate subtotal and handle gift items
                    itemList.querySelectorAll('li').forEach((item) => {
                        const checkbox = item.querySelector('.item-checkbox');
                        const itemId = item.getAttribute('data-item-id');
                        const itemEntry = addedItems.get(itemId);

                        if (itemEntry) {
                            const itemTotal = itemEntry.price * itemEntry.quantity;
                            if (checkbox && !checkbox.checked) {
                                // This is a gift item
                                giftTotal += itemTotal;
                            } else {
                                // This is a regular item
                                regularTotal += itemTotal;
                            }
                        }
                    });

                    subtotal = regularTotal + giftTotal;

                    // Calculate regular discount (only applied to regular items)
                    let regularDiscount = 0;
                    if (currentDiscountType === 'percentage') {
                        regularDiscount = regularTotal * (Math.min(currentDiscountValue, 100) / 100);
                    } else if (currentDiscountType === 'fixed') {
                        regularDiscount = Math.min(currentDiscountValue, regularTotal);
                    }

                    // Gift items are always 100% discounted
                    const totalDiscount = regularDiscount + giftTotal;

                    // Calculate final totals
                    const totalBeforeShipping = Math.max(0, subtotal - totalDiscount);
                    const finalTotal = totalBeforeShipping + shippingFees;

                    // Update display values
                    subtotalAmountDisplay.textContent = `EGP ${subtotal.toFixed(2)}`;
                    discountAmountDisplay.textContent = `EGP ${totalDiscount.toFixed(2)}`;
                    totalAmountDisplay.textContent = `EGP ${finalTotal.toFixed(2)}`;

                    // Update hidden inputs
                    document.getElementById('hiddenSubtotal').value = subtotal.toFixed(2);
                    document.getElementById('hiddenTotal').value = finalTotal.toFixed(2);
                    document.getElementById('hiddenDiscountType').value = currentDiscountType;
                    document.getElementById('hiddenDiscountValue').value = currentDiscountValue;
                }

                function updateNotesWithGiftItems() {
                    const giftItems = [];
                    itemList.querySelectorAll('li').forEach((item) => {
                        const checkbox = item.querySelector('.item-checkbox');
                        if (checkbox && !checkbox.checked) {
                            const itemName = item.querySelector('.d-flex').textContent.trim().split(' - EGP')[0];
                            const quantity = item.querySelector('.badge').textContent.match(/\d+/)[0];
                            giftItems.push(`${itemName} (${quantity})`);
                        }
                    });

                    const notesField = document.getElementById('notes');
                    const existingNotes = notesField.value.split('\n').filter(line => !line.startsWith('Gift Items:')).join('\n');
                    const giftItemsText = giftItems.length > 0 ? `Gift Items: ${giftItems.join(' | ')}` : '';

                    notesField.value = existingNotes.trim() + (existingNotes.trim() && giftItemsText ? '\n' : '') + giftItemsText;
                }

                // Add this new function after calculateTotal
                function updateNotesWithCheckedItems() {
                    const checkedItems = [];
                    itemList.querySelectorAll('li').forEach((item) => {
                        const checkbox = item.querySelector('.item-checkbox');
                        if (checkbox && !checkbox.checked) {
                            const itemName = item.querySelector('.d-flex').textContent.trim().split(' (')[0];
                            checkedItems.push(itemName);
                        }
                    });

                    const notesField = document.getElementById('notes');
                    const existingNotes = notesField.value.split('\n').filter(line => !line.startsWith('Free Items:')).join('\n');
                    const checkedItemsText = checkedItems.length > 0 ? `Free Items: ${checkedItems.join(' / ')}` : '';

                    notesField.value = existingNotes.trim() + (existingNotes.trim() && checkedItemsText ? '\n' : '') + checkedItemsText;
                }

                // Add shipping fees change handler
                document.getElementById('shippingFees').addEventListener('input', calculateTotal);

                function getSaleItemsFromForm() {
                    // This is a example structure - adapt it to match your form
                    const saleItems = [];
                    const itemRows = document.querySelectorAll('.sale-item-row');

                    itemRows.forEach(row => {
                        saleItems.push({
                            item_id: row.querySelector('[name="item_id[]"]').value,
                            quantity: row.querySelector('[name="quantity[]"]').value,
                            item: {
                                name: row.querySelector('[name="item_name[]"]').value
                            }
                        });
                    });
                    return saleItems;
                }

                // Barcode scanning function
                function handleBarcodeScanning(barcode) {
                    // Find the option with matching code
                    const matchingOption = Array.from(itemSelect.options).find(
                        option => option.getAttribute('data-code') === barcode
                    );

                    if (matchingOption) {
                        if (matchingOption.disabled) {
                            alert('This item is out of stock.');
                            barcodeInput.value = '';
                            return;
                        }

                        // Select the item and add to list
                        itemSelect.value = matchingOption.value;
                        const item = {
                            id: matchingOption.value,
                            name: matchingOption.text.replace(' Out of stock', ''),
                            price: parseFloat(matchingOption.getAttribute('data-price')),
                            originalPrice: parseFloat(matchingOption.getAttribute('data-original-price'))
                        };

                        if (addItemToList(item, 1)) {
                            barcodeInput.value = '';
                        }
                    } else {
                        alert('Item not found for the scanned barcode code.');
                        barcodeInput.value = '';
                    }
                }

                // Prevent form submission on Enter key and refocus barcode input
                saleForm.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && e.target === barcodeInput) {
                        e.preventDefault(); // Prevent form submission
                        barcodeInput.value = ''; // Clear the input
                        barcodeInput.focus(); // Refocus the input
                    }
                });

                // Add Item Button Handler
                addItemButton.addEventListener('click', function () {
                    const selectedOption = itemSelect.options[itemSelect.selectedIndex];

                    if (selectedOption.value) {
                        const item = {
                            id: selectedOption.value,
                            name: selectedOption.text,
                            price: parseFloat(selectedOption.getAttribute('data-price')),
                            originalPrice: parseFloat(selectedOption.getAttribute('data-original-price'))
                        };
                        const quantity = quantityInput.value || 1;

                        addItemToList(item, quantity);

                        // Reset inputs
                        itemSelect.value = '';
                        quantityInput.value = 1;
                    }

                });

                // Barcode Input Handler
                barcodeInput.addEventListener('input', function (e) {
                    const barcode = e.target.value.trim();

                    // Trigger barcode search immediately after input
                    if (barcode.length >= 14) {
                        handleBarcodeScanning(barcode);
                        setTimeout(() => {
                            barcodeInput.focus(); // Refocus after scanning
                        }, 100);
                    }
                });

                // Discount and Total Calculation Handlers
                discountTypeSelect.addEventListener('change', function () {
                    // Preserve the current discount value when changing type
                    const currentValue = parseFloat(discountValueInput.value) || 0;
                    discountValueInput.value = currentValue;
                    calculateTotal();
                });

                discountValueInput.addEventListener('input', function () {
                    calculateTotal();
                });

                // Print Gift Receipt Handler
                if (printGiftReceiptBtn) {
                    printGiftReceiptBtn.addEventListener('click', handlePrintGiftReceipt);
                }

                function handlePrintGiftReceipt() {
                    if (itemList.children.length === 0) {
                        alert('Please add at least one item before printing a gift receipt');
                        return;
                    }

                    const saleItems = Array.from(itemList.children).map(item => {
                        const itemId = item.getAttribute('data-item-id'); // Get item ID from the list item
                        const itemText = item.firstChild.textContent.split(' - ')[0].trim();
                        const quantityMatch = item.querySelector('.badge').textContent.match(/\d+/);
                        const quantity = quantityMatch ? parseInt(quantityMatch[0]) : 1;

                        return {
                            item_id: itemId, // Include item_id
                            name: itemText, // Include item name
                            quantity: quantity
                        };
                    });

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('{{ route('sales.print-gift-receipt') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            items: saleItems // Changed saleItems to items
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Gift receipt printed successfully');
                            } else {
                                throw new Error(data.message || 'Failed to print gift receipt');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error printing gift receipt: ' + error.message);
                        });
                }

                paymentMethodSelect.addEventListener('change', function () {
                    if (paymentMethodSelect.value === 'cod') {
                        shippingFeesContainer.style.display = 'block';
                        addressContainer.style.display = 'block';
                    } else {
                        shippingFeesContainer.style.display = 'none';
                        addressContainer.style.display = 'none';
                    }
                });

                // Add validation for discount value based on user role
                saleForm.addEventListener('submit', function (event) {
                    event.preventDefault(); // Prevent default form submission

                    // Get the clicked button
                    const submitter = event.submitter;

                    // Only proceed if it's not the print gift receipt button
                    if (!submitter || submitter.matches('#printGiftReceiptBtn')) {
                        return; // Exit early if print gift receipt or unknown button
                    }

                    // Validate discount based on user role
                    const discountType = discountTypeSelect.value;
                    const discountValue = parseFloat(discountValueInput.value) || 0;
                    const userRole = '{{ Auth::user()->role }}';

                    if (userRole === 'cashier') {
                        if (discountType === 'percentage' && discountValue > 20) {
                            alert('As a cashier, percentage discount cannot exceed 20%.');
                            return;
                        }

                        if (discountType === 'fixed' && discountValue > 100) {
                            alert('As a cashier, fixed amount discount cannot exceed 100 EGP.');
                            return;
                        }
                    }

                    // Show loading overlay
                    document.getElementById('loadingOverlay').style.display = 'block';

                    // Just submit the form if all validations pass
                    submitter.disabled = true;
                    this.submit();
                });

            });
        </script>
    @endpush
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection