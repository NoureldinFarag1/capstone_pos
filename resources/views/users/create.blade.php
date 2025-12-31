@extends('layouts.dashboard')
@section('title', 'Create User')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">Create User</h1>
    <form action="{{ route('users.store') }}" method="POST" class="bg-white shadow-lg rounded-lg px-8 pt-6 pb-8 mb-4 space-y-6">
        @csrf
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name Input -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Name:</label>
                <input type="text" name="name" id="name" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full border-gray-300 rounded-md py-2 px-3 text-gray-900 leading-tight focus:outline-none">
            </div>

            <!-- Email (Hidden) -->
            <input type="hidden" name="email" id="email" value="" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Password Input -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password:</label>
                <input type="password" name="password" id="password" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full border-gray-300 rounded-md py-2 px-3 text-gray-900 leading-tight focus:outline-none">
            </div>

            <!-- Confirm Password Input -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full border-gray-300 rounded-md py-2 px-3 text-gray-900 leading-tight focus:outline-none">
            </div>
        </div>

        <!-- Role Selection -->
        <div class="mb-4">
            <label for="role" class="block text-gray-700 text-sm font-medium mb-2">Role:</label>
            <select name="role" id="role" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full border-gray-300 rounded-md py-2 px-3 text-gray-900 leading-tight focus:outline-none">
                <option value="admin">Admin</option>
                <option value="moderator">Moderator</option>
                <option value="cashier">Cashier</option>
            </select>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                Create User
            </button>
        </div>
    </form>
</div>
@endsection
