{{-- Hidden div for contact ID --}}
<div id="hidden-contact-id" class="d-none" data-contact-id="{{ $contactId }}">
    @php
        // Debugging the contactId
        echo '<pre>';
        echo 'Contact ID: ';
        var_dump($contactId);
        echo '</pre>';
    @endphp
</div>

<div class="property_note">
    <span class="fw-semibold">
    <div class="compliance-update-ajax" id="section-compliance-{{ $contactId }}">
        @include("backend.contacts.popup_forms.compliance", ['contact' => $contact])
    </div>
    </span>
    <button class="btn btn-outline-danger btn-sm editForm" data-form="{{ 'compliance' }}" data-id="{{ $contactId }}">
        Edit
    </button>
</div>