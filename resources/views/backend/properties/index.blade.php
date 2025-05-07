@extends('backend.layout.app')

@section('content')
    <div class="row g-0 view_properties">
        <div class="col-lg-5 col-12">
            <div class="property_list_wrapper pt-lg-4 pt-2 ">
                <div class="pv_wrapper">
                    <div class="pv_header">
                        <div class="row">
                            <div class="col-3">
                                <div class="pv_title">Properties</div>
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
                                <a href="{{ route('admin.properties.quick') }}" class="btn mt-2 btn-sm btn-outline-danger">
                                    Add Property
                                </a>
                            </div>
                            {{-- <div class="pv_btn">
                                <x-backend.forms.button
                                    class=''
                                    name='Add Property'
                                    type='secondary'
                                    size='sm'
                                    isOutline={{false}}
                                    isLinkBtn={{true}}
                                    link="{{ route('admin.properties.quick') }}"
                                    onClick='onClick()'
                                />
                            </div> --}}
                        </div>

                    </div>
                    {{-- pv_header end --}}
                    <div class="pv_card_wrapper">
                        {{-- Dev Note: if select property from list add class 'current' to property card --}}
                        @foreach ($properties as $property)
                            @php
                                $addressParts = array_filter([
                                    $property['prop_name'],
                                    $property['line_1'],
                                    $property['line_2'],
                                    $property['city'],
                                    $property['country'],
                                    $property['postcode'],
                                ]);
                                $fullAddress = implode(', ', $addressParts);
                            @endphp
                                <x-backend.property-card 
                                class="property-card" 
                                propertyName="{{ $fullAddress }}" 
                                bed="{{ $property['bedroom'] }}" 
                                bath="{{ $property['bathroom'] }}" 
                                floor="{{ $property['floor'] }}" 
                                living="{{ $property['reception'] }}" 
                                type="{{ $property['property_type'] }}" 
                                available="{{ $property['available_from'] }}" 
                                price="{{ $property['price'] }}" 
                                lettingPrice="{{ $property['letting_price'] ?? '' }}" 
                                cardStyle="" 
                                propertyId="{{ $property['id'] }}" 
                                />
                        @endforeach

                    </div>
                    {{-- pv_card_wrapper end  --}}
                </div>
                {{-- pv_wrapper end  --}}
            </div>
        </div>
        <div class="col-lg-7 col-12 property_detail_wrapper hide_this pt-lg-4 pt-0">
            <div class="pv_detail_wrapper">

                <x-backend.properties-tabs :tabs="$tabs" class="poperty_tabs" />

                <div class="pv_detail_content">
                    <div class="pv_detail_header">
                        <div class="pv_main_title">{{ ucfirst($tabName) }} Detail</div>
                        <div class="pvdh_btns_wrapper d-flex gap-3">
                            {{-- <x-backend.link-button class="tab-owners-btn popup-tab-owners-create d-none" name="Add Owner"
                                link="{{ route('admin.owner-groups.create') }}" onClick="" /> --}}
                                {{-- <x-backend.forms.button
                                class="tab-owners-btn d-none"
                                name="Add Owner"
                                type="secondary"
                                size="sm"
                                isOutline={{false}}
                                isLinkBtn={{true}}
                                link="{{ route('admin.owner-groups.create') }}"
                                onclick=""
                                /> --}}
                            {{-- <x-backend.link-button class="tab-offers-btn popup-tab-offer-create d-none" name="Add Offer"
                                link="{{ route('admin.properties.quick') }}" onClick="" /> --}}
                                {{-- <x-backend.forms.button
                                    class="tab-offers-btn d-none"
                                    name="Add Offer"
                                    type="secondary"
                                    size="sm"
                                    isOutline={{false}}
                                    isLinkBtn={{true}}
                                    link="#"
                                    onclick=""
                                    /> --}}

                                    <!-- Modal Trigger Button -->
                                    <a type="button" class="tab-offers-btn btn btn-sm btn-outline-danger btn-sm d-none" data-bs-toggle="modal" data-bs-target="#addOfferModal">
                                        Add Offer
                                    </a>
                                    {{-- <a data-url="{{ route('admin.owner-groups.create') }}" class="popup-tab-owners-create btn btn_secondary btn-sm tab-owners-btn d-none">
                                        <span>Add Owner</span>
                                        <span class="icon_btn"></span>
                                    </a> --}}
                                    <a data-url="{{ route('admin.owner-groups.create_group') }}" class="popup-tab-owner-group-create btn btn-sm btn-outline-danger btn-sm tab-owners-group-btn d-none">
                                        <span>Add Owner Group</span>
                                        <span class="icon_btn"></span>
                                    </a>
                                    <a data-url="{{ route('admin.tenancies.create') }}" class="popup-tab-tenancy-create btn btn-sm btn-outline-danger tab-tenancy-group-btn d-none">
                                        <span>Add Tenancy</span>
                                        <span class="icon_btn"></span>
                                    </a>

                            {{-- @if (isset($property) && isset($propertyId)) --}}
                                {{-- <x-backend.outline-link-button class="" name="Edit Property"
                                    link="{{ route('admin.properties.edit', ['id' => $property->id]) }}" onClick="" /> --}}
                                    <x-backend.forms.button
                                    class="edit-property-btn d-none"
                                    name="Edit Property"
                                    type="secondary"
                                    size="sm"
                                    isOutline={{false}}
                                    isLinkBtn={{true}}
                                    {{-- link="{{ route('admin.properties.edit', ['id' => $propertyId]) }}" --}}
                                    link="#"
                                    onclick=""
                                    />
                            {{-- @endif --}}
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
                    <x-backend.forms.mobile_button
                        class=''
                        name='Add Tenacy'
                        link="{{ route('admin.properties.quick') }}"
                        iconName='plus-circle'
                    />
                    <x-backend.forms.mobile_button
                        class=''
                        name='Add Offer'
                        link="{{ route('admin.properties.quick') }}"
                        iconName='journal-plus'
                    />
                    @if ($property)                    
                    <x-backend.forms.mobile_button
                        class=''
                        name='Edit Property'
                        link="{{ route('admin.properties.edit', ['id' => $property->id]) }}"
                        iconName='pencil-square'
                    />
                    @endif
                </div>
            </div>
        </div>
    </div>
    <style>
        .hidden {
            display: none !important;
        }
        /* .modal-content {
            max-width: 900px;
            margin: auto;
        } */
        .modal-content {
            height: auto;
            margin: auto;
        }
        .add-tenant-btn {
            color: #ff4500;
            cursor: pointer;
            text-decoration: underline;
        }

        .modal-backdrop.modal-stack {
            opacity: 0.3 !important;
        }
    </style>
