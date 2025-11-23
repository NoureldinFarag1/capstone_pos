@extends('layouts.dashboard')

@section('content')
    @php
        use Illuminate\Support\Facades\Auth;
    @endphp
    <div class="sale-create py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm" aria-label="Back to previous">← Back</a>
                <h1 class="create-sale-heading mb-0">Create New Sale</h1>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="keyboardShortcutsBtn">
                <i class="fas fa-keyboard me-2"></i>Shortcuts
            </button>
        </div>

        <!-- Top Mini Summary Chip (sticky) -->
        <div class="position-sticky" style="top: 0; z-index: 6;">
            <div class="d-inline-flex align-items-center gap-3 bg-white border rounded-pill px-3 py-2 shadow-sm">
                <span class="text-muted small d-flex align-items-center gap-2">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Items</span>
                    <span class="badge bg-secondary" id="topMiniItems">0</span>
                </span>
                <span class="vr"></span>
                <span class="small d-flex align-items-center gap-2">
                    <span class="text-muted">Total</span>
                    <strong id="topMiniTotal">EGP 0.00</strong>
                </span>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left side - Item selection and cart -->
            <div class="col-lg-8">
                <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
                    @csrf
                    <!-- Item Selection Card -->
                    <div class="card sale-card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Add Items</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-danger" id="clearCartBtn" title="Clear cart">
                                    <i class="fas fa-trash-alt me-1"></i> Clear Cart
                                </button>
                                <button type="button" class="btn btn-primary" id="printGiftReceiptBtn" title="Print gift receipt">
                                    <i class="fas fa-gift me-1"></i> Gift Receipt
                                </button>
                                <div class="form-check form-switch d-inline-flex align-items-center ms-2">
                                    <input class="form-check-input" type="checkbox" id="rapidScanToggle" checked>
                                    <label class="form-check-label ms-2 small" for="rapidScanToggle">Rapid scan</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Barcode Scanner -->
                            <div class="mb-3">
                                <div class="input-group barcode-scanner-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-barcode text-muted"></i>
                                    </span>
                                    <input type="text" id="barcode" class="form-control form-control-lg border-start-0"
                                        placeholder="Scan barcode or press / to search" autofocus>
                                    <button class="btn btn-outline-secondary" type="button" id="focusBarcode" title="Focus barcode (/)">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Item Selection -->
                            <div class="mb-3">
                                <div class="row g-2">
                                    <div class="col">
                                        <select id="itemSelect" class="form-select">
                                            <option value="">Select an item</option>
                                            @php
                                                $sortedItems = $items->sortBy(function ($item) {
                                                    return $item->brand->name ?? 'No Brand';
                                                });
                                            @endphp
                                            @foreach ($sortedItems as $item)
                                                @if (!$item->is_parent)
                                                    <option
                                                        value="{{ $item->id }}"
                                                        data-price="{{ $item->priceAfterSale() }}"
                                                        data-original-price="{{ $item->selling_price }}"
                                                        data-code="{{ $item->code }}"
                                                        data-stock="{{ $item->quantity }}"
                                                        {{ $item->quantity <= 0 ? 'disabled' : '' }}
                                                    >
                                                        {{ $item->brand->name ?? 'No Brand' }} - {{ $item->name }}
                                                        ({{ $item->quantity }} in stock)
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <input type="number" id="quantity" class="form-control" min="1" value="1" style="width: 70px;">
                                            <button type="button" id="addItemButton" class="btn btn-success">
                                                <i class="fas fa-plus me-1"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Press Tab to navigate, Enter to add</small>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mb-3 d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary quick-action-btn" data-action="add-frequently-sold">
                                    <i class="fas fa-star me-1"></i> Popular Items
                                </button>
                            </div>

                            <!-- Cart Items -->
                            <div class="cart-container">
                                <ul id="itemList" class="list-group"></ul>
                                <div id="emptyCart" class="text-center py-5">
                                    <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                                    <p class="text-muted">Cart is empty. Add items to begin.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="subtotal" id="hiddenSubtotal">
                    <input type="hidden" name="total" id="hiddenTotal">
                    <input type="hidden" name="discount_type" id="hiddenDiscountType">
                    <input type="hidden" name="discount_value" id="hiddenDiscountValue">
                </form>
            </div>

            <!-- Right side - Summary, Customer info & payment -->
            <div class="col-lg-4">
                <!-- Summary Card -->
                <div class="card sale-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Summary</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Items</span>
                            <span id="saleSummaryItemCount" class="badge bg-secondary">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total</span>
                            <span id="saleSummaryTotal" class="fw-bold">EGP 0.00</span>
                        </div>
                    </div>
                </div>
                <!-- Customer Details Card -->
                <div class="card sale-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Customer</h5>
                        <div class="form-check form-switch">
                            <!-- Walk-in customer flag - include in form submission -->
                            <input class="form-check-input" type="checkbox" id="skipCustomerInfo" name="skip_customer" value="1" form="saleForm" checked>
                            <label class="form-check-label" for="skipCustomerInfo">Walk-in customer</label>
                        </div>
                    </div>
                    <div class="card-body customer-info-section">
                        <div class="mb-3">
                            <label for="customerPhone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <input type="text" id="customerPhone" name="customer_phone" class="form-control" form="saleForm">
                                <button class="btn btn-outline-secondary" type="button" id="customerLookupBtn" title="Find customer">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div id="customerPhoneError" class="invalid-feedback d-none"></div>
                            <div id="customerSearchResults" class="mt-1 d-none"></div>
                        </div>
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Customer Name</label>
                            <input type="text" id="customerName" name="customer_name" class="form-control" form="saleForm">
                            <div id="customerNameError" class="invalid-feedback d-none"></div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Card -->
                <div class="card sale-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payment</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select id="paymentMethod" name="payment_method" class="form-select" required form="saleForm">
                                <option value="cash" selected>Cash</option>
                                <option value="credit_card">Visa</option>
                                <option value="mobile_pay">Mobile Payment</option>
                                <option value="cod">Cash On Delivery (COD)</option>
                            </select>
                        </div>

                        <div id="codDetails" style="display: none;">
                            <div class="mb-3" id="addressContainer">
                                <label for="address" class="form-label">Delivery Address</label>
                                <input type="text" id="address" name="address" class="form-control" form="saleForm">
                                <div id="addressError" class="invalid-feedback d-none"></div>
                            </div>
                            <div class="mb-3" id="shippingFeesContainer">
                                <label for="shippingFees" class="form-label">Shipping Fees</label>
                                <div class="input-group">
                                    <span class="input-group-text">EGP</span>
                                    <input type="number" id="shippingFees" name="shipping_fees" class="form-control" min="0" value="0" form="saleForm">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Discount</label>
                                <a href="#" class="text-decoration-none text-muted small" id="clearDiscountBtn">Clear</a>
                            </div>
                            <div>
                                <div class="input-group">
                                    <select id="discountType" class="form-select">
                                        <option value="none" selected>No Discount</option>
                                        <option value="percentage">Percentage</option>
                                        <option value="fixed">Fixed Amount</option>
                                    </select>
                                    <input type="number" id="discountValue" class="form-control" min="0" value="0" style="max-width: 100px;">
                                </div>
                                <div id="discountError" class="invalid-feedback d-none"></div>
                                <div id="discountLimitInfo" class="small text-muted mt-1"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" name="notes" class="form-control" rows="2" form="saleForm"></textarea>
                        </div>

                        <!-- Totals Section -->
                        <div class="card bg-light mb-4">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Items:</span>
                                    <span id="itemCount" class="fw-bold">0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotalAmount" class="fw-bold">EGP 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Discount:</span>
                                    <span id="discountAmount" class="fw-bold text-danger">- EGP 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 shipping-row" style="display: none;">
                                    <span>Shipping:</span>
                                    <span id="shippingAmount" class="fw-bold">EGP 0.00</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Total:</span>
                                    <span id="totalAmount" class="fw-bold fs-5">EGP 0.00</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" form="saleForm" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Complete Sale (F8)
                            </button>
                            <button type="button" id="quickCashBtn" class="btn btn-success">
                                <i class="fas fa-money-bill me-2"></i>Quick Cash (F7)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compact floating summary box - just shows items count and total -->
    <div class="floating-summary">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span><i class="fas fa-shopping-cart me-2"></i></span>
            <span id="summaryItemCount" class="badge bg-primary">0</span>
        </div>
        <div>
            <span class="d-block text-muted small">Total</span>
            <span id="summaryTotal" class="fw-bold">EGP 0.00</span>
        </div>
    </div>

    <!-- Keyboard Shortcuts Modal -->
    <div class="modal fade" id="keyboardShortcutsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Keyboard Shortcuts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td><kbd>/</kbd></td>
                                <td>Focus on barcode scanner</td>
                            </tr>
                            <tr>
                                <td><kbd>F2</kbd></td>
                                <td>Focus on item search</td>
                            </tr>
                            <tr>
                                <td><kbd>F8</kbd></td>
                                <td>Complete sale</td>
                            </tr>
                            <tr>
                                <td><kbd>F9</kbd></td>
                                <td>Switch payment method</td>
                            </tr>
                            <tr>
                                <td><kbd>G</kbd></td>
                                <td>Print gift receipt</td>
                            </tr>
                            <tr>
                                <td><kbd>Esc</kbd></td>
                                <td>Clear current input / focus</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Frequently Sold Items Modal -->
    <div class="modal fade" id="frequentItemsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Popular Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row row-cols-2 row-cols-md-4 g-3 popular-items-container">
                        @php
                            $popularItems = \App\Models\SaleItem::with('item.brand')
                                ->select('item_id', DB::raw('COUNT(*) as count'))
                                ->groupBy('item_id')
                                ->orderByDesc('count')
                                ->limit(12)
                                ->get();
                        @endphp

                        @foreach($popularItems as $item)
                            @if($item->item && $item->item->quantity > 0)
                                <div class="col">
                                    <div class="card popular-item"
                                         data-id="{{ $item->item->id }}"
                                         data-price="{{ $item->item->priceAfterSale() }}"
                                         data-original-price="{{ $item->item->selling_price }}"
                                         data-name="{{ $item->item->brand->name ?? 'No Brand' }} - {{ $item->item->name }}">
                                        <div class="card-body text-center p-3">
                                            <h6 class="card-title mb-1">{{ $item->item->brand->name ?? 'No Brand' }}</h6>
                                            <p class="card-text small mb-2">{{ $item->item->name }}</p>
                                            <span class="badge bg-primary">{{ $item->item->priceAfterSale() }} EGP</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Mirror totals/item count into right Summary and sticky bar
                const sourceSummaryTotalEl = document.getElementById('summaryTotal');
                const sourceSummaryItemCountEl = document.getElementById('summaryItemCount');
                const saleSummaryTotalEl = document.getElementById('saleSummaryTotal');
                const saleSummaryItemCountEl = document.getElementById('saleSummaryItemCount');
                const stickySaleTotalEl = document.getElementById('stickySaleTotal');
                const topMiniItemsEl = document.getElementById('topMiniItems');
                const topMiniTotalEl = document.getElementById('topMiniTotal');

                function mirror() {
                    if (saleSummaryTotalEl && sourceSummaryTotalEl) saleSummaryTotalEl.textContent = sourceSummaryTotalEl.textContent;
                    if (stickySaleTotalEl && sourceSummaryTotalEl) stickySaleTotalEl.textContent = sourceSummaryTotalEl.textContent;
                    if (saleSummaryItemCountEl && sourceSummaryItemCountEl) saleSummaryItemCountEl.textContent = sourceSummaryItemCountEl.textContent;
                    if (topMiniTotalEl && sourceSummaryTotalEl) topMiniTotalEl.textContent = sourceSummaryTotalEl.textContent;
                    if (topMiniItemsEl && sourceSummaryItemCountEl) topMiniItemsEl.textContent = sourceSummaryItemCountEl.textContent;
                }
                mirror();
                if (sourceSummaryTotalEl) {
                    const mo1 = new MutationObserver(mirror);
                    mo1.observe(sourceSummaryTotalEl, { characterData: true, childList: true, subtree: true });
                }
                if (sourceSummaryItemCountEl) {
                    const mo2 = new MutationObserver(mirror);
                    mo2.observe(sourceSummaryItemCountEl, { characterData: true, childList: true, subtree: true });
                }

                // Prevent scroll from changing number inputs while focused
                (function preventScrollOnNumberInputs() {
                    function onWheelBlock(e) { e.preventDefault(); }
                    function onKeyBlock(e) {
                        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                            e.preventDefault();
                        }
                    }
                    document.addEventListener('focusin', function(e) {
                        const t = e.target;
                        if (t && t.tagName === 'INPUT' && t.type === 'number') {
                            t.addEventListener('wheel', onWheelBlock, { passive: false });
                            t.addEventListener('keydown', onKeyBlock);
                        }
                    });
                    document.addEventListener('focusout', function(e) {
                        const t = e.target;
                        if (t && t.tagName === 'INPUT' && t.type === 'number') {
                            t.removeEventListener('wheel', onWheelBlock);
                            t.removeEventListener('keydown', onKeyBlock);
                        }
                    });
                })();

                const form = document.getElementById('saleForm');
                const submitBtn = form.querySelector('button[type="submit"]');
                const submitSaleBtn = document.getElementById('submitSaleBtn');
                if (submitSaleBtn && form) {
                    submitSaleBtn.addEventListener('click', function() {
                        if (form.requestSubmit) form.requestSubmit(); else form.submit();
                    });
                }

                // Prevent double submit and show loading overlay early to reduce user retries on slow links
                form.addEventListener('submit', function (e) {
                    if (submitBtn.disabled) {
                        e.preventDefault();
                        return false;
                    }
                    submitBtn.disabled = true;
                    submitBtn.classList.add('disabled');
                    document.getElementById('loadingOverlay').style.display = 'block';
                    // Allow form to submit normally
                }, { once: true });
            });

            // Retry customer search function (used by error retry button)
            function retryCustomerSearch(phoneNumber) {
                const customerSearchResults = document.getElementById('customerSearchResults');
                const customerStatus = document.getElementById('customerStatus');

                if (phoneNumber) {
                    // Show a loading indicator
                    customerSearchResults.innerHTML = '<div class="text-center"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Searching again...</div>';
                    customerSearchResults.classList.remove('d-none');

                    // Fetch customer data with correct parameter
                    fetch(`/customers/search?query=${phoneNumber}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.customers && data.customers.length > 0) {
                                // Create HTML for customer results
                                let resultsHtml = '<div class="list-group">';
                                data.customers.forEach(customer => {
                                    resultsHtml += `
                                        <a href="#" class="list-group-item list-group-item-action customer-result-item"
                                           data-customer-id="${customer.id}"
                                           data-customer-name="${customer.name}"
                                           data-customer-phone="${customer.phone}">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">${customer.name}</h6>
                                                <small>${customer.total_visits || 0} visits</small>
                                            </div>
                                            <p class="mb-1">${customer.phone}</p>
                                            <small>Total spent: EGP ${customer.total_spent || 0}</small>
                                        </a>`;
                                });
                                resultsHtml += '</div>';
                                customerSearchResults.innerHTML = resultsHtml;
                            } else {
                                customerSearchResults.innerHTML = `
                                    <div class="alert alert-info">
                                        No customers found with this phone number.
                                        <a href="/customers/create" target="_blank" class="alert-link">Add a new customer</a>
                                    </div>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error searching customers (retry):', error);
                            let errorMessage = 'An unexpected error occurred while searching for customers.';

                            // Provide more specific error messages based on error type
                            if (error.message.includes('HTTP error')) {
                                errorMessage = 'Server communication error. The customer search service may be temporarily unavailable.';
                            } else if (error.name === 'TypeError') {
                                errorMessage = 'Network error. Please check your internet connection and try again.';
                            } else if (error.name === 'SyntaxError') {
                                errorMessage = 'Invalid data received from server. Please try again or contact support.';
                            }

                            customerSearchResults.innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ${errorMessage}
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="retryCustomerSearch('${phoneNumber}')">
                                        <i class="fas fa-sync-alt me-1"></i> Retry Again
                                    </button>
                                </div>`;
                        });
                }
            }

            // Declare calculateTotal as a global function so it can be called from outside
            let calculateTotal;

            $(document).ready(function () {
                // Initialize Select2 on dropdowns
                $('#itemSelect').select2({
                    placeholder: 'Select an item',
                    allowClear: true
                });

                // Define all DOM elements we'll work with
                const itemSelect = document.getElementById('itemSelect');
                const quantityInput = document.getElementById('quantity');
                const addItemButton = document.getElementById('addItemButton');
                const itemList = document.getElementById('itemList');
                const barcodeInput = document.getElementById('barcode');
                const subtotalAmountDisplay = document.getElementById('subtotalAmount');
                const discountAmountDisplay = document.getElementById('discountAmount');
                const totalAmountDisplay = document.getElementById('totalAmount');
                const shippingAmountDisplay = document.getElementById('shippingAmount');
                const discountTypeSelect = document.getElementById('discountType');
                const discountValueInput = document.getElementById('discountValue');
                const saleForm = document.getElementById('saleForm');
                const printGiftReceiptBtn = document.getElementById('printGiftReceiptBtn');
                const customerPhoneInput = document.getElementById('customerPhone');
                const customerNameInput = document.getElementById('customerName');
                const paymentMethodSelect = document.getElementById('paymentMethod');
                const skipCustomerInfoCheckbox = document.getElementById('skipCustomerInfo');
                const clearCartBtn = document.getElementById('clearCartBtn');
                const keyboardShortcutsBtn = document.getElementById('keyboardShortcutsBtn');
                const emptyCartMessage = document.getElementById('emptyCart');
                const summaryItemCount = document.getElementById('summaryItemCount');
                const summaryTotal = document.getElementById('summaryTotal');
                const itemCountDisplay = document.getElementById('itemCount');
                const customerLookupBtn = document.getElementById('customerLookupBtn');
                const customerSearchResults = document.getElementById('customerSearchResults');
                const customerStatus = document.getElementById('customerStatus');
                const customerPhoneError = document.getElementById('customerPhoneError');
                const customerNameError = document.getElementById('customerNameError');
                const addressError = document.getElementById('addressError');
                const discountError = document.getElementById('discountError');
                const rapidScanToggle = document.getElementById('rapidScanToggle');
                const quickCashBtn = document.getElementById('quickCashBtn');
                let rapidScan = true;
                if (rapidScanToggle) {
                    rapidScanToggle.addEventListener('change', () => { rapidScan = rapidScanToggle.checked; });
                }
                // Discount section is always visible; no collapse logic needed

                // Clear inline errors as user types/changes
                if (customerPhoneInput) customerPhoneInput.addEventListener('input', () => clearFieldError(customerPhoneInput, customerPhoneError));
                if (customerNameInput) customerNameInput.addEventListener('input', () => clearFieldError(customerNameInput, customerNameError));
                const addressInputEl = document.getElementById('address');
                if (addressInputEl) addressInputEl.addEventListener('input', () => clearFieldError(addressInputEl, addressError));
                if (discountValueInput) discountValueInput.addEventListener('input', () => clearFieldError(discountValueInput, discountError));
                if (discountTypeSelect) discountTypeSelect.addEventListener('change', () => clearFieldError(discountValueInput, discountError));

                // Track items in cart
                const addedItems = new Map();

                // Initialize keyboard shortcuts
                initializeKeyboardShortcuts();

                // Set up quick action buttons
                initializeQuickActionButtons();

                // Customer information handling
                initializeCustomerInfoHandling();

                // Initialize cart clear button
                clearCartBtn.addEventListener('click', function(e) {
                    if (itemList.children.length === 0) return;
                    const bypass = e.shiftKey;
                    if (bypass || confirm('Clear cart? Hold Shift to bypass this dialog.')) {
                        itemList.innerHTML = '';
                        addedItems.clear();
                        calculateTotal();
                        updateEmptyCartVisibility();
                        showToast('Cart cleared', 'success');
                    }
                });

                // Update empty cart message visibility
                updateEmptyCartVisibility();

                // Initialize discount limit info based on user role
                updateDiscountLimitInfo();

                // Auto-fetch customer name when phone number is entered
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

                // Initialize quick action buttons
                function initializeQuickActionButtons() {
                    document.querySelectorAll('.quick-action-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const action = this.getAttribute('data-action');

                            switch(action) {
                                case 'add-frequently-sold':
                                    const frequentItemsModal = new bootstrap.Modal(document.getElementById('frequentItemsModal'));
                                    frequentItemsModal.show();
                                    break;
                            }
                        });
                    });

                    // Add click handler for popular items in modal
                    document.querySelectorAll('.popular-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const id = this.getAttribute('data-id');
                            const price = parseFloat(this.getAttribute('data-price'));
                            const originalPrice = parseFloat(this.getAttribute('data-original-price'));
                            const name = this.getAttribute('data-name');

                            const item = {
                                id: id,
                                name: name,
                                price: price,
                                originalPrice: originalPrice
                            };

                            if (addItemToList(item, 1)) {
                                // Close the modal that contains this item
                                const modal = this.closest('.modal');
                                bootstrap.Modal.getInstance(modal).hide();
                            }
                        });
                    });
                }

                // Function to initialize keyboard shortcuts
                function initializeKeyboardShortcuts() {
                    // Show keyboard shortcuts modal
                    keyboardShortcutsBtn.addEventListener('click', function() {
                        // Use Bootstrap modal
                        const modal = new bootstrap.Modal(document.getElementById('keyboardShortcutsModal'));
                        modal.show();
                    });

                    // Global key press handlers
                    document.addEventListener('keydown', function(e) {
                        // Slash key for barcode focus
                        if (e.key === '/' && !isInputFocused()) {
                            e.preventDefault();
                            barcodeInput.focus();
                        }

                        // F2 for item select focus
                        if (e.key === 'F2') {
                            e.preventDefault();
                            $('#itemSelect').select2('open');
                        }

                        // F8 for complete sale
                        if (e.key === 'F8') {
                            e.preventDefault();
                            if (validateForm()) {
                                document.getElementById('loadingOverlay').style.display = 'block';
                                saleForm.submit();
                            }
                        }

                        // F7 quick cash checkout
                        if (e.key === 'F7') {
                            e.preventDefault();
                            paymentMethodSelect.value = 'cash';
                            paymentMethodSelect.dispatchEvent(new Event('change'));
                            skipCustomerInfoCheckbox.checked = true;
                            skipCustomerInfoCheckbox.dispatchEvent(new Event('change'));
                            if (validateForm()) {
                                document.getElementById('loadingOverlay').style.display = 'block';
                                saleForm.submit();
                            }
                        }

                        // F9 to cycle through payment methods
                        if (e.key === 'F9') {
                            e.preventDefault();
                            const options = paymentMethodSelect.options;
                            let nextIndex = (paymentMethodSelect.selectedIndex + 1) % options.length;
                            paymentMethodSelect.selectedIndex = nextIndex;
                            paymentMethodSelect.dispatchEvent(new Event('change'));
                        }

                        // G for gift receipt
                        if (e.key === 'g' || e.key === 'G') {
                            if (!isInputFocused()) {
                                e.preventDefault();
                                handlePrintGiftReceipt();
                            }
                        }

                        // Escape to clear current focus or input
                        if (e.key === 'Escape') {
                            const activeElement = document.activeElement;
                            if (activeElement) {
                                if (activeElement.tagName === 'INPUT') {
                                    activeElement.value = '';
                                }
                                activeElement.blur();
                            }
                        }
                    });
                }

                // Check if any input field is currently focused
                function isInputFocused() {
                    const activeElement = document.activeElement;
                    return activeElement && (
                        activeElement.tagName === 'INPUT' ||
                        activeElement.tagName === 'TEXTAREA' ||
                        activeElement.tagName === 'SELECT' ||
                        activeElement.classList.contains('select2-search__field')
                    );
                }

                // Initialize customer info section
                function initializeCustomerInfoHandling() {
                    // Handle walk-in customer checkbox
                    skipCustomerInfoCheckbox.addEventListener('change', function() {
                        const customerInfoSection = document.querySelector('.customer-info-section');

                        if (this.checked) {
                            customerInfoSection.style.opacity = '0.5';
                            customerInfoSection.style.pointerEvents = 'none';
                            customerPhoneInput.removeAttribute('required');
                            customerNameInput.removeAttribute('required');
                            customerPhoneInput.value = '';
                            customerNameInput.value = '';
                        } else {
                            customerInfoSection.style.opacity = '1';
                            customerInfoSection.style.pointerEvents = 'auto';
                            customerPhoneInput.setAttribute('required', 'required');
                            customerNameInput.setAttribute('required', 'required');
                        }
                    });
                    // Apply default state on load (checked by default)
                    skipCustomerInfoCheckbox.dispatchEvent(new Event('change'));

                    // Add debounce function to limit search frequency
                    function debounce(func, wait) {
                        let timeout;
                        return function(...args) {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => func.apply(this, args), wait);
                        };
                    }

                    // Auto search as user types in phone number
                    customerPhoneInput.addEventListener('input', debounce(function() {
                        const phoneNumber = customerPhoneInput.value.trim();
                        if (phoneNumber && phoneNumber.length >= 3) {
                            // Show a loading indicator
                            customerSearchResults.innerHTML = '<div class="text-center"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Searching...</div>';
                            customerSearchResults.classList.remove('d-none');

                            // Get CSRF token from meta tag
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                            // Use the full URL with domain to avoid path issues
                            const baseUrl = window.location.origin;
                            const searchUrl = `${baseUrl}/customers/search?query=${encodeURIComponent(phoneNumber)}`;

                            // Fetch customer data with proper headers
                            fetch(searchUrl, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                credentials: 'same-origin'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    // Instead of throwing error, just handle it silently
                                    console.log('Search response status:', response.status);
                                    customerSearchResults.classList.add('d-none');
                                    return { success: false };
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success && data.customers && data.customers.length > 0) {
                                    // If there's exactly one match and it's an exact match, auto-select it
                                    if (data.customers.length === 1 && data.customers[0].phone === phoneNumber) {
                                        const customer = data.customers[0];
                                        customerNameInput.value = customer.name;
                                        customerPhoneInput.value = customer.phone;
                                        customerStatus.innerHTML = `<span class="text-success"><i class="fas fa-user-check"></i> Selected customer: ${customer.name}</span>`;
                                        customerSearchResults.classList.add('d-none');
                                        return;
                                    }

                                    // Create HTML for customer results
                                    let resultsHtml = '<div class="list-group">';
                                    data.customers.forEach(customer => {
                                        resultsHtml += `
                                            <a href="#" class="list-group-item list-group-item-action customer-result-item"
                                               data-customer-id="${customer.id}"
                                               data-customer-name="${customer.name}"
                                               data-customer-phone="${customer.phone}">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">${customer.name}</h6>
                                                    <small>${customer.total_visits || 0} visits</small>
                                                </div>
                                                <p class="mb-1">${customer.phone}</p>
                                                <small>Total spent: EGP ${customer.total_spent || 0}</small>
                                            </a>`;
                                    });
                                    resultsHtml += '</div>';
                                    customerSearchResults.innerHTML = resultsHtml;
                                } else {
                                    customerSearchResults.innerHTML = `
                                        <div class="alert alert-info">
                                            No customers found with this phone number.
                                            <a href="/customers/create" target="_blank" class="alert-link">Add a new customer</a>
                                        </div>`;
                                }
                            })
                            .catch(error => {
                                // Just hide the search results on error - no error messages shown
                                console.log('Error searching for customer, hiding results');
                                customerSearchResults.classList.add('d-none');
                            });
                        } else if (!phoneNumber) {
                            customerSearchResults.classList.add('d-none');
                            customerStatus.innerHTML = '';
                        }
                    }, 500)); // 500ms debounce delay

                    // Keep the existing customer lookup button functionality as backup
                    customerLookupBtn.addEventListener('click', function() {
                        const phoneNumber = customerPhoneInput.value.trim();
                        if (phoneNumber) {
                            // Show a loading indicator
                            customerSearchResults.innerHTML = '<div class="text-center"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Searching...</div>';
                            customerSearchResults.classList.remove('d-none');

                            // Fetch customer data with correct parameter
                            fetch(`/customers/search?query=${phoneNumber}`)
                                .then(response => {
                                    if (!response.ok) {
                                        // Handle silently instead of showing error
                                        customerSearchResults.classList.add('d-none');
                                        return { success: false };
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success && data.customers && data.customers.length > 0) {
                                        // Create HTML for customer results
                                        let resultsHtml = '<div class="list-group">';
                                        data.customers.forEach(customer => {
                                            resultsHtml += `
                                                <a href="#" class="list-group-item list-group-item-action customer-result-item"
                                                   data-customer-id="${customer.id}"
                                                   data-customer-name="${customer.name}"
                                                   data-customer-phone="${customer.phone}">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">${customer.name}</h6>
                                                        <small>${customer.total_visits || 0} visits</small>
                                                    </div>
                                                    <p class="mb-1">${customer.phone}</p>
                                                    <small>Total spent: EGP ${customer.total_spent || 0}</small>
                                                </a>`;
                                        });
                                        resultsHtml += '</div>';
                                        customerSearchResults.innerHTML = resultsHtml;
                                    } else {
                                        customerSearchResults.innerHTML = `
                                            <div class="alert alert-info">
                                                No customers found with this phone number.
                                                <a href="/customers/create" target="_blank" class="alert-link">Add a new customer</a>
                                            </div>`;
                                    }
                                })
                                .catch(error => {
                                    // Just hide the search results on error - no error messages shown
                                    console.log('Error searching for customer, hiding results');
                                    customerSearchResults.classList.add('d-none');
                                });
                        } else {
                            // Alert if no phone number is entered
                            customerSearchResults.innerHTML = '<div class="alert alert-warning">Please enter a phone number to search</div>';
                            customerSearchResults.classList.remove('d-none');
                        }
                    });

                    // Handle customer search result click
                    customerSearchResults.addEventListener('click', function(e) {
                        const target = e.target.closest('.customer-result-item');
                        if (target) {
                            e.preventDefault();
                            const customerId = target.getAttribute('data-customer-id');
                            const customerName = target.getAttribute('data-customer-name');
                            const customerPhone = target.getAttribute('data-customer-phone');

                            // Fill in the form fields
                            customerNameInput.value = customerName;
                            customerPhoneInput.value = customerPhone;

                            // Update status display
                            customerStatus.innerHTML = `<span class="text-success"><i class="fas fa-user-check"></i> Selected customer: ${customerName}</span>`;

                            // Hide search results
                            customerSearchResults.classList.add('d-none');
                        }
                    });
                }

                // Update the empty cart message visibility
                function updateEmptyCartVisibility() {
                    const itemCount = itemList.children.length;

                    if (itemCount === 0) {
                        emptyCartMessage.style.display = 'block';
                    } else {
                        emptyCartMessage.style.display = 'none';
                    }

                    // Update item count in summary and display
                    summaryItemCount.textContent = itemCount;
                    itemCountDisplay.textContent = itemCount;
                }

                // Update discount limit info based on user role
                function updateDiscountLimitInfo() {
                    const discountLimitInfo = document.getElementById('discountLimitInfo');
                    const userRole = '{{ Auth::user()->role }}';

                    if (userRole === 'cashier') {
                        discountLimitInfo.textContent = 'Cashier limit: 20% or 100 EGP maximum';
                    } else {
                        discountLimitInfo.textContent = '';
                    }
                }

                // Add item to the cart with quantity consolidation
                function addItemToList(item, quantity) {
                    // Ensure quantity is a number
                    quantity = parseInt(quantity) || 1;

                    // If we're using item from dropdown, check stock
                    let stockQuantity = 0;
                    const stockOption = Array.from(itemSelect.options).find(
                        option => option.value === item.id
                    );

                    if (stockOption) {
                        stockQuantity = parseInt(stockOption.getAttribute('data-stock'));
                    } else {
                        // If item is from quick add or other source, look through all options
                        const allOptions = Array.from(itemSelect.options);
                        for (const option of allOptions) {
                            if (option.value === item.id) {
                                stockQuantity = parseInt(option.getAttribute('data-stock'));
                                break;
                            }
                        }
                    }

                    // Check if item is in stock
                    if (stockQuantity <= 0) {
                        showToast('This item is out of stock.', 'error');
                        return false;
                    }

                    // Calculate total requested quantity (existing + new)
                    let totalRequestedQuantity = quantity;
                    if (addedItems.has(item.id)) {
                        totalRequestedQuantity += addedItems.get(item.id).quantity;
                    }

                    // Check if total quantity exceeds stock
                    if (totalRequestedQuantity > stockQuantity) {
                        showToast(`Only ${stockQuantity} available in stock.`, 'error');
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

                    // Recalculate total and update UI
                    calculateTotal();
                    updateEmptyCartVisibility();

                    // Reset the select
                    $('#itemSelect').val(null).trigger('change');

                    return true;
                }

                // Create a new list entry for an item with a simplified design
                function createNewItemListEntry(itemEntry) {
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item p-3 mb-2';
                    listItem.setAttribute('data-item-id', itemEntry.id);

                    // Calculate the total for this item
                    const itemTotal = itemEntry.price * itemEntry.quantity;

                    listItem.innerHTML = `
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="d-flex align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input item-checkbox" checked id="cb-${itemEntry.id}">
                                        <label for="cb-${itemEntry.id}" class="form-check-label fw-medium">
                                            ${itemEntry.name}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <div class="quantity-control">
                                        <button type="button" class="btn btn-sm btn-outline-secondary decrease-quantity">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="quantity-text mx-2">${itemEntry.quantity}</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary increase-quantity">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="price-info me-2">
                                        <span class="item-price">EGP ${itemTotal.toFixed(2)}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="items[${itemEntry.id}][item_id]" value="${itemEntry.id}">
                        <input type="hidden" name="items[${itemEntry.id}][quantity]" value="${itemEntry.quantity}">
                        <input type="hidden" name="items[${itemEntry.id}][price]" value="${itemEntry.price}">
                        <input type="hidden" name="items[${itemEntry.id}][special_discount]" value="0">
                        <input type="hidden" name="items[${itemEntry.id}][as_gift]" value="0">
                    `;

                    itemList.appendChild(listItem);

                    // Add checkbox change event listener (gift item)
                    const checkbox = listItem.querySelector('.item-checkbox');
                    checkbox.addEventListener('change', function () {
                        const itemId = listItem.getAttribute('data-item-id');
                        const asGiftInput = listItem.querySelector(`input[name="items[${itemId}][as_gift]"]`);

                        if (!this.checked) {
                            asGiftInput.value = "1"; // Mark as gift
                            listItem.classList.add('gift-item');
                        } else {
                            asGiftInput.value = "0"; // Mark as regular item
                            listItem.classList.remove('gift-item');
                        }
                        calculateTotal();
                        updateNotesWithGiftItems();
                    });

                    // Remove item handler
                    listItem.querySelector('.remove-item').addEventListener('click', function () {
                        const itemId = listItem.getAttribute('data-item-id');
                        addedItems.delete(itemId);
                        listItem.remove();
                        calculateTotal();
                        updateEmptyCartVisibility();
                    });

                    // Quantity control handlers
                    const decreaseBtn = listItem.querySelector('.decrease-quantity');
                    const increaseBtn = listItem.querySelector('.increase-quantity');
                    const quantityText = listItem.querySelector('.quantity-text');
                    const quantityInput = listItem.querySelector(`input[name="items[${itemEntry.id}][quantity]"]`);
                    const priceInfo = listItem.querySelector('.item-price');

                    decreaseBtn.addEventListener('click', function() {
                        const itemId = listItem.getAttribute('data-item-id');
                        const itemEntry = addedItems.get(itemId);
                        if (itemEntry && itemEntry.quantity > 1) {
                            itemEntry.quantity -= 1;
                            quantityText.textContent = itemEntry.quantity;
                            quantityInput.value = itemEntry.quantity;

                            // Update the displayed price
                            const newTotal = itemEntry.price * itemEntry.quantity;
                            priceInfo.textContent = `EGP ${newTotal.toFixed(2)}`;

                            calculateTotal();
                        }
                    });

                    increaseBtn.addEventListener('click', function() {
                        const itemId = listItem.getAttribute('data-item-id');
                        const itemEntry = addedItems.get(itemId);
                        if (itemEntry) {
                            // Get current stock from any option with this ID
                            const stockOption = Array.from(itemSelect.options).find(
                                option => option.value === itemId
                            );
                            let stockQuantity = 0;
                            if (stockOption) {
                                stockQuantity = parseInt(stockOption.getAttribute('data-stock'));
                            }

                            if (itemEntry.quantity < stockQuantity) {
                                itemEntry.quantity += 1;
                                quantityText.textContent = itemEntry.quantity;
                                quantityInput.value = itemEntry.quantity;

                                // Update the displayed price
                                const newTotal = itemEntry.price * itemEntry.quantity;
                                priceInfo.textContent = `EGP ${newTotal.toFixed(2)}`;

                                calculateTotal();
                            } else {
                                showToast(`Only ${stockQuantity} available in stock.`, 'error');
                            }
                        }
                    });
                }

                // Update an existing item in the list
                function updateItemInList(item, newQuantity) {
                    const existingListItem = itemList.querySelector(`[data-item-id="${item.id}"]`);
                    if (existingListItem) {
                        const quantityText = existingListItem.querySelector('.quantity-text');
                        const quantityInput = existingListItem.querySelector(`input[name="items[${item.id}][quantity]"]`);
                        const priceInfo = existingListItem.querySelector('.item-price');

                        if (quantityText) quantityText.textContent = newQuantity;
                        if (quantityInput) quantityInput.value = newQuantity;

                        // Update the displayed price
                        const itemEntry = addedItems.get(item.id);
                        if (itemEntry && priceInfo) {
                            const newTotal = itemEntry.price * newQuantity;
                            priceInfo.textContent = `EGP ${newTotal.toFixed(2)}`;
                        }
                    }
                }

                // Discount change handlers
                discountTypeSelect.addEventListener('change', function() {
                    calculateTotal();
                });

                discountValueInput.addEventListener('input', function() {
                    const discountType = discountTypeSelect.value;
                    const discountValue = parseFloat(discountValueInput.value) || 0;

                    if (discountType === 'percentage' && discountValue > 100) {
                        showToast('Percentage discount cannot exceed 100%.', 'error');
                        discountValueInput.value = 100;
                    }

                    calculateTotal();
                });

                // Clear discount button
                document.getElementById('clearDiscountBtn').addEventListener('click', function(e) {
                    e.preventDefault();
                    discountTypeSelect.value = 'none';
                    discountValueInput.value = '0';
                    // Trigger change events
                    discountTypeSelect.dispatchEvent(new Event('change'));
                });

                // Ensure Clear doesn't toggle collapse
                const clearBtn = document.getElementById('clearDiscountBtn');
                if (clearBtn) {
                    clearBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });
                }

                // Calculate total price with discount - define as window level function
                calculateTotal = function() {
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
                            const baseItemTotal = itemEntry.price * itemEntry.quantity;

                            if (checkbox && !checkbox.checked) {
                                giftTotal += baseItemTotal;
                            } else {
                                regularTotal += baseItemTotal;
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
                    discountAmountDisplay.textContent = `- EGP ${totalDiscount.toFixed(2)}`;
                    shippingAmountDisplay.textContent = `EGP ${shippingFees.toFixed(2)}`;
                    totalAmountDisplay.textContent = `EGP ${finalTotal.toFixed(2)}`;

                    // Show/hide shipping row
                    const shippingRow = document.querySelector('.shipping-row');
                    if (shippingFees > 0) {
                        shippingRow.style.display = 'flex';
                    } else {
                        shippingRow.style.display = 'none';
                    }

                    // Update hidden inputs
                    document.getElementById('hiddenSubtotal').value = subtotal.toFixed(2);
                    document.getElementById('hiddenTotal').value = finalTotal.toFixed(2);
                    document.getElementById('hiddenDiscountType').value = currentDiscountType;
                    document.getElementById('hiddenDiscountValue').value = currentDiscountValue;

                    // Update sticky summary
                    summaryTotal.textContent = `EGP ${finalTotal.toFixed(2)}`;
                }

                function updateNotesWithGiftItems() {
                    const giftItems = [];
                    itemList.querySelectorAll('li').forEach((item) => {
                        const checkbox = item.querySelector('.item-checkbox');
                        if (checkbox && !checkbox.checked) {
                            const itemName = item.querySelector('.form-check-label').textContent.trim();
                            const quantity = item.querySelector('.quantity-text').textContent;
                            giftItems.push(`${itemName} (${quantity})`);
                        }
                    });

                    const notesField = document.getElementById('notes');
                    const existingNotes = notesField.value.split('\n').filter(line => !line.startsWith('Gift Items:')).join('\n');
                    const giftItemsText = giftItems.length > 0 ? `Gift Items: ${giftItems.join(' | ')}` : '';

                    notesField.value = existingNotes.trim() + (existingNotes.trim() && giftItemsText ? '\n' : '') + giftItemsText;
                }

                // Add item button event handler
                addItemButton.addEventListener('click', function () {
                    const selectedOption = itemSelect.options[itemSelect.selectedIndex];

                    if (selectedOption && selectedOption.value) {
                        const item = {
                            id: selectedOption.value,
                            name: selectedOption.text.split(' (')[0], // Extract name without stock info
                            price: parseFloat(selectedOption.getAttribute('data-price')),
                            originalPrice: parseFloat(selectedOption.getAttribute('data-original-price'))
                        };
                        const quantity = parseInt(quantityInput.value) || 1;

                        addItemToList(item, quantity);
                    }
                });

                // Handle Enter key on quantity input to add item
                quantityInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && itemSelect.value) {
                        e.preventDefault();
                        addItemButton.click();
                    }
                });

                // Focus on barcode button
                document.getElementById('focusBarcode').addEventListener('click', function() {
                    barcodeInput.focus();
                });

                // Barcode handlers: Enter submits, rapid-scan acts on full code
                barcodeInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const { code, qty } = parseBarcodeInput(barcodeInput.value.trim());
                        if (code) handleBarcodeScanning(code, qty);
                        if (rapidScan) setTimeout(() => barcodeInput.focus(), 50);
                    }
                });
                barcodeInput.addEventListener('input', function (e) {
                    const raw = e.target.value.trim();
                    const { code, qty } = parseBarcodeInput(raw);
                    if (rapidScan && code.length >= 8) {
                        handleBarcodeScanning(code, qty);
                        setTimeout(() => { barcodeInput.focus(); }, 50);
                    }
                });

                // Barcode scanning function
                function handleBarcodeScanning(barcode, qty = 1) {
                    // Find the option with matching code
                    const matchingOption = Array.from(itemSelect.options).find(
                        option => option.getAttribute('data-code') === barcode
                    );

                    if (matchingOption) {
                        if (matchingOption.disabled) {
                            showToast('This item is out of stock.', 'error');
                            barcodeInput.value = '';
                            return;
                        }

                        // Select the item and add to list
                        const item = {
                            id: matchingOption.value,
                            name: matchingOption.text.split(' (')[0], // Clean up name
                            price: parseFloat(matchingOption.getAttribute('data-price')),
                            originalPrice: parseFloat(matchingOption.getAttribute('data-original-price'))
                        };

                        if (addItemToList(item, qty)) {
                            barcodeInput.value = '';
                            const li = itemList.querySelector(`[data-item-id="${item.id}"]`);
                            if (li) { li.classList.add('flash'); setTimeout(() => li.classList.remove('flash'), 500); }
                        }
                    } else {
                        showToast('Item not found for the scanned barcode.', 'error');
                        barcodeInput.value = '';
                    }
                }

                // Parse patterns like CODE*3 or CODE x 3
                function parseBarcodeInput(str) {
                    let code = str, qty = 1;
                    const m = str.match(/^(.*?)\s*(?:[xX\*])\s*(\d{1,3})$/);
                    if (m) { code = m[1]; qty = parseInt(m[2]) || 1; }
                    return { code: code.trim(), qty };
                }

                // Payment method change
                paymentMethodSelect.addEventListener('change', function () {
                    const codDetails = document.getElementById('codDetails');

                    if (paymentMethodSelect.value === 'cod') {
                        codDetails.style.display = 'block';
                    } else {
                        codDetails.style.display = 'none';
                        document.getElementById('address').value = '';
                        document.getElementById('shippingFees').value = '0';
                        calculateTotal(); // Recalculate totals
                    }
                });

                // Add shipping fees change handler
                document.getElementById('shippingFees').addEventListener('input', calculateTotal);

                // Print Gift Receipt Handler
                if (printGiftReceiptBtn) {
                    printGiftReceiptBtn.addEventListener('click', handlePrintGiftReceipt);
                }

                if (quickCashBtn) {
                    quickCashBtn.addEventListener('click', function() {
                        paymentMethodSelect.value = 'cash';
                        paymentMethodSelect.dispatchEvent(new Event('change'));
                        skipCustomerInfoCheckbox.checked = true;
                        skipCustomerInfoCheckbox.dispatchEvent(new Event('change'));
                        if (validateForm()) {
                            document.getElementById('loadingOverlay').style.display = 'block';
                            saleForm.submit();
                        }
                    });
                }

                function handlePrintGiftReceipt() {
                    if (itemList.children.length === 0) {
                        alert('Please add at least one item before printing a gift receipt');
                        return;
                    }

                    const saleItems = Array.from(itemList.children).map(item => {
                        const itemId = item.getAttribute('data-item-id');
                        const itemText = item.querySelector('.form-check-label').textContent.trim();
                        const quantity = item.querySelector('.quantity-text').textContent;

                        return {
                            item_id: itemId,
                            name: itemText,
                            quantity: parseInt(quantity)
                        };
                    });

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Show loading overlay
                    document.getElementById('loadingOverlay').style.display = 'block';

                    fetch('{{ route('sales.print-gift-receipt') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            items: saleItems
                        })
                    })
                    .then(response => response.json())
                    .then((data) => {
                        // Hide loading overlay
                        document.getElementById('loadingOverlay').style.display = 'none';

                        if (data.success) {
                            // Show success toast instead of alert
                            showToast('Gift receipt printed successfully', 'success');
                        } else {
                            throw new Error(data.message || 'Failed to print gift receipt');
                        }
                    })
                    .catch(error => {
                        // Hide loading overlay
                        document.getElementById('loadingOverlay').style.display = 'none';

                        console.error('Error:', error);
                        showToast('Error printing gift receipt: ' + error.message, 'error');
                    });
                }

                // Simple toast notification function
                function showToast(message, type = 'info') {
                    // Create toast container if it doesn't exist
                    let toastContainer = document.querySelector('.toast-container');
                    if (!toastContainer) {
                        toastContainer = document.createElement('div');
                        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                        document.body.appendChild(toastContainer);
                    }

                    // Create toast element
                    const toastEl = document.createElement('div');
                    toastEl.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : 'success'} border-0`;
                    toastEl.setAttribute('role', 'alert');
                    toastEl.setAttribute('aria-live', 'assertive');
                    toastEl.setAttribute('aria-atomic', 'true');

                    toastEl.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    `;

                    toastContainer.appendChild(toastEl);

                    // Initialize and show the toast
                    const toast = new bootstrap.Toast(toastEl, {
                        autohide: true,
                        delay: 3000
                    });
                    toast.show();

                    // Remove the toast after it's hidden
                    toastEl.addEventListener('hidden.bs.toast', function() {
                        toastEl.remove();
                    });
                }

                // Tiny beep feedback using WebAudio
                function playBeep(kind = 'success') {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const o = ctx.createOscillator();
                        const g = ctx.createGain();
                        o.type = 'sine';
                        o.frequency.value = kind === 'success' ? 880 : 220;
                        g.gain.value = 0.05;
                        o.connect(g); g.connect(ctx.destination);
                        o.start();
                        setTimeout(() => { o.stop(); ctx.close(); }, 120);
                    } catch (_) { /* ignore */ }
                }

                // Form validation and submission
                saleForm.addEventListener('submit', function (event) {
                    event.preventDefault(); // Prevent default submission

                    // Validate form before submission
                    if (!validateForm()) {
                        return;
                    }

                    // Show loading overlay
                    document.getElementById('loadingOverlay').style.display = 'block';

                    // Submit the form
                    this.submit();
                });

                // Form validation function
                function validateForm() {
                    // Check if cart is empty
                    if (itemList.children.length === 0) {
                        showToast('Please add at least one item to the cart.', 'error');
                        return false;
                    }

                    // If walk-in customer is not checked, validate customer fields
                    if (!skipCustomerInfoCheckbox.checked) {
                        // Phone validation
                        if (!customerPhoneInput.value.trim()) {
                            setFieldError(customerPhoneInput, customerPhoneError, 'Please enter customer phone number.');
                            return false;
                        } else {
                            clearFieldError(customerPhoneInput, customerPhoneError);
                        }
                        // Name validation
                        if (!customerNameInput.value.trim()) {
                            setFieldError(customerNameInput, customerNameError, 'Please enter customer name.');
                            return false;
                        } else {
                            clearFieldError(customerNameInput, customerNameError);
                        }
                    }

                    // If COD is selected, validate address
                    if (paymentMethodSelect.value === 'cod') {
                        const addressInput = document.getElementById('address');
                        if (!addressInput.value.trim()) {
                            setFieldError(addressInput, addressError, 'Please enter delivery address for Cash on Delivery orders.');
                            return false;
                        } else {
                            clearFieldError(addressInput, addressError);
                        }
                    }

                    // Validate discount based on user role
                    const discountType = discountTypeSelect.value;
                    const discountValue = parseFloat(discountValueInput.value) || 0;
                    const userRole = '{{ Auth::user()->role }}';

                    if (userRole === 'cashier') {
                        if (discountType === 'percentage' && discountValue > 20) {
                            setFieldError(discountValueInput, discountError, 'As a cashier, percentage discount cannot exceed 20%.');
                            return false;
                        } else if (discountType === 'fixed' && discountValue > 100) {
                            setFieldError(discountValueInput, discountError, 'As a cashier, fixed discount cannot exceed 100 EGP.');
                            return false;
                        } else {
                            clearFieldError(discountValueInput, discountError);
                        }
                    }

                    return true;
                }
            });

            // Helpers to show/clear inline validation state
            function setFieldError(inputEl, errorEl, message) {
                if (!inputEl || !errorEl) return;
                inputEl.classList.add('is-invalid');
                errorEl.textContent = message;
                errorEl.classList.remove('d-none');
                inputEl.focus();
            }

            function clearFieldError(inputEl, errorEl) {
                if (!inputEl || !errorEl) return;
                inputEl.classList.remove('is-invalid');
                errorEl.textContent = '';
                errorEl.classList.add('d-none');
            }

            // Global special discount update function
            function updateSpecialDiscount(input, itemId) {
                const value = Math.min(100, Math.max(0, parseFloat(input.value) || 0));
                input.value = value;

                const listItem = input.closest('li');
                const specialDiscountInput = listItem.querySelector(`input[name="items[${itemId}][special_discount]"]`);
                specialDiscountInput.value = value;

                calculateTotal();
            }
        </script>
    @endpush

    <!-- Sticky Action Bar -->
    <div class="position-sticky bottom-0 bg-white border-top py-3 mt-3" style="z-index:5;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted">Total</span>
                <strong id="stickySaleTotal">EGP 0.00</strong>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="button" class="btn btn-success" id="submitSaleBtn">Complete Sale</button>
            </div>
        </div>
    </div>
                <style>
    /* Hide number input spinners to reduce accidental changes */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
    /* Quick flash highlight when an item gets added */
    .flash { animation: flashfade 600ms ease-out; }
    @keyframes flashfade { from { background-color: #fef3c7; } to { background-color: transparent; } }
                /* Modern neutral toggle styling (rapid scan & walk-in) */
                .sale-create .form-switch .form-check-input {
                    width: 2.5rem; height: 1.3rem; cursor: pointer; border-radius: 2rem; border: 1px solid #d1d5db;
                    background: linear-gradient(145deg,#f3f4f6,#ffffff); position: relative; transition: background .25s ease, box-shadow .25s ease;
                }
                .sale-create .form-switch .form-check-input:focus { box-shadow: 0 0 0 3px rgba(79,70,229,.25); outline: none; }
                .sale-create .form-switch .form-check-input:checked {
                    background: linear-gradient(145deg,#4f46e5,#6366f1); border-color: #4f46e5;
                }
                .sale-create .form-switch .form-check-input::before {
                    content:""; position:absolute; top:50%; left:4px; transform:translateY(-50%);
                    width:1rem; height:1rem; background:#ffffff; border-radius:50%; box-shadow:0 1px 2px rgba(0,0,0,.25);
                    transition:left .25s cubic-bezier(.4,0,.2,1);
                }
                .sale-create .form-switch .form-check-input:checked::before { left: calc(100% - 1rem - 4px); }
                .sale-create .form-switch .form-check-input:not(:checked) { background:#e5e7eb; }
                .sale-create .form-switch label.form-check-label { font-size: .75rem; font-weight:500; color:#374151; }
                .sale-create .form-switch .form-check-input:checked + .form-check-label { color:#111827; }
    </style>
@endsection
