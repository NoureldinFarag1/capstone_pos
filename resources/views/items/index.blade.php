@extends('layouts.dashboard')

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center my-4">
        <h1 class="fw-bold">Items</h1>
        <a href="{{ route('items.create') }}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Add new item to the stock">
            <i class="fas fa-plus-circle"></i> Add New Item
          </a>
    </div>

    <!-- Filter and Export Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <!-- Export Dropdown -->
        <div class="dropdown me-3">
            <button class="btn btn-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-export"></i> Export Items
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                <li><a class="dropdown-item" href="{{ route('items.export') }}">All Brands</a></li>
                @foreach($brands as $brand)
                    <li><a class="dropdown-item" href="{{ route('items.export', ['brand_id' => $brand->id]) }}">{{ $brand->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('items.index') }}" method="GET" class="d-flex">
            <select name="brand_id" class="form-select form-select me-2">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>
    </div>
    <!-- Modern Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Previous Page Link -->
                @if ($items->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $items->previousPageUrl() }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                @endif

                <!-- Page Number Links -->
                @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $items->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                <!-- Next Page Link -->
                @if ($items->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $items->nextPageUrl() }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    <!-- Items List -->
    <div class="row g-4">
        @foreach($items as $item)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column">
                        <a href="{{ route('items.show', $item->id) }}" class="text-decoration-none text-dark">
                            <h5 class="card-title fw-bold mb-3">{{ $item->name }}</h5>
                        </a>
                        <div class="mb-2">
                            <p class="mb-1">Price: <span class="fw-bold">${{ number_format($item->selling_price, 2) }}</span></p>
                            @if($item->discount_type === 'percentage')
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">{{ $item->discount_value }}%</span></p>
                            @else
                                <p class="mb-1 text-muted">Sale: <span class="fw-bold">${{ $item->discount_value }}</span></p>
                            @endif
                            <p class="mb-1">Total Amount: <span class="fw-bold">${{ number_format($item->PriceAfterSale(), 2) }}</span></p>
                            <p class="mb-1">Quantity: <span class="fw-bold">{{ $item->quantity }}</span></p>
                        </div>
                        <p class="mt-2">
                            Size:
                            @foreach($item->sizes as $size)
                                <span class="badge bg-secondary">{{ $size->name }}</span>
                            @endforeach
                        </p>
                        <p class="mt-2">
                            Color:
                            @foreach($item->colors as $color)
                                <span class="badge bg-secondary">{{ $color->name }}</span>
                            @endforeach
                        </p>
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="{{ route('items.edit', $item->id) }}" class="btn btn-outline-primary btn-sm" aria-label="Edit item">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @can('admin')
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" aria-label="Delete item">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Modern Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Previous Page Link -->
                @if ($items->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $items->previousPageUrl() }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                @endif

                <!-- Page Number Links -->
                @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $items->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                <!-- Next Page Link -->
                @if ($items->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $items->nextPageUrl() }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</div>
@endsection
