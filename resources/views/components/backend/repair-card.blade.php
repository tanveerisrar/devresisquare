@props(['repair'])

<div class="card mb-3 repair-card" data-url="{{ route('admin.property_repairs.show', $repair->id) }}" data-id="{{ $repair->id }}" onclick="loadRepairDetailByUrl(this)">
    <div class="card-body">
        <h5 class="card-title">Repair #{{ $repair->id }}</h5>
        <p class="mb-1"><strong>Status:</strong> {{ $repair->status }}</p>
        <p class="mb-1"><strong>Property:</strong> {{ $repair->property->prop_name ?? 'N/A' }}</p>
        <p class="text-muted"><small>{{ $repair->created_at->diffForHumans() }}</small></p>
    </div>
</div>
