@php
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
$user = auth()->user();
$navbarLogoPath = Config::get('navbar.logo_path');
$navbarTextLogoPath = Config::get('navbar.text_logo_path');
$siteTitle = Config::get('navbar.site_title');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteTitle }}</title>
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Add SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    @stack('styles')
</head>

<body class="bg-gray-100">
    <nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left Section: Session Timer then Logo (timer at far-left) -->
                <div class="flex items-center gap-4">
                    <!-- Floating session timer now positioned far-left -->
                    <div id="sessionTimer" class="session-timer-floating" aria-label="Active session duration"
                         @if($user && $user->last_login) data-login-at="{{ $user->last_login->toIso8601String() }}" @endif>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-sm text-indigo-700">
                            <i class="fas fa-hourglass-half"></i>
                            <span id="sessionDuration">--:--:--</span>
                        </span>
                    </div>

                    <a href="#" class="flex items-center no-underline pl-20">
                        <img src="{{ asset($navbarLogoPath) }}" alt="LocalHUB Logo" class="h-12 w-auto ml-1">
                        <img src="{{ asset($navbarTextLogoPath) }}" alt="LocalHUB" class="h-8 w-auto mr-1">
                    </a>
                </div>
                <!-- Mobile toggle -->
                <div class="flex items-center sm:hidden">
                    <button @click="mobileOpen = !mobileOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        aria-controls="mobile-menu" :aria-expanded="mobileOpen">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <!-- Main Navigation -->
                <div class="hidden sm:flex sm:items-center sm:space-x-4">
                    <!-- Dashboard -->
                    @role('admin|moderator')
                    <a href="{{ route('dashboard') }}"
                        class="nav-link-clean {{ request()->is('dashboard*') ? 'active' : '' }}">
                        <i class="fas fa-home icon"></i>
                        <span>Dashboard</span>
                    </a>
                    @endrole

                    <!-- Inventory Dropdown -->
            <div x-data="{ isOpen: false }" class="relative no-underline">
                        <button @click="isOpen = !isOpen" @keydown.escape.window="isOpen = false"
                class="nav-link-clean {{ (request()->is('brands*') || request()->is('categories*') || request()->is('items*') || request()->is('sizes*') || request()->is('colors*')) ? 'active' : '' }}">
                            <i class="fas fa-box-open icon"></i>
                            <span>Inventory</span>
                            <svg class="h-4 w-4 transform transition-transform duration-200"
                                :class="{ 'rotate-180': isOpen }" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="isOpen" @click.away="isOpen = false" class="nav-dropdown-panel">
                <div class="py-1 rounded-lg">
                                <a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.index') ? 'active' : '' }}">
                                    <i class="fas fa-tags icon"></i><span>Brands</span>
                                </a>
                                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.index') ? 'active' : '' }}">
                                    <i class="fas fa-th-list icon"></i><span>Categories</span>
                                </a>
                                <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.index') ? 'active' : '' }}">
                                    <i class="fas fa-box icon"></i><span>Items</span>
                                </a>
                                <a href="{{ route('sizes.index') }}" class="{{ request()->routeIs('sizes.index') ? 'active' : '' }}">
                                    <i class="fas fa-ruler icon"></i><span>Sizes</span>
                                </a>
                                <a href="{{ route('colors.index') }}" class="{{ request()->routeIs('colors.index') ? 'active' : '' }}">
                                    <i class="fas fa-tint icon"></i><span>Colors</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Dropdown -->
        <div x-data="{ isOpen: false }" class="relative">
            <button @click="isOpen = !isOpen" @keydown.escape.window="isOpen = false"
        class="nav-link-clean {{ request()->is('sales*') ? 'active' : '' }}">
                <i class="fas fa-chart-line icon"></i>
                <span>Sales</span>
                            @if(isset($pendingCodCount) && $pendingCodCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative flex rounded-full h-4 w-4 bg-amber-500 text-white text-xs justify-center items-center">
                                    {{ $pendingCodCount }}
                                </span>
                            </span>
                            @endif
                            <svg class="ml-2 h-4 w-4 transform transition-transform duration-200"
                                :class="{ 'rotate-180': isOpen }" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

            <div x-show="isOpen" @click.away="isOpen = false" class="nav-dropdown-panel">
        <div class="py-1 rounded-lg">
                                <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
                                    <i class="fas fa-list icon"></i><span>Sales List</span>
                                </a>
                                <a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">
                                    <i class="fas fa-plus-circle icon"></i><span>Create Sale</span>
                                </a>
                                <a href="{{ route('sales.cod') }}" class="{{ request()->routeIs('sales.cod') ? 'active' : '' }}">
                                    <i class="fas fa-truck icon"></i><span>COD Tracking</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Section -->
                    @can('admin')
                    <a href="{{ route('users.index') }}"
                        class="nav-link-clean {{ request()->is('users*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog icon"></i>
                        <span>Manage Users</span>
                    </a>
                    @endcan
                    <!-- Backup Button -->
                    <form action="{{ route('backup.download') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="nav-link-clean {{ request()->is('backup*') ? 'active' : '' }}">
                            <i class="fas fa-download icon"></i><span>Backup</span>
                        </button>
                    </form>
                </div>
                <!-- Notification and Logout aligned to the right -->
                <div class="hidden sm:flex sm:items-center">
                    <!-- Low Stock Notification Dropdown -->
                    @if (isset($lowStockItems) && $lowStockItems->isNotEmpty())
                    <div x-data="{ open: false, dotVisible: true }" class="relative mr-4">
                        <button @click="open = !open; dotVisible = false" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors">
                            <i :class="open ? 'far fa-bell' : 'fas fa-bell'" class="text-lg"></i>
                            <div x-show="dotVisible" class="notification-dot"></div>
                        </button>
                        <div x-show="open" @click.away="open = false" class="notification-dropdown">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <h6 class="text-sm font-semibold">Stock Alerts</h6>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @foreach ($lowStockItems->sortBy('quantity') as $item)
                                <a href="{{ route('items.edit', $item->id) }}" class="notification-item">
                                    <span class="font-medium">{{ $item->name }}</span>
                                    <span class="text-red-500">{{ $item->quantity }} left</span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center space-x-4">
                        <div class="user-identity">
                            <span class="user-name">{{ $user->name }}</span>
                            <span class="user-role">({{ $user->getRoleNames()->first() ?? 'Role not assigned' }})</span>
                        </div>
                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-red-500 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-red-600 transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- Mobile menu -->
    <div x-show="mobileOpen" x-transition id="mobile-menu" class="sm:hidden border-b border-gray-100 bg-white">
        <div class="px-4 pt-2 pb-3 space-y-1">
            @role('admin|moderator')
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->is('dashboard*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-home mr-2"></i>Dashboard
            </a>
            @endrole
            <a href="{{ route('items.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <i class="fas fa-box mr-2"></i>Items
            </a>
            <a href="{{ route('sales.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <i class="fas fa-list mr-2"></i>Sales
            </a>
            @can('admin')
            <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <i class="fas fa-users-cog mr-2"></i>Users
            </a>
            @endcan
            <form action="{{ route('backup.download') }}" method="POST" class="px-3 py-2">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <i class="fas fa-download mr-2"></i>Backup
                </button>
            </form>
        </div>
    </div>
    <!-- Content Area -->
    <div class="container mx-auto p-6">
        <div class="mb-4 flex justify-end">
            <button x-data="{ loading: false }" @click="loading = true; window.location.reload()"
                class="group bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2.5 rounded-lg flex items-center transition-all duration-300 relative tooltip-trigger"
                title="Refresh Page">
                <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"
                    class="mr-2 text-lg group-hover:rotate-180 transition-transform duration-500"></i>
                <!-- Tooltip -->
                <span
                    class="tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    Refresh page
                </span>
            </button>
        </div>
        @yield('content')
        <br>
        @if (Request::routeIs('dashboard'))
        <!-- Dashboard Widgets -->
        <div class="container mx-auto p-6">
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="border-b border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-gray-800">Quick Actions</h3>
                            <span class="px-3 py-1 text-sm font-medium text-teal-700 bg-teal-100 rounded-full">
                                Frequently Used
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="quick-actions-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                            <div class="col-span-full sm:col-span-2 lg:col-span-4">
                                <h4 class="text-lg font-semibold mb-2 text-gray-700">Sales</h4>
                            </div>
                            <a href="{{ route('sales.create') }}" class="qa-btn">
                                <i class="fas fa-plus-circle icon"></i>
                                <span>New Sale</span>
                            </a>
                            <a href="{{ route('items.trace') }}" class="qa-btn">
                                <i class="fas fa-search-location icon"></i>
                                <span>Trace Item</span>
                            </a>
                            <a href="{{ route('sales.overview') }}" class="qa-btn">
                                <i class="fas fa-chart-line icon"></i>
                                <span>Sales Overview</span>
                            </a>

                            <a href="{{ route('sales.cod') }}" class="qa-btn">
                                <i class="fas fa-truck icon"></i>
                                <span>COD Tracking</span>
                            </a>
                            <a href="{{ route('expenses.index') }}" class="qa-btn">
                                <i class="fas fa-receipt icon"></i>
                                <span>Expenses</span>
                            </a>
                            <a href="{{ route('refunds.index') }}" class="qa-btn qa-btn-danger">
                                <i class="fas fa-undo icon"></i>
                                <span>Refund Analytics</span>
                            </a>


                            <div class="col-span-full sm:col-span-2 lg:col-span-4">
                                <h4 class="text-lg font-semibold mb-2 text-gray-700">Inventory</h4>
                            </div>
                            <a href="/items/create" class="qa-btn">
                                <i class="fas fa-box-open icon"></i>
                                <span>Add Item</span>
                            </a>
                            <a href="/items" class="qa-btn">
                                <i class="fas fa-boxes icon"></i>
                                <span>View Inventory</span>
                            </a>
                            <a href="{{ route('items.exportCSV') }}" class="qa-btn">
                                <i class="fas fa-file-download icon"></i>
                                <span>Export Inventory</span>
                            </a>
                            <a href="{{ route('brands.trace') }}" class="qa-btn">
                                <i class="fas fa-chart-line icon"></i>
                                <span>Trace Brand Selling</span>
                            </a>

                            @can('admin')
                            <div class="col-span-full sm:col-span-2 lg:col-span-4">
                                <h4 class="text-lg font-semibold mb-2 text-gray-700">Administration</h4>
                            </div>
                            <a href="{{ route('store-settings.index') }}" class="qa-btn">
                                <i class="fas fa-store icon"></i>
                                <span>Store Settings</span>
                            </a>
                            <a href="/users/create" class="qa-btn">
                                <i class="fas fa-users-cog icon"></i>
                                <span>New User</span>
                            </a>
                            <a href="{{ route('backup.sync.form') }}" class="qa-btn qa-btn-success">
                                <i class="fas fa-sync-alt icon"></i>
                                <span>Sync Database</span>
                            </a>
                            @endcan

                            <div class="col-span-full sm:col-span-2 lg:col-span-4">
                                <h4 class="text-lg font-semibold mb-2 text-gray-700">Other</h4>
                            </div>
                            <a href="/brands/create" class="qa-btn">
                                <i class="fas fa-tag icon"></i>
                                <span>Add Brand</span>
                            </a>

                            <a href="/categories/create" class="qa-btn">
                                <i class="fas fa-th-list icon"></i>
                                <span>Add Category</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8" x-data="{
                    verified: false,
                    showVerifyModal: false,
                    password: '',
                    visibleSection: null,
                    currentSection: null,
                    showAllSections: false,

                    async verifyAccess() {
                        try {
                            const response = await fetch('/verify-access', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                },
                                body: JSON.stringify({ password: this.password })
                            });

                            const result = await response.json();
                            if (result.success) {
                                this.verified = true;
                                this.visibleSection = this.currentSection;
                                this.showVerifyModal = false;
                                this.password = '';
                            } else {
                                alert('Invalid password');
                            }
                        } catch (error) {
                            alert('Verification failed');
                        }
                    },

                    toggleVisibility(section) {
                        if (this.verified) {
                            this.visibleSection = this.visibleSection === section ? null : section;
                        } else {
                            this.currentSection = section;
                            this.showVerifyModal = true;
                        }
                    },
                }">

                <!-- Today's Earnings -->
                <div class="metric-card p-6 overflow-hidden">
                    <div class="relative z-10">
                        <button @click="toggleVisibility('earnings')" class="text-gray-500 hover:text-gray-700">
                            <i class="fas" :class="visibleSection === 'earnings' ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                        <!-- Added user-select-none to prevent text selection when blurred -->
                        <div :class="{ 'blur-lg pointer-events-none user-select-none': visibleSection !== 'earnings' && !showAllSections }">
                            <div class="flex items-center justify-between mb-4">
                                <h3>Today's Earnings</h3>
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-dollar-sign text-xl text-gray-600"></i>
                                </div>
                            </div>
                            <div class="metric-value mb-2">{{ number_format($todayRevenue ?? 0, 2) }} EGP</div>
                            @if (isset($revenueGrowth) && $revenueGrowth > 0)
                            <div class="chip">
                                <i class="fas fa-arrow-up mr-1 text-green-600"></i>{{ number_format($revenueGrowth, 1) }}% from yesterday
                            </div>
                            @endif
                            <div class="mt-6 space-y-3">
                                <a href="{{ route('sales.by-payment-method', ['period' => 'daily', 'method' => 'cash']) }}"
                                    class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg text-sm hover:bg-gray-100 no-underline"
                                    :class="{ 'pointer-events-none': visibleSection !== 'earnings' && !showAllSections }">
                                    <span class="font-medium">Cash</span>
                                    <span class="font-bold text-gray-700">{{ number_format($cashPayments, 2) }} EGP</span>
                                </a>
                                <a href="{{ route('sales.by-payment-method', ['period' => 'daily', 'method' => 'credit_card']) }}"
                                    class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg text-sm hover:bg-gray-100"
                                    :class="{ 'pointer-events-none': visibleSection !== 'earnings' && !showAllSections }">
                                    <span class="font-medium">Visa</span>
                                    <span class="font-bold text-gray-700">{{ number_format($creditPayments, 2) }} EGP</span>
                                </a>
                                <a href="{{ route('sales.by-payment-method', ['period' => 'daily', 'method' => 'mobile_pay']) }}"
                                    class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg text-sm hover:bg-gray-100"
                                    :class="{ 'pointer-events-none': visibleSection !== 'earnings' && !showAllSections }">
                                    <span class="font-medium">Mobile Payment</span>
                                    <span class="font-bold text-gray-700">{{ number_format($mobilePayments, 2) }} EGP</span>
                                </a>
                                <a href="{{ route('sales.by-payment-method', ['period' => 'daily', 'method' => 'cod']) }}"
                                    class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg text-sm hover:bg-gray-100"
                                    :class="{ 'pointer-events-none': visibleSection !== 'earnings' && !showAllSections }">
                                    <span class="font-medium">COD</span>
                                    <span class="font-bold text-gray-700">{{ number_format($codPayments, 2) }} EGP</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Sales -->
                <div class="metric-card p-6 overflow-hidden">
                    <div class="relative z-10">
                        <button @click="toggleVisibility('monthly')" class="text-gray-500 hover:text-gray-700">
                            <i class="fas" :class="visibleSection === 'monthly' ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                        <!-- Added user-select-none to prevent text selection when blurred -->
                        <div :class="{ 'blur-lg pointer-events-none user-select-none': visibleSection !== 'monthly' && !showAllSections }">
                            <div class="flex items-center justify-between mb-4">
                                <h3>Monthly Sales</h3>
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-xl text-gray-600"></i>
                                </div>
                            </div>
                            <div class="metric-value mb-2">{{ number_format($monthlySales, 2) }} EGP</div>
                            <div class="chip">
                                {{ $salesGrowthPercentage >= 0 ? '+' : '' }}{{ number_format($salesGrowthPercentage, 2) }}% from last month
                            </div>
                            <div class="mt-6 space-y-3">
                                <a href="{{ route('sales.by-payment-method', ['period' => 'monthly', 'method' => 'cash']) }}"
                                    class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg text-sm hover:bg-gray-100"
                                    :class="{ 'pointer-events-none': visibleSection !== 'monthly' && !showAllSections }">
                                    <span class="font-medium">Cash</span>
                                    <span class="font-bold text-gray-700">{{ number_format($cashPaymentsMonthly, 2) }} EGP</span>
                                </a>
                                <a href="{{ route('sales.by-payment-method', ['period' => 'monthly', 'method' => 'credit_card']) }}"
                                    class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg text-sm hover:bg-gray-100"
                                    :class="{ 'pointer-events-none': visibleSection !== 'monthly' && !showAllSections }">
                                    <span class="font-medium">Visa</span>
                                    <span class="font-bold text-gray-700">{{ number_format($creditPaymentsMonthly, 2) }} EGP</span>
                                </a>
                                <a href="{{ route('sales.by-payment-method', ['period' => 'monthly', 'method' => 'mobile_pay']) }}"
                                    class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg text-sm hover:bg-gray-100"
                                    :class="{ 'pointer-events-none': visibleSection !== 'monthly' && !showAllSections }">
                                    <span class="font-medium">Mobile Payment</span>
                                    <span class="font-bold text-gray-700">{{ number_format($mobilePaymentsMonthly, 2) }} EGP</span>
                                </a>
                                <a href="{{ route('sales.by-payment-method', ['period' => 'monthly', 'method' => 'cod']) }}"
                                    class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg text-sm hover:bg-gray-100"
                                    :class="{ 'pointer-events-none': visibleSection !== 'monthly' && !showAllSections }">
                                    <span class="font-medium">COD</span>
                                    <span class="font-bold text-gray-700">{{ number_format($codPaymentsMonthly, 2) }} EGP</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Payment Method -->
                <div class="metric-card p-6 overflow-hidden">
                    <div class="relative z-10">
                        <button @click="toggleVisibility('payment')" class="text-gray-500 hover:text-gray-700">
                            <i class="fas" :class="visibleSection === 'payment' ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                        <!-- Added user-select-none to prevent text selection when blurred -->
                        <div :class="{ 'blur-lg pointer-events-none user-select-none': visibleSection !== 'payment' && !showAllSections }">
                            <div class="flex items-center justify-between mb-4">
                                <h3>Top Payment Method</h3>
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    @if ($topPaymentMethod === 'cash')
                                    <i class="fas fa-cash-register text-xl text-gray-600"></i>
                                    @elseif ($topPaymentMethod === 'credit_card')
                                    <i class="fas fa-credit-card text-xl text-gray-600"></i>
                                    @elseif($topPaymentMethod === 'mobile_pay')
                                    <i class="fas fa-mobile-alt text-xl text-gray-600"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="metric-value mb-4">{{ ucfirst($topPaymentMethod) }}</div>
                            <div class="payment-box text-sm">
                                <div class="mb-2 flex justify-between text-gray-700">
                                    <span class="font-medium">Top Payment Method</span>
                                    <span class="font-bold">{{ number_format($topPaymentMethodPercentage, 2) }}%</span>
                                </div>
                                <div class="track mb-4">
                                    <div class="track-fill" style="width: {{ $topPaymentMethodPercentage }}%"></div>
                                </div>
                                <div class="text-gray-600 text-xs">{{ $topPaymentMethodCount }} out of {{ $AllSalesCount }} transactions</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Single Verify Modal for all sections -->
                <div x-show="showVerifyModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                            <div class="mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Verify Access</h3>
                                <p class="text-sm text-gray-600 mt-1">Please enter your password to view sensitive data
                                </p>
                            </div>
                            <form @submit.prevent="verifyAccess">
                                <div class="mb-4">
                                    <input type="password" x-model="password"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Enter your password">
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" @click="showVerifyModal = false"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                        Verify
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add after existing metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Sales Analytics -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Sales Performance</h3>
                    <!-- Peak Hours -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-3">Peak Business Hours</h4>
                        <div class="space-y-2">
                            @foreach ($peakHours as $hour)
                            <div class="flex justify-between items-center">
                                <span
                                    class="font-medium w-20">{{ Carbon\Carbon::createFromFormat('H', $hour->hour)->format('g:i A') }}</span>
                                <div class="flex-1 mx-4 relative group">
                                    <div class="h-2 bg-blue-100 rounded-full">
                                        <div class="h-2 bg-blue-500 rounded-full shadow-md"
                                            style="width: {{ ($hour->count / $peakHours->max('count')) * 100 }}%">
                                        </div>
                                    </div>
                                    <div
                                        class="absolute left-1/2 transform -translate-x-1/2 -translate-y-full bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        {{ $hour->count }} sales
                                    </div>
                                </div>
                                <span class="text-sm w-20 text-right">{{ $hour->count }} sales</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Best Selling Days -->
                    <div>
                        <h4 class="text-lg font-semibold mb-3">Best Selling Days</h4>
                        <div class="grid grid-cols-7 gap-2">
                            @foreach ($bestSellingDays as $day)
                            <div
                                class="text-center p-2 {{ $day->count === $bestSellingDays->max('count') ? 'bg-green-100' : 'bg-gray-50' }} rounded">
                                <div class="text-sm font-medium">{{ substr($day->day, 0, 3) }}</div>
                                <div class="text-xs">{{ $day->count }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Customer Insights -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Customer Insights</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-purple-50 rounded-lg hover:bg-purple-100">
                            <a href="{{ route('sales.loyal-customers') }}" class="block no-underline">
                                <div class="text-sm text-purple-600 font-medium">Loyal Customers</div>
                                <div class="text-2xl font-bold text-purple-700">
                                    {{ $customerMetrics['repeat_customers'] }}
                                </div>
                            </a>
                        </div>
                        <!-- All Customers -->
                        <a href="{{ route('customers.index') }}" class="p-4 bg-blue-50 rounded-lg hover:bg-blue-100 no-underline">
                            <div class="text-sm text-blue-600 font-medium">All Customers</div>
                            <div class="text-2xl font-bold text-blue-700">
                                {{ $customerMetrics['total_customers'] }}
                            </div>
                        </a>
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-sm text-blue-600 font-medium">Avg Transaction</div>
                            <div class="text-2xl font-bold text-blue-700">
                                {{ number_format($customerMetrics['avg_transaction'], 2) }} EGP
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Inventory Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 relative" x-data="{
                    isInventoryVisible: false
                }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Inventory Insights</h3>
                    <button @click="toggleVisibility('inventory')" class="text-gray-600 hover:text-gray-900">
                        <i class="fas" :class="visibleSection === 'inventory' ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>

                <div
                    :class="{ 'blur-lg pointer-events-none user-select-none': visibleSection !== 'inventory' && !showAllSections }">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="stat-box">
                            <div class="stat-label">Total Value</div>
                            <div class="stat-value">{{ number_format($inventoryMetrics['total_value'], 2) }} EGP</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Avg Item Price</div>
                            <div class="stat-value">{{ number_format($inventoryMetrics['avg_item_price'], 2) }} EGP</div>
                        </div>
                        <div class="stat-box stat-box-danger">
                            <div class="stat-label">Out of Stock</div>
                            <div class="stat-value">{{ $inventoryMetrics['out_of_stock'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Inventory Turnover</div>
                            <div class="stat-value">{{ number_format($inventoryMetrics['inventory_turnover'], 2) }}x</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Category Performance - Collapsible Version -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8" x-data="{ showAllCategories: false }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Category Performance</h3>
                    <button @click="showAllCategories = !showAllCategories"
                        class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                        <span x-text="showAllCategories ? 'Show Less' : 'View All'">View All</span>
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($categoryPerformance as $index => $category)
                    <div x-show="showAllCategories || $index < 8" class="category-chip">
                        <div class="text-sm font-medium text-gray-600">{{ $category->name }}</div>
                        <div class="mt-2 space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="font-medium">Items:</span>
                                <span class="font-medium">{{ $category->items_count }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="font-medium">Stock:</span>
                                <span class="font-medium">{{ $category->items_sum_quantity ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="font-medium">Avg:</span>
                                <span
                                    class="font-medium">{{ number_format($category->items_avg_selling_price ?? 0, 0) }}
                                    EGP</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- Refund Metrics -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 stripe-critical" x-data="{ isOpen: false }">
                <div class="border-b border-gray-100 mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-gray-900">Refund Analytics</h3>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 text-sm font-medium text-red-700 bg-red-50 rounded-full">
                                Refund Overview
                            </span>
                            <button @click="isOpen = !isOpen" class="text-gray-400 hover:text-gray-600">
                                <i class="fas" :class="isOpen ? 'fa-eye' : 'fa-eye-slash'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div x-show="isOpen" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Today's Refunds -->
                        <div class="p-4 bg-red-50 rounded-lg">
                            <div class="text-sm text-red-600 font-medium">Today's Refunds</div>
                            <div class="text-2xl font-bold text-red-700">
                                {{ number_format($refundMetrics['today_refunds'], 2) }} EGP
                            </div>
                        </div>
                        <!-- Monthly Refunds -->
                        <div class="p-4 bg-orange-50 rounded-lg">
                            <div class="text-sm text-orange-600 font-medium">Monthly Refunds</div>
                            <div class="text-2xl font-bold text-orange-700">
                                {{ number_format($refundMetrics['month_refunds'], 2) }} EGP
                            </div>
                        </div>
                        <!-- Refund Rate -->
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-sm text-blue-600 font-medium">Refund Rate</div>
                            <div class="text-2xl font-bold text-blue-700">
                                {{ number_format($refundMetrics['refund_rate'], 1) }}%
                            </div>
                        </div>
                    </div>
                    <!-- Recent Refunds -->
                    @if ($refundMetrics['recent_refunds']->isNotEmpty())
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold mb-4">Recent Refunds</h4>
                        <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                            @foreach ($refundMetrics['recent_refunds'] as $refund)
                            <div
                                class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                <div class="flex flex-col">
                                    <span class="font-medium">Sale #{{ $refund->sale_id }}</span>
                                    <span class="text-sm text-gray-500 font-medium">{{ $refund->item->name }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-red-600">{{ number_format($refund->refund_amount, 2) }}
                                        EGP</span>
                                    <div class="text-xs text-gray-500">
                                        {{ $refund->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-8 px-4">
                        <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                            <i class="fas fa-check text-green-500 text-xl"></i>
                        </div>
                        <p class="text-green-500 font-medium">No recent refunds!</p>
                    </div>
                    @endif
                </div>
            </div>
            <!-- Details Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Top Selling Brands Widget -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden" x-data="{ viewMode: 'alltime' }">
                    <div class="border-b border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-gray-900">GOATS</h3>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 text-sm font-medium text-purple-700 bg-purple-50 rounded-full">
                                    <span x-text="viewMode === 'alltime' ? 'All Time' : 'Monthly'"></span> Top Performers
                                </span>
                                <div class="flex space-x-1">
                                    <button @click="viewMode = 'alltime'"
                                        :class="viewMode === 'alltime' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors">
                                        All Time
                                    </button>
                                    <button @click="viewMode = 'monthly'"
                                        :class="viewMode === 'monthly' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors">
                                        Monthly
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- All Time Top Selling Brands -->
                        <div x-show="viewMode === 'alltime'" class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                            @foreach ($topSellingBrands as $index => $brand)
                            <div
                                class="group flex items-center justify-between p-4 rounded-lg transition-all duration-300 hover:bg-gray-50/80 hover:shadow-sm">
                                <div class="flex items-center space-x-4">
                                    @if ($brand['image'])
                                    <img src="{{ asset('storage/' . $brand['image']) }}" alt="{{ $brand['name'] }}"
                                        class="w-12 h-12 rounded-lg object-cover border-2
                                                rank-box {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : '')) }}">
                                    @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center border-2 rank-box {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : '')) }}">
                                        <span class="text-xl font-bold text-gray-400">
                                            {{ substr($brand['name'], 0, 1) }}
                                        </span>
                                    </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="text-lg font-semibold text-gray-900">{{ $brand['name'] }}</span>
                                        <span class="text-sm text-gray-500 font-medium">Rank
                                            #{{ $index + 1 }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-xl font-bold text-gray-900">{{ number_format($brand['total_sales'], 0) }}</span>
                                    <span class="text-sm text-gray-500 font-medium">Total Sales</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Monthly Top Selling Brands -->
                        <div x-show="viewMode === 'monthly'" class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                            @foreach ($topSellingBrandsMonthly as $index => $brand)
                            <div
                                class="group flex items-center justify-between p-4 rounded-lg transition-all duration-300 hover:bg-gray-50/80 hover:shadow-sm">
                                <div class="flex items-center space-x-4">
                                    @if ($brand->picture)
                                    <img src="{{ asset('storage/' . $brand->picture) }}" alt="{{ $brand->name }}"
                                        class="w-12 h-12 rounded-lg object-cover border-2
                                                rank-box {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : '')) }}">
                                    @else
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center border-2 rank-box {{ $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : '')) }}">
                                        <span class="text-xl font-bold text-white">
                                            {{ substr($brand->name, 0, 1) }}
                                        </span>
                                    </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="text-lg font-semibold text-gray-900">{{ $brand->name }}</span>
                                        <span class="text-sm text-gray-500 font-medium">Rank
                                            #{{ $index + 1 }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-xl font-bold text-gray-900">{{ number_format($brand->total_sales, 0) }}</span>
                                    <span class="text-sm text-gray-500 font-medium">Monthly Sales</span>
                                </div>
                            </div>
                            @endforeach
                            @if($topSellingBrandsMonthly->isEmpty())
                            <div class="flex flex-col items-center justify-center py-8 px-4">
                                <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-chart-bar text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No sales data for this month</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Inventory Status Widget -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="border-b border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-gray-900">Stock Overview</h3>
                            <span class="px-3 py-1 text-sm font-medium text-blue-700 bg-blue-50 rounded-full">
                                Stock Levels
                            </span>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 font-medium">Total Items</span>
                            <span class="text-xl font-bold text-gray-900">{{ number_format($totalItems) }}</span>
                        </div>
                        <div class="space-y-6">
                            <!-- Critical Stock -->
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                        <span class="text-gray-600 font-medium">Critical Stock</span>
                                    </div>
                                    <span
                                        class="text-lg font-bold text-red-500">{{ number_format($stockLevels['critical']) }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-red-500 h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ ($stockLevels['critical'] / $totalItems) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                            <!-- Low Stock -->
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                        <span class="text-gray-600 font-medium">Low Stock</span>
                                    </div>
                                    <span
                                        class="text-lg font-bold text-yellow-500">{{ number_format($stockLevels['low']) }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-yellow-500 h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ ($stockLevels['low'] / $totalItems) * 100 }}%"></div>
                                </div>
                            </div>
                            <!-- Well Stocked -->
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                        <span class="text-gray-600 font-medium">Well Stocked</span>
                                    </div>
                                    <span
                                        class="text-lg font-bold text-green-500">{{ number_format($stockLevels['healthy']) }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-green-500 h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ ($stockLevels['healthy'] / $totalItems) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bottom Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Top Selling Items -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="border-b border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-gray-900">BEST Sellers</h3>
                            <span class="px-3 py-1 text-sm font-medium text-emerald-700 bg-emerald-50 rounded-full">
                                Last 30 Days
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach ($topSellingItems as $item)
                            <div
                                class="group flex justify-between items-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-50 hover:shadow-sm">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-sm font-bold">
                                        #{{ $loop->iteration }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="text-lg font-bold text-gray-900">{{ number_format($item->total_quantity) }}</span>
                                    <span class="text-sm text-gray-500 font-medium">sold</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Low Stock Alerts -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden stripe-critical" x-data="{ isOpen: false }">
                    <div class="border-b border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-gray-900">Stock Warnings</h3>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="px-3 py-1 text-sm font-medium text-red-700 bg-red-50 rounded-full animate-">
                                    Requires Attention
                                </span>
                                <button @click="isOpen = !isOpen" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas" :class="isOpen ? 'fa-eye' : 'fa-eye-slash'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div x-show="isOpen" x-transition>
                        <div class="p-6">
                            @if ($lowStockItems->isNotEmpty())
                            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                                @foreach ($lowStockItems->sortBy('quantity') as $item)
                                <div
                                    class="group flex justify-between items-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-50 hover:shadow-sm">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span
                                                class="text-sm font-semibold text-gray-900">{{ $item->priceAfterSale() }}
                                                EGP</span>
                                            <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                                            <span class="text-sm text-red-400 font-medium">{{ $item->quantity }}
                                                left</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('items.edit', $item->id) }}"
                                        class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-all duration-300">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="flex flex-col items-center justify-center py-8 px-4">
                                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-check text-green-500 text-xl"></i>
                                </div>
                                <p class="text-green-500 font-medium">All items are well-stocked!</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Add SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Flash Messages -->
        @if (session('success'))
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 3000,
            timerProgressBar: true
        });
        </script>


        @endif

        @if (session('error'))
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}"
        });
        </script>
        @endif

        @if (session('warning'))
        <script>
        let warningHtml = "{{ session('warning') }}";
        @if (session('errors'))
            warningHtml += "<br><br><strong>Errors encountered:</strong><ul style='text-align: left; margin: 10px 0;'>";
            @foreach (session('errors') as $error)
                warningHtml += "<li>{{ $error }}</li>";
            @endforeach
            warningHtml += "</ul>";
        @endif

        Swal.fire({
            icon: 'warning',
            title: 'Import Completed with Issues',
            html: warningHtml,
            width: 600,
            showConfirmButton: true
        });
        </script>
        @endif

        <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('categoryPerformance', () => ({
                showAllCategories: false
            }))
        })
        </script>
        <script>
        // Session duration timer driven by server-side last_login; runs until logout
        (function () {
            const timer = document.getElementById('sessionTimer');
            if (!timer) return;
            const loginAtIso = timer.getAttribute('data-login-at');
            const out = document.getElementById('sessionDuration');
            if (!loginAtIso || !out) return;

            const loginAtMs = new Date(loginAtIso).getTime();
            function fmt(ms) {
                if (ms < 0) ms = 0;
                const t = Math.floor(ms / 1000);
                const h = String(Math.floor(t / 3600)).padStart(2, '0');
                const m = String(Math.floor((t % 3600) / 60)).padStart(2, '0');
                const s = String(t % 60).padStart(2, '0');
                return `${h}:${m}:${s}`;
            }
            function tick() {
                out.textContent = fmt(Date.now() - loginAtMs);
            }
            tick();
            const id = setInterval(tick, 1000);

            // Stop updating on logout form submission
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!form || !(form instanceof HTMLFormElement)) return;
                const action = form.getAttribute('action') || '';
                if (action.includes('/logout')) clearInterval(id);
            }, true);
        })();
        </script>
        <script>
        // Lightweight heartbeat to keep the session active while the user is logged in
        (function () {
            const HEARTBEAT_MS = 180 * 1000; // every 180s
            function ping() {
                fetch('{{ route('session.keepalive') }}', {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).catch(() => { /* ignore network errors */ });
            }
            // Start soon after load, then repeat
            setTimeout(ping, 5000);
            setInterval(ping, HEARTBEAT_MS);
        })();
        </script>
</body>
<footer class="bg-gray-100 border-t border-gray-200 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="mt-4 md:mt-0">
                <p class="text-sm text-gray-600">
                    Connecting communities, empowering local businesses.
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <p class="text-sm text-gray-600">
                    &copy; {{ date('Y') }} Local Hub<sup class="text-xs align-top">&reg;</sup>. All rights
                    reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
@stack('scripts')

</html>
