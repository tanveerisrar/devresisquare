
@extends('backend.layout.app')

@section('content')
<div class="container">
    <h1>Category Details</h1>
    <table class="table table-bordered">
        <tr><th>ID</th><td>{{ $transaction_category->id }}</td></tr>
        <tr><th>Name</th><td>{{ $transaction_category->name }}</td></tr>
        <tr><th>Code</th><td>{{ $transaction_category->code ?? '-' }}</td></tr>
        <tr><th>Type</th><td>{{ $transaction_category->is_income ? 'Income' : 'Expense' }}</td></tr>
        <tr><th>Status</th><td>{{ $transaction_category->is_active ? 'Active' : 'Inactive' }}</td></tr>
        <tr><th>System</th><td>{{ $transaction_category->is_system ? 'Yes' : 'No' }}</td></tr>
    </table>
    <a href="{{ route('backend.transaction_categories.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('backend.transaction_categories.edit', $transaction_category) }}" class="btn btn-warning">Edit</a>
</div>
@endsection
