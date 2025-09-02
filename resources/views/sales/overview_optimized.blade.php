@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-6 space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Sales Overview</h1>
        <div class="text-sm text-gray-500">
            Last updated: {{ now()->format('M d, Y H:i') }}
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Sales -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Today's Sales</h3>
                    <p class="text-2xl font-bold text-gray-900">EGP {{ number_format($todaySales, 2) }}</p>
                    @if($dailyGrowth > 0)
                        <span class="text-green-600 text-xs font-medium">+{{ number_format($dailyGrowth, 1) }}%</span>
                    @elseif($dailyGrowth < 0)
                        <span class="text-red-600 text-xs font-medium">{{ number_format($dailyGrowth, 1) }}%</span>
                    @endif
                </div>
                <div class="text-blue-500">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Weekly Sales -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Weekly Sales</h3>
                    <p class="text-2xl font-bold text-gray-900">EGP {{ number_format($weeklySales, 2) }}</p>
                    @if($weeklyGrowth > 0)
                        <span class="text-green-600 text-xs font-medium">+{{ number_format($weeklyGrowth, 1) }}%</span>
                    @elseif($weeklyGrowth < 0)
                        <span class="text-red-600 text-xs font-medium">{{ number_format($weeklyGrowth, 1) }}%</span>
                    @endif
                </div>
                <div class="text-green-500">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Monthly Sales</h3>
                    <p class="text-2xl font-bold text-gray-900">EGP {{ number_format($monthlySales, 2) }}</p>
                </div>
                <div class="text-purple-500">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Avg Order Value</h3>
                    <p class="text-2xl font-bold text-gray-900">EGP {{ number_format($avgOrderValue, 2) }}</p>
                    <span class="text-gray-600 text-xs">This month</span>
                </div>
                <div class="text-yellow-500">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Sales Trend -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sales Trend (Last 7 Days)</h3>
            <div class="h-64">
                <canvas id="dailyTrendChart"></canvas>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Methods (This Month)</h3>
            <div class="h-64">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Top Items -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Selling Items (This Month)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topItems as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->total_sold }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">EGP {{ number_format($item->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Brands -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Brands (This Month)</h3>
            <div class="space-y-4">
                @forelse($brandSales as $brand)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        @if($brand->picture)
                            <img src="{{ asset('storage/' . $brand->picture) }}" alt="{{ $brand->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-gray-600">{{ substr($brand->name, 0, 2) }}</span>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900">{{ $brand->name }}</p>
                            <p class="text-sm text-gray-500">{{ $brand->total_sold }} units</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">EGP {{ number_format($brand->total_revenue, 2) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Customer Insights and Large Transactions -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Top Customers -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Customers (This Month)</h3>
            <div class="space-y-3">
                @forelse($topCustomers as $customer)
                <div class="flex items-center justify-between p-3 border-b border-gray-100 last:border-b-0">
                    <div>
                        <p class="font-medium text-gray-900">{{ $customer->customer_name ?: 'Guest' }}</p>
                        <p class="text-sm text-gray-500">{{ $customer->customer_phone ?: 'No phone' }} • {{ $customer->order_count }} orders</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">EGP {{ number_format($customer->total_spent, 2) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-sm text-gray-500">No customer data available</p>
                @endforelse
            </div>
        </div>

        <!-- Large Transactions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Large Transactions</h3>
            <div class="space-y-3">
                @forelse($largeTransactions as $transaction)
                <div class="flex items-center justify-between p-3 border-b border-gray-100 last:border-b-0">
                    <div>
                        <p class="font-medium text-gray-900">Sale #{{ $transaction->display_id }}</p>
                        <p class="text-sm text-gray-500">{{ $transaction->customer_name ?: 'Guest Customer' }}</p>
                        <p class="text-xs text-gray-400">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-lg text-green-600">EGP {{ number_format($transaction->total_amount, 2) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-sm text-gray-500">No large transactions found</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Sales Trend Chart
    const dailyTrendCtx = document.getElementById('dailyTrendChart').getContext('2d');
    const dailyTrendData = @json($dailySalesTrend);

    new Chart(dailyTrendCtx, {
        type: 'line',
        data: {
            labels: dailyTrendData.map(item => new Date(item.date).toLocaleDateString()),
            datasets: [{
                label: 'Sales Amount',
                data: dailyTrendData.map(item => item.total_amount),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'EGP' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Payment Methods Chart
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    const paymentMethodData = @json($paymentMethods);

    new Chart(paymentMethodCtx, {
        type: 'doughnut',
        data: {
            labels: paymentMethodData.map(item => item.payment_method || 'Unknown'),
            datasets: [{
                data: paymentMethodData.map(item => item.total),
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)',
                    'rgb(139, 92, 246)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
@endsection
