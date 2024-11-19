<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <title>Admin Panel - local HUB</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">local HUB</a>
                <img src="{{ asset('images/logo.png') }}" alt="Capstone Logo" style="width: 150px; height: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('brands.index') }}">Brands</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('items.index') }}">Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sales.index') }}">Sales</a>
                    </li>
                </ul>
            </div>
            <!-- Low Stock Notification Dropdown -->
            @if(isset($lowStockItems) && $lowStockItems->isNotEmpty())
                <li class="nav-item dropdown me-3"> <!-- Adding margin-right to separate elements -->
                    <a class="nav-link" href="#" id="lowStockDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        @if(count($lowStockItems) > 0)
                            <span class="badge badge-danger">{{ count($lowStockItems) }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="lowStockDropdown">
                        <h6 class="dropdown-header">Low Stock Alerts</h6>
                        @if(count($lowStockItems) > 0)
                            @foreach($lowStockItems as $item)
                                <a class="dropdown-item" href="{{ route('items.show', $item->id) }}">
                                    {{ $item->name }} (Stock: {{ $item->quantity }})
                                </a>
                            @endforeach
                        @else
                            <span class="dropdown-item text-muted">No low-stock items</span>
                        @endif
                    </div>
                </li>
                @endif
        </div>
         <!-- Filter dropdown menu -->
         <div class="dropdown me-3"> <!-- Margin added to move to the left -->
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Filter Items
                    </button>
                    <form action="{{ route('items.index') }}" method="GET">
                        <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="filterDropdown" style="width: 300px;">
                            <!-- Brand Filter Section -->
                            <li class="dropdown-header">Filter by Brand</li>
                            @foreach($brands as $brand)
                            <li class="dropdown-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="brand" value="{{ $brand->id }}" id="brand{{ $brand->id }}"
                                        {{ request('brand') == $brand->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="brand{{ $brand->id }}">
                                        {{ $brand->name }}
                                    </label>
                                </div>
                            </li>
                            @endforeach

                            <li><hr class="dropdown-divider"></li>

                            <!-- Category Filter Section -->
                            <li class="dropdown-header">Filter by Category</li>
                            @foreach($categories as $category)
                            <li class="dropdown-item">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="category" value="{{ $category->id }}" id="category{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            </li>
                            @endforeach
                        </ul>

                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </form>
                </div>

       <!-- Logout Button -->
         <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
         </form>
    </nav>

    <div class="container">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
