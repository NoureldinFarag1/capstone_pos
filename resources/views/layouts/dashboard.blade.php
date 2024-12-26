@php
    use Illuminate\Support\Facades\Request;
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>local HUB</title>
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body>
            <nav class="bg-white shadow-lg">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Logo Section -->
                        <div class="flex items-center">
                            <a href=# class="flex items-center">
                                <img src="{{ asset('images/logo.png') }}" alt="LocalHUB Logo" class="h-10 w-auto">
                                <span class="ml-3 text-xl font-bold text-gray-800">
                                    <span class="text-black no-underline">LOCAL</span>
                                    <span class="text-red-600 no-underline">HUB</span>
                                </span>
                            </a>
                        </div>

                        <!-- Main Navigation -->
                        <div class="hidden sm:flex sm:items-center sm:space-x-4">
                            <!-- Dashboard -->
                            @role('admin|moderator')
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-home mr-2"></i>Dashboard
                            </a>
                            @endrole


                            <!-- Inventory Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button
                                    @click="open = !open"
                                    class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center transition-colors"
                                >
                                    <i class="fas fa-box-open mr-2"></i>Inventory
                                    <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    class="absolute z-10 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                >
                                    <div class="py-1 bg-white rounded-md shadow-xs">
                                        <a href="{{ route('items.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-box mr-2"></i>Items
                                        </a>
                                        <a href="{{ route('brands.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-tags mr-2"></i>Brands
                                        </a>
                                        <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-th-list mr-2"></i>Categories
                                        </a>
                                        <a href="{{ route('sizes.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-ruler mr-2"></i>Sizes
                                        </a>
                                        <a href="{{ route('colors.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-tint mr-2"></i>Colors
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Sales Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button
                                    @click="open = !open"
                                    class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center transition-colors"
                                >
                                    <i class="fas fa-chart-line mr-2"></i>Sales
                                    <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    class="absolute z-10 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                >
                                    <div class="py-1 bg-white rounded-md shadow-xs">
                                        <a href="{{ route('sales.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-list mr-2"></i>Sales List
                                        </a>
                                        <a href="{{ route('sales.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-plus-circle mr-2"></i>Create Sale
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Section -->
                            @can('admin')
                                <a href="{{ route('users.index') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="fas fa-users-cog mr-2"></i>Manage Users
                                </a>
                            @endcan
                        </div>

                    <!-- Notification and Logout aligned to the right -->
                    <div class="hidden sm:flex sm:items-center">
                        <!-- Low Stock Notification Dropdown -->
                        @if(isset($lowStockItems) && $lowStockItems->isNotEmpty())
                        <li class="nav-item dropdown me-3" style="list-style-type: none;">
                            <a class="nav-link position-relative" href="#" id="lowStockDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                @if(count($lowStockItems) > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ count($lowStockItems) }}
                                    </span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="lowStockDropdown">
                                <h6 class="dropdown-header">Low Stock Alerts</h6>
                                @foreach($lowStockItems as $item)
                                    <a class="dropdown-item" href="{{ route('items.edit', $item->id) }}">
                                        {{ $item->name }} (Stock: {{ $item->quantity }})
                                    </a>
                                @endforeach
                            </div>
                        </li>
                        @endif

                        <div>
                            <span>{{ $user->name }}</span>
                            <span>({{ $user->getRoleNames()->first() ?? 'Role not assigned' }})</span>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-red-500 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-red-600 transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>



        <!-- Content Area -->
        <div class="row">
            <div class="col-md-12">
                @yield('content')
                <br>
                @if(Request::routeIs('dashboard')) <!-- Show only on the dashboard page -->
                <!-- Dashboard Widgets -->
                <div class="container mx-auto p-6">
                    <!-- Quick Actions Card -->
                    <div class="mb-8">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="border-b border-gray-100 p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-2xl font-bold text-gray-900">Quick Actions</h3>
                                    <span class="px-3 py-1 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-full">
                                        Frequently Used
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <a href="/sales/create" class="group flex items-center justify-center p-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                        <i class="fas fa-plus-circle text-white text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                                        <span class="text-white font-medium">New Sale</span>
                                    </a>

                                    <a href="/items/create" class="group flex items-center justify-center p-6 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                        <i class="fas fa-box-open text-white text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                                        <span class="text-white font-medium">Add Item</span>
                                    </a>

                                    <a href="/items" class="group flex items-center justify-center p-6 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                        <i class="fas fa-boxes text-white text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                                        <span class="text-white font-medium">View Inventory</span>
                                    </a>

                                    <a href="/sales" class="group flex items-center justify-center p-6 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl hover:from-amber-600 hover:to-amber-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                        <i class="fas fa-chart-line text-white text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                                        <span class="text-white font-medium">Sales Overview</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Key Metrics Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        <!-- Today's Revenue -->
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium">Today's Earnings</h3>
                                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                                        <i class="fas fa-dollar-sign text-2xl"></i>
                                    </div>
                                </div>
                                <div class="text-3xl font-bold mb-2">{{ number_format($todayRevenue ?? 0, 2) }} EGP </div>
                                @if(isset($revenueGrowth) && $revenueGrowth > 0)
                                    <div class="text-sm bg-white/10 rounded-lg px-3 py-1.5 inline-block">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        {{ number_format($revenueGrowth, 1) }}% from yesterday
                                    </div>
                                @endif

                                <div class="mt-6 space-y-3">
                                    <div class="flex justify-between items-center py-2.5 px-4 bg-white/10 rounded-lg">
                                        <span class="text-sm font-medium">Cash</span>
                                        <span class="font-bold">{{ number_format($cashPayments, 2) }} EGP</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 px-4 bg-white/10 rounded-lg">
                                        <span class="text-sm font-medium">Visa</span>
                                        <span class="font-bold">{{ number_format($creditPayments, 2) }} EGP</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 px-4 bg-white/10 rounded-lg">
                                        <span class="text-sm font-medium">Mobile Payment</span>
                                        <span class="font-bold">{{ number_format($mobilePayments, 2) }} EGP</span>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full -mr-16 -mt-16"></div>
                        </div>

                        <!-- Monthly Sales -->
                        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium">Monthly Sales</h3>
                                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                                        <i class="fas fa-chart-bar text-2xl"></i>
                                    </div>
                                </div>
                                <div class="text-3xl font-bold mb-2">{{ number_format($monthlySales, 2) }} EGP</div>
                                <div class="text-sm bg-white/10 rounded-lg px-3 py-1.5 inline-block">
                                    {{ $salesGrowthPercentage >= 0 ? '+' : '' }}{{ number_format($salesGrowthPercentage, 2) }}% from last month
                                </div>

                                <div class="mt-6 space-y-3">
                                    <div class="flex justify-between items-center py-2.5 px-4 bg-white/10 rounded-lg">
                                        <span class="text-sm font-medium">Cash</span>
                                        <span class="font-bold">{{ number_format($cashPaymentsMonthly, 2) }} EGP</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 px-4 bg-white/10 rounded-lg">
                                        <span class="text-sm font-medium">Visa</span>
                                        <span class="font-bold">{{ number_format($creditPaymentsMonthly, 2) }} EGP</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2.5 px-4 bg-white/10 rounded-lg">
                                        <span class="text-sm font-medium">Mobile Payment</span>
                                        <span class="font-bold">{{ number_format($mobilePaymentsMonthly, 2) }} EGP</span>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full -mr-16 -mt-16"></div>
                        </div>

                        <!-- Top Payment Method -->
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium">Top Payment Method</h3>
                                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                                        @if($topPaymentMethod === 'cash')
                                            <i class="fas fa-cash-register text-2xl text-white"></i>
                                        @elseif ($topPaymentMethod === 'credit_card')
                                            <i class="fas fa-credit-card text-2xl text-white"></i>
                                        @elseif($topPaymentMethod === 'mobile_pay')
                                            <i class="fas fa-mobile-alt text-2xl text-white"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-3xl font-bold mb-4">{{ ucfirst($topPaymentMethod) }}</div>
                                <div class="bg-white/10 rounded-lg p-4">
                                    <div class="mb-2">
                                        <span class="text-sm font-medium">Top Payment Method</span>
                                        <span class="float-right font-bold">{{ number_format($topPaymentMethodPercentage, 2) }}%</span>
                                    </div>
                                    <div class="w-full bg-white/10 rounded-full h-2 mb-4">
                                        <div class="bg-white h-2 rounded-full" style="width: {{ $topPaymentMethodPercentage }}%"></div>
                                    </div>
                                    <div class="text-sm text-white/90">
                                        {{ $topPaymentMethodCount }} out of {{ $AllSalesCount }} transactions
                                    </div>
                                </div>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full -mr-16 -mt-16"></div>
                        </div>
                    </div>

                    <!-- Details Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Top Selling Brands Widget -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="border-b border-gray-100 p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-2xl font-bold text-gray-900">GOATS</h3>
                                    <span class="px-3 py-1 text-sm font-medium text-purple-700 bg-purple-50 rounded-full">
                                        Top Performers
                                    </span>
                                </div>
                            </div>

                            <div class="p-6">
                                @foreach($topSellingBrandDetails as $index => $brand)
                                    <div class="group flex items-center justify-between mb-4 last:mb-0 p-4 rounded-xl transition-all duration-300 hover:bg-gray-50/80 hover:shadow-sm">
                                        <div class="flex items-center space-x-4">
                                            <div class="relative">
                                                @if($index == 0)
                                                    <i class="fas fa-trophy text-2xl transition-all duration-300 group-hover:scale-110 text-yellow-400"></i>
                                                @elseif ($index == 1)
                                                    <i class="fas fa-trophy text-2xl transition-all duration-300 group-hover:scale-110 text-gray-400"></i>
                                                @elseif ($index == 2)
                                                    <i class="fas fa-trophy text-2xl transition-all duration-300 group-hover:scale-110 text-orange-600"></i>
                                                @endif
                                            </div>

                                            @if($brand['image'])
                                                <img src="{{ asset('storage/' . $brand['image']) }}"
                                                     alt="{{ $brand['name'] }}"
                                                     class="w-12 h-12 rounded-lg object-cover border-2 border-gray-100">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                                    <span class="text-xl font-bold text-gray-400">
                                                        {{ substr($brand['name'], 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="flex flex-col">
                                                <span class="text-lg font-semibold text-gray-900">{{ $brand['name'] }}</span>
                                                <span class="text-sm text-gray-500">Rank #{{ $index + 1 }}</span>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-end">
                                            <span class="text-xl font-bold text-gray-900">{{ number_format($brand['total_sales'], 0) }}</span>
                                            <span class="text-sm text-gray-500">Sales</span>
                                        </div>
                                    </div>
                                @endforeach
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
                                        <span class="text-lg font-bold text-red-500">{{ number_format($stockLevels['critical']) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-red-500 h-2.5 rounded-full transition-all duration-500"
                                             style="width: {{ ($stockLevels['critical'] / $totalItems) * 100 }}%"></div>
                                    </div>
                                </div>

                                <!-- Low Stock -->
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                            <span class="text-gray-600 font-medium">Low Stock</span>
                                        </div>
                                        <span class="text-lg font-bold text-yellow-500">{{ number_format($stockLevels['low']) }}</span>
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
                                        <span class="text-lg font-bold text-green-500">{{ number_format($stockLevels['healthy']) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-green-500 h-2.5 rounded-full transition-all duration-500"
                                             style="width: {{ ($stockLevels['healthy'] / $totalItems) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                   <!-- Bottom Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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
                                    @foreach($topSellingItems as $item)
                                        <div class="group flex justify-between items-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-50 hover:shadow-sm">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 text-sm font-bold">
                                                    #{{ $loop->iteration }}
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg font-bold text-gray-900">{{ number_format($item->total_quantity) }}</span>
                                                <span class="text-sm text-gray-500">sold</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Low Stock Alerts -->
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="border-b border-gray-100 p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-2xl font-bold text-gray-900">Stock Warnings</h3>
                                    <span class="px-3 py-1 text-sm font-medium text-red-700 bg-red-50 rounded-full animate-pulse">
                                        Requires Attention
                                    </span>
                                </div>
                            </div>

                            <div class="p-6">
                                @if($lowStockItems->isNotEmpty())
                                    <div class="space-y-3">
                                        @foreach($lowStockItems->take(5) as $item)
                                            <div class="group flex justify-between items-center p-4 rounded-lg transition-all duration-300 hover:bg-gray-50 hover:shadow-sm">
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                                    <div class="flex items-center space-x-2 mt-1">
                                                        <span class="text-sm font-semibold text-gray-900">{{ $item->priceAfterSale() }} EGP</span>
                                                        <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                                                        <span class="text-sm text-red-400 font-medium">{{ $item->quantity }} left</span>
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
                @endif
            </div>
        </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
                    &copy; {{ date('Y') }} Local Hub<sup class="text-xs align-top">&reg;</sup>. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
@stack('scripts')
</html>
