<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
    @isset($current)
      <li class="breadcrumb-item active" aria-current="page">{{ $current }}</li>
    @endisset
  </ol>
</nav>
