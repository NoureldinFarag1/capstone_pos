@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-100 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">All Customers</h1>
        </div>

        <div class="p-6">
            @if ($customers->isEmpty())
                <div class="text-center text-gray-500 py-4">No customers found.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-auto border-collapse w-full shadow-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Phone</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Latest Purchase Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $customer->customer_name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center">
                                        @if ($customer->customer_phone)
                                            <span id="phone-number-{{ $customer->id }}">{{ $customer->customer_phone }}</span>
                                            <button class="ml-2 text-gray-500 hover:text-blue-500 focus:outline-none"
                                                    onclick="copyToClipboard('{{ $customer->customer_phone }}', 'phone-number-{{ $customer->id }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h6m-2 4h4" />
                                                </svg>
                                            </button>
                                            <span id="copy-message-{{ $customer->id }}" class="ml-2 text-green-500 hidden whitespace-nowrap">Copied!</span>
                                        @else
                                            <span class="text-gray-400">No phone number available</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($customer->latest_sale_date)
                                                {{ \Carbon\Carbon::parse($customer->latest_sale_date)->format('Y-m-d') }}
                                            @else
                                                <span class="text-gray-400">No sale date available</span>
                                            @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($customers->hasPages())
            <div class="px-6 py-4 bg-gray-100 border-t border-gray-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function copyToClipboard(text, elementId) {
        navigator.clipboard.writeText(text).then(() => {
            const messageSpan = document.getElementById('copy-message-' + elementId.split('-')[2]);
            messageSpan.classList.remove('hidden');
            setTimeout(() => {
                messageSpan.classList.add('hidden');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy phone number. Please try again.');
        });
    }
</script>
@endsection
