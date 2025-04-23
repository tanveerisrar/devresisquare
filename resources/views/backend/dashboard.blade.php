@extends('backend.layout.app')

@section('content')
<div class="p-4">
    <h1>Welcome to Admin Dashboard</h1>
    <table id="users-table" class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->role->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
    {{-- Calendar --}}
    @include('backend.partials.calendar')
@endsection

@section('page.scripts')
<script>
    $(document).ready(function() {
        $('#users-table').DataTable({
            responsive:true
        });
    });
</script>
@endsection