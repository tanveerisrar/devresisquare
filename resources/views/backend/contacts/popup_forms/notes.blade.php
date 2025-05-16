@if(!isset($editMode) || !$editMode)
@php
    $impNotes = isset($contact) && $contact->imp_notes ? $contact->imp_notes : '';
@endphp
    <!-- Display View Mode -->
    <x-toggle-description :text="$impNotes" :limit="120" />
@else
    <form id="contactNotesForm">
        @csrf
        <input type="hidden" name="contact_id" value="{{ $contact->id }}">
        <input type="hidden" name="form_type" value="notes">

        <div class="form-group">
            <label for="notes">Note</label>
            <textarea name="imp_notes" id="notes" rows="6" placeholder="Notes" class="form-control">{{ isset($contact) && $contact->imp_notes ? $contact->imp_notes : '' }}</textarea>
        </div>

        <button type="submit" class="btn btn_secondary mt-3 float-end">Save Changes</button>
    </form>
@endif