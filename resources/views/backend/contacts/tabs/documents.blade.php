
<x-backend-documents-component
    :documentable-type="get_class($contact)"
    :documentable-id="$contact->id"
    :document-types="$documentTypes"
    :initial-documents="$documents"
/>