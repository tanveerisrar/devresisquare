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
<script src="{{ asset('/asset/backend/js/common-notes.js') }}"></script>
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
    var responseHandler = function(response) {
        location.reload();
    }
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
<script>
$(function() {
    // Delegate the change event to document
    $(document).on('change', '#right_to_rent_check', function() {
      if (this.checked) {
        $('#rent-check-person-details').css('display', 'flex');
        $('#rent-check-message').show();
      } else {
        $('#rent-check-person-details, #rent-check-message').hide();
      }
    });

    // Trigger the handler once on load to initialize visibility
    $(document).find('#right_to_rent_check').trigger('change');
});

 // Open modal and load form via AJAX
$(document).on('click', '.editForm, .addForm', function() {
    let formType = $(this).data("form");
    let contactId = $(this).data("id");
    let noteId     = $(this).data('note-id') || '';
    let bankId     = $(this).data('bank-detail-id') || '';
    let formTitles = {
        "contact_detail": "Edit Contact Details",
        // "property_info": "Edit Property Information",
        // "property_features": "Edit Property Features",
        // "property_compliance": "Edit Compliance Details",
        // "property_media": "Edit Media Details",
        // "property_accessibility": "Edit Property Accessibility",
        // "property_services": "Edit Property Services",
        // "property_status": "Edit Property Status",
        // "notes": "Edit Important Note",
        notes_tab: noteId ? 'Edit Note' : 'Add Note',
        bank_detail: bankId ? 'Edit Bank Detail' : 'Add Bank Detail',
    };
    
    let modalTitle = formTitles[formType] || "Edit Details"; // Default title if form type is not found

    $("#extraLargeModal .modal-title").html(modalTitle); // Set dynamic title

    // Remove previous modal size classes
    $("#extraLargeModal .modal-dialog").removeClass("modal-sm modal-lg modal-xl");

    // Apply the appropriate modal size based on the formType
    // if (formType === "property_status" || formType === "notes" || formType === "property_services") {
    //     // Use small modal for "notes" or "notes_tab"
    //     $("#extraLargeModal .modal-dialog").addClass("modal-md");
    // } else {
        // Default size (medium size) for other forms
        $("#extraLargeModal .modal-dialog").addClass("modal-xl");
    // }

    $.ajax({
        url: "{{ route('admin.contacts.loadForm') }}", // Route to get form dynamically
        type: "GET",
        data: { form_type: formType, contact_id: contactId, note_id: noteId, bank_detail_id: bankId },
        success: function (response) {
            $("#extraLargeModal .modal-body").html(response.form_html);
            $("#extraLargeModal").modal("show");

            // **Trigger the function ONLY for a specific form**
            if (formType === "contact_detail") {
                $('.select2').select2();
                AIZ.extra.addMore();
                AIZ.extra.removeParent();
            }
            // if (formType === "property_media") {
            //     AIZ.uploader.previewGenerate();
            // }
            // if (formType === "property_accessibility") {
            //     initDynamicTagify();
            //     // initPlaces('#places-wrapper', '#add-place-btn');
            //     AIZ.extra.addMore();
            //     AIZ.extra.removeParent();
            // }
            // if (formType === "availability_pricing") {
            //     $('.select2').select2();
            // }
            if (formType === "notes_tab") {
                AIZ.plugins.textEditor();
            }
            // if (formType === "property_info") {
            //     toggleDescriptions();
            // }
        },
        error: function (error) {
            console.error(error);
            let errorMessage = error.responseJSON?.message || 'An error occurred while saving the compliance record.';
            AIZ.plugins.notify('danger', errorMessage);
        }
    });
});
$(document).on("submit", "#extraLargeModal form", function (e) {
    e.preventDefault(); 

    let form = $(this);
    let formData = form.serialize();
    let formType = form.find('input[name="form_type"]').val(); // Get form type dynamically
    let contactId = form.find('input[name="contact_id"]').val(); // Get property ID

    $.ajax({
        url: "{{ route('admin.contacts.saveForm') }}",
        type: "POST",
        data: formData,
        success: function (response) {
            if (response.success) {
                // Dynamically update the relevant accordion section
                $("#section-" + formType + "-" + contactId).html(response.updated_html);

                // Close the modal
                $("#extraLargeModal").modal("hide");
                AIZ.plugins.notify('success', response.message);
            } else {
                alert("Error: " + response.error);
            }
        },
        error: function (error) {
            console.error(error);
            let errorMessage = error.responseJSON?.message || 'An error occurred while saving the form.';
            AIZ.plugins.notify('danger', errorMessage);
        }
    });
});

$(function(){
  // Add new email field
  $(document).on('click', '#add-email', function(e){
    e.preventDefault();
    $('#emails-wrapper').append(`
      <div class="flex items-center mb-2">
        <input
          type="email"
          name="emails[]"
          class="form-input flex-1"
          placeholder="email@example.com"
        >
        <button type="button" class="ml-2 text-red-600 remove-email">&times;</button>
      </div>
    `);
  });

  // Remove an email field
  $(document).on('click', '.remove-email', function(e){
    e.preventDefault();
    $(this).closest('div').remove();
  });

  // Add new phone field
  $(document).on('click', '#add-phone', function(e){
    e.preventDefault();
    $('#phones-wrapper').append(`
      <div class="flex items-center mb-2">
        <input
          type="text"
          name="phones[]"
          class="form-input flex-1"
          placeholder="+44 7000 000000"
        >
        <button type="button" class="ml-2 text-red-600 remove-phone">&times;</button>
      </div>
    `);
  });

  // Remove a phone field
  $(document).on('click', '.remove-phone', function(e){
    e.preventDefault();
    $(this).closest('div').remove();
  });
});
// View Bank Details
$(document).on('click', '.viewBank', function() {
    const bankId = $(this).data('id');
    const bankUrl = $(this).data('url');
    const type   = $(this).data('type');

    $.ajax({
        url: bankUrl,
        method: 'GET',
        success: function(response) {
            $("#extraLargeModal .modal-title").text(type);
            $("#extraLargeModal .modal-body").html(response.content); // show as plain text
            $("#extraLargeModal").modal("show");
        },
        error: function() {
            alert("Failed to load bank details.");
        }
    });
});
// View Notes
$(document).on('click', '.viewNote', function() {
        const noteId = $(this).data('id');
        const noteUrl = $(this).data('url');
        const type   = $(this).data('type');

        $.ajax({
            url: noteUrl,
            method: 'GET',
            success: function(response) {
                $("#extraLargeModal .modal-title").text(type);
                $("#extraLargeModal .modal-body").html(response.content); // show as plain text
                $("#extraLargeModal").modal("show");
            },
            error: function() {
                alert("Failed to load note content.");
            }
        });
    });
</script>
@endsection
