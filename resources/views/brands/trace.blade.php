@extends('layouts.dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
<style>
.ts-wrapper {
    width: 100%;
}

.ts-control {
    background-color: #fff !important;
    border-color: #e5e7eb !important;
    border-radius: 0.5rem !important;
    padding: 0.625rem !important;
    min-height: 46px !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
}

.ts-dropdown {
    border-radius: 0.5rem !important;
    margin-top: 0.5rem !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.brand-stats {
    @apply grid grid-cols-1 md: grid-cols-3 gap-6 mb-8;
}

.stats-card {
    @apply p-6 rounded-xl shadow-lg transition-all duration-300;
    background: linear-gradient(135deg, var(--tw-gradient-from) 0%, var(--tw-gradient-to) 100%);
}

.stats-card:hover {
    transform: translateY(-2px);
    @apply shadow-xl;
}

.search-section {
    background: linear-gradient(to right, #f8fafc, #f1f5f9);
    @apply rounded-xl shadow-lg p-6 mb-8 transition-all duration-300;
}

.search-section:hover {
    @apply shadow-xl;
}

.results-table {
    @apply bg-white rounded-xl shadow-lg overflow-hidden;
}

.table-header {
    @apply bg-gray-50 text-gray-600 font-medium px-6 py-3 text-left text-xs uppercase tracking-wider;
}

.table-row {
    @apply hover: bg-gray-50 transition-colors duration-200;
}

.table-cell {
    @apply px-6 py-4 whitespace-nowrap text-sm;
}

.badge {
    @apply px-3 py-1 rounded-full text-sm font-semibold;
}

.badge-blue {
    @apply bg-blue-100 text-blue-800;
}

.badge-green {
    @apply bg-green-100 text-green-800;
}

.trace-button {
    background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
    @apply text-white rounded-lg px-5 py-3 transition duration-200;
}

.trace-button:hover {
    transform: translateY(-2px);
    @apply shadow-lg;
}

.empty-state {
    @apply flex flex-col items-center justify-center p-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

.loading-spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #4f46e5;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Enhanced Styles */
.trace-container {
    background: linear-gradient(to right bottom, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.3));
    backdrop-filter: blur(2rem);
    border-radius: 1rem;
}

.search-container {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.results-table {
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.results-table th {
    background: #f8fafc;
    padding: 1rem;
    font-weight: 500;
    color: #4b5563;
}

.results-table td {
    padding: 1rem;
    color: #374151;
}

.results-table tr:hover {
    background: #f1f5f9;
    transition: all 0.3s ease;
}

.stats-card {
    @apply p-4 rounded-lg border border-gray-200 bg-white shadow-sm;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1
                    class="text-3xl font-bold text-gray-900 flex items-center gap-3 animate__animated animate__fadeInDown">
                    <i class="fas fa-chart-line text-blue-500"></i>
                    Brand Performance Tracker
                </h1>
                <p class="mt-2 text-gray-600 animate__animated animate__fadeInUp">
                    Monitor sales performance and analytics by brand
                </p>
            </div>
            @if(isset($selectedBrand))
            <div class="badge badge-blue">
                <h1
                    class="text-3xl font-bold text-gray-900 flex items-center gap-3 animate__animated animate__fadeInDown">
                    Tracking: {{ $selectedBrand->name }}
                </h1>
            </div>
            @endif
        </div>

        @if(session('error'))
        <div
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate__animated animate__fadeIn">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Enhanced Search Section -->
        <div class="search-section p-6 mb-8">
            <form id="traceForm" action="{{ route('brands.trace.search') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">
                            <i class="fas fa-search mr-2 text-blue-500"></i>
                            Search Brands
                        </h3>
                        <span class="text-sm text-gray-500">{{ $brands->count() }} brands available</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Select Brand to Trace
                            </label>
                            <select id="brandSelect" name="brand_name" class="form-select w-full">
                                <option value="">Select a brand</option>
                                @foreach($brands->sortBy('name') as $brand)
                                <option value="{{ $brand->name }}"
                                    {{ isset($selectedBrand) && $selectedBrand->id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start
                                Date</label>
                            <input type="date" id="start_date" name="start_date"
                                value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" id="end_date" name="end_date"
                                value="{{ request('end_date', now()->format('Y-m-d')) }}"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="md:col-span-1 flex items-end">
                        <button type="submit"
                            class="trace-button w-full text-white rounded-lg px-5 py-3 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i>
                            Trace History
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if(isset($selectedBrand))
        @if($sales->count() > 0)
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 animate__animated animate__fadeIn">
            <div class="stats-card">
                <div class="text-sm text-gray-600">Total Sales Count</div>
                <div class="text-2xl font-bold text-blue-600">{{ $sales->count() }}</div>
            </div>
            <div class="stats-card">
                <div class="text-sm text-gray-600">Total Units Sold</div>
                <div class="text-2xl font-bold text-green-600">
                    {{ $sales->sum(function($sale) { return $sale->items->sum('pivot.quantity'); }) }}
                </div>
            </div>
            <div class="stats-card">
                <div class="text-sm text-gray-600">Total Revenue</div>
                <div class="text-2xl font-bold text-indigo-600">
                    {{ number_format($sales->sum(function($sale) {
                            return $sale->items->sum(function($item) {
                                return $item->pivot->quantity * $item->pivot->price;
                            });
                        }), 2) }} EGP
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="results-table bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 results-table">
                    <thead class="table-header">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-receipt mr-2"></i>Receipt #
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sales as $sale)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-blue-600 font-medium">
                                #{{ $sale->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                {{ $sale->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @foreach($sale->items as $item)
                                    <div>{{ $item->name }}</div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                    {{ $sale->items->sum('pivot.quantity') }} units
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-green-600">
                                {{ number_format($sale->items->sum(function ($item) {
                                                        return $item->pivot->quantity * $item->pivot->price;
                                                    }), 2) }} EGP
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('sales.show', $sale->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-eye mr-1"></i>View Sale
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <!-- Enhanced No Results State -->
        <div
            class="flex flex-col items-center justify-center p-8 bg-red-50 rounded-lg border border-red-200 no-results">
            <div class="h-20 w-20 text-red-400 mb-4">
                <i class="fas fa-exclamation-triangle text-6xl animate__animated animate__pulse animate__infinite"></i>
            </div>
            <h3 class="text-xl font-semibold text-red-900">No Sales Records Found</h3>
            <p class="text-red-700 mt-2 text-center">
                There are no sales records for "{{ $selectedBrand->name }}" matching the selected criteria.
            </p>
        </div>
        @endif
        @else
        <!-- Initial State Message -->
        <div class="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-lg border border-gray-200">
            <div class="h-20 w-20 text-gray-400 mb-4">
                <i class="fas fa-search text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900">Start Tracing Brands</h3>
            <p class="text-gray-700 mt-2 text-center">
                Select a brand from the dropdown to view its sales history and performance metrics.
            </p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#brandSelect').select2({
        placeholder: 'Select a brand to analyze',
        allowClear: true,
        theme: 'classic',
        dropdownCssClass: 'text-sm'
    });
});

function printReport() {
    window.print();
}
</script>
@endpush
@endsection