@extends('backend.layout.app')

@section('content')
<div class="container">
    <h1>Add Transaction</h1>

    <form action="{{ route('backend.transactions.store') }}" method="POST">
        @include('backend.transactions._form')

        <button type="submit" class="btn btn-success">Save Transaction</button>
        <a href="{{ route('backend.transactions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