<!-- property offer add Modal -->
<div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModal-label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addOfferModal-label">Add Offer</h5>
          <a type="button" class="btn-close" onclick="closeModel();" data-bs-dismiss="modal" aria-label="Close"></a>
        </div>
        <div class="modal-body">
            <!-- Main Form -->
            <form action="{{ route('admin.offers.store') }}" method="POST" class="tenantOfferForm" id="tenantOfferForm">
                @csrf
                <input type="hidden" name="property_id" class="form-control" value="">
                <!-- Steps Container -->
                <div id="steps-container">
                    <input type="hidden" id="mainPersonId" name="mainPersonId">
                    <!-- Tenant Forms -->
                    <div id="tenant-forms" class="step"></div>

                    <!-- Offer Details Step -->
                    <div id="offer-step" class="step hidden">
                        <h6>Offer Details</h6>
                        <div class="row">
                          <div class="col-lg-6 col-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" placeholder="Enter price" required>
                                </div>
                            </div>
                          </div>
                          <div class="col-lg-6 col-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="deposit" class="form-label">Deposit</label>
                                    <input type="number" class="form-control" id="deposit" name="deposit" placeholder="Enter deposit amount" required>
                                </div>
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-6 col-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="term" class="form-label">Term</label>
                                    <input type="text" class="form-control" id="term" name="term" placeholder="Enter term" required>
                                </div>
                            </div>
                          </div>
                          <div class="col-lg-6 col-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="move_in_date" class="form-label">Move-in Date</label>
                                    <input type="date" class="form-control" id="moveInDate" name="moveInDate" required>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </form>
            <span id="addTenantButton" class="add-tenant-btn hidden" onclick="addTenant()">Add More Tenant</span>
        </div>
        <!-- Modal Footer Navigation -->
        <div class="modal-footer px-0">
            <button type="button" class="btn btn_outline_secondary btn-sm" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
            <button id="backButton" type="button" class="btn btn_secondary btn-md hidden">Back</button>
            <button id="nextButton" type="button" class="btn btn_secondary btn-md ">Next</button>
            <button id="submitButton" type="submit" form="tenantOfferForm" class="btn btn_secondary btn-md hidden">Submit</button>
        </div>
      </div>
    </div>
