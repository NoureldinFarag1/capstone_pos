@extends('layouts.dashboard')
@section('title', 'Store Settings')

@section('content')
    <div class="container">
        <h1>Store Settings</h1>

        <h2 class="mt-4 mb-3">Website Settings</h2>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Site Title</h5>
                        <p class="card-text">{{ $siteTitle }}</p>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-4 mb-3">Store Information</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Store Name</h5>
                        <p class="card-text">{{ $storeName }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Store Slogan</h5>
                        <p class="card-text">{{ $storeSlogan }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Store Instagram</h5>
                        <p class="card-text">{{ $storeInstagram }}</p>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-4 mb-3">Receipt Settings</h2>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Receipt Logo</h5>
                        @if(isset($receiptLogoPath))
                            <img src="{{ asset($receiptLogoPath) }}" alt="Receipt Logo" style="max-width: 100px;">
                        @else
                            <p class="card-text text-muted">No logo uploaded</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-4 mb-3">Navbar Settings</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Navbar Logo (Graphic)</h5>
                        @if(isset($navbarLogoPath))
                            <img src="{{ asset($navbarLogoPath) }}" alt="Navbar Logo" style="max-width: 100px;">
                        @else
                            <p class="card-text text-muted">No logo uploaded</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Navbar Text Logo</h5>
                        @if(isset($navbarTextLogoPath))
                            <img src="{{ asset($navbarTextLogoPath) }}" alt="Navbar Text Logo" style="max-width: 100px;">
                        @else
                            <p class="card-text text-muted">No logo uploaded</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('store-settings.edit') }}" class="btn btn-primary">Edit Settings</a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
@endsection
