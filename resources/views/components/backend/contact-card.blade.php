<div class="pv_content_wrapper {{ $cardStyle == 'vertical'? 'vertical_card' : '' }} {{$class}}" data-contact-id="{{ $contactId }}">
    {{-- <div class="pv_image">
        <img src="{{ asset('/asset/images/temp-contact.webp') }}" alt="contact">
    </div> --}}

    <div class="pv_content">
        <div class="pvc_contact_name">
            @if($contactName)
                {{ $contactName }}
            @else
                <em>Contact name not available</em>
            @endif
        </div>

        <div class="rs_row">
            <div class="rs_col">
                <div class="pv_email">
                    Email: 
                    <strong>
                        @if($email)
                            {{ $email }}
                        @else
                            Unknown
                        @endif
                    </strong>
                </div>
            </div>

            <div class="rs_col">
                <div class="pv_phone">
                    Phone:
                    <strong>
                        @if($phone)
                            {{ $phone }}
                        @else
                            Not specified
                        @endif
                    </strong>
                </div>
            </div>
        </div>
        
    </div>
</div>
