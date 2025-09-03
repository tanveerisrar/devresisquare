@extends('backend.layout.app')

@section('content')
<div class="container">
    <h1>Transaction Categories</h1>

    <a href="{{ route('backend.transaction_categories.create') }}" class="btn btn-primary mb-3">+ Add Category</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Code</th>
                <th>Type</th>
                <th>Status</th>
                <th>System</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->code ?? '-' }}</td>
                    <td>{{ $cat->is_income ? 'Income' : 'Expense' }}</td>
                    <td>{{ $cat->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $cat->is_system ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('backend.transaction_categories.show', $cat) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('backend.transaction_categories.edit', $cat) }}" class="btn btn-sm btn-warning">Edit</a>
                        @if(!$cat->is_system)
                        <form action="{{ route('backend.transaction_categories.destroy', $cat) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No categories found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}
</div>
@endsection
