@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-7xl">
    <!-- Futuristic Header -->
    <div class="relative mb-10 p-6 rounded-2xl bg-white/80 backdrop-blur-xl border border-white/20 shadow-lg">
        <!-- Decorative Background Elements (Clipped) -->
        <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-purple-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight">
                    Sales Command Center
                </h1>
                <p class="mt-2 text-gray-500 font-medium">Monitor transactions and performance in real-time</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('sales.create') }}"
                   class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-indigo-600 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 no-underline">
                    <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-black"></span>
                    <span class="relative flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Sale
                    </span>
                </a>

                <a href="{{ route('items.trace') }}"
                   class="inline-flex items-center px-5 py-3 text-sm font-semibold text-indigo-700 transition-all duration-200 bg-indigo-50 rounded-xl hover:bg-indigo-100 hover:text-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 border border-indigo-200/50 no-underline">
                    <i class="fas fa-search-location mr-2"></i> Trace Item
                </a>

                <!-- Reports Dropdown (Glassmorphism) -->
                <div class="relative" x-data="{ open: false, selectedDate: '{{ now()->format('Y-m-d') }}', startDate: '{{ now()->subDays(30)->format('Y-m-d') }}', endDate: '{{ now()->format('Y-m-d') }}' }">
                    <button @click="open = !open" type="button"
                        class="inline-flex items-center px-5 py-3 text-sm font-semibold text-white transition-all duration-200 bg-emerald-500 rounded-xl hover:bg-emerald-600 shadow-lg shadow-emerald-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 no-underline">
                        <i class="fas fa-chart-pie mr-2"></i> Reports
                        <svg class="ml-2 w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="absolute right-0 mt-3 w-80 origin-top-right bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl ring-1 ring-black/5 focus:outline-none z-50 divide-y divide-gray-100">

                        <!-- Daily Sales -->
                        <div class="p-4">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Daily Reports</h3>
                            <input type="date" x-model="selectedDate" class="w-full mb-3 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <div class="grid grid-cols-2 gap-2">
                                <a href="#" @click.prevent="window.location.href='{{ route('sales.dailyReport') }}?format=excel&date=' + selectedDate" class="flex items-center justify-center px-3 py-2 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors no-underline">
                                    <i class="fas fa-file-excel mr-1.5"></i> Excel
                                </a>
                                <a href="#" @click.prevent="window.location.href='{{ route('sales.dailyReport') }}?format=csv&date=' + selectedDate" class="flex items-center justify-center px-3 py-2 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors no-underline">
                                    <i class="fas fa-file-csv mr-1.5"></i> CSV
                                </a>
                            </div>
                        </div>

                        <!-- Range Reports -->
                        <div class="p-4">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Range Reports</h3>
                            <div class="flex gap-2 mb-3">
                                <input type="date" x-model="startDate" class="w-1/2 px-2 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                                <input type="date" x-model="endDate" class="w-1/2 px-2 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                            </div>
                            <a href="#" @click.prevent="window.location.href='{{ route('sales.dailyTotalsReport') }}?format=excel&start_date=' + startDate + '&end_date=' + endDate" class="block w-full text-center px-3 py-2 text-xs font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors no-underline">
                                <i class="fas fa-layer-group mr-1.5"></i> Generate Totals
                            </a>
                        </div>

                        <!-- Other Reports -->
                        <div class="p-4 bg-gray-50/50 rounded-b-2xl">
                            <div class="space-y-2">
                                <a href="#" @click.prevent="window.location.href='{{ route('sales.paymentMethodReport') }}?format=excel&date=' + selectedDate" class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition-all no-underline">
                                    <i class="fas fa-credit-card w-5 text-center mr-2 text-gray-400"></i> Payment Methods
                                </a>
                                <a href="#" @click.prevent="window.location.href='{{ route('sales.hourlyReport') }}?format=excel&date=' + selectedDate" class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition-all no-underline">
                                    <i class="fas fa-clock w-5 text-center mr-2 text-gray-400"></i> Hourly Analysis
                                </a>
                                <a href="#" @click.prevent="window.location.href='{{ route('sales.refundsReport') }}?format=excel&date=' + selectedDate" class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white rounded-lg transition-all no-underline">
                                    <i class="fas fa-undo w-5 text-center mr-2 text-gray-400"></i> Refunds Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Component (Modernized) -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="mb-8 rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-start gap-3">
        <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
        <div class="flex-1 pt-1">
            <p class="text-sm font-medium text-emerald-900">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    <!-- Control Panel (Filters) -->
    <div class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-1">
        <div class="p-5">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Search -->
                <form method="GET" action="{{ route('sales.index') }}" class="flex-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Search Transactions</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-xl text-gray-900 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200"
                            placeholder="Search by ID, Customer, or Date...">
                        <button type="submit" class="absolute right-2 top-2 bottom-2 px-4 bg-white text-indigo-600 text-sm font-medium rounded-lg shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                            Search
                        </button>
                    </div>
                </form>

                <!-- Filters & Export -->
                <form action="{{ route('items.exportSalesCSV') }}" method="GET" class="flex-1 lg:flex-none flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-48">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Brand</label>
                        <select name="brand_id" class="block w-full py-3 px-4 bg-gray-50 border-transparent rounded-xl text-gray-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">From</label>
                            <input type="date" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}"
                                class="block w-full py-3 px-3 bg-gray-50 border-transparent rounded-xl text-gray-700 text-sm focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">To</label>
                            <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                                class="block w-full py-3 px-3 bg-gray-50 border-transparent rounded-xl text-gray-700 text-sm focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-transparent uppercase tracking-wider mb-2">Action</label>
                        <button type="submit" class="w-full md:w-auto py-3 px-6 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 transition-all shadow-lg shadow-indigo-500/30 flex items-center justify-center gap-2">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Grid -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Transaction</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Date & Time</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Cashier</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse($sales as $sale)
                    <tr class="group hover:bg-indigo-50/30 transition-colors duration-150 cursor-pointer" onclick="window.location='{{ route('sales.show', $sale->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                    #{{ $sale->display_id }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">ID: {{ $sale->id }}</div>
                                    <div class="text-xs text-gray-500">{{ $sale->customer_name ?? 'Walk-in Customer' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">EGP {{ number_format($sale->total_amount, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $sale->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $sale->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 font-medium mr-2">
                                    {{ substr($sale->user ? $sale->user->name : 'U', 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-600">{{ $sale->user ? $sale->user->name : 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($sale->refund_status)
                                @case('no_refund')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Completed
                                    </span>
                                    @break
                                @case('partial_refund')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Partial Refund
                                    </span>
                                    @break
                                @case('full_refund')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Refunded
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Unknown
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-2">
                                @if(in_array($sale->refund_status, ['no_refund', 'partial_refund']))
                                <a href="{{ route('refund.create', $sale->id) }}" class="text-amber-600 hover:text-amber-900 p-2 hover:bg-amber-50 rounded-lg transition-colors no-underline" title="Process Refund">
                                    <i class="fas fa-undo-alt"></i>
                                </a>
                                @endif

                                @role('admin|moderator')
                                <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900 p-2 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Record">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endrole
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">No sales found</h3>
                                <p class="mt-1 text-sm text-gray-500 max-w-xs mx-auto">Try adjusting your search or filters, or create a new sale to get started.</p>
                                <a href="{{ route('sales.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 no-underline">
                                    Create New Sale
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
    <div class="mt-6 flex justify-center">
        <div class="bg-white/50 backdrop-blur-sm rounded-xl p-2 shadow-sm border border-gray-100">
            {{ $sales->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
    /* Custom Scrollbar for Tables */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #c7c7c7;
        border-radius: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Enhanced Delete Confirmation
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Delete Transaction?',
                text: "This action cannot be undone. The transaction record will be permanently removed.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-gray-100',
                    title: 'text-gray-900 font-bold',
                    content: 'text-gray-500',
                    confirmButton: 'rounded-lg px-4 py-2 font-medium shadow-sm',
                    cancelButton: 'rounded-lg px-4 py-2 font-medium shadow-sm'
                }
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
