@extends('layouts.dashboard')

@section('content')
<style>
    #barcode:focus {
        outline: 2px solid #96c9ff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
</style>
<div class="container">
    <h1>Create New Sale</h1>

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

        <div class="row mb-3">
            <!-- Item Selection -->
            <div class="col-md-6">
                <label for="itemSelect" class="form-label">Item</label>
                <select id="itemSelect" class="form-select">
                    <option value="">Select an item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}"
                                data-price="{{ $item->priceAfterSale() }}"
                                data-original-price="{{ $item->selling_price }}"
                                data-code="{{ $item->code }}">
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Quantity Selection -->
            <div class="col-md-4">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" id="quantity" class="form-control" min="1" value="1">
            </div>

            <!-- Add Button -->
            <div class="col-md-2 align-self-end">
                <button type="button" id="addItemButton" class="btn btn-success w-100">Add</button>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Barcode Scan -->
            <div class="col-md-6">
                <label for="barcode" class="form-label">Scan Barcode</label>
                <input type="text" id="barcode" class="form-control" placeholder="Scan barcode" autofocus>
            </div>

            <!-- Customer Details -->
            <div class="col-md-6">
                <label for="customerName" class="form-label">Customer Name</label>
                <input type="text" id="customerName" name="customer_name" class="form-control">

                <label for="customerPhone" class="form-label">Phone Number</label>
                <input type="text" id="customerPhone" name="customer_phone" class="form-control">
            </div>
        </div>

        <!-- Item List -->
        <ul id="itemList" class="list-group mb-3"></ul>

        <!-- Discount Section -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="discountType" class="form-label">Discount Type</label>
                <select id="discountType" class="form-select">
                    <option value="none">No Discount</option>
                    <option value="percentage">Percentage</option>
                    <option value="fixed">Fixed Amount</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="discountValue" class="form-label">Discount Value</label>
                <input type="number" id="discountValue" class="form-control" min="0" value="0">
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="paymentMethod" class="form-label">Payment Method</label>
                <select id="paymentMethod" name="payment_method" class="form-select" required>
                    <option value="">Select Payment Method</option>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Visa</option>
                    <option value="mobile_pay">Mobile Payment</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="paymentReference" class="form-label">Payment Reference</label>
                <input type="text" id="paymentReference" name="payment_reference" class="form-control" placeholder="Transaction ID, etc.">
            </div>
        </div>

        <!-- Total Amount -->
        <div class="row mb-3">
            <div class="col-md-12">
                <h4>Subtotal: <span id="subtotalAmount" class="text">$0.00</span></h4>
                <h4>Discount: <span id="discountAmount" class="text-danger">$0.00</span></h4>
                <h4>Total: <span id="totalAmount" class="text">$0.00</span></h4>
            </div>
        </div>

        <input type="hidden" name="subtotal" id="hiddenSubtotal">
        <input type="hidden" name="total" id="hiddenTotal">
        <input type="hidden" name="discount_type" id="hiddenDiscountType">
        <input type="hidden" name="discount_value" id="hiddenDiscountValue">

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Sale</button>
        <button type="button" id="printGiftReceiptBtn" class="btn btn-secondary">
            Print Gift Receipt
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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



    // Store items in a map to consolidate quantities
    const addedItems = new Map();

    // Add item to list with quantity consolidation
    function addItemToList(item, quantity) {
        // Ensure quantity is a number
        quantity = parseInt(quantity) || 1;

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
    }

    // Create a new list entry for an item
    function createNewItemListEntry(itemEntry) {
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.setAttribute('data-item-id', itemEntry.id);

        listItem.innerHTML = `
            ${itemEntry.name} - $${itemEntry.price.toFixed(2)}
            <div class="d-flex align-items-center">
                <span class="badge bg-primary rounded-pill me-2">
                    Qty: ${itemEntry.quantity}
                </span>
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    Remove
                </button>
                <input type="hidden" name="items[${itemEntry.id}][item_id]" value="${itemEntry.id}">
                <input type="hidden" name="items[${itemEntry.id}][quantity]" value="${itemEntry.quantity}">
                <input type="hidden" name="items[${itemEntry.id}][price]" value="${itemEntry.price}">
            </div>
        `;

        itemList.appendChild(listItem);

        // Add remove item event listener
        listItem.querySelector('.remove-item').addEventListener('click', function() {
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

    // Calculate total price with discount
    function calculateTotal() {
        let subtotal = 0;
        let discountAmount = 0;

        // Calculate subtotal
        addedItems.forEach((itemEntry) => {
            subtotal += itemEntry.price * itemEntry.quantity;
        });

        // Calculate discount
        const discountType = discountTypeSelect.value;
        const discountValue = parseFloat(discountValueInput.value) || 0;

        if (discountType === 'percentage') {
            discountAmount = subtotal * (discountValue / 100);
        } else if (discountType === 'fixed') {
            discountAmount = Math.min(discountValue, subtotal);
        }

        const total = subtotal - discountAmount;

        // Update display
        subtotalAmountDisplay.textContent = `$${subtotal.toFixed(2)}`;
        discountAmountDisplay.textContent = `$${discountAmount.toFixed(2)}`;
        totalAmountDisplay.textContent = `$${total.toFixed(2)}`;

        // Update hidden fields
        document.getElementById('hiddenSubtotal').value = subtotal.toFixed(2);
        document.getElementById('hiddenTotal').value = total.toFixed(2);
        document.getElementById('hiddenDiscountType').value = discountType;
        document.getElementById('hiddenDiscountValue').value = discountValue;
    }

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
            // Select the item and add to list
            itemSelect.value = matchingOption.value;
            const item = {
                id: matchingOption.value,
                name: matchingOption.text,
                price: parseFloat(matchingOption.getAttribute('data-price')),
                originalPrice: parseFloat(matchingOption.getAttribute('data-original-price'))
            };

            addItemToList(item, 1);

            // Clear barcode input
            barcodeInput.value = '';
        } else {
            alert('Item not found for the scanned barcode code.');
        }
    }

    // Prevent form submission on Enter key and refocus barcode input
    saleForm.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target === barcodeInput) {
            e.preventDefault(); // Prevent form submission
            barcodeInput.value = ''; // Clear the input
            barcodeInput.focus(); // Refocus the input
        }
    });

    // Add Item Button Handler
    addItemButton.addEventListener('click', function() {
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
        } else {
            alert('Please select an item.');
        }
    });

    // Barcode Input Handler
    barcodeInput.addEventListener('input', function(e) {
        const barcode = e.target.value.trim();

        // Trigger barcode search if length is sufficient
        if (barcode.length >= 10) {
            handleBarcodeScanning(barcode);
            setTimeout(() => {
                barcodeInput.focus(); // Refocus after scanning
            }, 100);
        }
    });

    // Discount and Total Calculation Handlers
    discountTypeSelect.addEventListener('change', calculateTotal);
    discountValueInput.addEventListener('input', calculateTotal);

    // Print Gift Receipt Handler
