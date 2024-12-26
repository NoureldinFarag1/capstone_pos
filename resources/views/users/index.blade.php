@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-8">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-4xl font-bold text-gray-800">User List</h1>
        <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow transition duration-200">
            + Create User
        </a>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        @if($users->count())
            <table class="min-w-full divide-y divide-gray-200">
                <!-- Table Header -->
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $user->hasRole('Admin') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $user->getRoleNames()->implode(', ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                                <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center space-x-1">
                                    <span>Edit</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-2.036L8.5 15.5a2.121 2.121 0 01-.707.707L5 17l.793-2.793a2.121 2.121 0 01.707-.707L16.5 6.5m0 0L14.464 4.464a2 2 0 00-2.828 0L8.586 7.414a2 2 0 000 2.828L6.464 10.464a2 2 0 000 2.828L10.5 17.5" />
                                    </svg>
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold flex items-center space-x-1">
                                        <span>Delete</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-6 text-center text-gray-600">
                <p>No users found. Start by creating a new user!</p>
            </div>
        @endif
    </div>
</div>
@endsection
