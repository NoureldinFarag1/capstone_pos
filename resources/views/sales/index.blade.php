@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-6 px-4">
    <!-- Title Section -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Sales Management</h1>
        <a href="{{ route('sales.create') }}" class="bg-blue-600 text-white px-5 py-3 rounded-md shadow hover:bg-blue-700 transition">
            + Create New Sale
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success p-4 mb-6 text-green-800 bg-green-200 rounded-md shadow-md">
            {{ session('success') }}
        </div>
    @endif

    <!-- Action Bar -->
    <div class="bg-gray-50 p-6 rounded-md shadow-md mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <!-- Search Form -->
            <form method="GET" action="{{ route('sales.index') }}" class="flex flex-1 items-center gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by Transaction ID"
                    class="w-full md:w-80 border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                />
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700">
                    Search
                </button>
            </form>

            <!-- Filter & Export -->
            <form action="{{ route('items.exportCSV') }}" method="POST" class="flex items-center gap-3">
                @csrf
                <select name="brand_id" class="border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="start_date" class="border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500" />
                <input type="date" name="end_date" class="border-gray-300 rounded-md p-2 text-sm focus:ring-blue-500 focus:border-blue-500" />
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-md shadow hover:bg-green-800">
                    Export CSV
                </button>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white shadow rounded-md overflow-x-auto">
        <table class="min-w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Total Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Actions</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Issued by</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sales as $sale)
                <tr class="{{ request('search') == $sale->id ? 'bg-yellow-100' : '' }}">
                    <td class="px-6 py-4 text-gray-800">{{ $sale->id }}</td>
                    <td class="px-6 py-4 text-gray-800">${{ number_format($sale->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
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
                                    Partial Refund
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
                            <td>{{ $sale->user ? $sale->user->name : 'Unknown User' }}</td>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @switch($sale->refund_status)
                            @case('no_refund')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    Active
                                </span>
                                @break
                            @case('partial_refund')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    Partial Refund
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
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-500">
                        No sales records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $sales->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>
@endsection
