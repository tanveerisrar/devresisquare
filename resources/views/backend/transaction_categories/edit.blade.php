@extends('backend.layout.app')

@section('content')
<div class="container">
    <h1>Edit Transaction Category</h1>
    <form action="{{ route('backend.transaction_categories.update', $transaction_category) }}" method="POST">
        @method('PUT')
        @include('backend.transaction_categories._form')
        <button type="submit" class="btn btn-primary">Update Category</button>
        <a href="{{ route('backend.transaction_categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection