@extends('layouts.dashboard')
@section('title', 'COD Tracking')

@section('content')
<div class="container mx-auto py-6 px-4 max-w-7xl">
    <!-- Header Section with Stats -->
    <div class="bg-white shadow-sm rounded-xl p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">COD Tracking</h1>
                <p class="mt-1 text-sm text-gray-600">Track and manage Cash on Delivery orders</p>
            </div>

            <!-- Stats Cards -->
            <div class="flex flex-wrap gap-4">
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 flex items-center">
                    <div class="mr-3 bg-amber-100 text-amber-700 rounded-full p-2">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <div>
                        <p class="text-xs text-amber-600 font-medium">Pending</p>
                        <p class="text-xl font-bold text-amber-700">{{ $pendingCount }}</p>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 flex items-center">
                    <div class="mr-3 bg-green-100 text-green-700 rounded-full p-2">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p class="text-xs text-green-600 font-medium">Delivered</p>
                        <p class="text-xl font-bold text-green-700">{{ $deliveredCount }}</p>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 flex items-center">
                    <div class="mr-3 bg-blue-100 text-blue-700 rounded-full p-2">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-600 font-medium">Total Value</p>
                        <p class="text-xl font-bold text-blue-700">EGP {{ number_format($totalValue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Component -->
    @if(session('success'))
    <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200 relative" x-data="{ show: true }" x-show="show"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button @click="show = false"
                        class="inline-flex text-green-500 hover:text-green-600 focus:outline-none">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Filter COD Orders</h2>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('sales.cod') }}"
                    class="px-5 py-2.5 rounded-lg text-sm font-medium flex items-center transition-all duration-200 {{ !request()->has('status') ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    <i class="fas fa-list-ul mr-2"></i>
                    All Orders
                    <span class="ml-2 w-6 h-6 flex items-center justify-center rounded-full {{ !request()->has('status') ? 'bg-white text-indigo-600' : 'bg-gray-200 text-gray-700' }}">
                        {{ $pendingCount + $deliveredCount }}
                    </span>
                </a>

                <a href="{{ route('sales.cod', ['status' => 'pending']) }}"
                    class="px-5 py-2.5 rounded-lg text-sm font-medium flex items-center transition-all duration-200 {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    <i class="fas fa-clock mr-2"></i>
                    Pending
                    <span class="ml-2 w-6 h-6 flex items-center justify-center rounded-full {{ request('status') === 'pending' ? 'bg-white text-amber-600' : 'bg-gray-200 text-gray-700' }}">
                        {{ $pendingCount }}
                    </span>
                </a>

                <a href="{{ route('sales.cod', ['status' => 'arrived']) }}"
                    class="px-5 py-2.5 rounded-lg text-sm font-medium flex items-center transition-all duration-200 {{ request('status') === 'arrived' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                    <i class="fas fa-check-circle mr-2"></i>
                    Delivered
                    <span class="ml-2 w-6 h-6 flex items-center justify-center rounded-full {{ request('status') === 'arrived' ? 'bg-white text-green-600' : 'bg-gray-200 text-gray-700' }}">
                        {{ $deliveredCount }}
                    </span>
                </a>
            </div>

            <!-- Search Form -->
            <div class="mt-6">
                <form action="{{ route('sales.cod') }}" method="GET" class="flex items-center space-x-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative flex-grow">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full border border-gray-300 rounded-lg py-2 px-4 pr-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Search by customer name or phone...">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        Search
                    </button>
                    @if(request('search'))
                        <a href="{{ request()->url() }}{{ request('status') ? '?status='.request('status') : '' }}" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times-circle"></i> Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Transaction Details</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer Info</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50 transition-colors {{ $sale->is_arrived === 'pending' ? 'bg-amber-50' : '' }}">
                        <td class="px-6 py-4 text-gray-800">
                            <a href="{{ route('sales.show', $sale->id) }}" class="no-underline text-indigo-600 hover:text-indigo-800">
                                #{{ $sale->id }}
                            </a>
                            <span class="text-sm text-gray-500">
                                ({{ $sale->sale_date->format('d/m') }} -
                                #{{ str_pad($sale->display_id, 4, '0', STR_PAD_LEFT) }})
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $sale->customer_name }}</div>
                            <div class="text-sm text-gray-500">{{ $sale->customer_phone }}</div>
                            @if($sale->address)
                            <div class="text-xs text-gray-500 mt-1 max-w-xs overflow-hidden text-ellipsis">
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                {{ $sale->address }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800">EGP {{ number_format($sale->total_amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-800">
                            <div>{{ $sale->created_at->format('Y-m-d') }}</div>
                            <div class="text-xs text-gray-500">{{ $sale->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($sale->is_arrived === 'pending')
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                <span class="w-2 h-2 bg-amber-500 rounded-full mr-1.5 animate-pulse"></span>
                                Pending Delivery
                            </span>
                            @elseif($sale->is_arrived === 'arrived')
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                <i class="fas fa-check-circle mr-1.5 text-green-500"></i>
                                Delivered
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                Unknown
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <!-- View Button -->
                                <a href="{{ route('sales.show', $sale->id) }}"
                                    class="bg-indigo-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm hover:bg-indigo-700 transition-colors duration-200 no-underline">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>

                                <!-- Update Status -->
                                <form action="{{ route('sales.updateCodStatus', $sale->id) }}" method="POST">
                                    @csrf
                                    @if($sale->is_arrived === 'pending')
                                    <input type="hidden" name="is_arrived" value="arrived">
                                    <button type="submit"
                                        class="bg-green-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm hover:bg-green-700 transition-colors duration-200">
                                        <i class="fas fa-check mr-1"></i> Mark Delivered
                                    </button>
                                    @elseif($sale->is_arrived === 'arrived')
                                    <input type="hidden" name="is_arrived" value="pending">
                                    <button type="submit"
                                        class="bg-amber-500 text-white px-3 py-1.5 rounded-md text-sm shadow-sm hover:bg-amber-600 transition-colors duration-200">
                                        <i class="fas fa-undo mr-1"></i> Revert to Pending
                                    </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-truck text-gray-400 text-xl"></i>
                                </div>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No COD sales found</h3>
                                <p class="mt-1 text-sm text-gray-500">No cash on delivery orders match your criteria.</p>
                                <a href="{{ route('sales.cod') }}" class="mt-4 text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-sync-alt mr-1"></i> Reset Filters
                                </a>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('form[action*="updateCodStatus"]').forEach(form => {
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const isMarking = form.querySelector('input[name="is_arrived"]').value === 'arrived';
        const title = isMarking ? 'Mark as Delivered?' : 'Return to Pending?';
        const text = isMarking
            ? 'This will mark the order as successfully delivered. Proceed?'
            : 'This will change the status back to pending. Proceed?';

        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isMarking ? '#10b981' : '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: isMarking ? 'Yes, Delivered!' : 'Yes, Set as Pending',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
