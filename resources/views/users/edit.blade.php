@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ $user->name }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ $user->email }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    readonly
                >
            </div>

            <div class="mb-6">
                <label for="role" class="block text-gray-700 font-bold mb-2">Role</label>
                <select
                    name="role"
                    id="role"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                    <option value="moderator" {{ $user->hasRole('moderator') ? 'selected' : '' }}>Moderator</option>
                    <option value="cashier" {{ $user->hasRole('cashier') ? 'selected' : '' }}>Cashier</option>
                </select>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors"
                >
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
