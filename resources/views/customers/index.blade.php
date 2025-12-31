@extends('layouts.dashboard')
@section('title', 'Customer Management')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800">Customer Management</h1>
            <!--
            <a href="{{ route('customers.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md">
                <i class="fas fa-plus mr-2"></i>Add New Customer
            </a>
            -->
        </div>

        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form action="{{ route('customers.index') }}" method="GET" class="flex items-center space-x-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone or email..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                @if(request('search'))
                    <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times-circle"></i> Clear
                    </a>
                @endif
            </form>
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

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($customers->isEmpty())
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                    <p>No customers found{{ request('search') ? ' matching your search criteria' : '' }}.</p>
                    @if(request('search'))
                        <a href="{{ route('customers.index') }}" class="text-blue-500 hover:underline mt-2 inline-block">
                            Clear search and show all customers
                        </a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-auto border-collapse w-full shadow-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Contact Information</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Purchase History</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Last Visit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            @if($customer->phone)
                                                <div class="flex items-center text-sm text-gray-900 mb-1">
                                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                                    <span id="phone-{{ $customer->id }}">{{ $customer->phone }}</span>
                                                    <button class="ml-2 text-gray-400 hover:text-blue-500 focus:outline-none"
                                                            onclick="copyToClipboard('{{ $customer->phone }}', 'copy-msg-{{ $customer->id }}')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <span id="copy-msg-{{ $customer->id }}" class="ml-2 text-green-500 text-xs hidden">Copied!</span>
                                                </div>
                                            @endif
                                            @if($customer->email)
                                                <div class="flex items-center text-sm text-gray-900">
                                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                                    <span>{{ $customer->email }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm text-gray-900">
                                                <div class="mb-1">
                                                    <span class="font-semibold">{{ $customer->total_visits }}</span> purchase(s)
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-green-600">EGP {{ number_format($customer->total_spent, 2) }}</span> spent
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($customer->last_visit)
                                                {{ $customer->last_visit->format('Y-m-d') }}
                                                <div class="text-xs text-gray-500">{{ $customer->last_visit->diffForHumans() }}</div>
                                            @else
                                                <span class="text-gray-400">Never</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('customers.show', $customer->id) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!--
                                            <a href="{{ route('customers.edit', $customer->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            -->
                                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
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
    function copyToClipboard(text, messageId) {
        navigator.clipboard.writeText(text).then(() => {
            const messageSpan = document.getElementById(messageId);
            messageSpan.classList.remove('hidden');
            setTimeout(() => {
                messageSpan.classList.add('hidden');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy. Please try again.');
        });
    }
</script>
@endsection
