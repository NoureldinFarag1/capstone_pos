@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1>Store Settings</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Store Name</h5>
                        <p class="card-text">{{ $storeName }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Store Slogan</h5>
                        <p class="card-text">{{ $storeSlogan }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Store Instagram</h5>
                        <p class="card-text">{{ $storeInstagram }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Logo</h5>
                        <img src="{{ asset(str_replace(public_path(), '', $logoPath)) }}" alt="Store Logo" style="max-width: 100px;">
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
