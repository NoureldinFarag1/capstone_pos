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

        .ts-dropdown .option {
            padding: 0.75rem 1rem !important;
        }

        .ts-dropdown .active {
            background-color: #EBF5FF !important;
            color: #1D4ED8 !important;
        }

        .ts-wrapper.multi .ts-control>div {
            background: #EBF5FF !important;
            color: #1D4ED8 !important;
            border-radius: 0.375rem !important;
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
            /* animation: fadeIn 0.5s ease-in-out; */
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

        .badge {
            @apply px-3 py-1 rounded-full text-sm font-semibold;
        }

        .badge-blue {
            @apply bg-blue-100 text-blue-800;
        }

        .badge-green {
            @apply bg-green-100 text-green-800;
        }

        .stats-card {
            @apply p-4 rounded-lg border border-gray-200 bg-white shadow-sm;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-section {
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .search-section:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .form-input {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }

        .trace-button {
            background: linear-gradient(to right, #3b82f6, #6366f1);
            transition: all 0.3s ease;
        }

        .trace-button:hover {
            background: linear-gradient(to right, #6366f1, #3b82f6);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .ts-wrapper .ts-control {
            border: 2px solid #e2e8f0 !important;
            transition: all 0.3s ease;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }

        .ts-dropdown .create {
            padding: 1rem !important;
            color: #4b5563 !important;
        }

        .ts-dropdown .option {
            display: flex !important;
            align-items: center !important;
            padding: 0.75rem 1rem !important;
            border-bottom: 1px solid #f3f4f6 !important;
        }

        .ts-dropdown .option:last-child {
            border-bottom: none !important;
        }

        .ts-dropdown .active {
            background: linear-gradient(to right, #dbeafe, #eff6ff) !important;
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
                        <i class="fas fa-search-location text-blue-500"></i>
                        Item Trace System
                    </h1>
                    <p class="mt-2 text-gray-600 animate__animated animate__fadeInUp">Track sales history and performance
                        metrics for any item</p>
                </div>
                @if(isset($selectedItem))
                    <div class="badge badge-blue">
                        Tracking: {{ $selectedItem->name }}
                    </div>
                @endif
            </div>

            <!-- Error Message Display -->
            @if(isset($error))
                <div
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate__animated animate__fadeIn">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
            @endif

            <!-- Enhanced Search Section -->
            <div class="search-section p-6 mb-8">
                <form action="{{ route('items.trace.search') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <!-- Search Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">
                                <i class="fas fa-search-location mr-2 text-blue-500"></i>
                                Search Items
                            </h3>
                            <span class="text-sm text-gray-500">{{ $items->count() }} items available</span>
                        </div>

                        <!-- Search Input Group -->
                        <div class="grid md:grid-cols-4 gap-6">
                            <div class="md:col-span-3">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Select Item to Trace
                                    </label>
                                    <select id="itemSelect" name="item_name" class="form-select">
                                        <option value="">Select an item</option>
                                        @php
                                            $sortedItems = $items->sortBy(function ($item) {
                                                return $item->brand?->name ?? 'No Brand';
                                            });
                                        @endphp
                                        @foreach($sortedItems as $item)
                                            @if(!$item->is_parent)
                                                <option value="{{ $item->name }}" data-code="{{ $item->code }}"
                                                    data-brand="{{ $item->brand?->name ?? 'No Brand' }}">
                                                    {{ $item->brand?->name ?? 'No Brand' }} - {{ $item->name }}
                                                </option>
                                            @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Search by item name.
                                    </p>
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
                    </div>
                </form>
            </div>

            @if(isset($sales))
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
                                {{ $sales->sum(function ($sale) {
                        return $sale->items->first()->pivot->quantity ?? 0; }) }}
                            </div>
                        </div>
                        <div class="stats-card">
                            <div class="text-sm text-gray-600">Average Price</div>
                            <div class="text-2xl font-bold text-purple-600">
                                {{ number_format($sales->avg(function ($sale) {
                        return $sale->items->first()->pivot->price ?? 0;
                    }), 2) }} EGP
                            </div>
                        </div>
                        <div class="stats-card">
                            <div class="text-sm text-gray-600">Total Revenue</div>
                            <div class="text-2xl font-bold text-indigo-600">
                                {{ number_format($sales->sum(function ($sale) {
                        return ($sale->items->first()->pivot->quantity ?? 0) * ($sale->items->first()->pivot->price ?? 0);
                    }), 2) }} EGP
                            </div>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="results-table bg-white">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="group px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i class="fas fa-receipt mr-2"></i>Receipt #
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-2"></i>Date
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i class="fas fa-box mr-2"></i>Quantity
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i class="fas fa-tag mr-2"></i>Price
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <i class="fas fa-money-bill-wave mr-2"></i>Total
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sales as $sale)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-blue-600 font-medium">
                                            #{{ $sale->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                            {{ $sale->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                                {{ $sale->items->first()->pivot->quantity ?? 0 }} units
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                            {{ number_format($sale->items->first()->pivot->price ?? 0, 2) }} EGP
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-green-600">
                                            {{ number_format(($sale->items->first()->pivot->quantity ?? 0) * ($sale->items->first()->pivot->price ?? 0), 2) }}
                                            EGP
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
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-right font-medium text-gray-700">Totals:</td>
                                    <td class="px-6 py-4 font-bold text-blue-600">
                                        {{ $sales->sum(function ($sale) {
                        return $sale->items->first()->pivot->quantity ?? 0; }) }}
                                        units
                                    </td>
                                    <td class="px-6 py-4">-</td>
                                    <td class="px-6 py-4 font-bold text-green-600">
                                        {{ number_format($sales->sum(function ($sale) {
                        return ($sale->items->first()->pivot->quantity ?? 0) * ($sale->items->first()->pivot->price ?? 0);
                    }), 2) }} EGP
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
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
                            There are no sales records for "{{ $selectedItem->name }}" matching the selected criteria.
                            <br>
                            Try selecting a different item or adjusting your search parameters.
                        </p>
                        <div class="mt-6 flex gap-3">
                            <button onclick="window.location.reload()"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                                Reset Search
                            </button>
                            <a href="{{ route('items.index') }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                View All Items
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <!-- Initial State Message -->
                <div class="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="h-20 w-20 text-gray-400 mb-4">
                        <i class="fas fa-search text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Start Tracing Items</h3>
                    <p class="text-gray-700 mt-2 text-center">
                        Select an item from the dropdown to view its sales history and performance metrics.
                    </p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#itemSelect').select2({
                    placeholder: 'Select an item',
                    allowClear: true
                });
            });
        </script>
    @endpush
@endsection