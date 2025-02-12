@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Printer Settings</h1>
    <form action="{{ route('settings.printer.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="printer_name">Printer Name</label>
            <input type="text" name="printer_name" id="printer_name" class="form-control" value="{{ old('printer_name', config('printer.name')) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
