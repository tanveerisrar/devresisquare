<div class="card">
    <div class="card-header">
        <strong>Contact Owner</strong>
    </div>
    <div class="card-body">
        @if($contact->creator)
            <p><strong>Name:</strong> {{ $contact->creator->name }}</p>
            <p><strong>Email:</strong> {{ $contact->creator->email }}</p>
            <p>
                <strong>Role:</strong>
                @if($contact->creator && $contact->creator->role)
                    <span class="badge bg-primary text-uppercase">
                        {{ str_replace('_', ' ', $contact->creator->role->name) }}
                    </span>
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </p>
        @else
            <p class="text-muted">No owner assigned to this contact.</p>
        @endif
    </div>
</div>
