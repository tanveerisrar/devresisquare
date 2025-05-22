<div class="card mb-4">
    <div class="card-header">
        <strong>Linked Properties</strong>
    </div>
    <div class="card-body">
        @if($properties->isEmpty())
            <p class="text-muted">No properties linked to this contact.</p>
        @else
            <ul class="list-group">
                @foreach($properties as $property)
                    <li class="list-group-item">
                        {!! get_contact_address_name_by_id($property->id) !!}
                        <span class="text-muted">(#{{ $property->prop_ref_no }})</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <strong>Linked Tenancies</strong>
    </div>
    <div class="card-body">
        @if($contact->tenancies->isEmpty())
            <p class="text-muted">No tenancies linked to this contact.</p>
        @else
            <ul class="list-group">
                @foreach($contact->tenancies as $tenancy)
                    <li class="list-group-item">
                        {{ $tenancy->title ?? 'Unnamed Tenancy' }}
                        <span class="text-muted">(#{{ $tenancy->id }})</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
