@extends('backend.layout.app')

@section('content')
<div class="container">
    <h1>Add Transaction Category</h1>
    <form action="{{ route('backend.transaction_categories.store') }}" method="POST">
        @include('backend.transaction_categories._form')
        <button type="submit" class="btn btn-success">Save Category</button>
        <a href="{{ route('backend.transaction_categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
