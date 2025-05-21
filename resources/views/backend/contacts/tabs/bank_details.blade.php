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


<div class="bank_detail-update-ajax" id="section-bank_detail-{{ $contactId }}">
    @include("backend.contacts.popup_forms.bank_detail", ['contact' => $contact, 'bankDetails' => $bankDetails])
    {{-- @include("backend.contacts.popup_forms.bank_detail", ['contact' => $contact, 'bankDetails' => $bankDetails, 'bankDetail' => $bankDetail]) --}}
</div>

