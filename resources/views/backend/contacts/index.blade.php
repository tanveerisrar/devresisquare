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
    function uploadImageToServer(file, editor) {
        let formData = new FormData();
        formData.append("file", file);

        $.ajax({
            url: "{{ route('notes.upload_image') }}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.url) {
                    editor.summernote('insertImage', data.url);
                }
            },
            error: function (err) {
                console.error("Upload failed:", err.responseText);
                alert("Image upload failed.");
            }
        });
    }

    var responseHandler = function(response) {
        location.reload();
    }

    // Global close button function
    function closeModal() {
        $('#smallModal2').modal('hide'); // Close the modal
    }

    function openImageModal(imageSrc) {
        $("#previewImage").attr("src", imageSrc); // Set image source
        $("#imagePreviewModal").modal("show"); // Show modal
    }

    // Hide modal when close button is clicked
    $("#closeModalBtn").click(function () {
        $("#imagePreviewModal").modal("hide");
    });

    // Hide modal when clicking outside modal content
    $(document).on("click", function (event) {
        if (!$(event.target).closest(".modal-content").length) {
            $("#imagePreviewModal").modal("hide");
        }
    });

    // Utility function to initialize Tagify dynamically based on data attributes
    function initDynamicTagify() {
        $('.tagify-input').each(function () {
            let $inputElement = $(this);
            let values = $inputElement.data('values') || ''; // Pre-selected values
            let options = $inputElement.data('options') || {}; // Max tags, dropdown options
            let idValue = $inputElement.data('id-value') || []; // ID-Value pairs

            let data = idValue; // Use the provided ID-Value data
            let selectedIds = [];

            if (Array.isArray(values)) {
                selectedIds = values.map(id => id.toString().trim());
            } else if (typeof values === 'string') {
                const trimmed = values.trim();

                if (trimmed.startsWith('[') || trimmed.startsWith('{')) {
                    try {
                        let parsed = JSON.parse(trimmed);
                        if (Array.isArray(parsed)) {
                            selectedIds = parsed.map(id => id.toString().trim());
                        } else {
                            selectedIds = [parsed.toString().trim()];
                        }
                    } catch (e) {
                        console.error("Error parsing data-values JSON:", e);
                    }
                } else if (trimmed.includes(',')) {
                    selectedIds = trimmed.split(',').map(id => id.trim());
                } else if (trimmed) {
                    selectedIds = [trimmed];
                }
            } else if (typeof values === 'number') {
                selectedIds = [values.toString()];
            }


            // Initialize Tagify
            let tagify = new Tagify($inputElement[0], {
                whitelist: data.map(item => item.name),
                maxTags: options.maxTags || 5,
                dropdown: {
                    enabled: options.dropdownEnabled === 1,
                    maxItems: options.maxItems || 10,
                    searchKeys: options.searchKeys || ['name'],
                    closeOnSelect: options.closeOnSelect || false,
                },
                pattern: /[\w\s]/,
            });

            // Populate Tagify with existing selected items
            let selectedNames = selectedIds.map(id => {
                let item = data.find(item => item.id == id);
                return item ? item.name : '';
            }).filter(name => name);

            tagify.addTags(selectedNames);

            // Update the hidden input field
            let $hiddenInput = $inputElement.closest('.form-group').find('.hidden-input');
            $hiddenInput.val(selectedIds.join(','));

            // Handle adding a new tag
            tagify.on('add', function (e) {
                let newTag = e.detail.data;
                let selectedItem = data.find(item => item.name === newTag.value);
                if (selectedItem) {
                    let selectedIds = tagify.value.map(tag => {
                        let item = data.find(item => item.name === tag.value);
                        return item ? item.id : null;
                    });
                    $hiddenInput.val(selectedIds.join(','));
                }
            });

            // Handle removing a tag
            tagify.on('remove', function (e) {
                let removedTag = e.detail.data;
                let selectedItem = data.find(item => item.name === removedTag.value);
                if (selectedItem) {
                    let selectedIds = tagify.value.map(tag => {
                        let item = data.find(item => item.name === tag.value);
                        return item ? item.id : null;
                    });
                    $hiddenInput.val(selectedIds.join(','));
                }
            });
        });
    }

    // Open modal and load form via AJAX
    $(document).on('click', '.editForm, .addForm', function() {
        let formType = $(this).data("form");
        let contactId = $(this).data("id");
        let noteId     = $(this).data('note-id') || '';
        let formTitles = {
            "availability_pricing": "Edit Availability & Pricing",
            "property_info": "Edit Contact Information",
            "property_features": "Edit Contact Features",
            "property_compliance": "Edit Compliance Details",
            "property_media": "Edit Media Details",
            "property_accessibility": "Edit Contact Accessibility",
            "property_services": "Edit Contact Services",
            "property_status": "Edit Contact Status",
            "notes": "Edit Important Note",
            notes_tab: noteId ? 'Edit Note' : 'Add Note',
        };
        
        let modalTitle = formTitles[formType] || "Edit Details"; // Default title if form type is not found

        $("#extraLargeModal .modal-title").html(modalTitle); // Set dynamic title

        // Remove previous modal size classes
        $("#extraLargeModal .modal-dialog").removeClass("modal-sm modal-lg modal-xl");

        // Apply the appropriate modal size based on the formType
        if (formType === "property_status" || formType === "notes" || formType === "property_services") {
            // Use small modal for "notes" or "notes_tab"
            $("#extraLargeModal .modal-dialog").addClass("modal-md");
        // } else if (formType === "property_info") {
            // Use large modal for "property_details" or "availability_pricing"
            // $("#extraLargeModal .modal-dialog").addClass("modal-lg");
        } else {
            // Default size (medium size) for other forms
            $("#extraLargeModal .modal-dialog").addClass("modal-xl");
        }

        $.ajax({
            url: "{{ route('admin.properties.loadForm') }}", // Route to get form dynamically
            type: "GET",
            data: { form_type: formType, contact_id: contactId, note_id: noteId },
            success: function (response) {
                $("#extraLargeModal .modal-body").html(response.form_html);
                $("#extraLargeModal").modal("show");

                // **Trigger the function ONLY for a specific form**
                if (formType === "property_compliance") {
                    $('.select2').select2();
                    toggleEPCRating();
                }
                if (formType === "property_media") {
                    AIZ.uploader.previewGenerate();
                }
                if (formType === "property_accessibility") {
                    initDynamicTagify();
                    // initPlaces('#places-wrapper', '#add-place-btn');
                    AIZ.extra.addMore();
                    AIZ.extra.removeParent();
                }
                if (formType === "availability_pricing") {
                    $('.select2').select2();
                }
                if (formType === "notes_tab") {
                    AIZ.plugins.textEditor();
                }
                if (formType === "property_info") {
                    toggleDescriptions();
                }
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
        let contactId = form.find('input[name="contact_id"]').val(); // Get contact ID

        $.ajax({
            url: "{{ route('admin.properties.saveForm') }}",
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

        document.addEventListener('show.bs.modal', function (event) {
            const zIndex = 1040 + (10 * document.querySelectorAll('.modal.show').length);
            const modal = event.target;

            modal.style.zIndex = zIndex;
            setTimeout(function () {
                const backdrop = document.querySelectorAll('.modal-backdrop:not(.modal-stack)');
                backdrop.forEach(function (el) {
                    el.style.zIndex = zIndex - 1;
                    el.classList.add('modal-stack');
                });
            }, 0);
        });


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

            // Show or hide the button based on the tabName
            if (tabName === 'owners') {
                // $('.tab-owners-btn').removeClass('d-none'); // Show the button for 'owner' tab
                $('.tab-owners-group-btn').removeClass('d-none'); // Show the button for 'owner' tab
            } else {
                // $('.tab-owners-btn').addClass('d-none'); // Hide the button for other tabs
                $('.tab-owners-group-btn').addClass('d-none'); // Hide the button for other tabs
            }
            if (tabName === 'offers') {
                $('.tab-offers-btn').removeClass('d-none'); // Show the button for 'owner' tab
            } else {
                $('.tab-offers-btn').addClass('d-none'); // Hide the button for other tabs
            }
            if (tabName === 'tenancy') {
                $('.tab-tenancy-group-btn').removeClass('d-none'); // Show the button for 'owner' tab
            } else {
                $('.tab-tenancy-group-btn').addClass('d-none'); // Hide the button for other tabs
            }
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
                var displayTabName = tabName.replace(/_/g, ' ');

                // Find the tab and contact card with the matching data attributes
                var selectedTab = $('.tab-link[data-tab-name="' + displayTabName + '"]');
                var selectedContactCard = $('.contact-card[data-contact-id="' + contactId + '"]');

                // Mark the selected tab and contact card as active/current
                $('.tab-link').removeClass('active');
                $('.contact-card').removeClass('current');
                selectedTab.addClass('active');
                selectedContactCard.addClass('current');

                // Load the content dynamically
                loadTabContent(contactId, displayTabName);
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
            var formattedTabName = tabName.replace(/\s+/g, '_');
            // Correctly format the URL with query parameters instead of placeholders
            var url = '{{ route('admin.contacts.index') }}' + '?contact_id=' + contactId + '&tabname=' + formattedTabName;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    // Update the content of the tab with the response
                    // You might want to populate the content into a specific div
                    // Example: $('.pv_content_detail').html(response.content);
                    $('.pv_content_detail').html(response.content);
                    updateTitle(tabName, contactId);
                    // Update URL (optional, for browser navigation)
                    window.history.pushState(null, null, url);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading tab content:', error);
                }
            });
        }

        $(document).on('change', '#pets_allow', function () {
            // Set the value to 1 if checked, otherwise set to 0
            this.value = this.checked ? 1 : 0;
        });

        // Trigger change once on page load to set initial value
        // $(function() {
        //     $('#pets_allow').trigger('change');
        // });

    });
</script>

<script>
    // Function to open the compliance modal and fetch the form
    function openComplianceModal(complianceTypeId, complianceRecordId = null) {
        var contactId = document.getElementById('hidden-contact-id').getAttribute('data-contact-id') ?? ''; // Fetch the contact_id

        let url = complianceRecordId
        ? '{{ route('admin.compliance.type.form', [':complianceTypeId', ':complianceRecordId']) }}'
              .replace(':complianceTypeId', complianceTypeId)
              .replace(':complianceRecordId', complianceRecordId)
        : '{{ route('admin.compliance.type.form', ':complianceTypeId') }}'.replace(':complianceTypeId', complianceTypeId);

        $.ajax({
            url: url,
            // url: '{{ route('admin.compliance.type.form', ':complianceTypeId') }}'.replace(':complianceTypeId', complianceTypeId),
            type: 'GET',
            success: function(response) {

                // Load the dynamic form content into the modal body
                $('#complianceModalLabel').html(response.heading);
                $('#complianceModalBody').html(response.content);

                // Find the form inside the modal and get its ID
                var formId = $('#complianceModalBody form').attr('id');
                // Set the contact_id in the hidden input field inside the modal form
                $("input[name='contact_id']").val(contactId);
                $("input[name='compliance_type_id']").val(complianceTypeId);

                AIZ.uploader.previewGenerate();

                // Set the form ID dynamically to the submit button
                $('#submitComplianceForm').attr('form', formId);

                // Show the modal
                $('#complianceModal').modal('show');
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
    $(document).on('click', '#submitComplianceForm', function (e) {
        e.preventDefault();  // Prevent the default form submission behavior
        let formId = $(this).attr('form'); // Get the form ID dynamically
        let formData = new FormData(document.getElementById(formId));

        $.ajax({
            url: formData.get('record_id')
            ? '{{ route('admin.compliance.update') }}'
            : '{{ route('admin.compliance.store') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    AIZ.plugins.notify('success', response.message);
                    $('#complianceModal').modal('hide'); // Close the modal
                    location.reload(); // Optionally reload the page to update the compliance list
                } else {
                    AIZ.plugins.notify('danger', 'Failed to save compliance record.');
                }
            },
            error: function (error) {
                console.error(error);
                let errorMessage = error.responseJSON?.message || 'An error occurred while saving the compliance record.';
                AIZ.plugins.notify('danger', errorMessage);
            }
        });
    });

    let complianceRecordIdToDelete = null;

    // Confirm Delete Record
    function confirmDelete(complianceRecordId) {
        complianceRecordIdToDelete = complianceRecordId;
        $('#deleteConfirmationModal').modal('show');
    }

    // Execute Delete Action
    $(document).on('click', '#confirmDeleteBtn', function() {
        if (complianceRecordIdToDelete) {
            $.ajax({
                url: `{{ route('admin.compliance.delete', ':complianceRecordId') }}`.replace(':complianceRecordId', complianceRecordIdToDelete),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'  // Include the CSRF token in the request
                },
                success: function (response) {
                    if (response.success) {
                        AIZ.plugins.notify('success', response.message);
                        $('#deleteConfirmationModal').modal('hide');
                        location.reload(); // Reload page to reflect the changes
                    } else {
                        AIZ.plugins.notify('danger', 'Failed to delete compliance record.');
                    }
                },
                error: function (error) {
                    console.error(error);
                    let errorMessage = error.responseJSON?.message || 'An error occurred while deleting the compliance record.';
                    AIZ.plugins.notify('danger', errorMessage);
                }
            });
        }
    });
</script>

@endsection
