@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold text-success">
                        <i class="fas fa-check-circle me-2"></i>Import Summary
                    </h1>
                    <p class="text-muted mb-0">Review the results of your bulk import operation</p>
                </div>
                <div>
                    <a href="{{ route('items.bulkImportPage') }}" class="btn btn-primary me-2">
                        <i class="fas fa-upload me-1"></i> Import More Items
                    </a>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-1"></i> Back to All Items
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                @if(isset($results))
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                            <h3 class="text-success mb-1">{{ count($results['created_items']) }}</h3>
                            <p class="text-muted mb-0">Items Created</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-edit fa-3x text-info mb-3"></i>
                            <h3 class="text-info mb-1">{{ count($results['updated_items']) }}</h3>
                            <p class="text-muted mb-0">Items Updated</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h3 class="text-warning mb-1">{{ count($results['warnings']) }}</h3>
                            <p class="text-muted mb-0">Warnings</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                            <h3 class="text-danger mb-1">{{ count($results['errors']) }}</h3>
                            <p class="text-muted mb-0">Errors</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Detailed Results -->
            @if(isset($results))
            <div class="row">
                <!-- Created Items -->
                @if(count($results['created_items']) > 0)
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Items Created</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach(array_slice($results['created_items'], 0, 10) as $item)
                                <div class="list-group-item border-0">
                                    <i class="fas fa-check text-success me-2"></i>{{ $item }}
                                </div>
                                @endforeach
                                @if(count($results['created_items']) > 10)
                                <div class="list-group-item border-0 text-muted">
                                    <i class="fas fa-ellipsis-h me-2"></i>and {{ count($results['created_items']) - 10 }} more items...
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Updated Items -->
                @if(count($results['updated_items']) > 0)
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Updated Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach(array_slice($results['updated_items'], 0, 10) as $item)
                                <div class="list-group-item border-0">
                                    <i class="fas fa-sync text-info me-2"></i>{{ $item }}
                                </div>
                                @endforeach
                                @if(count($results['updated_items']) > 10)
                                <div class="list-group-item border-0 text-muted">
                                    <i class="fas fa-ellipsis-h me-2"></i>and {{ count($results['updated_items']) - 10 }} more items...
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Errors -->
                @if(count($results['errors']) > 0)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Errors ({{ count($results['errors']) }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <strong>The following errors were encountered during import:</strong>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach($results['errors'] as $error)
                                <div class="list-group-item border-0 text-danger">
                                    <i class="fas fa-times me-2"></i>{{ $error }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Warnings -->
                @if(count($results['warnings']) > 0)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Warnings ({{ count($results['warnings']) }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($results['warnings'] as $warning)
                                <div class="list-group-item border-0 text-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $warning }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Next Steps -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-lightbulb me-2 text-warning"></i>What's Next?</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-eye fa-2x text-primary mb-2"></i>
                                        <h6>Review Items</h6>
                                        <p class="text-muted small">Check your imported items and verify the data</p>
                                        <a href="{{ route('items.index') }}" class="btn btn-outline-primary btn-sm">View Items</a>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-barcode fa-2x text-info mb-2"></i>
                                        <h6>Generate Barcodes</h6>
                                        <p class="text-muted small">Create barcodes for your new items</p>
                                        <button class="btn btn-outline-info btn-sm" onclick="generateBarcodes()">Generate</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-upload fa-2x text-success mb-2"></i>
                                        <h6>Import More</h6>
                                        <p class="text-muted small">Continue adding more items to your inventory</p>
                                        <a href="{{ route('items.bulkImportPage') }}" class="btn btn-outline-success btn-sm">Import More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function generateBarcodes() {
    // You can implement the barcode generation functionality here
    // This could make an AJAX call to generate barcodes for new items
    alert('Barcode generation feature will be implemented here');
}
</script>
@endpush
@endsection
