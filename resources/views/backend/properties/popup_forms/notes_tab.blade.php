@if(!isset($editMode) || !$editMode)
    <!-- Display View Mode -->
    @if($notes->isNotEmpty())
        <!-- Loop through the notes -->
        <div class="note-list">
            @foreach($notes as $note)
            <div class="note">
                <strong>{{ $note->type }}:</strong>
                <p>{!! $note->content !!}</p>
            </div>
            @endforeach
        </div>
    @else
        <p>No notes available.</p>
    @endif

@else
    <form id="propertyNotesTabForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="form_type" value="notes_tab">

        <select id="noteType">
            <option>Email</option>
            <option>Call</option>
            <option>Text</option>
            <option>General</option>
            <option>MIS</option>
        </select>

        <div class="form-group">
            <label for="notes">Note</label>
            <textarea name="notes" id="notes_tab" rows="6" placeholder="Notes" class="form-control">{{ isset($property) && $property->notes ? $property->notes : '' }}</textarea>
        </div>

        <button type="submit" class="btn btn_secondary mt-3 float-end">Save Changes</button>
    </form>
@endif