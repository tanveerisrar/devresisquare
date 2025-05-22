<div class="row g-3">
    @forelse($notes as $note)
        <div class="col-12">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-secondary">{{ $note->noteType->name ?? 'N/A' }}</span>
                    <span class="d-flex align-items-end">
                        <small class="text-muted ms-2">Added At: {{ formatDateTime($note->created_at) }}</small>
                        <small class="text-muted ms-2">Updated At: {{ formatDateTime($note->updated_at) }}</small>
                    </span>
                </div>
                <div class="card-body">
                    {{-- <p class="card-text">{!! $note->content !!}</p> --}}
                    <p class="card-text">{!! Str::limit($note->content, 200) !!}</p>
                </div>
                <div class="card-footer text-end">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-outline-info notes-view me-1" data-id="{{ $note->id }}"
                            title="View Full Note">
                            <i class="bi bi-eye"> View</i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger notes-edit me-1" data-id="{{ $note->id }}"
                            title="Edit Note">
                            <i class="bi bi-pencil">Edit</i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger deleteNote me-1" title="Delete Note"
                            data-id="{{ $note->id }}">
                            <i class="bi bi-trash">Delete</i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">No notes found.</div>
        </div>
    @endforelse
</div>

<div class="mt-3">
    {{ $notes->links() }}
</div>