</div>
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
<script src="{{ asset('/asset/backend/js/property-offer.js') }}"></script>
@endpush
@section('page.scripts')
@if (isset($propertyId) && isset($property) && $propertyId != $property->id)
{{-- @php
var_dump($propertyId);
@endphp --}}
<script>
    const url = new URL(window.location.href);
    url.searchParams.set('property_id', '{{ $propertyId }}');
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

    function handleGasSafeModal() {
        // Check initially on page load
        if ($('#gas_safe_acknowledged').val() !== '1' && $('#is_gas_no').is(':checked')) {
            // If "No" is selected and gas acknowledgment is not 1, show the modal
            $('#smallModal2').modal('show');
        }

        // Event delegation for changes to the radio buttons
        $(document).on('change', 'input[name="is_gas"]', function () {
            const selected = $('input[name="is_gas"]:checked').val(); // Get the value of the selected radio

            if (selected === '1') { // Gas = Yes
                console.log('Gas = Yes selected');
                if ($('#gas_safe_acknowledged').val() !== '1') {
                    $('#smallModal2').modal('show');
                }
            } else if (selected === '0') { // Gas = No
                console.log('Gas = No selected');
                $('#gas_safe_acknowledged').val('0'); // Reset acknowledgment if "No" is selected
            }
        });

        // Confirm Acknowledgement
        $(document).on('click', '#confirm_gas', function (event) {
            event.preventDefault();
            $('#gas_safe_acknowledged').val('1'); // Set acknowledgment
            $('#smallModal2').modal('hide'); // Hide the modal
        });

        // Cancel button click
        $(document).on('click', '#cancel_gas', function (event) {
            event.preventDefault();    
            // Set the "No" radio button for "is_gas"
            $('#is_gas_no').prop('checked', true); // Select the "No" option
            // Reset the hidden input value
            $('#gas_safe_acknowledged').val('0'); // Reset the acknowledgment to 0
            $('#smallModal2').modal('hide'); // Hide the modal
        });
    }

    // Global close button function
    function closeModal() {
        $('#smallModal2').modal('hide'); // Close the modal
    }

    // Call the handler
    handleGasSafeModal();

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

        // Parse the pre-selected values
        // let selectedIds = [];
        // if (typeof values === 'string' && values.includes(',')) {
        //     selectedIds = values.split(',').map(id => id.trim());
        // } else if (typeof values === 'string' && (values.startsWith('{') || values.startsWith('['))) {
        //     try {
        //         selectedIds = JSON.parse(values).map(item => item.trim());
        //     } catch (e) {
        //         console.error("Error parsing data-values:", e);
        //         selectedIds = [];
        //     }
        // } else if (values) {
        //     selectedIds = [values.trim()];
        // }
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
        let propertyId = $(this).data("id");
        let noteId     = $(this).data('note-id') || '';
        let formTitles = {
            "availability_pricing": "Edit Availability & Pricing",
            "property_info": "Edit Property Information",
            "property_features": "Edit Property Features",
            "property_compliance": "Edit Compliance Details",
            "property_media": "Edit Media Details",
            "property_accessibility": "Edit Property Accessibility",
            "property_services": "Edit Property Services",
            "property_status": "Edit Property Status",
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
            data: { form_type: formType, property_id: propertyId, note_id: noteId },
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
        let propertyId = form.find('input[name="property_id"]').val(); // Get property ID

        $.ajax({
            url: "{{ route('admin.properties.saveForm') }}",
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    // Dynamically update the relevant accordion section
                    $("#section-" + formType + "-" + propertyId).html(response.updated_html);

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
    
    // “View” button handler
    // $(document).on('click', '.viewNote', function(){
    //     const type    = $(this).data('type');
    //     const content = $(this).data('content');

    //     $("#largeModal .modal-title").html(type);
    //     $("#largeModal .modal-body").html(content);
    //     $("#largeModal").modal("show");
    // });
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

        // Step 1: Event listener for clicks on the document for the "Add New Contact" button
        $(document).on('click', '#addContactBtn', function() {
            $('#mainForm').hide(); // Hide the main form
            $('#addContactFormContainer').show(); // Show the Add Contact form
        });

        // Step 2: Event listener for clicks on the document for the "Back" button
        $(document).on('click', '#backToMainForm', function() {
            $('#addContactFormContainer').hide(); // Hide the Add Contact form
            $('#mainForm').show(); // Show the main form
        });

        // Step 3: Handle the form submission for adding a new contact via AJAX
        $(document).on('submit', '#addContactForm', function(event) {
            event.preventDefault(); // Prevent normal form submission

            // Clear any previous error messages
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            var formData = $(this).serialize(); // Serialize the form data

            $.ajax({
                url: '{{ route("admin.contacts.quick_contact_store") }}', // Make sure this route exists for adding contacts
                method: 'POST',
                data: formData,
                success: function(response) {
                    // Assuming the response contains the new contact's ID and full name
                    if (response.success) {
                        // Add the new contact to the dropdown in the main form
                        $('#contact_id').append(
                            `<option value="${response.contact.id}">${response.contact.full_name}</option>`
                        );

                        // Optionally, select the new contact
                        // $('#contact_id').val(response.contact.id);

                        // Hide the Add Contact form and show the Main Form
                        $('#addContactFormContainer').hide();
                        $('#mainForm').show();

                        // Reset the Add Contact form
                        $('#addContactForm')[0].reset();
                    } else {
                        alert('Failed to add contact.');
                    }
                },
                error: function(xhr) {
                    // Check if the status code is 422 (validation error)
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors; // Assuming errors are structured like this
                        console.log(errors); // Log the errors object for debugging

                        // Clear previous error messages and styling
                        $('input').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        // Loop through the errors and display them in the form
                        $.each(errors, function(field, messages) {
                            // Check if the field exists in the form
                            var input = $('#contact_' + field);

                            if (input.length > 0) {  // Make sure the input field exists
                                input.addClass('is-invalid'); // Add the 'is-invalid' class to the field

                                // Check if the field already has an error message to avoid appending multiple messages
                                if (input.next('.invalid-feedback').length === 0) {
                                    input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                                }
                            } else {
                                console.log('Input field with id ' + field + ' not found!');
                            }
                        });
                    } else {
                        alert('An error occurred while adding the contact.');
                    }
                }


            });
        });
    });




    var responseHandler = function(response) {
        location.reload();
    }
    $(document).ready(function() {

        // Function to check if the device is mobile
        function is_mobile() {
            return (
                /Mobi|Android/i.test(navigator.userAgent) || $(window).width() < 768
            );
        }

        if (is_mobile()) {
            $(document).on('click', '.property-card', function() {
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

        $(document).on('click', '.popup-tab-owners-create', function(e) {
            e.preventDefault(); // Prevent the default action (e.g., following the link)

            // Get the URL from the link (you can dynamically get the URL as needed)
            var url = $(this).attr('data-url'); // Assuming you're passing the URL in the 'href' attribute
            var header = 'Add Owner'; // You can set a custom header or get it dynamically
            // Access the data-property-id using JavaScript
            var propertyId = document.getElementById('hidden-property-id').getAttribute(
                'data-property-id') ?? '';

            smallModal(url, header);
            // Ensure the modal content is loaded and then set the property_id in the hidden input field inside the modal form
            $('#smallModal').on('shown.bs.modal', function() {
                // Set the property_id in the hidden input field inside the modal form
                $("input[name='property_id']").val(propertyId);
            });
        });

        // Function to handle the AJAX form submission
        function submitOwnerGroupForm(e) {
            var form = $('#owner-group-form');
            var btn = form.find('button[type="submit"]');
            var btn_text = btn.html();

            e.preventDefault();  // Prevent default form submission

            btn.html('<i class="ri-refresh-line"></i>');
            btn.css("opacity", "0.7");
            btn.css("pointer-events", "none");

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function(response) {
                    btn.html(btn_text);
                    btn.css("opacity", "1");
                    btn.css("pointer-events", "inherit");

                    if (response.status) {
                        toastr.success(response.notification, "Success");
                        // Close the modal on success
                        $('#smallModal').modal('hide');
                        setTimeout(function() {
                            location.reload(); // Reload the page after 1 second
                        }, 1000);
                    } else {
                        // Handle validation errors
                        if (response.errors) {
                            // Loop through each error and display it
                            $.each(response.errors, function(field, messages) {
                                // For each field with errors, show them
                                toastr.error(messages.join(', '), field.charAt(0).toUpperCase() + field.slice(1));
                            });
                        }
                       // Check if the response is asking for confirmation
                        if (response.notification.includes('Do you want to archive the existing one and activate the new group?') ||
                            response.notification.includes('Are you sure you want to archive this active owner group?') ||
                            response.notification.includes('Do you want to archive it and activate this one?')) {

                            // Display confirmation dialog for archiving the existing group
                            if (confirm(response.notification)) {
                                // If user confirms, add a hidden field to the form to confirm archiving
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'confirm_archive',
                                    value: 'yes'
                                }).appendTo(form);

                                // Resubmit the form with the confirmation
                                submitOwnerGroupForm(e);
                            }
                        } else {
                            toastr.error(response.notification, "Error");
                        }
                    }
                },
                error: function() {
                    // toastr.error(response.notification, 'Error')
                    btn.html(btn_text);
                    btn.css("opacity", "1");
                    btn.css("pointer-events", "inherit");
                    toastr.error("There was an error with the form submission. Please try again.", "Error");
                }
            });
        }

        // Bind the function to the form submission using event delegation
        $(document).on('submit', '#owner-group-form', function(e) {
            submitOwnerGroupForm(e);  // Call the submit function when the form is submitted
        });

        $(document).on('click', '.popup-tab-owner-group-create', function(e) {
            e.preventDefault(); // Prevent the default action (e.g., following the link)

            // Get the URL for the modal (you can dynamically fetch it as needed)
            var url = $(this).attr('data-url'); // URL passed in the 'data-url' attribute
            var header = 'Add Owner Group'; // Custom header or dynamic header
            var propertyId = document.getElementById('hidden-property-id').getAttribute('data-property-id') ?? ''; // Fetch the property_id

            // Open the modal (assuming smallModal is a function that handles modal rendering)
            smallModal(url, header);

            // Ensure modal content is loaded and set the property_id in the hidden field inside the modal form
            $('#smallModal').on('shown.bs.modal', function() {
                // Set the property_id in the hidden input field inside the modal form
                $("input[name='property_id']").val(propertyId);
                initSelect2('.select2');

                const contactSelect = $('#contact_id');
                const contactOptionsContainer = $('#contact-options');

                // Listen for changes in the contact dropdown
                contactSelect.on('change', function () {
                    const selectedContacts = contactSelect.val() || [];
                    contactOptionsContainer.empty();

                    if (selectedContacts.length > 0) {
                        // Add default label
                        contactOptionsContainer.append(`
                            <label class="mb-2">Select Main Contact</label>
                        `);

                        // Add radio buttons for each selected contact
                        selectedContacts.forEach(contactId => {
                            const contactName = contactSelect.find(`option[value="${contactId}"]`).text(); // Get the name from the option
                            contactOptionsContainer.append(`
                                <div class="form-check">
                                    <input type="radio" name="is_main" value="${contactId}" id="is_main_${contactId}" class="form-check-input">
                                    <label for="is_main_${contactId}" class="form-check-label">${contactName}</label>
                                </div>
                            `);
                        });
                    }
                });
            });
        });

        // Trigger the modal when an element with the 'popup-tab-owner-group-edit' class is clicked
        $(document).on('click', '.popup-tab-owner-group-edit', function(e) {
            e.preventDefault(); // Prevent the default action (e.g., following the link)

            // Get the URL from the link (you can dynamically get the URL as needed)
            // var url = $(this).attr('href'); // Assuming you're passing the URL in the 'href' attribute
            var url = $(this).attr('data-url'); // URL passed in the 'data-url' attribute
            var header = 'Edit Owner Group'; // You can set a custom header or get it dynamically
            // Access the data-property-id using JavaScript
            var propertyId = document.getElementById('hidden-property-id').getAttribute('data-property-id') ?? '';

            smallModal(url, header);
            // Ensure the modal content is loaded and then set the property_id in the hidden input field inside the modal form
            $('#smallModal').on('shown.bs.modal', function() {
                // Set the property_id in the hidden input field inside the modal form
                $("input[name='property_id']").val(propertyId);
                initSelect2('.select2');

                const contactSelect2 = $('#contact_id');
                const contactOptionsContainer2 = $('#contact-options');

                // Store the previously selected main contact
                let previouslySelectedMainContact = $('input[name="is_main"]:checked').val() || null;

                // Listen for changes in the contact dropdown
                contactSelect2.on('change', function () {
                    const selectedContacts = contactSelect2.val() || [];
                    contactOptionsContainer2.empty();

                    if (selectedContacts.length > 0) {
                        // Add default label
                        contactOptionsContainer2.append(`
                            <label class="mb-2">Select Main Contact</label>
                        `);

                        // Add radio buttons for each selected contact
                        selectedContacts.forEach(contactId => {
                            const contactName = contactSelect2.find(`option[value="${contactId}"]`).text(); // Get the name from the option
                            const isChecked = previouslySelectedMainContact === contactId ? 'checked' : ''; // Preserve previously selected main contact
                            contactOptionsContainer2.append(`
                                <div class="form-check">
                                    <input type="radio" name="is_main" value="${contactId}" id="is_main_${contactId}" class="form-check-input" ${isChecked}>
                                    <label for="is_main_${contactId}" class="form-check-label">${contactName}</label>
                                </div>
                            `);
                        });

                        // Check if the previously selected main contact is no longer in the selected contacts
                        if (!selectedContacts.includes(previouslySelectedMainContact)) {
                            // Reset previously selected main contact
                            previouslySelectedMainContact = null;
                            // alert('Please reselect the main contact as the previous one is no longer selected.');
                        }
                    }
                });

                // Update the stored value when a main contact is selected
                $(document).on('change', 'input[name="is_main"]', function () {
                    previouslySelectedMainContact = $(this).val();
                });


            });
        });

        // Trigger the modal when an element with the 'popup-tab-offer-create' class is clicked
        $(document).on('click', '.popup-tab-offer-create', function(e) {
            e.preventDefault(); // Prevent the default action (e.g., following the link)

            // Get the URL from the link (you can dynamically get the URL as needed)
            var url = $(this).attr('href'); // Assuming you're passing the URL in the 'href' attribute
            var header = 'Add Offer'; // You can set a custom header or get it dynamically
            // Access the data-property-id using JavaScript
            var propertyId = document.getElementById('hidden-property-id').getAttribute(
                'data-property-id') ?? '';

            smallModal(url, header);
            // Ensure the modal content is loaded and then set the property_id in the hidden input field inside the modal form
            $('#smallModal').on('shown.bs.modal', function() {
                // Set the property_id in the hidden input field inside the modal form
                $("input[name='property_id']").val(propertyId);
            });
        });

    });

    // document.querySelectorAll('.tab-link').forEach(tab => {
    //     tab.addEventListener('click', function(event) {
    //         event.preventDefault();

    //         // Remove active class from all tabs and tab content
    //         document.querySelectorAll('.tab-link').forEach(link => link.classList.remove('active'));
    //         document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

    //         // Add active class to the clicked tab and corresponding content
    //         this.classList.add('active');
    //         document.getElementById(this.getAttribute('href').substring(1)).classList.add('active');
    //     });
    // });



        $(document).on('click', '.popup-tab-tenancy-create', function(e) {
            e.preventDefault(); // Prevent the default action (e.g., following the link)

            // Get the URL for the modal (you can dynamically fetch it as needed)
            var url = $(this).attr('data-url'); // URL passed in the 'data-url' attribute
            var header = 'Add Tenancy'; // Custom header or dynamic header
            var propertyId = document.getElementById('hidden-property-id').getAttribute('data-property-id') ?? ''; // Fetch the property_id

            // Open the modal (assuming smallModal is a function that handles modal rendering)
            smallModal(url, header);

            // Ensure modal content is loaded and set the property_id in the hidden field inside the modal form
            $('#smallModal').on('shown.bs.modal', function() {
                // Set the property_id in the hidden input field inside the modal form
                $("input[name='property_id']").val(propertyId);
                initSelect3('.select2');

                const contactSelect3 = $('#tenant_id');
                const contactOptionsContainer3 = $('#tenant-options');

                // Listen for changes in the contact dropdown
                contactSelect3.on('change', function () {
                    const selectedContacts = contactSelect3.val() || [];
                    contactOptionsContainer3.empty();

                    if (selectedContacts.length > 0) {
                        // Add default label
                        contactOptionsContainer3.append(`
                            <label class="mb-2">Select Main Contact</label>
                        `);

                        // Add radio buttons for each selected contact
                        selectedContacts.forEach(contactId => {
                            const contactName = contactSelect3.find(`option[value="${contactId}"]`).text(); // Get the name from the option
                            contactOptionsContainer3.append(`
                                <div class="form-check">
                                    <input type="radio" name="is_main_person" value="${contactId}" id="is_main_${contactId}" class="form-check-input">
                                    <label for="is_main_${contactId}" class="form-check-label">${contactName}</label>
                                </div>
                            `);
                        });
                    }
                });

            });
        });

        $(document).on('click', '.popup-tab-tenancy-edit', function(e) {
            e.preventDefault(); // Prevent the default action (e.g., following the link)

            // Get the URL for the modal (you can dynamically fetch it as needed)
            var url = $(this).attr('data-url'); // URL passed in the 'data-url' attribute
            var header = 'Edit Tenancy'; // Custom header or dynamic header
            var propertyId = document.getElementById('hidden-property-id').getAttribute('data-property-id') ?? ''; // Fetch the property_id

            // Open the modal (assuming smallModal is a function that handles modal rendering)
            smallModal(url, header);

            // Ensure modal content is loaded and set the property_id in the hidden field inside the modal form
            $('#smallModal').on('shown.bs.modal', function() {
                // Set the property_id in the hidden input field inside the modal form
                $("input[name='property_id']").val(propertyId);
                initSelect3('.select2');

                const contactSelect4 = $('#tenant_id');
                const contactOptionsContainer4 = $('#tenant-options');

                // Store the previously selected main contact
                let previouslySelectedMainTenant = $('input[name="is_main_person"]:checked').val() || null;

                // Listen for changes in the contact dropdown
                contactSelect4.on('change', function () {
                    const selectedContacts = contactSelect4.val() || [];
                    contactOptionsContainer4.empty();

                    if (selectedContacts.length > 0) {
                        // Add default label
                        contactOptionsContainer4.append(`
                            <label class="mb-2">Select Main Contact</label>
                        `);

                        // Add radio buttons for each selected contact
                        selectedContacts.forEach(contactId => {
                            const contactName = contactSelect4.find(`option[value="${contactId}"]`).text(); // Get the name from the option
                            const isChecked = previouslySelectedMainTenant === contactId ? 'checked' : ''; // Preserve previously selected main contact
                            contactOptionsContainer4.append(`
                                <div class="form-check">
                                    <input type="radio" name="is_main_person" value="${contactId}" id="is_main_${contactId}" class="form-check-input" ${isChecked}>
                                    <label for="is_main_${contactId}" class="form-check-label">${contactName}</label>
                                </div>
                            `);
                        });

                        // Check if the previously selected main contact is no longer in the selected contacts
                        if (!selectedContacts.includes(previouslySelectedMainTenant)) {
                            // Reset previously selected main contact
                            previouslySelectedMainTenant = null;
                        }
                    }
                });

                // Update the stored value when a main contact is selected
                $(document).on('change', 'input[name="is_main_person"]', function () {
                    previouslySelectedMainTenant = $(this).val();
                });


            });
        });

        $('#editTenancyForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Gather the form data
        var formData = new FormData(this); // This includes the form fields and file inputs

        // Send the AJAX request
        $.ajax({
            url: $(this).attr('action'), // Use the form's action attribute
            method: 'POST', // Form method (use 'PUT' or 'PATCH' if it's an update)
            data: formData, // Form data
            processData: false, // Prevent jQuery from automatically transforming the data
            contentType: false, // Let the browser set the content type
            success: function(response) {
                // Handle success response
                if (response.success) {
                    // Display success message
                    flashMessage('Tenancy updated successfully!', 'success');
                    // Optionally redirect or update the UI (e.g., close modal, refresh data)
                    location.reload(); // Reload the page (if necessary)
                } else {
                    // Handle errors if any (validation errors, etc.)
                    flashMessage(response.message || 'An error occurred, please try again.', 'error');
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX error (e.g., network issue, server issue)
                flashMessage('An error occurred while submitting the form. Please try again.', 'error');
            }
        });
    });


    // Function to show a flash message (You can customize this to use your preferred alert system)
    function flashMessage(message, type) {
        var flashMessage = $('<div>', {
            class: 'flash-message ' + type,
            text: message
        }).appendTo('body').fadeIn().delay(3000).fadeOut();
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



    $(document).ready(function() {

        // Function to get URL parameters
        function getUrlParameter(name) {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        // Check if URL parameters are present (property_id and tabname)
        function hasUrlParams() {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.has('property_id') && urlParams.has('tabname');
        }

        // Function to update the title dynamically
        function updateTitle(tabName, propertyId = null) {
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
                // Update the "Edit Property" button dynamically if property ID exists
            // if (propertyId) {
            //     var editButtonLink = '{{ route('admin.properties.edit', ['id' => ':id']) }}'.replace(':id', propertyId);
            //     $('.pvdh_btns_wrapper .edit-property-btn').removeClass('d-none').attr('href', editButtonLink);
            //         // console.log(editButtonLink);
            // } else {
            //     $('.pvdh_btns_wrapper .edit-property-btn').addClass('d-none'); // Hide the button if no property ID
            // }

        }

        // Handle Tab Clicks
        // Event listener for property cards (left side)
        $(document).on('click', '.property-card', function() {
            var propertyId = $(this).data('property-id');
            $('.property-card').removeClass('current');
            $(this).addClass('current');
            var tabName = $('.tab-link.active').data('tab-name');
            loadTabContent(propertyId, tabName);
        });

        // Event listener for tabs (right side)
        $(document).on('click', '.tab-link', function(e) {
            e.preventDefault();
            var tabName = $(this).data('tab-name');
            var propertyId = $('.property-card.current').data('property-id');
            $('.tab-link').removeClass('active');
            $(this).addClass('active');
            loadTabContent(propertyId, tabName); // Load content dynamically
        });

        // Check if URL parameters are present (property_id and tabname)
        function hasUrlParams() {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.has('property_id') && urlParams.has('tabname');
        }
        // Call the appropriate function based on URL parameters or default
        if (hasUrlParams()) {
            activateTabFromUrl(); // Handle tabs based on URL parameters
        } else {
            simulateTabClickAndPropertyCard(); // Default behavior
        }


        // Function to activate tab based on URL parameter
        function activateTabFromUrl() {
            var tabName = getUrlParameter('tabname'); // Get tabname from URL
            var propertyId = getUrlParameter('property_id'); // Get property_id from URL

            if (tabName && propertyId) {
                // Find the tab and property card with the matching data attributes
                var selectedTab = $('.tab-link[data-tab-name="' + tabName + '"]');
                var selectedPropertyCard = $('.property-card[data-property-id="' + propertyId + '"]');

                // Mark the selected tab and property card as active/current
                $('.tab-link').removeClass('active');
                $('.property-card').removeClass('current');
                selectedTab.addClass('active');
                selectedPropertyCard.addClass('current');

                // Load the content dynamically
                loadTabContent(propertyId, tabName);
            }
        }

        // Simulate the first tab and first property card selection on page load
        function simulateTabClickAndPropertyCard() {
            var firstPropertyCard = $('.property-card').first(); // Get the first property card
            var firstTab = $('.tab-link').first(); // Get the first tab

            // Get the propertyId and tabName from the first property card and tab
            var propertyId = firstPropertyCard.data('property-id');
            var tabName = firstTab.data('tab-name');
            console.log(propertyId);
            console.log(tabName);

            // Trigger the AJAX load
            if (propertyId && tabName) {
                loadTabContent(propertyId, tabName);
                firstPropertyCard.addClass('current'); // Add 'current' class to the first property card
                firstTab.addClass('active'); // Add 'active' class to the first tab
            }
        }

        // Call the simulateTabClickAndPropertyCard function on document ready only if URL parameters are NOT present
        // if (!hasUrlParams()) {
        //     simulateTabClickAndPropertyCard();
        // }

        // Function to load tab content dynamically via AJAX
        function loadTabContent(propertyId, tabName) {
            // Correctly format the URL with query parameters instead of placeholders
            var url = '{{ route('admin.properties.index') }}' + '?property_id=' + propertyId + '&tabname=' + tabName;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Update the content of the tab with the response
                    // You might want to populate the content into a specific div
                    // Example: $('.pv_content_detail').html(response.content);
                    $('.pv_content_detail').html(response.content);
                    updateTitle(tabName, propertyId);
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
        var propertyId = document.getElementById('hidden-property-id').getAttribute('data-property-id') ?? ''; // Fetch the property_id

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
                // Set the property_id in the hidden input field inside the modal form
                $("input[name='property_id']").val(propertyId);
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
