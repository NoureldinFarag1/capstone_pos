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

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="store_name">Store Name</label>
                        <input type="text" name="store_name" id="store_name" class="form-control" value="{{ $storeName }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="store_slogan">Store Slogan</label>
                        <input type="text" name="store_slogan" id="store_slogan" class="form-control" value="{{ $storeSlogan }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="store_instagram">Store Instagram</label>
                        <input type="text" name="store_instagram" id="store_instagram" class="form-control" value="{{ $storeInstagram }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control">
                        <img src="{{ asset(str_replace(public_path(), '', $logoPath)) }}" alt="Current Logo" style="max-width: 100px; margin-top: 5px;">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Settings</button>
            <a href="{{ route('store-settings.index') }}" class="btn btn-secondary">Back to Store Settings</a>
        </form>
    </div>
@endsection
