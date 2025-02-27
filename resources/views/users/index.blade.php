@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Add New User Button -->
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('users.create') }}" class="btn bg-gradient-dark mb-0 add-user-btn">
            <i class="fas fa-plus me-2"></i>Add New User
        </a>
    </div>
    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Users</p>
                                <h5 class="font-weight-bolder">{{ $users->count() }}</h5>
                                <p class="mb-0 text-sm">
                                    <span class="text-success text-sm font-weight-bolder">+3%</span>
                                    since last month
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fas fa-users text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Admins</p>
                                <h5 class="font-weight-bolder">{{ $adminCount }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="fas fa-user-shield text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Moderators</p>
                                <h5 class="font-weight-bolder">{{ $moderatorCount }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fas fa-user-cog text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">Cashiers</p>
                                <h5 class="font-weight-bolder">{{ $cashierCount }}</h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                <i class="fas fa-cash-register text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Users Management</h6>
                    <p class="text-sm mb-0">
                        Manage your team members and their account permissions here
                    </p>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <!-- Search Bar -->
                <div class="p-4">
                    <div class="input-group">
                        <span class="input-group-text bg-gradient-primary">
                            <i class="fas fa-search text-black"></i>
                        </span>
                        <input type="text" class="form-control" id="userSearch" placeholder="Search by name, email, or role...">
                    </div>
                </div>

                <div class="table-responsive p-0">
                    @if($users->count())
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Last Login</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Created</th>
                                    <th class="text-secondary opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users->sortBy('name') as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $user->email }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge text-white {{ $user->hasRole('Admin') ? 'bg-primary' : ($user->hasRole('Moderator') ? 'bg-warning' : 'bg-success') }}">
                                            {{ $user->getRoleNames()->implode(', ') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-secondary text-xs">
                                            <i class="fas fa-clock me-1"></i>
                                            @if($user->last_login)
                                                {{ $user->last_login->diffForHumans() }}
                                            @else
                                                Never
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-secondary text-xs">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $user->created_at->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('users.edit', $user->id) }}"
                                               class="btn btn-link text-dark px-3 mb-0"
                                               data-bs-toggle="tooltip"
                                               title="Edit User">
                                                <i class="fas fa-pencil-alt text-dark"></i>
                                            </a>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-link text-danger px-3 mb-0"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete User"
                                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-users fa-3x text-secondary mb-3"></i>
                            <p class="text-secondary">No users found. Start by creating a new user!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('userSearch');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchText) ? '' : 'none';
                });
            });
        }

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .add-user-btn {
        background-color: #343a40;
        color: #fff;
        transition: background-color 0.3s ease;
    }
    .add-user-btn:hover {
        background-color: #23272b;
        color: #fff;
    }
</style>
@endpush
@endsection
