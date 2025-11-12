<div class="btn-toolbar gap-2" role="toolbar" aria-label="Items actions">
  <div class="btn-group btn-group-sm" role="group" aria-label="Primary">
    <a href="{{ route('items.create') }}" class="btn btn-primary" title="Create a new item" aria-label="Create a new item">
      <i class="fas fa-plus-circle me-1"></i> New Item
    </a>
    <a href="{{ route('items.bulkImportPage') }}" class="btn btn-outline-success" title="Import items from an Excel file" aria-label="Import items from an Excel file">
      <i class="fas fa-upload me-1"></i> Import Items (Excel)
    </a>
  </div>

  <div class="btn-group btn-group-sm" role="group" aria-label="Labels and barcodes">
    <button id="generateBarcodeBtn" class="btn btn-outline-secondary" title="Create barcodes for items" aria-label="Create barcodes for items">
      <i class="fas fa-barcode me-1"></i> Create Barcodes
    </button>
    <button id="bulkPrintBtn" class="btn btn-outline-dark" disabled title="Print labels for selected items" aria-label="Print labels for selected items">
      <i class="fas fa-print me-1"></i> Print Selected Labels
    </button>
  </div>

  <div class="btn-group btn-group-sm" role="group" aria-label="Export">
    <a href="{{ route('items.exportXlsx') }}" class="btn btn-outline-secondary" title="Download items to an Excel file" aria-label="Download items to an Excel file">
      <i class="fas fa-file-excel me-1"></i> Export to Excel
    </a>
  </div>
</div>
