{{-- <h1>Notes</h1> --}}
{{-- @include("backend.properties.popup_forms.notes_tab", ['property' => $property, 'notes' => $notes, ]) --}}

<div id="section-notes_tab-{{ $property->id }}">
    @include("backend.properties.popup_forms.notes_tab", ['property' => $property, 'notes' => $notes,])
</div>