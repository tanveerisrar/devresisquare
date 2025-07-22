@extends('backend.layout.app')

@section('content')
<div class="container">
    <h2 class="mb-4">User Profile</h2>
    <div class="card">
        <div class="card-body">
            <p><strong>Name:</strong> {{ $user->name ?? 'N/A' }}</p>
            <p><strong>Role{{ ($user && method_exists($user, 'getRoleNames') && $user->getRoleNames()->count() > 1) ? 's' : '' }}:</strong> {{ ($user && method_exists($user, 'getRoleNames') && $user->getRoleNames()->count() > 0) ? $user->getRoleNames()->implode(', ') : 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
            <p><strong>Address Line 1:</strong> {{ $user->address_line_1 ?? 'N/A' }}</p>
            <p><strong>Address Line 2:</strong> {{ $user->address_line_2 ?? 'N/A' }}</p>
            <p><strong>City:</strong> {{ $user->city ?? 'N/A' }}</p>
            <p><strong>State:</strong> {{ $user->state ?? 'N/A' }}</p>
            <p><strong>Zip:</strong> {{ $user->zip ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@endsection