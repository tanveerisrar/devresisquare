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
    <div class="contact_detail-update-ajax" id="section-contact_detail-{{ $contactId }}">
        @include("backend.contacts.popup_forms.contact_detail", ['contact' => $contact])
    </div>
    </span>
    <button class="btn btn-outline-danger btn-sm editForm" data-form="{{ 'contact_detail' }}" data-id="{{ $contactId }}">
        Edit
    </button>
</div>
