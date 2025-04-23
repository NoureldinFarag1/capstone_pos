@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1>Edit Store Settings</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('store-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Website Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="site_title">Site Title</label>
                                <input type="text" name="site_title" id="site_title" class="form-control" value="{{ $siteTitle }}" required>
                                <small class="form-text text-muted">This is the title that appears in the browser tab.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Store Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="store_name">Store Name</label>
                                <input type="text" name="store_name" id="store_name" class="form-control" value="{{ $storeName }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="store_slogan">Store Slogan</label>
                                <input type="text" name="store_slogan" id="store_slogan" class="form-control" value="{{ $storeSlogan }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="store_instagram">Store Instagram</label>
                                <input type="text" name="store_instagram" id="store_instagram" class="form-control" value="{{ $storeInstagram }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Receipt Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="receipt_logo">Receipt Logo</label>
                                <input type="file" name="receipt_logo" id="receipt_logo" class="form-control">
                                @if(isset($receiptLogoPath))
                                    <div class="mt-2">
                                        <strong>Current Receipt Logo:</strong>
                                        <img src="{{ asset($receiptLogoPath) }}" alt="Current Receipt Logo" style="max-width: 100px; margin-top: 5px;">
                                    </div>
                                @endif
                                <small class="form-text text-muted">This logo appears on printed receipts.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Navbar Logos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="navbar_logo">Navbar Logo (Graphic)</label>
                                <input type="file" name="navbar_logo" id="navbar_logo" class="form-control">
                                @if(isset($navbarLogoPath))
                                    <div class="mt-2">
                                        <strong>Current Navbar Logo:</strong>
                                        <img src="{{ asset($navbarLogoPath) }}" alt="Current Navbar Logo" style="max-width: 100px; margin-top: 5px;">
                                    </div>
                                @endif
                                <small class="form-text text-muted">This is the graphic logo that appears in the navbar (left side).</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="navbar_text_logo">Navbar Text Logo</label>
                                <input type="file" name="navbar_text_logo" id="navbar_text_logo" class="form-control">
                                @if(isset($navbarTextLogoPath))
                                    <div class="mt-2">
                                        <strong>Current Navbar Text Logo:</strong>
                                        <img src="{{ asset($navbarTextLogoPath) }}" alt="Current Navbar Text Logo" style="max-width: 100px; margin-top: 5px;">
                                    </div>
                                @endif
                                <small class="form-text text-muted">This is the text logo that appears in the navbar (next to the graphic logo).</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Settings</button>
                <a href="{{ route('store-settings.index') }}" class="btn btn-secondary">Back to Store Settings</a>
            </div>
        </form>
    </div>
@endsection
