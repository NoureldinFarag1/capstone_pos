@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Enter Cash Drawer Amount</h3>
    <form action="{{ route('cash.drawer.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>
@endsection
