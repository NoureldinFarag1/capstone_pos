@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-7xl">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sales Management</h1>
            <p class="mt-1 text-sm text-gray-600">Manage and monitor your sales transactions</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('sales.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Sale
            </a>
            <!-- Daily Sales Report Dropdown -->
            <div class="relative" x-data="{ open: false, selectedDate: '{{ now()->format('Y-m-d') }}' }">
                <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Generate Reports
                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open"
                     @click.away="open = false"
                     class="origin-top-right absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                    <div class="p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                        <input type="date"
                               x-model="selectedDate"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-3">
                        <div class="flex flex-col gap-2">
                            <!-- Daily Sales -->
                            <div class="text-sm font-medium text-gray-900 mb-2">Daily Sales</div>
                            <a href="#"
                               @click.prevent="window.location.href='{{ route('sales.dailyReport') }}?format=excel&date=' + selectedDate"
                               class="text-sm text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Daily Sales (Excel)</a>
                            <a href="#"
                               @click.prevent="window.location.href='{{ route('sales.dailyReport') }}?format=csv&date=' + selectedDate"
                               class="text-sm text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Daily Sales (CSV)</a>

                            <!-- Payment Methods -->
                            <div class="text-sm font-medium text-gray-900 mb-2 mt-3">Payment Methods</div>
                            <a href="#"
                               @click.prevent="window.location.href='{{ route('sales.paymentMethodReport') }}?format=excel&date=' + selectedDate"
                               class="text-sm text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Payment Methods Report</a>

                            <!-- Hourly Sales -->
                            <div class="text-sm font-medium text-gray-900 mb-2 mt-3">Hourly Analysis</div>
                            <a href="#"
                               @click.prevent="window.location.href='{{ route('sales.hourlyReport') }}?format=excel&date=' + selectedDate"
                               class="text-sm text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Hourly Sales Report</a>

                            <!-- Refunds -->
                            <div class="text-sm font-medium text-gray-900 mb-2 mt-3">Refunds</div>
                            <a href="#"
                               @click.prevent="window.location.href='{{ route('sales.refundsReport') }}?format=excel&date=' + selectedDate"
                               class="text-sm text-gray-700 hover:bg-gray-100 px-3 py-2 rounded-md">Refunds Report</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Component -->
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200 relative"
             x-data="{ show: true }"
             x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button @click="show = false" class="inline-flex text-green-500 hover:text-green-600 focus:outline-none">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Enhanced Search Form -->
                <form method="GET" action="{{ route('sales.index') }}" class="flex-1">
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Transaction</label>
                            <div class="relative">
                                <input
                                    id="search"
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Enter Transaction ID or Daily ID..."
                                    class="w-full border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="mt-auto bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition-colors">
                            Search
                        </button>
                    </div>
                </form>

                <!-- Enhanced Filter & Export -->
                <form action="{{ route('items.exportSalesCSV') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">Select Brand</label>
                        <select id="brand_id"
                                name="brand_id"
                                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date"
                               id="start_date"
                               name="start_date"
                               value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date"
                               id="end_date"
                               name="end_date"
                               value="{{ request('end_date', now()->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <button type="submit" class="mt-auto bg-green-600 text-white px-6 py-2 rounded-lg shadow hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Sales Report (Excel)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction Details</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued by</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-800">
                                #{{ $sale->id }}
                                <span class="text-sm text-gray-500">
                                    ({{ $sale->sale_date->format('d/m') }} - #{{ str_pad($sale->display_id, 4, '0', STR_PAD_LEFT) }})
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-800">EGP {{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-800">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4">{{ $sale->user ? $sale->user->name : 'Unknown User' }}</td>
                            <td class="px-6 py-4">
                                @switch($sale->refund_status)
                                    @case('no_refund')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            No Refund
                                        </span>
                                        @break
                                    @case('partial_refund')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            Partially Refunded
                                        </span>
                                        @break
                                    @case('full_refund')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                            Refunded
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            Unknown
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <!-- View Button -->
                                    <a href="{{ route('sales.show', $sale->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700">
                                        View
                                    </a>

                                    <!-- Refund Button -->
                                    @if($sale->refund_status === 'no_refund')
                                        <a href="{{ route('refund.create', $sale->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md shadow hover:bg-yellow-600">
                                            Refund
                                        </a>
                                    @elseif($sale->refund_status === 'partial_refund')
                                        <a href="{{ route('refund.create', $sale->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md shadow hover:bg-yellow-600">
                                            Refund
                                        </a>
                                    @endif

                                    <!-- Delete Button (Admin Only) -->
                                    @role('admin|moderator')
                                        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sale?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md shadow hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No sales found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new sale.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $sales->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>

@push('styles')
<style>
    .fade-out {
        animation: fadeOut 0.3s ease-in-out forwards;
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you absolutely sure?',
                text: "This action is cannot be undone and will permanently delete the sale!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        };
    });
</script>
@endpush
@endsection
