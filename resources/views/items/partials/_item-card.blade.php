<div class="card shadow-sm border-0 h-100 hover-shadow transition">
  @if($item->quantity > 0)
    <div class="position-absolute top-0 end-0 p-2">
      <div class="form-check">
        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}" id="item_{{ $item->id }}">
        <label class="form-check-label" for="item_{{ $item->id }}">
          <span class="visually-hidden">Select for printing</span>
        </label>
      </div>
    </div>
  @endif
  @if(optional($item->brand)->picture)
    <div class="card-header bg-light border-0 py-2">
      <img src="{{ asset('storage/' . $item->brand->picture) }}" alt="{{ $item->brand->name }}" class="brand-logo-sm" style="height: 30px; object-fit: contain;">
    </div>
  @endif
  <div class="card-body d-flex flex-column">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <a href="{{ route('items.show', $item->id) }}" class="text-decoration-none text-reset">
        <h5 class="card-title fw-bold m-0">
          {{ $item->name }}@if(optional($item->brand)->name) - {{ $item->brand->name }}@endif
        </h5>
      </a>
      @if($item->quantity <= 0)
        <div class="stock-badge">
          <span class="badge bg-danger">Out of Stock</span>
        </div>
      @endif
    </div>

    <div class="item-details">
      <div class="price-info mb-2">
        <p class="mb-1 d-flex justify-content-between small">
          <span class="text-muted">Base Price:</span>
          <span class="fw-bold">EGP{{ number_format($item->selling_price, 2) }}</span>
        </p>
        @if($item->discount_type === 'percentage')
          <p class="mb-1 d-flex justify-content-between small">
            <span class="text-muted">Discount:</span>
            <span class='text-danger'>{{ $item->discount_value }}% OFF</span>
          </p>
        @elseif($item->discount_type === 'fixed')
          <p class="mb-1 d-flex justify-content-between small">
            <span class="text-muted">Discount:</span>
            <span class='text-danger'>EGP{{ number_format($item->discount_value, 2) }} OFF</span>
          </p>
        @endif
        <p class="mb-0 d-flex justify-content-between small">
          <span class="text-muted">Final Price:</span>
          <span class="fw-bold">EGP{{ number_format($item->priceAfterSale(), 2) }}</span>
        </p>
      </div>
      <p class="mb-0 d-flex justify-content-between align-items-center small">
        <span class="text-muted">Stock:</span>
        <span class="fw-medium">{{ $item->quantity }} units</span>
      </p>
      <div class="mt-2 d-flex justify-content-between align-items-center text-muted small">
        <span>Last updated</span>
        <span>{{ $item->updatedBy->name ?? 'System' }} · {{ $item->updated_at->diffForHumans() }}</span>
      </div>
    </div>

    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
      <div class="d-flex gap-2">
        @role('admin|moderator')
          <a href="{{ route('items.edit', $item->id) }}" class="btn btn-light btn-sm border">
            <i class="fas fa-edit"></i> Edit
          </a>
        @endrole
        @if($item->quantity > 0)
          <a href="{{ route('items.printSingleLabel', $item->id) }}" class="btn btn-light btn-sm border" title="Print label for this item">
            <i class="fas fa-print"></i> Print Label
          </a>
        @endif
      </div>
      @role('admin')
        <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="delete-item-form">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-light btn-sm border text-danger">
            <i class="fas fa-trash-alt"></i> Delete
          </button>
        </form>
      @endrole
    </div>
  </div>
</div>
