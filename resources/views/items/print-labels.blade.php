@extends('layouts.dashboard')

@section('title', 'Print Item Labels')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Print Price Labels</h1>
                    <p class="text-muted">{{ count($labelData) }} items ready for printing</p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print me-2"></i>Print Labels
                    </button>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to All Items
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Labels Grid -->
    <div class="labels-container" id="labelsPrintArea">
        @foreach($labelData as $label)
            <div class="label-item">
                <div class="label-content">
                    <!-- Item Info -->
                    <div class="label-header">
                        <h6 class="item-name">{{ str($label['name'])->limit(25) }}</h6>
                        <span class="brand-name">{{ $label['brand'] }}</span>
                    </div>

                    <!-- Details -->
                    <div class="label-details">
                        <div class="detail-row">
                            <span class="label-key">Category:</span>
                            <span class="label-value">{{ $label['category'] }}</span>
                        </div>
                        @if($label['size'] !== 'N/A')
                        <div class="detail-row">
                            <span class="label-key">Size:</span>
                            <span class="label-value">{{ $label['size'] }}</span>
                        </div>
                        @endif
                        @if($label['color'] !== 'N/A')
                        <div class="detail-row">
                            <span class="label-key">Color:</span>
                            <span class="label-value">{{ $label['color'] }}</span>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="label-key">Qty:</span>
                            <span class="label-value">{{ $label['quantity'] }}</span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="label-price">
                        <span class="price-label">Price:</span>
                        <span class="price-value">EGP {{ $label['price'] }}</span>
                    </div>

                    <!-- Barcode -->
                    @if($label['barcode_path'])
                    <div class="label-barcode">
                        <img src="{{ $label['barcode_path'] }}" alt="Barcode" class="barcode-img">
                        <div class="barcode-text">{{ $label['code'] }}</div>
                    </div>
                    @else
                    <div class="label-code">
                        <span class="code-text">{{ $label['code'] }}</span>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
/* Print Styles */
@media print {
    body * {
        visibility: hidden;
    }
    #labelsPrintArea, #labelsPrintArea * {
        visibility: visible;
    }
    #labelsPrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .labels-container {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 0.2cm !important;
        padding: 0.5cm !important;
    }
    .label-item {
        break-inside: avoid;
        border: 1px solid #000 !important;
        padding: 0.3cm !important;
        margin: 0 !important;
        background: white !important;
        box-shadow: none !important;
    }
}

/* Screen Styles */
.labels-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    padding: 1rem 0;
}

.label-item {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    page-break-inside: avoid;
}

.label-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.label-header {
    margin-bottom: 0.75rem;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 0.5rem;
}

.item-name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    color: #333;
}

.brand-name {
    font-size: 0.85rem;
    color: #666;
    font-weight: 500;
}

.label-details {
    margin-bottom: 0.75rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.25rem;
    font-size: 0.85rem;
}

.label-key {
    font-weight: 500;
    color: #555;
}

.label-value {
    color: #333;
}

.label-price {
    margin-bottom: 0.75rem;
    text-align: center;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.price-label {
    font-size: 0.8rem;
    color: #666;
}

.price-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: #28a745;
    margin-left: 0.5rem;
}

.label-barcode {
    text-align: center;
}

.barcode-img {
    max-width: 100%;
    height: auto;
    margin-bottom: 0.25rem;
}

.barcode-text, .code-text {
    font-size: 0.75rem;
    font-family: monospace;
    color: #666;
}

.label-code {
    text-align: center;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add print button shortcut
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
});
</script>
@endpush
