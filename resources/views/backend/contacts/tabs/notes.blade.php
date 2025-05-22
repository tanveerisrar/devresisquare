
<x-backend-notes-component
    :noteable-type="get_class($contact)"
    :noteable-id="$contact->id"
    :note-types="$noteTypes"
    :initial-notes="$notes"
/>