@extends('backend.layout.app')

@section('content')
<div class="mt-md-4 me-md-4 me-3 mt-3">
    <h2>Account Headers List</h2>
    <a href="{{ route('backend.account_headers.create') }}" class="btn btn-primary mb-3">Create Account Header</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Reference</th>
                <th>Status</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accountHeaders as $header)
            <tr>
                <td>{{ ucwords(str_replace('_',' ', $header->header_type)) }}</td>
                <td>{{ $header->name }}</td>
                <td>{{ $header->reference_number ?? '—' }}</td>
                <td>{!! booleanBadge($header->status ?? false) !!}</td>
                <td>{{ $header->description }}</td>
                <td>
                    <a href="{{ route('backend.account_headers.edit', $header->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('backend.account_headers.destroy', $header->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this header?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="text-center">No Account Headers Found.</td>
            </tr>
            @endforelse
        </tbody>

    </table>

    {{ $accountHeaders->links() }}
</div>
@endsection
