@props(['noteableType', 'noteableId', 'noteTypes'])
<div class="notes-component" data-noteable-type="{{ $noteableType }}" data-noteable-id="{{ $noteableId }}">

    {{-- ADD NEW --}}
    <div class="mb-3">
        <button type="button" class="btn btn-outline-primary notes-add">Add New Note</button>
    </div>

    {{-- FILTER FORM --}}
    <form class="notes-filter-form row g-2 mb-3">
        <div class="col-md-3">
            <select name="note_type_id" class="form-select">
                <option value="">All Types</option>
                @foreach($noteTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search content…">
        </div>
        <div class="col-md-2">
            <input type="date" name="from_date" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="date" name="to_date" class="form-control">
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary">Filter</button>
            <button type="button" class="btn btn-secondary notes-reset">Reset</button>
        </div>
    </form>

    {{-- LIST --}}
    {{-- <div class="notes-list"></div> --}}
    {{-- Render initial list server-side: --}}
    <div class="notes-list">
        @include('components.backend.notes._notes_list', [
        'notes'     => $initialNotes,
        'noteTypes' => $noteTypes
        ])
    </div>

    {{-- MODAL for Add/Edit/View --}}
    <div class="modal fade notes-modal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="notes-form">
                        @csrf
                        <input type="hidden" name="note_id" value="{{ $note->id ?? '' }}">
                        <input type="hidden" name="noteable_type" value="{{ $noteableType }}">
                        <input type="hidden" name="noteable_id" value="{{ $noteableId }}">

                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="note_type_id" class="form-select">
                                @foreach($noteTypes as $t)
                                    <option value="{{ $t->id }}" {{ (isset($note) && $note->note_type_id == $t->id) ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control aiz-text-editor" rows="4">{{ $note->content ?? '' }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary notes-save">
                            {{ isset($note) ? 'Update' : 'Save' }}
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>