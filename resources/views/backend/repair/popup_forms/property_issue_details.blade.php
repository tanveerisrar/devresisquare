@if(!isset($editMode) || !$editMode)
    <!-- Display View Mode -->
    <p><strong>Notes:</strong> {{ isset($property) && $property->notes ? $property->notes : 'N/A' }}</p>
@else
    <form id="propertyNotesForm">
        @csrf
        <input type="hidden" name="repair_id" value="{{ $repairIssue->id }}">
        <input type="hidden" name="form_type" value="notes">

        <div class="form-group">
            <label for="notes">Note</label>
            <textarea name="notes" id="notes" rows="6" placeholder="Notes" class="form-control">{{ isset($property) && $property->notes ? $property->notes : '' }}</textarea>
        </div>

        <button type="submit" class="btn btn-success mt-3 float-end">Save Changes</button>
    </form>
@endif