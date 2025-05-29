<html>

<head>
    <title>Temp Page</title>
    <link rel="stylesheet" href="{{ asset('asset/backend/css/temp.css') }}">
</head>

<body>
    <div class="main_wrapper">
        <div id="wrapper" class="main_content">
            <div class="view_properties">
                <div class="property_list_wrapper pt-lg-4 pt-2 ">
                    <div class="pv_wrapper">
                        <div class="pv_header">
                            <div class="pv_title">Properties</div>
                            <div class="rs_search ">
                                <input type="text" value="" placeholder="Search">
                                <i class="bi bi-search pointer" onclick="onClick()"></i>
                            </div>
                            <div class="pv_btn">
                                <a href="http://127.0.0.1:8000/admin/properties/quick-create"
                                    class="btn mt-2 btn-sm btn-outline-danger">
                                    Add Property
                                </a>
                            </div>
                        </div>

                        <div class="pv_card_wrapper">
                            <div class="pv_content_wrapper property-card current" data-property-id="5">
                                <div class="pv_image">
                                    <img src="http://127.0.0.1:8000/asset/images/temp-property.webp" alt="property">
                                </div>

                                <div class="pv_content">
                                    <div class="pvc_poperty_name">
                                        property 5, 73-79 Balham High Road, London, SW12 9AP, london, United Kingdom,
                                        SW12 9AP
                                    </div>

                                    <div class="rs_property_icons">
                                        <div class="bed_icon rs_tooltip" data-label="bedroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bed.svg"
                                                alt="bedroom">
                                            3
                                        </div>

                                        <div class="bath_icon rs_tooltip" data-label="bathroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bath.svg"
                                                alt="bathroom">
                                            6+
                                        </div>

                                        <div class="floors_icon rs_tooltip" data-label="Floors">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/floor.svg"
                                                alt="Floors">
                                            basement
                                        </div>

                                        <div class="living_icon rs_tooltip" data-label="Sofa">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/sofa.svg" alt="sofa">
                                            6+
                                        </div>
                                    </div>

                                    <div class="rs_row">
                                        <div class="rs_col">
                                            <div class="pv_type">
                                                Type:
                                                <strong>
                                                    sales
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="rs_col">
                                            <div class="pv_availability">
                                                Availability:
                                                <strong>
                                                    Not specified
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pvc_price">
                                        Price:
                                        <span>
                                            N/A
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="pv_content_wrapper  property-card" data-property-id="4">
                                <div class="pv_image">
                                    <img src="http://127.0.0.1:8000/asset/images/temp-property.webp" alt="property">
                                </div>

                                <div class="pv_content">
                                    <div class="pvc_poperty_name">
                                        property 4, 50 Baker Street, Address Line 2, London, United Kingdom, W1U 8AN
                                    </div>

                                    <div class="rs_property_icons">
                                        <div class="bed_icon rs_tooltip" data-label="bedroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bed.svg"
                                                alt="bedroom">
                                            N/A
                                        </div>

                                        <div class="bath_icon rs_tooltip" data-label="bathroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bath.svg"
                                                alt="bathroom">
                                            N/A
                                        </div>

                                        <div class="floors_icon rs_tooltip" data-label="Floors">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/floor.svg"
                                                alt="Floors">
                                            N/A
                                        </div>

                                        <div class="living_icon rs_tooltip" data-label="Sofa">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/sofa.svg" alt="sofa">
                                            N/A
                                        </div>
                                    </div>

                                    <div class="rs_row">
                                        <div class="rs_col">
                                            <div class="pv_type">
                                                Type:
                                                <strong>
                                                    lettings
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="rs_col">
                                            <div class="pv_availability">
                                                Availability:
                                                <strong>
                                                    Not specified
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pvc_price">
                                        Letting Price:
                                        <span>
                                            N/A
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="pv_content_wrapper  property-card" data-property-id="3">
                                <div class="pv_image">
                                    <img src="http://127.0.0.1:8000/asset/images/temp-property.webp" alt="property">
                                </div>

                                <div class="pv_content">
                                    <div class="pvc_poperty_name">
                                        123 King&amp;#039;s Road, Chelsea, London, United Kingdom, SW3 4NX
                                    </div>

                                    <div class="rs_property_icons">
                                        <div class="bed_icon rs_tooltip" data-label="bedroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bed.svg"
                                                alt="bedroom">
                                            N/A
                                        </div>

                                        <div class="bath_icon rs_tooltip" data-label="bathroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bath.svg"
                                                alt="bathroom">
                                            N/A
                                        </div>

                                        <div class="floors_icon rs_tooltip" data-label="Floors">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/floor.svg"
                                                alt="Floors">
                                            N/A
                                        </div>

                                        <div class="living_icon rs_tooltip" data-label="Sofa">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/sofa.svg" alt="sofa">
                                            N/A
                                        </div>
                                    </div>

                                    <div class="rs_row">
                                        <div class="rs_col">
                                            <div class="pv_type">
                                                Type:
                                                <strong>
                                                    sales
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="rs_col">
                                            <div class="pv_availability">
                                                Availability:
                                                <strong>
                                                    Not specified
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pvc_price">
                                        Price:
                                        <span>
                                            N/A
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="pv_content_wrapper  property-card" data-property-id="2">
                                <div class="pv_image">
                                    <img src="http://127.0.0.1:8000/asset/images/temp-property.webp" alt="property">
                                </div>

                                <div class="pv_content">
                                    <div class="pvc_poperty_name">
                                        dwsdsa, 22 Queen Victoria Street, Address Line 2, London, United Kingdom, EC4N
                                        4TE
                                    </div>

                                    <div class="rs_property_icons">
                                        <div class="bed_icon rs_tooltip" data-label="bedroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bed.svg"
                                                alt="bedroom">
                                            5
                                        </div>

                                        <div class="bath_icon rs_tooltip" data-label="bathroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bath.svg"
                                                alt="bathroom">
                                            4
                                        </div>

                                        <div class="floors_icon rs_tooltip" data-label="Floors">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/floor.svg"
                                                alt="Floors">
                                            furnished
                                        </div>

                                        <div class="living_icon rs_tooltip" data-label="Sofa">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/sofa.svg" alt="sofa">
                                            2
                                        </div>
                                    </div>

                                    <div class="rs_row">
                                        <div class="rs_col">
                                            <div class="pv_type">
                                                Type:
                                                <strong>
                                                    lettings
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="rs_col">
                                            <div class="pv_availability">
                                                Availability:
                                                <strong>
                                                    2024-11-10
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pvc_price">
                                        Letting Price:
                                        <span>
                                            £500.00
                                            <br>
                                            <small>Weekly: £115.38</small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="pv_content_wrapper  property-card" data-property-id="1">
                                <div class="pv_image">
                                    <img src="http://127.0.0.1:8000/asset/images/temp-property.webp" alt="property">
                                </div>

                                <div class="pv_content">
                                    <div class="pvc_poperty_name">
                                        property 1, 12 Park Lane, Mayfair, London, United Kingdom, W1K 1QA
                                    </div>

                                    <div class="rs_property_icons">
                                        <div class="bed_icon rs_tooltip" data-label="bedroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bed.svg"
                                                alt="bedroom">
                                            5
                                        </div>

                                        <div class="bath_icon rs_tooltip" data-label="bathroom">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/bath.svg"
                                                alt="bathroom">
                                            3
                                        </div>

                                        <div class="floors_icon rs_tooltip" data-label="Floors">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/floor.svg"
                                                alt="Floors">
                                            basement
                                        </div>

                                        <div class="living_icon rs_tooltip" data-label="Sofa">
                                            <img src="http://127.0.0.1:8000/asset/images/svg/icons/sofa.svg" alt="sofa">
                                            6+
                                        </div>
                                    </div>

                                    <div class="rs_row">
                                        <div class="rs_col">
                                            <div class="pv_type">
                                                Type:
                                                <strong>
                                                    both
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="rs_col">
                                            <div class="pv_availability">
                                                Availability:
                                                <strong>
                                                    2024-11-13
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pvc_price">
                                        Price:
                                        <span>
                                            £100.00
                                        </span>
                                    </div>
                                    <div class="pvc_price">
                                        Letting Price:
                                        <span>
                                            £500.00
                                            <br>
                                            <small>Weekly: £115.38</small>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="property_detail_wrapper hide_this pt-lg-4 pt-0">
                    <div class="pv_detail_wrapper">
                        <div class="pv_detail_content">
                            <div class="pv_detail_header">
                                <div class="pv_main_title">Property Detail</div>
                                <div class="pvdh_btns_wrapper d-flex gap-3">

                                    <!-- Modal Trigger Button -->
                                    <a type="button" class="tab-offers-btn btn btn-sm btn-outline-danger btn-sm d-none"
                                        data-bs-toggle="modal" data-bs-target="#addOfferModal">
                                        Add Offer
                                    </a>

                                    <a data-url="http://127.0.0.1:8000/admin/owner-groups/create-group"
                                        class="popup-tab-owner-group-create btn btn-sm btn-outline-danger btn-sm tab-owners-group-btn d-none">
                                        <span>Add Owner Group</span>
                                        <span class="icon_btn"></span>
                                    </a>
                                    <a data-url="http://127.0.0.1:8000/admin/tenancies/create"
                                        class="popup-tab-tenancy-create btn btn-sm btn-outline-danger tab-tenancy-group-btn d-none">
                                        <span>Add Tenancy</span>
                                        <span class="icon_btn"></span>
                                    </a>



                                    <a href="#" class="btn btn_secondary btn-sm edit-property-btn d-none" onclick="">
                                        <span>Edit Property</span>
                                        <span class="icon_btn"></span>
                                    </a>

                                </div>
                            </div>
                            <div class="pv_content_detail_wrapper">
                                <i class="bi bi-chevron-left" id="backBtn"></i>
                                <div class="pv_content_detail">
                                    <div class="flex flex_row gap_16">


                                        <div class="pv_content w-100">

                                            <div class="pvc_property_name_wrapper">
                                                <div>
                                                    <div class="pvc_ref_id"> <strong> Property Ref: RESISQP0000005
                                                        </strong>
                                                    </div>
                                                    <div class="pvc_poperty_name">73-79 Balham High Road, London, SW12
                                                        9AP,
                                                        london, United Kingdom, SW12 9AP</div>
                                                </div>
                                                <!-- Delete Button -->
                                                <button type="button" class="float-end btn btn-sm btn-outline-danger"
                                                    onclick="confirmModal('http://127.0.0.1:8000/admin/properties/delete/5', responseHandler)">
                                                    <i class="mdi mdi-delete" title="Delete"></i>
                                                    Delete
                                                </button>
                                            </div>




                                        </div>

                                    </div>

                                    <div class="property_note">
                                        <span class="fw-semibold">Important Note
                                            <div class="notes-update-ajax" id="section-notes-5">
                                                <!-- Display View Mode -->
                                                <div class="description-toggle">
                                                    <span class="short-text" id="desc_6838078882016_short"></span>
                                                    <span class="full-text d-none" id="desc_6838078882016_full"></span>

                                                </div>
                                            </div>
                                        </span>
                                        <button class="btn btn-outline-danger btn-sm editForm" data-form="notes"
                                            data-id="5">
                                            Edit
                                        </button>
                                    </div>
                                    <div class="property_note">
                                        <span class="fw-semibold">
                                            <div class="property_status-update-ajax" id="section-property_status-5">
                                                <!-- Display View Mode -->

                                                <div class="accordion_inner_heading mb-2">Status </div>

                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <div class="left_item">Sales Status:</div>
                                                        <div class="right_item">
                                                            <span class="badge bg-secondary"></span>
                                                        </div>
                                                    </div>
                                                </div>








                                            </div>
                                        </span>
                                        <button class="btn btn-outline-danger btn-sm editForm"
                                            data-form="property_status" data-id="5">
                                            Edit
                                        </button>
                                    </div>

                                    <div class="pvd_content_wrapper">
                                        <!-- Button to Collapse/Expand All -->
                                        <div class="d-flex justify-content-end mb-3">
                                            <a id="toggleAll" class="pointer underline">Collapse All</a>
                                        </div>

                                        <div class="accordion" id="propertyAccordion">


                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading-availability_pricing">
                                                    <button class="accordion-button " type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-availability_pricing"
                                                        aria-expanded="true"
                                                        aria-controls="collapse-availability_pricing">
                                                        Availability &amp; Pricing
                                                    </button>
                                                </h2>
                                                <div id="collapse-availability_pricing"
                                                    class="accordion-collapse collapse show"
                                                    aria-labelledby="heading-availability_pricing">

                                                    <button class="btn btn_outline_secondary mt-2 float-end editForm"
                                                        data-form="availability_pricing" data-id="5">
                                                        Edit
                                                    </button>
                                                    <div class="accordion-body" id="section-availability_pricing-5">
                                                        <!-- Marketing Details -->
                                                        <div class="accordion_inner">
                                                            <p class="accordion_inner_heading">Marketing Details</p>
                                                            <div class="row mb-2">
                                                                <div class="col mb-2"><span class="left_item">Move-in
                                                                        Date :
                                                                    </span>
                                                                    <span class="right_item"></span>
                                                                </div>

                                                                <div class="row mb-2">
                                                                    <div class="col"><span class="left_item">Length of
                                                                            Lease
                                                                            : </span>
                                                                        <span class="right_item"></span>
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-2">
                                                                    <div class="col"><span class="left_item">Local
                                                                            Authority
                                                                            : </span>
                                                                        <span class="right_item">N/A</span>
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-2">
                                                                    <div class="col"><span class="left_item">Tenure :
                                                                        </span>
                                                                        <span class="right_item">
                                                                            N/A
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <!-- Price Section -->
                                                                <div class="mt-md-4 mt-3">
                                                                    <p class="accordion_inner_heading">Price</p>

                                                                    <div class="row mb-2">
                                                                        <div class="col-4">
                                                                            <span class="left_item">Estate Charges :
                                                                            </span>
                                                                            <span class="right_item"> </span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <span class="left_item">Miscellaneous Charge
                                                                                (annual) : </span>
                                                                            <span class="right_item"> </span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-2">
                                                                        <div class="col-4">
                                                                            <span class="left_item">Ground Rent :
                                                                            </span>
                                                                            <span class="right_item"> </span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <span class="left_item">Service Charge
                                                                                (annual)
                                                                                :</span>
                                                                            <span class="right_item"> </span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mb-2">
                                                                        <div class="col-4">
                                                                            <span class="left_item">Sales Price :
                                                                            </span>
                                                                            <span class="right_item"> </span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <span class="left_item">Letting Price :
                                                                            </span>
                                                                            <span class="right_item">£ 500.00</span>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                <!-- Council Tax Section -->
                                                                <div class="mt-md-4 mt-3">
                                                                    <h5 class="accordion_inner_heading">Council Tax</h5>
                                                                    <div class="row mb-2">
                                                                        <div class="col">
                                                                            <span class="left_item">Annual Council Tax :
                                                                            </span>
                                                                            <span class="right_item"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-2">
                                                                        <div class="col">
                                                                            <span class="left_item">Council Tax Band
                                                                                :</span>
                                                                            <span class="right_item"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <style>
                                                                .select2-container,
                                                                .select2-container--bootstrap-5 .select2-selection,
                                                                .select2-container--default .select2-selection {
                                                                    width: 100% !important;
                                                                }

                                                                .select2-container--bootstrap-5 .select2-selection {
                                                                    height: calc(1.5em + .75rem + 2px);
                                                                    padding: .375rem .75rem;
                                                                    font-size: 1rem;
                                                                    line-height: 1.5;
                                                                }

                                                                .select2-results__group {
                                                                    font-weight: 600;
                                                                }
                                                            </style>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-property_info">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse-property_info"
                                                            aria-expanded="false"
                                                            aria-controls="collapse-property_info">
                                                            Property Information
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-property_info"
                                                        class="accordion-collapse collapse "
                                                        aria-labelledby="heading-property_info">

                                                        <button
                                                            class="btn btn_outline_secondary mt-2 float-end editForm"
                                                            data-form="property_info" data-id="5">
                                                            Edit
                                                        </button>
                                                        <div class="accordion-body" id="section-property_info-5">
                                                            <div class="accordion_inner">
                                                                <!-- Display View Mode -->
                                                                <div class="accordion_property_info_item">
                                                                    <span class="left_item">Frunishing Type:</span>
                                                                    <span class="right_item capitalize"> </span>
                                                                </div>
                                                                <div class="accordion_property_info_item">
                                                                    <span class="left_item">Property Type:</span>
                                                                    <span class="right_item capitalize"> sales </span>
                                                                </div>
                                                                <div class="accordion_property_info_item">
                                                                    <span class="left_item">Transaction Type:</span>
                                                                    <span class="right_item capitalize"> residential
                                                                    </span>
                                                                </div>
                                                                <div class="accordion_property_info_item">
                                                                    <span class="left_item">Specific Property
                                                                        Type:</span>
                                                                    <span class="right_item capitalize">appartment
                                                                    </span>
                                                                </div>
                                                                <div class="accordion_property_info_item">
                                                                    <span class="left_item">Sales Status
                                                                        Description:</span>
                                                                    <span class="right_item capitalize">
                                                                        <div class="description-toggle">
                                                                            <span class="short-text"
                                                                                id="desc_68380788883ba_short"></span>
                                                                            <span class="full-text d-none"
                                                                                id="desc_68380788883ba_full"></span>

                                                                        </div>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-property_features">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse-property_features"
                                                            aria-expanded="false"
                                                            aria-controls="collapse-property_features">
                                                            Property Features
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-property_features"
                                                        class="accordion-collapse collapse "
                                                        aria-labelledby="heading-property_features">

                                                        <button
                                                            class="btn btn_outline_secondary mt-2 float-end editForm"
                                                            data-form="property_features" data-id="5">
                                                            Edit
                                                        </button>
                                                        <div class="accordion-body" id="section-property_features-5">
                                                            <!-- Display View Mode -->

                                                            <div class="accordion_inner">
                                                                <div class="accordion_features_wrapper">
                                                                    <div class="accordion_features_item">
                                                                        <div class="accordion_features_label">
                                                                            <div class="accordion_inner_heading">
                                                                                Furniture
                                                                            </div>
                                                                        </div>
                                                                        <div class="accordion_features_content">
                                                                            N/A
                                                                        </div>
                                                                    </div>
                                                                    <div class="accordion_features_item">
                                                                        <div class="accordion_features_label">
                                                                            <div class="accordion_inner_heading">Kitchen
                                                                            </div>
                                                                        </div>
                                                                        <div class="accordion_features_content">
                                                                            N/A
                                                                        </div>
                                                                    </div>

                                                                    <div class="accordion_features_item">
                                                                        <div class="accordion_features_label">
                                                                            <div class="accordion_inner_heading">Safety
                                                                            </div>
                                                                        </div>
                                                                        N/A
                                                                    </div>
                                                                    <div class="accordion_features_item">
                                                                        <div class="accordion_features_label">
                                                                            <div class="accordion_inner_heading">Other
                                                                            </div>
                                                                            <div class="accordion_features_content">
                                                                                N/A
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Display View Mode -->
                                                                    <div class=" ">
                                                                        <div class="accordion_inner_heading mb-2">Rooms
                                                                        </div>
                                                                        <div class="accordion_features_rooms">
                                                                            <div class="accordion_features_item rooms">
                                                                                <span class="gray-950 fw-400"> 3 </span>
                                                                                <sapn class="gray-500"> Bedrooms </sapn>
                                                                            </div>
                                                                            <div class="accordion_features_item rooms">
                                                                                <span class="gray-950 fw-400">6+ </span>
                                                                                <sapn class="gray-500"> Bathrooms
                                                                                </sapn>
                                                                            </div>
                                                                            <div class="accordion_features_item rooms">
                                                                                <span class="gray-950 fw-400"> 6+</span>
                                                                                <sapn class="gray-500"> Reception Rooms
                                                                                </sapn>
                                                                            </div>
                                                                            <div class="accordion_features_item rooms">
                                                                                <span
                                                                                    class="gray-950 fw-400 capitalize">basement</span>
                                                                                <sapn class="gray-500"> Floor </sapn>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                    <div class=" ">
                                                                        <div class="accordion_inner_heading mb-2">
                                                                            Balcony
                                                                        </div>
                                                                        <div class="accordion_features_item balcony">
                                                                            <div><span class="gray-500">Balcony:</span>
                                                                                <span class="gray-950 fw-400">Yes</span>
                                                                            </div>
                                                                            <div><span class="gray-500">Garden:
                                                                                </span><span class="gray-950 fw-400">
                                                                                    Yes</span>
                                                                            </div>
                                                                            <div><span
                                                                                    class="gray-500">Aspects:</span><span
                                                                                    class="gray-950 fw-400">
                                                                                    north-east</span></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class=" ">
                                                                        <div class="accordion_inner_heading mb-2">Area
                                                                        </div>
                                                                        <div class="accordion_features_item area">
                                                                            <div>
                                                                                <span class="gray-500">Square Feet:
                                                                                </span>
                                                                                <span class="gray-950 fw-400">
                                                                                    450.00 sqft
                                                                                </span>
                                                                            </div>

                                                                            <div>
                                                                                <span class="gray-500">Square
                                                                                    Meter:</span>
                                                                                <span class="gray-950 fw-400">
                                                                                    41.81 sqm
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-property_services">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse-property_services"
                                                            aria-expanded="false"
                                                            aria-controls="collapse-property_services">
                                                            Service
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-property_services"
                                                        class="accordion-collapse collapse "
                                                        aria-labelledby="heading-property_services">

                                                        <button
                                                            class="btn btn_outline_secondary mt-2 float-end editForm"
                                                            data-form="property_services" data-id="5">
                                                            Edit
                                                        </button>
                                                        <div class="accordion-body" id="section-property_services-5">
                                                            <!-- Display View Mode -->
                                                            <div class="accordion_inner">
                                                                <div class="accordion_services_item">
                                                                    <div>
                                                                        <span class="left_item">Parking:</span>
                                                                        Yes
                                                                    </div>
                                                                    <div>
                                                                        <span class="left_item">Parking Location:</span>
                                                                        park1
                                                                    </div>
                                                                    <div>
                                                                        <span class="left_item">Service:</span> fully
                                                                        manged
                                                                    </div>
                                                                    <div>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>


                                            </div>


                                            <div class="pv_content mobile_only">
                                                <div class="rs_property_icons">
                                                    <div class="bed_icon rs_tooltip" data-label="Bedroom">
                                                        <img src=" http://127.0.0.1:8000/asset/images/svg/icons/bed.svg "
                                                            alt="bedroom"> 3
                                                    </div>
                                                    <div class="bath_icon rs_tooltip" data-label="Bathroom">
                                                        <img src=" http://127.0.0.1:8000/asset/images/svg/icons/bath.svg "
                                                            alt="bathroom"> 6+
                                                    </div>
                                                    <div class="floors_icon rs_tooltip" data-label="Floors">
                                                        <img src=" http://127.0.0.1:8000/asset/images/svg/icons/floor.svg "
                                                            alt="Floors">basement
                                                    </div>
                                                    <div class="living_icon rs_tooltip" data-label="Sofa">
                                                        <img src=" http://127.0.0.1:8000/asset/images/svg/icons/sofa.svg "
                                                            alt="sofa"> 6+
                                                    </div>
                                                </div>
                                                <div class="pvc_ref_id">Ref: 1234SSSD</div>
                                                <div class="pvc_poperty_name">73-79 Balham High Road, London, SW12 9AP,
                                                    london, United Kingdom, SW12 9AP</div>
                                                <div class="pvc_price">
                                                    Price: <span>£3000</span>
                                                </div>
                                                <div class="rs_row">
                                                    <div class="rs_col">
                                                        <div class="pv_type">Type: <strong> Apparment</strong></div>
                                                    </div>
                                                    <div class="rs_col">
                                                        <div class="pv_availability">Availability:
                                                            <strong>11/02/25</strong>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="rs_row">
                                                    <div class="rs_col">
                                                        <div class="pv_status">Status: <strong> For Sale</strong></div>
                                                    </div>
                                                    <div class="rs_col">
                                                        <div class="pv_service">Service: <strong>Let Only</strong></div>
                                                    </div>
                                                </div>


                                            </div>







                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mobile_footer mobile_only">
                            <div class="pvdh_btns_wrapper">
                                <a href="http://127.0.0.1:8000/admin/properties/quick-create"
                                    class="mobile_icon_btn | ">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Add Tenacy</span>
                                </a>
                                <a href="http://127.0.0.1:8000/admin/properties/quick-create"
                                    class="mobile_icon_btn | ">
                                    <i class="bi bi-journal-plus"></i>
                                    <span>Add Offer</span>
                                </a>

                                <a href="http://127.0.0.1:8000/admin/properties/edit/1" class="mobile_icon_btn | ">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Edit Property</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>