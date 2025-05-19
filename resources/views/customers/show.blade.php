@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-100 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">Customer Details</h1>
            <div class="flex space-x-3">
                <a href="{{ route('customers.edit', $customer->id) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-md inline-flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('customers.index') }}" class="text-blue-500 hover:text-blue-700">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Personal Information</h2>

                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Name</h3>
                        <p class="mt-1 text-md text-gray-900">{{ $customer->name }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Phone Number</h3>
                        <p class="mt-1 text-md text-gray-900">
                            @if($customer->phone)
                                <div class="flex items-center">
                                    <span id="phone-number">{{ $customer->phone }}</span>
                                    <button class="ml-2 text-gray-400 hover:text-blue-500 focus:outline-none"
                                            onclick="copyToClipboard('{{ $customer->phone }}', 'phone-copy-msg')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <span id="phone-copy-msg" class="ml-2 text-green-500 text-xs hidden">Copied!</span>
                                </div>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Email Address</h3>
                        <p class="mt-1 text-md text-gray-900">
                            {{ $customer->email ?? 'Not provided' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Address</h3>
                        <p class="mt-1 text-md text-gray-900">
                            {{ $customer->address ?? 'Not provided' }}
                        </p>
                    </div>

                    @if($customer->notes)
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                        <p class="mt-1 text-md text-gray-900 whitespace-pre-line">{{ $customer->notes }}</p>
                    </div>
                    @endif
                </div>

                <div>
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Purchase History</h2>

                    <div class="bg-gray-50 p-4 rounded-md mb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Total Visits</h3>
                                <p class="mt-1 text-xl font-bold text-gray-900">{{ $customer->total_visits }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Total Spent</h3>
                                <p class="mt-1 text-xl font-bold text-green-600">EGP {{ number_format($customer->total_spent, 2) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Average Transaction</h3>
                                <p class="mt-1 text-xl font-bold text-blue-600">EGP {{ number_format($customer->averageTransaction, 2) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Last Visit</h3>
                                <p class="mt-1 text-md text-gray-900">
                                    @if($customer->last_visit)
                                        {{ $customer->last_visit->format('Y-m-d') }}
                                        <span class="text-xs text-gray-500 block">{{ $customer->last_visit->diffForHumans() }}</span>
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($customer->sales->count() > 0)
                        <h3 class="text-md font-medium text-gray-700 mb-2">Recent Transactions</h3>
                        <div class="overflow-hidden rounded-md border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($customer->sales as $sale)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                #{{ $sale->id }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $sale->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-green-600">
                                                EGP {{ number_format($sale->total_amount, 2) }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                                <a href="{{ route('sales.show', $sale->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-gray-500">
                            <p>No purchase history available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text, messageId) {
        navigator.clipboard.writeText(text).then(() => {
            const messageSpan = document.getElementById(messageId);
            messageSpan.classList.remove('hidden');
            setTimeout(() => {
                messageSpan.classList.add('hidden');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
</script>
@endsection
