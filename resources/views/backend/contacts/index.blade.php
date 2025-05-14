@extends('backend.layout.app')

@section('content')
    <div class="row g-0 view_contacts">
        <div class="col-lg-5 col-12">
            <div class="property_list_wrapper pt-lg-4 pt-2 ">
                <div class="pv_wrapper">
                    <div class="pv_header">
                        <div class="row">
                            <div class="col-3">
                                <div class="pv_title">Contacts</div>
                            </div>
                            <div class="col-9">
                                <x-backend.forms.search
                                    class=''
                                    placeholder='Search'
                                    value=''
                                    onClick='onClick()'
                                />
                            </div>
                            <div class="pv_btn">
                                <a href="{{ route('admin.contacts.create') }}" class="btn mt-2 btn-sm btn-outline-danger">
                                    Add Contact
                                </a>
                            </div>
                        </div>

                    </div>
                    
                    
                    <div class="pv_card_wrapper">
                        {{-- Dev Note: if select contact from list add class 'current' to contact card --}}
                        @foreach ($contacts as $contact)
                            @php
                                $nameParts = array_filter([
                                    $contact['full_name'] ?? '',
                                    $contact['first_name'] ?? '',
                                    $contact['middle_name'] ?? '',
                                    $contact['last_name'] ?? '',
                                ]);

                                $fullName = !empty($contact['full_name'])
                                    ? $contact['full_name']
                                    : implode(' ', $nameParts);
                            @endphp

                            <x-backend.contact-card
                                class="contact-card"
                                contact-name="{{ $fullName }}"
                                email="{{ $contact['email'] }}"
                                phone="{{ $contact['phone'] }}"
                                card-style=""
                                contact-id="{{ $contact['id'] }}" />
                        @endforeach

                    </div>                 
                    
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-12 property_detail_wrapper hide_this pt-lg-4 pt-0">
            <div class="pv_detail_wrapper">

                <x-backend.contacts-tabs :tabs="$tabs" class="contact_tabs" />

                <div class="pv_detail_content">
                    <div class="pv_detail_header">
                        <div class="pv_main_title">{{ ucfirst($tabName) }} Detail</div>
                        <div class="pvdh_btns_wrapper d-flex gap-3">
                            
                        </div>
                    </div>
                    <div class="pv_content_detail_wrapper">
                        <i class="bi bi-chevron-left" id="backBtn"></i>
                        <div class="pv_content_detail">
                            {!! $content !!}
                            <!-- The dynamic tab content will be injected here by AJAX -->
                            {{-- render first tabs blade file from view example @include('backend.properties.tabs' . $tabname) $tabname in small case --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="mobile_footer mobile_only">
                <div class="pvdh_btns_wrapper">
                    
                </div>
            </div>
        </div>
    </div>
    <style>
        .hidden {
            display: none !important;
        }
        .modal-content {
            height: auto;
            margin: auto;
        }
        .modal-backdrop.modal-stack {
            opacity: 0.3 !important;
        }
    </style>

</div>

    <!-- Include the Modal Component -->
    @include('backend.components.modal')
@endsection
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@section('page.scripts')
@if (isset($contactId) && isset($contact) && $contactId != $contact->id)
{{-- @php
var_dump($contactId);
@endphp --}}
<script>
    const url = new URL(window.location.href);
    url.searchParams.set('contact_id', '{{ $contactId }}');
    history.replaceState(null, '', url.toString());
</script>
@endif

<script>

    $(document).ready(function() {
        let isExpanded = true; // Initially, all accordions are open
    
        $(document).on('click', '#toggleAll', function() {
            if (isExpanded) {
                $(".accordion-collapse").collapse('hide'); // Collapse all
                $(this).text("Expand All");
            } else {
                $(".accordion-collapse").collapse('show'); // Expand all
                $(this).text("Collapse All");
            }
            isExpanded = !isExpanded; // Toggle state
        });

        // Function to check if the device is mobile
        function is_mobile() {
            return (
                /Mobi|Android/i.test(navigator.userAgent) || $(window).width() < 768
            );
        }

        if (is_mobile()) {
            $(document).on('click', '.contact-card', function() {
                $('#backBtn').addClass('property_bk_btn_show');
                $('.property_detail_wrapper').removeClass('hide_this');
                $('.property_list_wrapper').toggleClass('hide_this'); // Hide left column
                $('.property_detail_wrapper').addClass('show_this'); // Show right column
            });

            $(document).on('click', '#backBtn', function() {
                $('#backBtn').removeClass('property_bk_btn_show');
                $('.property_detail_wrapper').addClass('hide_this');
                $('.property_detail_wrapper').toggleClass('show_this'); // Hide right column
                $('.property_list_wrapper').toggleClass('hide_this'); // Show left column
            });
        }

        // Function to get URL parameters
        function getUrlParameter(name) {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        // Check if URL parameters are present (contact_id and tabname)
        function hasUrlParams() {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.has('contact_id') && urlParams.has('tabname');
        }

        // Function to update the title dynamically
        function updateTitle(tabName, contactId = null) {
            // Capitalize the first letter of the tab name for display
            var formattedTitle = tabName.charAt(0).toUpperCase() + tabName.slice(1);

            // Update the content of the title div
            $('.pv_main_title').text(formattedTitle + ' Detail');
        }

        // Handle Tab Clicks
        // Event listener for contact cards (left side)
        $(document).on('click', '.contact-card', function() {
            var contactId = $(this).data('contact-id');
            $('.contact-card').removeClass('current');
            $(this).addClass('current');
            var tabName = $('.tab-link.active').data('tab-name');
            loadTabContent(contactId, tabName);
        });

        // Event listener for tabs (right side)
        $(document).on('click', '.tab-link', function(e) {
            e.preventDefault();
            var tabName = $(this).data('tab-name');
            var contactId = $('.contact-card.current').data('contact-id');
            $('.tab-link').removeClass('active');
            $(this).addClass('active');
            loadTabContent(contactId, tabName); // Load content dynamically
        });

        // Check if URL parameters are present (contact_id and tabname)
        function hasUrlParams() {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.has('contact_id') && urlParams.has('tabname');
        }
        // Call the appropriate function based on URL parameters or default
        if (hasUrlParams()) {
            activateTabFromUrl(); // Handle tabs based on URL parameters
        } else {
            simulateTabClickAndContactCard(); // Default behavior
        }


        // Function to activate tab based on URL parameter
        function activateTabFromUrl() {
            var tabName = getUrlParameter('tabname'); // Get tabname from URL
            var contactId = getUrlParameter('contact_id'); // Get contact_id from URL

            if (tabName && contactId) {
                // Convert underscores back to spaces
                // var displayTabName = tabName.replace(/_/g, ' ');

                // Find the tab and contact card with the matching data attributes
                var selectedTab = $('.tab-link[data-tab-name="' + tabName + '"]');
                var selectedContactCard = $('.contact-card[data-contact-id="' + contactId + '"]');

                // Mark the selected tab and contact card as active/current
                $('.tab-link').removeClass('active');
                $('.contact-card').removeClass('current');
                selectedTab.addClass('active');
                selectedContactCard.addClass('current');

                // Load the content dynamically
                loadTabContent(contactId, tabName);
            }
        }

        // Simulate the first tab and first contact card selection on page load
        function simulateTabClickAndContactCard() {
            var firstContactCard = $('.contact-card').first(); // Get the first contact card
            var firstTab = $('.tab-link').first(); // Get the first tab

            // Get the contactId and tabName from the first contact card and tab
            var contactId = firstContactCard.data('contact-id');
            var tabName = firstTab.data('tab-name');
            console.log(contactId);
            console.log(tabName);

            // Trigger the AJAX load
            if (contactId && tabName) {
                loadTabContent(contactId, tabName);
                firstContactCard.addClass('current'); // Add 'current' class to the first contact card
                firstTab.addClass('active'); // Add 'active' class to the first tab
            }
        }

        // Call the simulateTabClickAndContactCard function on document ready only if URL parameters are NOT present
        // if (!hasUrlParams()) {
        //     simulateTabClickAndContactCard();
        // }

        // Function to load tab content dynamically via AJAX
        function loadTabContent(contactId, tabName) {
            // Replace spaces with underscores for the URL
            // var formattedTabName = tabName.replace(/\s+/g, '_');
            // Correctly format the URL with query parameters instead of placeholders
            var url = '{{ route('admin.contacts.index') }}' + '?contact_id=' + contactId + '&tabname=' + tabName;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('.pv_content_detail').html(response.content);
                    updateTitle(response.tabName, contactId);
                    // Update URL (optional, for browser navigation)
                    window.history.pushState(null, null, url);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading tab content:', error);
                }
            });
        }

    });
</script>

@endsection
