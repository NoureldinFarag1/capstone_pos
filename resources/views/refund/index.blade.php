@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-6 px-4">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Refund Analytics</h1>
            <p class="text-gray-600 mt-1">Comprehensive refund tracking and analytics</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm font-medium text-red-700 bg-red-50 rounded-full">
                {{ $kpis['total_refunds'] }} Total Refunds
            </span>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Refunds</option>
                    <option value="partial_refund" {{ $status === 'partial_refund' ? 'selected' : '' }}>Partial Refunds</option>
                    <option value="full_refund" {{ $status === 'full_refund' ? 'selected' : '' }}>Full Refunds</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </form>
    </div>

    <!-- KPI Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Refunds -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Refunds</p>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($kpis['total_refunds']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-undo text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Refund Amount -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Amount</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($kpis['total_refund_amount'], 2) }} EGP</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-money-bill text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Refund -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Average Refund</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($kpis['average_refund_amount'], 2) }} EGP</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Refund Rate -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Refund Rate</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($kpis['refund_rate'], 1) }}%</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-percentage text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Refund Reasons -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Refund Reasons</h3>
            @if($kpis['top_refund_reasons']->isNotEmpty())
                <div class="space-y-3">
                    @foreach($kpis['top_refund_reasons'] as $reason)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-orange-500">
                        <div>
                            <p class="font-medium text-gray-900">{{ $reason->reason ?: 'No reason provided' }}</p>
                            <p class="text-sm text-gray-600">{{ $reason->count }} refunds</p>
                        </div>
                        <p class="font-bold text-orange-600">{{ number_format($reason->total_amount, 2) }} EGP</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No refund reasons available for this period.</p>
            @endif
        </div>

        <!-- Top Refunded Items -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Refunded Items</h3>
            @if($kpis['top_refunded_items']->isNotEmpty())
                <div class="space-y-3">
                    @foreach($kpis['top_refunded_items'] as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->item->name ?? 'Unknown Item' }}</p>
                            <p class="text-sm text-gray-600">{{ $item->total_quantity }} units refunded</p>
                        </div>
                        <p class="font-bold text-blue-600">{{ number_format($item->total_amount, 2) }} EGP</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No refunded items data for this period.</p>
            @endif
        </div>
    </div>

    <!-- Refunds Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900">Refund Details</h3>
        </div>

        @if($refunds->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($refunds as $refund)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('sales.show', $refund->sale_id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    #{{ $refund->sale_id }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $refund->item->name ?? 'N/A' }}</p>
                                    @if($refund->item && $refund->item->brand)
                                        <p class="text-sm text-gray-600">{{ $refund->item->brand->name }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                {{ $refund->quantity_refunded }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-orange-600">{{ number_format($refund->refund_amount, 2) }} EGP</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $refund->reason ?: 'No reason provided' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $refund->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($refund->sale->refund_status === 'partial_refund')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Partial
                                    </span>
                                @elseif($refund->sale->refund_status === 'full_refund')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Full
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $refunds->appends(request()->query())->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <i class="fas fa-undo text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No refunds found</h3>
                <p class="text-gray-500">No refunds match the selected criteria for this period.</p>
            </div>
        @endif
    </div>
</div>
@endsection