if (printGiftReceiptBtn) {
    printGiftReceiptBtn.addEventListener('click', handlePrintGiftReceipt);
}

function handlePrintGiftReceipt() {
    if (itemList.children.length === 0) {
        alert('Please add at least one item before printing a gift receipt');
        return;
    }

    // Collect sale items (without prices)
    const saleItems = Array.from(itemList.querySelectorAll('li.list-group-item')).map(item => {
        // Get the raw text content and split it properly
        const itemText = item.firstChild.textContent.trim();
        const itemName = itemText.split(' - ')[0].trim();

        // Get quantity from the badge
        const quantityBadge = item.querySelector('.badge');
        const quantity = parseInt(quantityBadge.textContent.replace('Qty:', '').trim()) || 1;

        // Return item data (with the item object containing the name)
        return {
            item: {
                name: itemName  // Wrap the name inside an 'item' object
            },
            quantity: quantity
        };
    });

    if (saleItems.length === 0) {
        alert('Please add valid items before printing a gift receipt');
        return;
    }

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    console.log(saleItems);

    // Send AJAX request to print the gift receipt without prices
    fetch('{{ route("sales.print-gift-receipt") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            saleItems,  // Send the sale items as an array
            customer_name: document.getElementById('customerName').value,
            subtotal: document.getElementById('hiddenSubtotal').value,
            total: document.getElementById('hiddenTotal').value,
            discount_type: document.getElementById('hiddenDiscountType').value,
            discount_value: document.getElementById('hiddenDiscountValue').value
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

});
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
