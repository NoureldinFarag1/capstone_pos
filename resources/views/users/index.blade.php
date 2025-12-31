@extends('layouts.dashboard')
@section('title', 'Users')

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
    <div class="card mb-4 users-card shadow-sm border-0">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Users Management</h6>
                    <p class="text-sm mb-0">
                        Manage your team members and their account permissions here
                    </p>
                </div>
                <form method="GET" action="{{ route('users.index') }}" class="d-flex align-items-center gap-2 role-filter-form">
                    <label for="role" class="text-sm text-secondary me-2 mb-0">Filter by Role:</label>
                    <select name="role" id="role" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="" {{ empty($normalizedSelected) ? 'selected' : '' }}>All</option>
                        @foreach($availableRoles as $r)
                            <option value="{{ strtolower($r) }}" {{ (strtolower($r) === strtolower($normalizedSelected)) ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                    @if(request('role'))
                        <a href="{{ route('users.index') }}" class="btn btn-link btn-sm text-decoration-none text-secondary" title="Clear filter">Reset</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
                <!-- Search Bar -->
                <div class="p-4">
                    <div class="input-group neutral-input-group">
                        <span class="input-group-text neutral-input-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control neutral-input" id="userSearch" placeholder="Search by name, email, or role...">
                    </div>
                </div>

                <div class="table-responsive p-0">
                    @if($users->count())
                        <table class="table table-hover users-table align-items-center mb-0">
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
                                        <span class="badge role-badge {{ $user->hasRole('Admin') ? 'role-admin' : ($user->hasRole('Moderator') ? 'role-moderator' : 'role-cashier') }}">
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

    .users-card {
        background-color: #ffffff;
        border-radius: 1rem;
        border: 1px solid #e7e7ec;
    }

    .users-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid #f1f1f4;
    }

    .neutral-input-group .form-control,
    .neutral-input {
        border-color: #d5d5dc;
        background-color: #fafafb;
        color: #2d2d34;
    }

    .neutral-input:focus {
        border-color: #b7b7c4;
        box-shadow: none;
    }

    .neutral-input-addon {
        background-color: #f0f0f3;
        border-color: #d5d5dc;
        color: #6a6a74;
    }

    .users-table thead th {
        background-color: #f8f8fb;
        color: #6c6f7a;
        border-bottom-width: 1px;
        border-bottom-color: #e6e6ef;
    }

    .users-table tbody tr {
        border-bottom: 1px solid #f0f0f5;
    }

    .users-table tbody tr:last-child {
        border-bottom: none;
    }

    .users-table tbody tr:hover {
        background-color: #f6f6f9;
    }

    .role-badge {
        border-radius: 999px;
        font-size: 0.75rem;
        text-transform: capitalize;
        padding: 0.35rem 0.85rem;
    }

    .role-badge.role-admin {
        background-color: #2f2f37;
        color: #fff;
    }

    .role-badge.role-moderator {
        background-color: #5d5d68;
        color: #fff;
    }

    .role-badge.role-cashier {
        background-color: #8d8d98;
        color: #fff;
    }
    .role-filter-form select.form-select.form-select-sm {
        min-width: 140px;
        background-color: #fafafb;
        border-color: #d5d5dc;
        color: #2d2d34;
    }
    .role-filter-form select.form-select.form-select-sm:focus {
        border-color: #b7b7c4;
        box-shadow: none;
    }
</style>
@endpush
@endsection
