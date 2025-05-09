{{-- Hidden div for contact ID --}}
<div id="hidden-contact-id" class="" data-contact-id="{{ $contactId }}">
    @php
        // Debugging the contactId
        echo '<pre>';
        echo 'Contact ID: ';
        var_dump($contactId);
        echo '</pre>';
    @endphp
</div>