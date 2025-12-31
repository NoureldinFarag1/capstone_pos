{{-- resources/views/refunds/create.blade.php --}}
@extends('layouts.dashboard')
@section('title', 'Create Refund')
@section('content')
<div class="container mx-auto py-6 px-4">
    <h3 class="text-2xl font-bold mb-6">Refund for Sale #{{ $sale->id }}</h3>

    <form action="{{ route('refund.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">

        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Quantity Sold</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Quantity to Refund</th>
                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600 uppercase tracking-wider">Reason for Refund</th>
                </tr>
            </thead>
            <tbody>
                @foreach($saleItems as $saleItem)
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                        {{ $saleItem->item->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                        {{ $saleItem->quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                        <input
                            type="number"
                            name="refund[{{ $saleItem->id }}][quantity]"
                            min="0"
                            max="{{ $saleItem->quantity }}"
                            value="0"
                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                        <input
                            type="text"
                            name="refund[{{ $saleItem->id }}][reason]"
                            placeholder="Enter reason for refund"
                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex items-center justify-between mt-6">
            <button
                type="submit"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            >
                Process Refund
            </button>
        </div>
    </form>
</div>
@endsection
