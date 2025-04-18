<!-- Button to Collapse/Expand All -->
<div class="d-flex justify-content-end gap-3 mb-3">
    <a href="{{ route('admin.property_repairs.edit', $repairIssue->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a class="btn btn-primary"
        href="{{ route('admin.repair.workorder.invoice', $repairIssue->id) }}">{{ $repairIssue->workOrder ? 'Edit Work Order & Invoice' : 'Create Work Order & Invoice' }}</a>
    <button id="toggleAll" class="btn btn-primary">Collapse All</button>
</div>

<div class="accordion" id="propertyAccordion">

    @php
        $formSections = [
            ['key' => 'property_details', 'title' => 'Property Details', 'order' => 1],
            ['key' => 'property_issue_details', 'title' => 'Property Issue Details', 'order' => 2],
            ['key' => 'manager_assign', 'title' => 'Manager Assignments', 'order' => 3],
            ['key' => 'contractor_assign', 'title' => 'Contractor Assignments', 'order' => 4],
            ['key' => 'final_contractor', 'title' => 'Final Contractor', 'order' => 5],
            ['key' => 'repair_history', 'title' => 'Repair History', 'order' => 6],
            ['key' => 'work_order_detail', 'title' => 'Work Order Detail', 'order' => 7],
            ['key' => 'invoice_detail', 'title' => 'Invoice Detail', 'order' => 8],
            // Add more sections with order values as needed
        ];

        // Sort by 'order' key
        usort($formSections, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });
    @endphp

    @foreach($formSections as $section)
        @php
            $formType = $section['key'];
            $title = $section['title'];
        @endphp

        <div class="accordion-item">
            <h2 class="accordion-header" id="heading-{{ $formType }}">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse-{{ $formType }}" aria-expanded="true"
                    aria-controls="collapse-{{ $formType }}">
                    {{ $title }}
                </button>
            </h2>
            <div id="collapse-{{ $formType }}" class="accordion-collapse collapse show"
                aria-labelledby="heading-{{ $formType }}">
                <button class="btn btn-primary float-end editForm" data-form="{{ $formType }}"
                    data-id="{{ $repairIssue->id }}">
                    Edit
                </button>
                <div class="accordion-body" id="section-{{ $formType }}-{{ $repairIssue->id }}">
                    @include("backend.repair.popup_forms.$formType", ['repairIssue' => $repairIssue])
                </div>
            </div>
        </div>
    @endforeach

    <!-- Features -->
    {{-- <div class="accordion-item">
        <h2 class="accordion-header" id="headingFeatures">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFeatures"
                aria-expanded="true" aria-controls="collapseFeatures">
                Features
            </button>
        </h2>
        <div id="collapseFeatures" class="accordion-collapse collapse show" aria-labelledby="headingFeatures">
            <div class="accordion-body">
                <ul>
                    @foreach($allFeatures as $feature)
                    <li>{{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div> --}}

</div>

@push('extra.scripts')
    <script>
        ; (function ($) {
            // Define your form namespace
            var WorkOrderForm = {
                // Store saved assignments if you need them elsewhere
                savedContractorAssignments: {!! json_encode($contractorAssignments->toArray()) !!},

                // 1) Job Sub‑types
                loadJobSubTypes: function (jobTypeId, selectedSubTypeId) {
                    var $subSelect = $("#jobSubTypeSelect");
                    if (!jobTypeId) {
                        return $subSelect.html('<option disabled value="">Select Job Sub Type</option>');
                    }

                    var url = "{{ route('admin.job_types.getSubCategories', ':id') }}"
                        .replace(':id', jobTypeId);

                    $subSelect.html('<option value="">Loading...</option>');

                    return $.get(url)
                        .done(function (response) {
                            $subSelect
                                .empty()
                                .append('<option disabled value="">Select Job Sub Type</option>');
                            response.forEach(function (st) {
                                var sel = (st.id == selectedSubTypeId) ? ' selected' : '';
                                $subSelect.append(
                                    `<option value="${st.id}"${sel}>${st.name}</option>`
                                );
                            });
                        })
                        .fail(function () {
                            $subSelect.html('<option disabled value="">No Sub Types Found</option>');
                        });
                },

                bindJobTypeSelect: function () {
                    var self = this;
                    $(document).off("change.jobSubType").on("change.jobSubType", "#jobTypeSelect", function () {
                        self.loadJobSubTypes($(this).val());
                    });
                },

                initJobSubType: function () {
                    // on page‑load edit scenario
                    var jt = $("#jobTypeSelect").val();
                    var jst = "{{ $repairIssue->workOrder->job_sub_type_id ?? '' }}";
                    if (jt) this.loadJobSubTypes(jt, jst);
                },


                // 2) Generate Invoice
                generateInvoice: function () {
                    var workOrderId = $("#work_order_id").val();
                    if (!workOrderId) {
                        return alert("No Work Order found!");
                    }
                    var url = "{{ route('admin.invoices.generate', ['workOrderId' => 'id']) }}"
                        .replace('id', workOrderId);

                    return $.ajax({
                        url: url,
                        type: "POST",
                        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
                    })
                        .done(function (res) {
                            alert(res.message);
                            $("#workOrderForm :input").prop("disabled", true);
                            $("#generateInvoiceBtn")
                                .text("Invoice Generated")
                                .prop("disabled", true);
                        })
                        .fail(function (xhr) {
                            alert(xhr.responseJSON.message || "An error occurred");
                        });
                },

                bindGenerateInvoice: function () {
                    var self = this;
                    $(document).off("click.genInv").on("click.genInv", "#generateInvoiceBtn", function () {
                        self.generateInvoice();
                    });
                },


                // 3) Invoice‑To Details & Status
                loadInvoiceToDetails: function (invoiceTo, propertyId) {
                    var self = this,
                        endpoint, cat;

                    $("#contactDetails").hide();
                    var existingId = $("#existingInvoiceToId").val();

                    if (invoiceTo === "Landlord") {
                        cat = 4;
                        endpoint = "{{ route('admin.getContactsByProperty', ['propertyId' => 'PP', 'categoryId' => 'CC']) }}"
                            .replace('PP', propertyId)
                            .replace('CC', cat);
                    }
                    else if (invoiceTo === "Tenant") {
                        endpoint = "{{ route('admin.getTenantsByProperty', ['propertyId' => 'PP']) }}"
                            .replace('PP', propertyId);
                    }
                    else {
                        $("#invoiceToContainer").empty();
                        return;
                    }

                    $("#invoiceToContainer").html('<select class="form-control"><option>Loading...</option></select>');

                    $.get(endpoint)
                        .done(function (list) {
                            var html = '<div class="form-group">'
                                + '<label class="form-label">Select ' + invoiceTo + '</label>'
                                + '<select id="invoiceToSelect" name="invoice_to_id" class="form-control">'
                                + '<option value="">Select ' + invoiceTo + '</option>';

                            list.forEach(function (item) {
                                var sel = (item.id == existingId) ? ' selected' : '';
                                html += `<option value="${item.id}"${sel}`
                                    + ` data-email="${item.email}" `
                                    + ` data-phone="${item.phone}" `
                                    + ` data-name="${item.full_name}" `
                                    + ` data-address="${item.full_address || ''}">`
                                    + `${item.full_name}`
                                    + `</option>`;
                            });
                            html += '</select></div>';
                            $("#invoiceToContainer").html(html);

                            // trigger change if pre‑selected
                            if (existingId) $("#invoiceToSelect").trigger("change");
                        })
                        .fail(function () {
                            $("#invoiceToContainer").html('<p class="text-danger">Unable to load details</p>');
                        });
                },

                updateStatusOptions: function (invoiceTo) {
                    var opts = [], sel = $("#existingStatus").val(), $s = $("#statusSelect").empty();

                    if (invoiceTo === "Company") {
                        opts = [
                            { v: "Raised", t: "Raised" },
                            { v: "Sent to Contractor", t: "Sent to Contractor" },
                            { v: "Completed", t: "Completed" },
                            { v: "Cancelled", t: "Cancelled" }
                        ];
                    } else {
                        opts = [
                            { v: "Raised", t: "Raised" },
                            { v: "Sent to Contractor", t: "Sent to Contractor" },
                            { v: "Work Completed - Invoice Received From Contractor", t: "Work Completed - Invoice Received From Contractor" },
                            { v: "Work Completed - Invoice Generated to Landlord", t: "Work Completed - Invoice Generated to Landlord (If landlord paying)" },
                            { v: "Work Completed - Invoice Generated to Tenant", t: "Work Completed - Invoice Generated to Tenant (If tenant paying)" },
                            { v: "Completed - Invoice Generated", t: "Completed - Invoice Generated (to Landlord-Tenant)" },
                            { v: "Work Completed - Invoice Paid To Contractor", t: "Work Completed - Invoice Paid To Contractor" },
                            { v: "Cancelled", t: "Cancelled" }
                        ];
                    }

                    opts.forEach(function (o) {
                        $s.append(new Option(o.t, o.v));
                    });
                    if (sel) $s.val(sel);
                },

                bindInvoiceToRadios: function () {
                    var self = this;
                    // on radio change
                    $(document).off("change.invTo").on("change.invTo", "input[name='invoice_to']", function () {
                        var inv = $(this).val(),
                            prop = $("#property_id").val();
                        self.loadInvoiceToDetails(inv, prop);
                        self.updateStatusOptions(inv);
                    });
                    // on select contact change
                    $(document).off("change.contactSel").on("change.contactSel", "#invoiceToSelect", function () {
                        var d = $(this).find(":selected").data();
                        if (d.address) {
                            $("#contactAddress").text(d.address);
                            $("#contactPhone").text(d.phone);
                            $("#contactEmail").text(d.email);
                            $("#contactDetails").show();
                        } else {
                            $("#contactDetails").hide();
                        }
                    });
                },


                // Call this after you insert/re-insert the form via AJAX
                init: function () {
                    this.bindJobTypeSelect();
                    this.initJobSubType();
                    this.bindGenerateInvoice();
                    this.bindInvoiceToRadios();

                    // If you need to pre‑load Landlord/Tenant on page load:
                    var sel = $("input[name='invoice_to']:checked").val(),
                        pid = $("#property_id").val();
                    if (sel) {
                        this.loadInvoiceToDetails(sel, pid);
                        this.updateStatusOptions(sel);
                    }
                }
            };

            // Initialize on first page load
            $(function () { WorkOrderForm.init(); });

            // Expose globally so you can re‑init after AJAX
            window.WorkOrderForm = WorkOrderForm;

        })(jQuery);
    </script>

    <script>
        ; (function ($) {
            var RepairForm = {
                // State
                allCategories: {},
                currentCategoryLevel: 1,
                selectedCategories: {},
                breadcrumbItems: [],
                initialSelectedCategories: [],
                initialLastCategory: '',
                initialTenantId: '',
                initialPropertyIds: [],

                // 1) — Categories
                loadAllCategories: function () {
                    return $.get("{{ route('admin.get.repair.categories') }}")
                        .done(data => this.allCategories = data)
                        .fail(() => console.error("Could not load categories"));
                },
                updateBreadcrumbUI: function () {
                    var html = this.breadcrumbItems.map((it, i) =>
                        i < this.breadcrumbItems.length - 1
                            ? `<li class="breadcrumb-item clickable" data-index="${i}">${it}</li>`
                            : `<li class="breadcrumb-item active" aria-current="page">${it}</li>`
                    ).join('');
                    $('ol.breadcrumb').html(html);
                },
                updateHiddenCategories: function () {
                    $('#selected_categories').val(JSON.stringify(this.selectedCategories));
                    var lvlCount = Object.keys(this.selectedCategories).length;
                    $('#last_selected_category')
                        .val(lvlCount === this.currentCategoryLevel
                            ? this.selectedCategories['level_' + lvlCount] : '');
                },
                nextCategoryLevel: function () {
                    var lvl = this.currentCategoryLevel,
                        $lvlDiv = $(`.category-level[data-level="${lvl}"]`),
                        sel = $lvlDiv.find('input:checked');
                    if (!sel.length) return;
                    var val = sel.val(),
                        name = sel.closest('label').text().trim();

                    this.selectedCategories['level_' + lvl] = val;
                    this.breadcrumbItems = this.breadcrumbItems.slice(0, lvl).concat(name);
                    this.updateBreadcrumbUI();

                    var kids = this.allCategories[val] || [];
                    if (!kids.length) {
                        this.updateHiddenCategories();
                        return;
                    }

                    // render next-level radios
                    var next = lvl + 1,
                        $nextDiv = $(`.category-level[data-level="${next}"]`);
                    var html = kids.map(c =>
                        `<div class="col-md-4 mb-2">
                   <div class="form-check">
                     <input class="form-check-input" type="radio" name="category_${next}"
                            id="cat-${c.id}" value="${c.id}">
                     <label class="form-check-label" for="cat-${c.id}">${c.name}</label>
                   </div>
                 </div>`
                    ).join('');
                    $lvlDiv.hide();
                    $nextDiv.find('.row').html(html).show();
                    this.currentCategoryLevel = next;
                    this.updateHiddenCategories();
                },
                prevCategoryLevel: function () {
                    if (this.currentCategoryLevel > 1) {
                        $(`.category-level[data-level="${this.currentCategoryLevel}"]`)
                            .hide().find('.row').empty();
                        delete this.selectedCategories['level_' + this.currentCategoryLevel];
                        this.currentCategoryLevel--;
                        $(`.category-level[data-level="${this.currentCategoryLevel}"]`)
                            .show().find('input').prop('checked', false);
                        this.breadcrumbItems.pop();
                        this.updateBreadcrumbUI();
                        this.updateHiddenCategories();
                    } else {
                        // cancel editing
                        $('#category-edit-card').addClass('d-none');
                        $('#category-display-card').removeClass('d-none');
                    }
                },
                bindCategoryEvents: function () {
                    var self = this;
                    $(document)
                        .off("click.catNext").on("click.catNext", "#category-next-btn", () => self.nextCategoryLevel())
                        .off("click.catPrev").on("click.catPrev", "#category-prev-btn", () => self.prevCategoryLevel())
                        .off("change.catRadio").on("change.catRadio", ".category-level input[type=radio]", function () {
                            self.updateHiddenCategories();
                        })
                        .off("click.cancelCat").on("click.cancelCat", "#cancel-category-btn", e => {
                            e.preventDefault(); self.restoreOriginalCategory();
                        })
                        .off("click.changeCat").on("click.changeCat", "#change-category-btn", () => {
                            $('#category-display-card').addClass('d-none');
                            $('#category-edit-card').removeClass('d-none');
                            $('#cancel-category-btn, #change-category-btn').toggleClass('d-none');
                        });
                },
                restoreOriginalCategory: function () {
                    var orig = JSON.parse($('#selected_categories_old').val() || '{}');
                    this.selectedCategories = orig;
                    this.currentCategoryLevel = Math.max(
                        1,
                        ...Object.keys(orig).map(k => parseInt(k.replace('level_', '')))
                    );
                    $.each(orig, (k, v) =>
                        $(`.category-level[data-level="${k.split('_')[1]}"] input[value="${v}"]`)
                            .prop('checked', true)
                    );
                    this.updateHiddenCategories();
                    $('#category-edit-card').addClass('d-none');
                    $('#category-display-card').removeClass('d-none');
                },
                initCategories: function () {
                    this.initialSelectedCategories = JSON.parse($('#selected_categories').val() || '[]');
                    this.initialLastCategory = $('#last_selected_category').val() || '';
                    this.breadcrumbItems = ['Select Category'];
                    this.updateBreadcrumbUI();
                    return this.loadAllCategories();
                },


                // 2) — Property & Tenant selection
                bindPropertyEvents: function () {
                    var self = this;
                    // Change property
                    $(document).off("click.changeProp").on("click.changeProp", "#change_property_button", function (e) {
                        e.preventDefault();
                        window.previousTenantId = $('#tenant-select').val();
                        $('#dynamic_property_table').addClass('d-none');
                        $('#search_property_section, #cancel_property_change').removeClass('d-none');
                        $('#change_property_button').addClass('d-none');
                        $('#selected_properties').val('[]');
                    });
                    // Cancel change
                    $(document).off("click.cancelProp").on("click.cancelProp", "#cancel_property_change", function (e) {
                        e.preventDefault();
                        $('#selected_properties').val(JSON.stringify(self.initialPropertyIds));
                        $('#search_property_section').hide();
                        $('#dynamic_property_table').removeClass('d-none');
                        self.searchPropertiesByIds(self.initialPropertyIds);
                        self.fetchTenants(self.initialPropertyIds[0], function () {
                            $('#tenant-select').val(window.previousTenantId).trigger('change');
                        });
                        $(this).addClass('d-none');
                        $('#change_property_button').removeClass('d-none');
                    });
                    // Property search input
                    $(document).off("keyup.propSearch").on("keyup.propSearch", "#search_property1", function () {
                        var q = $(this).val().trim();
                        if (q.length < 3) {
                            $('#property_results').empty();
                            $('#error_message').text('Please enter at least 3 characters').show();
                        } else {
                            $('#error_message').hide();
                            self.searchProperties(q);
                        }
                    });
                    // Select from results
                    $(document).off("click.propResult").on("click.propResult", ".property-result", function () {
                        var id = $(this).data('id'),
                            txt = $(this).text(),
                            type = $(this).data('type'),
                            avail = $(this).data('availability');
                        $('#dynamic_property_table tbody').empty().append(
                            `<tr data-id="${id}">
                     <td>${txt}</td><td>${type}</td><td>${avail}</td>
                     <td><button class="btn btn-danger remove-btn">Remove</button></td>
                   </tr>`
                        ).removeClass('d-none');
                        self.updateSelectedProperties();
                        $('#property_results, #search_property1').empty();
                        self.fetchTenants(id, () => $('#tenant-preview').empty());
                    });
                    // Remove row
                    $(document).off("click.removeProp").on("click.removeProp", ".remove-btn", function () {
                        $(this).closest('tr').remove();
                        self.updateSelectedProperties();
                        if (!$('#dynamic_property_table tbody tr').length) {
                            $('#dynamic_property_table').addClass('d-none');
                        }
                    });
                },
                searchPropertiesByIds: function (ids) {
                    return $.get("{{ route('admin.contacts.properties.search') }}", { ids })
                        .done(resp => {
                            var $tb = $('#dynamic_property_table tbody').empty();
                            resp.forEach(p => {
                                if (!$tb.find(`tr[data-id="${p.id}"]`).length) {
                                    $tb.append(
                                        `<tr data-id="${p.id}">
                           <td>${p.address || 'N/A'} - ${p.prop_ref_no || 'N/A'} - ${p.prop_name || 'N/A'}</td>
                           <td>${p.type}</td><td>${p.availability}</td>
                         </tr>`
                                    );
                                }
                            });
                            this.updateSelectedProperties();
                            $('#dynamic_property_table').removeClass('d-none');
                        })
                        .fail(() => toastr.error('Error fetching properties'));
                },
                searchProperties: function (q) {
                    return $.get("{{ route('properties.search') }}", { query: q })
                        .done(resp => {
                            var $out = $('#property_results').empty();
                            if (!resp.length) return $out.append('<li class="list-group-item">No properties found.</li>');
                            resp.forEach(p => this.appendPropertyToResults(p));
                        })
                        .fail(() => $('#property_results').append('<li class="list-group-item">Error fetching results.</li>'));
                },
                appendPropertyToResults: function (p) {
                    var $out = $('#property_results'),
                        li = `<li class="list-group-item property-result"
                           data-id="${p.id}" data-type="${p.type}"
                           data-availability="${p.availability}">
                          ${p.address || 'N/A'} - ${p.prop_ref_no || 'N/A'} - ${p.prop_name || 'N/A'}
                        </li>`;
                    if (!$out.find(`li[data-id="${p.id}"]`).length) $out.append(li);
                },
                updateSelectedProperties: function () {
                    var ids = [];
                    $('#dynamic_property_table tbody tr').each(function () {
                        ids.push($(this).data('id'));
                    });
                    $('#selected_properties').val(JSON.stringify(ids));
                },
                fetchTenants: function (propId, cb) {
                    return $.get("{{ route('admin.get.property_repairs.tenants') }}", { property_id: propId })
                        .done(data => {
                            var opts = '<option value="">-- Select Tenant --</option>';
                            data.forEach(t => {
                                opts += `<option value="${t.id}"
                               data-email="${t.email}" data-phone="${t.phone}"
                               data-address="${t.address}">${t.full_name}</option>`;
                            });
                            $('#tenant-select').html(opts);
                            if (cb) cb();
                        });
                },
                bindTenantPreview: function () {
                    $('#tenant-select').on('change', function () {
                        var o = $(this).find(':selected');
                        if (!o.val()) return $('#tenant-preview').empty();
                        $('#tenant-preview').html(`
                  <p><strong>Name:</strong> ${o.text()}</p>
                  <p><strong>Email:</strong> ${o.data('email') || 'N/A'}</p>
                  <p><strong>Phone:</strong> ${o.data('phone') || 'N/A'}</p>
                `);
                    });
                },
                initProperties: function () {
                    this.initialPropertyIds = JSON.parse($('#selected_properties').val() || '[]');
                    this.initialTenantId = $('#selected_tenant').val() || '';
                    if (this.initialPropertyIds.length) {
                        $('#change_property_button, #dynamic_property_table').removeClass('d-none');
                        this.searchPropertiesByIds(this.initialPropertyIds);
                    }
                    this.fetchTenants(this.initialPropertyIds[0], () => {
                        if (this.initialTenantId)
                            $('#tenant-select').val(this.initialTenantId).trigger('change');
                    });
                },


                // 3) — Contractor Assignments & VAT (unchanged; keep your existing code or encapsulate similarly)


                // Kickoff
                init: function () {
                    // categories
                    this.initCategories().then(() => {
                        this.bindCategoryEvents();
                    });

                    // properties & tenants
                    this.bindPropertyEvents();
                    this.bindTenantPreview();
                    this.initProperties();

                    // here you can also call your existing contractor‑assignment & VAT init code...
                }
            };

            // auto‑init on DOM ready
            $(function () { RepairForm.init(); });
            // expose for AJAX re‑init
            window.RepairForm = RepairForm;
        })(jQuery);

    (function($) {
    var PropertySelector = {
        state: {
        initialSelectedProperties: [],
        initialPropertyId: null,
        initialTenantId: null
        },

        init: function() {
        this._readInitial();
        this._toggleInitialUI();
        this._bindEvents();
        this._initSelectedProperties();
        this._initTenants();
        },

        _readInitial: function() {
        var selProps = JSON.parse($('#selected_properties').val() || '[]');
        this.state.initialSelectedProperties = Array.isArray(selProps) ? selProps : [];
        this.state.initialPropertyId = this.state.initialSelectedProperties[0] || null;
        this.state.initialTenantId = $('#selected_tenant').val() || null;
        },

        _toggleInitialUI: function() {
        if (this.state.initialSelectedProperties.length > 0) {
            $('#change_property_button, #dynamic_property_table').removeClass('d-none');
        }
        },

        _bindEvents: function() {
        var self = this;

        $(document).on('click.propertyChange', '#change_property_button', function(e) {
            e.preventDefault();
            window.previousTenantId = $('#tenant-select').val();
            $('#dynamic_property_table').addClass('d-none');
            $('#search_property_section, #cancel_property_change').show();
            $(this).addClass('d-none');
            $('#search_property1').val('');
            $('#selected_properties').val('[]');
        });

        $(document).on('click.cancelProperty', '#cancel_property_change', function(e) {
            e.preventDefault();
            $('#selected_properties').val(JSON.stringify(self.state.initialSelectedProperties));
            $('#search_property_section').hide();
            $('#dynamic_property_table').removeClass('d-none');
            self.searchPropertiesByIds(self.state.initialSelectedProperties);
            self.fetchTenants(self.state.initialPropertyId, function() {
            if (window.previousTenantId) {
                $('#tenant-select').val(window.previousTenantId).trigger('change');
            }
            });
            $(this).addClass('d-none');
            $('#change_property_button').removeClass('d-none');
        });
        
        // Search input with debounce
        $(document).off("input.propertySearch").on('input.propertySearch', '#search_property1', debounce(function() {
            var q = $(this).val().trim();
            if (q.length >= 3) {
            $('#error_message').hide();
            self.searchProperties(q);
            } else {
            $('#property_results').empty();
            $('#error_message').text('Please enter at least 3 characters to search.').show();
            }
        }, 1000));

        $(document).on('click.propertyResult', '.property-result', function() {
            var id = $(this).data('id');
            var text = $(this).text();
            var type = $(this).data('type');
            var avail = $(this).data('availability');

            $('#property_results').empty();
            $('#search_property1').val('');

            $('#dynamic_property_table tbody').empty().append(
            `<tr data-id="${id}">` +
            `<td>${text}</td><td>${type}</td><td>${avail}</td>` +
            `<td><button type="button" class="btn btn-danger remove-btn">Remove</button></td>` +
            `</tr>`
            );
            $('#dynamic_property_table').removeClass('d-none');
            self.updateSelectedProperties();
            self.fetchTenants(id, function() {
            $('#tenant-preview').empty();
            });
        });

        $(document).on('click.removeProperty', '.remove-btn', function() {
            $(this).closest('tr').remove();
            self.updateSelectedProperties();
            if ($('#dynamic_property_table tbody tr').length === 0) {
            $('#dynamic_property_table').addClass('d-none');
            if (typeof disableNextButton === 'function') disableNextButton();
            }
        });

        $(document).on('change.tenantSelect', '#tenant-select', function() {
            var o = $(this).find('option:selected');
            if (!o.val()) {
            $('#tenant-preview').empty();
            return;
            }
            $('#tenant-preview').html(
            `<p><strong>Name:</strong> ${o.text()}</p>` +
            `<p><strong>Email:</strong> ${o.data('email') || 'N/A'}</p>` +
            `<p><strong>Phone:</strong> ${o.data('phone') || 'N/A'}</p>`
            );
        });
        },

        _initSelectedProperties: function() {
        if (this.state.initialSelectedProperties.length) {
            this.searchPropertiesByIds(this.state.initialSelectedProperties);
        }
        },

        searchPropertiesByIds: function(ids) {
        var self = this;
        $.get('{{ route("admin.contacts.properties.search") }}', { ids: ids })
            .done(function(resp) {
            var $tb = $('#dynamic_property_table tbody').empty();
            resp.forEach(function(p) {
                if (!$tb.find(`tr[data-id="${p.id}"]`).length) {
                $tb.append(
                    `<tr data-id="${p.id}">` +
                    `<td>${p.address || 'N/A'} - ${p.prop_ref_no || 'N/A'} - ${p.prop_name || 'N/A'}</td>` +
                    `<td>${p.type}</td><td>${p.availability}</td>` +
                    `</tr>`
                );
                }
            });
            self.updateSelectedProperties();
            $('#dynamic_property_table').removeClass('d-none');
            })
            .fail(function() {
            toastr.error('Error fetching properties by IDs.', 'Error');
            });
        },

        searchProperties: function(query) {
        var self = this;
        $.get('{{ route("properties.search") }}', { query: query })
            .done(function(resp) {
            var $out = $('#property_results').empty();
            if (!resp.length) {
                return $out.append('<li class="list-group-item">No properties found.</li>');
            }
            resp.forEach(function(p) {
                self._appendPropertyToResults(p);
            });
            })
            .fail(function() {
            $('#property_results').append('<li class="list-group-item">Error fetching results.</li>');
            });
        },

        _appendPropertyToResults: function(p) {
        var html = `<li class="list-group-item property-result" ` +
                    `data-id="${p.id}" data-type="${p.type}" data-availability="${p.availability}">` +
                    `${p.address || 'N/A'} - ${p.prop_ref_no || 'N/A'} - ${p.prop_name || 'N/A'}` +
                    `</li>`;
        if (!$('#property_results li[data-id="' + p.id + '"]').length) {
            $('#property_results').append(html);
        }
        },

        updateSelectedProperties: function() {
        var ids = [];
        $('#dynamic_property_table tbody tr').each(function() {
            ids.push($(this).data('id'));
        });
        $('#selected_properties').val(JSON.stringify(ids));
        },

        fetchTenants: function(propertyId, cb) {
        $.get('{{ route("admin.get.property_repairs.tenants") }}', { property_id: propertyId })
            .done(function(data) {
            var opts = '<option value="">-- Select Tenant --</option>';
            data.forEach(function(t) {
                opts += `<option value="${t.id}" ` +
                        `data-email="${t.email}" data-phone="${t.phone}" data-address="${t.address}">` +
                        `${t.full_name}</option>`;
            });
            $('#tenant-select').html(opts);
            if (typeof cb === 'function') cb();
            });
        },

        _initTenants: function() {
        if (this.state.initialPropertyId) {
            this.fetchTenants(this.state.initialPropertyId, function() {
            if (PropertySelector.state.initialTenantId) {
                $('#tenant-select').val(PropertySelector.state.initialTenantId).trigger('change');
            }
            });
        }
        }
    };

    $(function() {
        PropertySelector.init();
        window.PropertySelector = PropertySelector;
    });
    
    // Define only once
    function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
    }

})(jQuery);


        (function($) {
            const RepairFormHelpers = {
                // 1) Initialize Select2
                initSelect2: function() {
                initSelect2('.select2');
                },

                // 2) Form validation setup
                initValidation: function() {
                const rules = {
                    description: { required: true },
                    priority: { required: true },
                    status: { required: true },
                    estimated_price: { required: true, number: true },
                    vat_type: { required: true },
                    tenant_availability: { required: true },
                    access_details: { required: true },
                    tenant_id: { required: true },
                    "property_managers[]": { required: true }
                };

                this.validator = $('#repair-form-page').validate({
                    rules,
                    errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.form-group'));
                    },
                    highlight: function(el, errClass, validClass) {
                    const $el = $(el);
                    if ($el.is('select') && !$el.hasClass('select2-hidden-accessible')) {
                        $el.addClass('border-danger');
                    } else if ($el.is('select') && $el.hasClass('select2-hidden-accessible')) {
                        $el.next('.select2-container').find('.select2-selection').addClass('border-danger');
                    } else {
                        $el.closest('.form-group').find('select, input, textarea').addClass('border-danger');
                    }
                    $el.addClass(errClass).removeClass(validClass);
                    },
                    unhighlight: function(el, errClass, validClass) {
                    const $el = $(el);
                    if ($el.is('select') && !$el.hasClass('select2-hidden-accessible')) {
                        $el.removeClass('border-danger');
                    } else if ($el.is('select') && $el.hasClass('select2-hidden-accessible')) {
                        $el.next('.select2-container').find('.select2-selection').removeClass('border-danger');
                    } else {
                        $el.closest('.form-group').find('select, input, textarea').removeClass('border-danger');
                    }
                    $el.removeClass(errClass).addClass(validClass);
                    },
                    invalidHandler: function(event, validator) {
                    if (validator.numberOfInvalids()) {
                        AIZ.plugins.notify('error', 'Please fill out all required fields.');
                    }
                    },
                    submitHandler: function(form) {
                    form.submit();
                    }
                });
                },

                // 3) Contractor Assignments
                contractorTemplate: `
                <div class="contractor-assignment mb-3 border p-3">
                    <button type="button" class="btn btn-danger btn-sm remove-contractor-assignment" style="float: right;">Remove</button>
                    <input type="hidden" name="contractor_assignments[__index__][id]" value="">
                    <div class="form-group">
                    <label>Contractor</label>
                    <select name="contractor_assignments[__index__][contractor_id]" class="form-control contractor-id">@foreach($contractors as $contractor)<option value="{{ $contractor->id }}">{{ $contractor->full_name }} ({{ $contractor->email }})</option>@endforeach</select>
                    </div>
                    <div class="form-group">
                    <label for="cost_price___index__">Cost Price</label>
                    <input type="number" step="0.01" name="contractor_assignments[__index__][cost_price]" id="cost_price___index__" class="form-control cost-price">
                    </div>
                    <div class="form-group rs_upload_btn">
                    <h5 class="sub_title mt-4">Upload Quote Price Document</h5>
                    <div class="media_wrapper">
                        <div class="input-group" data-toggle="aizuploader" data-type="document" data-multiple="false">
                        <label for="quote_attachment___index__">Select Document</label>
                        <div class="d-none input-group-prepend"><div class="input-group-text bg-soft-secondary font-weight-medium">Browse</div></div>
                        <div class="d-none form-control file-amount">Choose File</div>
                        <input type="hidden" name="contractor_assignments[__index__][quote_attachment]" id="quote_attachment___index__" class="selected-files quote_attachment">
                        </div>
                        <div class="d-flex gap-3 file-preview box sm"></div>
                    </div>
                    </div>
                    <div class="form-group">
                    <label for="contractor_preferred_availability___index__">Preferred Availability (Contractor)</label>
                    <input type="datetime-local" name="contractor_assignments[__index__][contractor_preferred_availability]" id="contractor_preferred_availability___index__" class="form-control contractor-availability">
                    </div>
                </div>
                `,

                updateAssignmentIndexes: function() {
                $('#contractor-assignments .contractor-assignment').each((i, el) => {
                    const $el = $(el);
                    $el.find('.assignment-index').remove();
                    $el.prepend(`<div class="assignment-index badge bg-secondary mb-2">#${i+1}</div>`);
                });
                const count = $('#contractor-assignments .contractor-assignment').length;
                $('#contractor-assignments .remove-contractor-assignment').toggle(count > 1);
                },

                attachRules: function($block) {
                const v = this.validator;
                if (!v) return;
                $block.find('.contractor-id').rules('add', { required: true, messages: { required: 'Please select a contractor' } });
                $block.find('.cost-price').rules('add', { required: true, number: true, messages: { required: 'Please enter a cost price', number: 'Enter a valid number' } });
                $block.find('.contractor-availability').rules('add', { required: true, messages: { required: 'Please select preferred availability' } });
                },

                addAssignment: function(idx) {
                const html = this.contractorTemplate.replace(/__index__/g, idx);
                const $block = $(html);
                $('#contractor-assignments').append($block);
                this.updateAssignmentIndexes();
                this.attachRules($block);
                },

                addSavedAssignment: function(idx, data) {
                const html = this.contractorTemplate.replace(/__index__/g, idx);
                const $block = $(html);
                $block.find(`[name="contractor_assignments[${idx}][id]"]`).val(data.id);
                $block.find('.contractor-id').val(data.contractor_id);
                $block.find('.cost-price').val(data.cost_price);
                $block.find('.contractor-availability').val(data.contractor_preferred_availability);
                if (data.quote_attachment) $block.find('.quote_attachment').val(data.quote_attachment);
                $('#contractor-assignments').append($block);
                this.updateAssignmentIndexes();
                this.attachRules($block);
                },

                bindAssignmentEvents: function() {
                const self = this;
                $('#add-contractor-assignment').off('click').on('click', () => {
                    const idx = $('#contractor-assignments .contractor-assignment').length;
                    self.addAssignment(idx);
                });
                $('#contractor-assignments').off('click', '.remove-contractor-assignment').on('click', '.remove-contractor-assignment', function() {
                    $(this).closest('.contractor-assignment').remove();
                    self.updateAssignmentIndexes();
                });
                },

                initAssignments: function() {
                this.bindAssignmentEvents();
                if (window.savedContractorAssignments?.length) {
                    window.savedContractorAssignments.forEach((as, i) => this.addSavedAssignment(i, as));
                    AIZ.uploader.previewGenerate();
                } else {
                    this.addAssignment(0);
                }
                },

                // 4) VAT calculation
                initVAT: function() {
                const vatType = '{{ old('vat_type', $repairIssue->vat_type) }}';
                const vatPct = '{{ old('vat_percentage', $repairIssue->vat_percentage) }}';

                function calculateVAT() {
                    const price = parseFloat($('#estimated_price').val())||0;
                    const pct   = parseFloat($('#vat_percentage').val())||0;
                    if (price>0 && pct>0) {
                    const amt = price*(pct/100), total = price+amt;
                    $('#vat_calculation').html(`VAT Amount: $${amt.toFixed(2)}<br>Total Price (including VAT): $${total.toFixed(2)}`);
                    } else {
                    $('#vat_calculation').empty();
                    }
                }

                if (vatType==='exclusive') {
                    $('#vat_type_exclusive').prop('checked',true);
                    $('#exclusive_vat_fields').removeClass('d-none');
                    if (vatPct>0) {
                    $('#vat_percentage').val(vatPct);
                    $('#vat_calculation_preview').removeClass('d-none');
                    calculateVAT();
                    }
                    $('#vat_percentage').prop('required',true);
                } else {
                    $('#vat_type_inclusive').prop('checked',true);
                    $('#exclusive_vat_fields, #vat_calculation_preview').addClass('d-none');
                    $('#vat_percentage').prop('required',false).val('');
                }

                $('input[name="vat_type"]').off('change').on('change', function() {
                    if ($('#vat_type_exclusive').is(':checked')) {
                    $('#exclusive_vat_fields').removeClass('d-none');
                    $('#vat_percentage').prop('required',true);
                    if ($('#vat_percentage').val()>0) $('#vat_calculation_preview').removeClass('d-none');
                    calculateVAT();
                    } else {
                    $('#exclusive_vat_fields, #vat_calculation_preview').addClass('d-none');
                    $('#vat_percentage').prop('required',false).val('');
                    }
                });

                $('#vat_percentage').off('input').on('input', function() {
                    if ($(this).val()>0) $('#vat_calculation_preview').removeClass('d-none');
                    else $('#vat_calculation_preview').addClass('d-none');
                    calculateVAT();
                });
                },

                // Master init
                init: function() {
                this.initSelect2();
                this.initValidation();
                this.initAssignments();
                this.initVAT();
                }
            };

            $(function() {
                RepairFormHelpers.init();
                window.RepairFormHelpers = RepairFormHelpers;
            });
            })(jQuery);

        // How to use it in an AJAX‑loaded form:
        // // After you inject or update the form markup via AJAX...
        // success: function(html) {
        // $("#myFormContainer").html(html);
        // // re‑bind & initialize all behaviors on the new form:
        // WorkOrderForm.init();
        // }
        // Or call individual methods anywhere:
        // // just reload sub‑types for type = 5, preselect = 12:
        // WorkOrderForm.loadJobSubTypes(5, 12);

        // How to re‑initialize after an AJAX load:
        // // e.g. in your AJAX success callback:
        // $("#myFormContainer").html(newFormHtml);
        // RepairForm.init();

        // I’ve wrapped the Select2, form validation, dynamic contractor-assignments, and VAT logic into a single RepairFormHelpers object. You can now call:
        // RepairFormHelpers.init();
        // after any AJAX load to re-bind everything, or invoke any individual method:
        // RepairFormHelpers.initValidation();
        // RepairFormHelpers.initAssignments();
        // RepairFormHelpers.initVAT();
    </script>

@endpush