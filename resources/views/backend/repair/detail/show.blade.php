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
        (function ($) {
            var RepairForm = {
                allCategories: {},
                currentCategoryLevel: 1,
                selectedCategories: {},
                breadcrumbItems: ['Select Category'],

                // 1) new state for tenants
                initialPropertyId: $('#selected_property').val() || '',   // adjust selector
                initialTenantId: $('#selected_tenant').val() || '',
          
                loadAllCategories() {
                    return $.get("{{ route('admin.get.repair.categories') }}")
                        .done(data => this.allCategories = data)
                        .fail(() => console.error("Could not load categories"));
                },

                updateBreadcrumbUI() {
                    var html = this.breadcrumbItems.map((label, idx) =>
                        idx < this.breadcrumbItems.length - 1
                            ? `<li class="breadcrumb-item clickable" data-index="${idx}">${label}</li>`
                            : `<li class="breadcrumb-item active" aria-current="page">${label}</li>`
                    ).join('');
                    $('ol.breadcrumb').html(html);
                },

                updateHiddenCategories() {
                    $('#selected_categories').val(JSON.stringify(this.selectedCategories));
                    var deepest = Object.keys(this.selectedCategories).length;
                    var last = deepest === this.currentCategoryLevel
                        ? this.selectedCategories['level_' + deepest]
                        : '';
                    $('#last_selected_category').val(last);
                },

                populateNextLevel(selectEl, parentId) {
                    var currentLvl = +selectEl.data('level'),
                        nextLevel = currentLvl + 1,
                        $nextDiv = $(`.category-level[data-level="${nextLevel}"]`);

                    // build the next‐level options
                    var children = this.allCategories[parentId] || [];
                    $nextDiv
                        .find('select')
                        .empty()
                        .append('<option value="">-- Select --</option>')
                        .end();

                    children.forEach(cat => {
                        $nextDiv.find('select')
                            .append(`<option value="${cat.id}">${cat.name}</option>`);
                    });

                    // 1) hide & clear absolutely all deeper levels (lvl > currentLvl)
                    $(`.category-level`)
                        .filter((i, el) => $(el).data('level') > currentLvl)
                        .hide()
                        .find('select').val('');

                    // 2) if there _are_ children, show just the next level
                    if (children.length) {
                        $nextDiv.show();
                    }
                },

                onLevelChange(e) {
                    var $sel = $(e.currentTarget);
                    var lvl = $sel.data('level');
                    var val = $sel.val();
                    var text = $sel.find('option:selected').text();

                    // set selected and breadcrumb
                    this.selectedCategories['level_' + lvl] = val || undefined;

                    // trim breadcrumb back to this level
                    this.breadcrumbItems = this.breadcrumbItems.slice(0, lvl);

                    if (val) this.breadcrumbItems.push(text);

                    this.currentCategoryLevel = lvl;
                    this.updateBreadcrumbUI();

                    // --- NEW: clear all deeper levels ---
                    var maxLevel = {{ $maxLevel }};    // make sure you have maxLevel available here
                    for (var deeper = lvl + 1; deeper <= maxLevel; deeper++) {
                        // remove from your state object
                        delete this.selectedCategories['level_' + deeper];
                        // hide the deeper dropdown
                        $(`.category-level[data-level="${deeper}"]`).hide();
                        // reset its <select> back to default
                        $(`.category-level[data-level="${deeper}"] select`).val('');
                    }
                    this.updateHiddenCategories();

                    if (val) {
                        this.populateNextLevel($sel, val);
                    }
                },

                bindCategoryEvents() {
                    var self = this;
                    // dropdown change
                    $(document).on('change', '.category-select', function (e) {
                        self.onLevelChange(e);
                    });

                    // prev/next buttons, change/cancel
                    $('#category-next-btn').on('click', function () {
                        // advance to next non-empty level
                        var next = self.currentCategoryLevel + 1;
                        var $nextDiv = $(`.category-level[data-level="${next}"]`);
                        if ($nextDiv.find('option').length > 1) {
                            $(`.category-level[data-level]`).hide();
                            $nextDiv.show();
                            self.currentCategoryLevel = next;
                        }
                        self.updateHiddenCategories();
                    });

                    $('#category-prev-btn').on('click', function () {
                        if (self.currentCategoryLevel > 1) {
                            // clear current selection
                            delete self.selectedCategories['level_' + self.currentCategoryLevel];
                            self.currentCategoryLevel--;
                            $(`.category-level[data-level]`).hide();
                            $(`.category-level[data-level="${self.currentCategoryLevel}"]`).show();
                            self.breadcrumbItems.pop();
                            self.updateBreadcrumbUI();
                            self.updateHiddenCategories();
                        } else {
                            // cancel
                            self.restoreOriginalCategory();
                            $('#category-edit-card').addClass('d-none');
                            $('#category-display-card').removeClass('d-none');
                        }
                    });

                    $('#change-category-btn').on('click', function () {
                        $('#category-display-card').addClass('d-none');
                        $('#category-edit-card').removeClass('d-none');
                        $('#change-category-btn, #cancel-category-btn').toggleClass('d-none');
                    });

                    $('#cancel-category-btn').on('click', function (e) {
                        e.preventDefault();
                        self.restoreOriginalCategory();
                    });

                    // breadcrumb click (optional)
                    $(document).on('click', '.breadcrumb-item.clickable', function () {
                        var idx = +$(this).data('index');
                        var targetLevel = idx + 1;
                        self.currentCategoryLevel = targetLevel;
                        $(`.category-level[data-level]`).hide();
                        $(`.category-level[data-level="${targetLevel}"]`).show();
                        self.breadcrumbItems = self.breadcrumbItems.slice(0, targetLevel);
                        self.updateBreadcrumbUI();
                    });
                },

                restoreOriginalCategory() {
                    var origNav = JSON.parse($('#selected_categories_old').val() || '{}');
                    var origCat = $('#last_selected_category_old').val() || '';
                    this.selectedCategories = origNav;
                    this.currentCategoryLevel = Math.max(1, ...Object.keys(origNav).map(k => +k.split('_')[1]));
                    this.breadcrumbItems = ['Select Category'];

                    // repopulate selects
                    for (var lvl in origNav) {
                        var id = origNav[lvl];
                        var num = +lvl.split('_')[1];
                        var $sel = $(`.category-level[data-level="${num}"] select`);
                        // ensure children have been loaded
                        if (num > 1) {
                            this.populateNextLevel($(`.category-level[data-level="${num - 1}"] select`), origNav['level_' + (num - 1)]);
                        }
                        $sel.val(id);
                        this.breadcrumbItems.push($sel.find('option:selected').text());
                    }

                    this.updateBreadcrumbUI();
                    this.updateHiddenCategories();
                    $('#category-edit-card').addClass('d-none');
                    $('#category-display-card').removeClass('d-none');
                    $('#change-category-btn, #cancel-category-btn').toggleClass('d-none');
                },

                initCategories() {
                    this.breadcrumbItems = ['Select Category'];
                    this.updateBreadcrumbUI();
                    // pre-fill from hidden if needed
                    return this.loadAllCategories();
                },

                // 2) pull‐in fetchTenants as a method
                fetchTenants(propertyId, callback) {
                    if (!propertyId) {
                        $('#tenant-select').html('<option value="">-- Select Tenant --</option>');
                        return typeof callback === 'function' && callback();
                    }

                    $.ajax({
                        url: "{{ route('admin.get.property_repairs.tenants') }}",
                        method: "GET",
                        data: { property_id: propertyId },
                        success: data => {
                            let opts = '<option value="">-- Select Tenant --</option>';
                            data.forEach(t => {
                                opts += `<option value="${t.id}"
                                                                    data-email="${t.email}"
                                                                    data-phone="${t.phone}"
                                                                    data-address="${t.address}"
                                                                >${t.full_name}</option>`;
                            });
                            $('#tenant-select').html(opts);
                            if (typeof callback === 'function') callback();
                        },
                        error: () => console.error("Error fetching tenants.")
                    });
                },

                // 3) bind the tenant‐select change event
                bindTenantEvents() {
                    var self = this;
                    // on property change → reload tenants
                    $('#selected_property')             // <-- your real ID
                        .off('change.tenant')            // unbind any old handlers
                        .on('change.tenant', function () {
                            self.initialPropertyId = $(this).val();
                            self.fetchTenants(self.initialPropertyId);
                        });

                    // on tenant change → preview details
                    $('#tenant-select')
                        .off('change.preview')
                        .on('change.preview', function () {
                            let sel = $(this).find('option:selected');
                            if (!sel.val()) return $('#tenant-preview').empty();

                            $('#tenant-preview').html(`
                                <p><strong>Name:</strong>    ${sel.text()}</p>
                                <p><strong>Email:</strong>   ${sel.data('email') || 'N/A'}</p>
                                <p><strong>Phone:</strong>   ${sel.data('phone') || 'N/A'}</p>
                                <p><strong>Address:</strong> ${sel.data('address') || 'N/A'}</p>
                            `);
                        });
                },

                init() {
                    // console.log(this.initialPropertyId, this.initialTenantId);
                    // 1) init categories as before
                    this.initCategories().then(() => {
                        this.bindCategoryEvents();
                    }).catch(() => {
                        console.warn("Category load failed, but continuing init.");
                        this.bindCategoryEvents();
                    });

                    // 2) immediately fetch & bind tenants
                    this.fetchTenants(this.initialPropertyId, () => {
                        if (this.initialTenantId) {
                            $('#tenant-select')
                                .val(this.initialTenantId)
                                .trigger('change');
                        }
                    });
                    this.bindTenantEvents();
                }
            };

            $(function () { RepairForm.init(); });
            window.RepairForm = RepairForm;
        })(jQuery);




        (function ($) {
            var PropertySelector = {
                state: {
                    initialSelectedProperties: [],
                    initialPropertyId: null,
                    initialTenantId: null
                },

                init: function () {
                    this._readInitial();
                    this._toggleInitialUI();
                    this._bindEvents();
                    this._initSelectedProperties();
                    this._initTenants();
                },

                _readInitial: function () {
                    var selProps = JSON.parse($('#selected_properties').val() || '[]');
                    this.state.initialSelectedProperties = Array.isArray(selProps) ? selProps : [];
                    this.state.initialPropertyId = this.state.initialSelectedProperties[0] || null;
                    this.state.initialTenantId = $('#selected_tenant').val() || null;
                },

                _toggleInitialUI: function () {
                    if (this.state.initialSelectedProperties.length > 0) {
                        $('#change_property_button, #dynamic_property_table').removeClass('d-none');
                    }
                },

                _bindEvents: function () {
                    var self = this;

                    $(document).on('click.propertyChange', '#change_property_button', function (e) {
                        e.preventDefault();
                        window.previousTenantId = $('#tenant-select').val();
                        $('#dynamic_property_table').addClass('d-none');
                        $('#search_property_section, #cancel_property_change').show();
                        $(this).addClass('d-none');
                        $('#search_property1').val('');
                        $('#selected_properties').val('[]');
                    });

                    $(document).on('click.cancelProperty', '#cancel_property_change', function (e) {
                        e.preventDefault();
                        $('#selected_properties').val(JSON.stringify(self.state.initialSelectedProperties));
                        $('#search_property_section').hide();
                        $('#dynamic_property_table').removeClass('d-none');
                        self.searchPropertiesByIds(self.state.initialSelectedProperties);
                        self.fetchTenants(self.state.initialPropertyId, function () {
                            if (window.previousTenantId) {
                                $('#tenant-select').val(window.previousTenantId).trigger('change');
                            }
                        });
                        $(this).addClass('d-none');
                        $('#change_property_button').removeClass('d-none');
                    });

                    // Search input with debounce
                    $(document).off("input.propertySearch").on('input.propertySearch', '#search_property1', debounce(function () {
                        var q = $(this).val().trim();
                        if (q.length >= 3) {
                            $('#error_message').hide();
                            self.searchProperties(q);
                        } else {
                            $('#property_results').empty();
                            $('#error_message').text('Please enter at least 3 characters to search.').show();
                        }
                    }, 1000));

                    $(document).on('click.propertyResult', '.property-result', function () {
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
                        self.fetchTenants(id, function () {
                            $('#tenant-preview').empty();
                        });
                    });

                    $(document).on('click.removeProperty', '.remove-btn', function () {
                        $(this).closest('tr').remove();
                        self.updateSelectedProperties();
                        if ($('#dynamic_property_table tbody tr').length === 0) {
                            $('#dynamic_property_table').addClass('d-none');
                            if (typeof disableNextButton === 'function') disableNextButton();
                        }
                    });

                    $(document).on('change.tenantSelect', '#tenant-select', function () {
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

                _initSelectedProperties: function () {
                    if (this.state.initialSelectedProperties.length) {
                        this.searchPropertiesByIds(this.state.initialSelectedProperties);
                    }
                },

                searchPropertiesByIds: function (ids) {
                    var self = this;
                    $.get('{{ route("admin.contacts.properties.search") }}', { ids: ids })
                        .done(function (resp) {
                            var $tb = $('#dynamic_property_table tbody').empty();
                            resp.forEach(function (p) {
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
                        .fail(function () {
                            toastr.error('Error fetching properties by IDs.', 'Error');
                        });
                },

                searchProperties: function (query) {
                    var self = this;
                    $.get('{{ route("properties.search") }}', { query: query })
                        .done(function (resp) {
                            var $out = $('#property_results').empty();
                            if (!resp.length) {
                                return $out.append('<li class="list-group-item">No properties found.</li>');
                            }
                            resp.forEach(function (p) {
                                self._appendPropertyToResults(p);
                            });
                        })
                        .fail(function () {
                            $('#property_results').append('<li class="list-group-item">Error fetching results.</li>');
                        });
                },

                _appendPropertyToResults: function (p) {
                    var html = `<li class="list-group-item property-result" ` +
                        `data-id="${p.id}" data-type="${p.type}" data-availability="${p.availability}">` +
                        `${p.address || 'N/A'} - ${p.prop_ref_no || 'N/A'} - ${p.prop_name || 'N/A'}` +
                        `</li>`;
                    if (!$('#property_results li[data-id="' + p.id + '"]').length) {
                        $('#property_results').append(html);
                    }
                },

                updateSelectedProperties: function () {
                    var ids = [];
                    $('#dynamic_property_table tbody tr').each(function () {
                        ids.push($(this).data('id'));
                    });
                    $('#selected_properties').val(JSON.stringify(ids));
                },

                fetchTenants: function (propertyId, cb) {
                    $.get('{{ route("admin.get.property_repairs.tenants") }}', { property_id: propertyId })
                        .done(function (data) {
                            var opts = '<option value="">-- Select Tenant --</option>';
                            data.forEach(function (t) {
                                opts += `<option value="${t.id}" ` +
                                    `data-email="${t.email}" data-phone="${t.phone}" data-address="${t.address}">` +
                                    `${t.full_name}</option>`;
                            });
                            $('#tenant-select').html(opts);
                            if (typeof cb === 'function') cb();
                        });
                },

                _initTenants: function () {
                    if (this.state.initialPropertyId) {
                        this.fetchTenants(this.state.initialPropertyId, function () {
                            if (PropertySelector.state.initialTenantId) {
                                $('#tenant-select').val(PropertySelector.state.initialTenantId).trigger('change');
                            }
                        });
                    }
                }
            };

            $(function () {
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


        (function ($) {
            const RepairFormHelpers = {
                // 1) Initialize Select2
                initSelect2: function () {
                    initSelect2('.select2');
                },

                // 2) Form validation setup
                initValidation: function () {
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
                        errorPlacement: function (error, element) {
                            error.insertAfter(element.closest('.form-group'));
                        },
                        highlight: function (el, errClass, validClass) {
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
                        unhighlight: function (el, errClass, validClass) {
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
                        invalidHandler: function (event, validator) {
                            if (validator.numberOfInvalids()) {
                                AIZ.plugins.notify('error', 'Please fill out all required fields.');
                            }
                        },
                        submitHandler: function (form) {
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

                updateAssignmentIndexes: function () {
                    $('#contractor-assignments .contractor-assignment').each((i, el) => {
                        const $el = $(el);
                        $el.find('.assignment-index').remove();
                        $el.prepend(`<div class="assignment-index badge bg-secondary mb-2">#${i + 1}</div>`);
                    });
                    const count = $('#contractor-assignments .contractor-assignment').length;
                    $('#contractor-assignments .remove-contractor-assignment').toggle(count > 1);
                },

                attachRules: function ($block) {
                    const v = this.validator;
                    if (!v) return;
                    $block.find('.contractor-id').rules('add', { required: true, messages: { required: 'Please select a contractor' } });
                    $block.find('.cost-price').rules('add', { required: true, number: true, messages: { required: 'Please enter a cost price', number: 'Enter a valid number' } });
                    $block.find('.contractor-availability').rules('add', { required: true, messages: { required: 'Please select preferred availability' } });
                },

                addAssignment: function (idx) {
                    const html = this.contractorTemplate.replace(/__index__/g, idx);
                    const $block = $(html);
                    $('#contractor-assignments').append($block);
                    this.updateAssignmentIndexes();
                    this.attachRules($block);
                },

                addSavedAssignment: function (idx, data) {
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

                bindAssignmentEvents: function () {
                    const self = this;
                    $('#add-contractor-assignment').off('click').on('click', () => {
                        const idx = $('#contractor-assignments .contractor-assignment').length;
                        self.addAssignment(idx);
                    });
                    $('#contractor-assignments').off('click', '.remove-contractor-assignment').on('click', '.remove-contractor-assignment', function () {
                        $(this).closest('.contractor-assignment').remove();
                        self.updateAssignmentIndexes();
                    });
                },

                initAssignments: function () {
                    this.bindAssignmentEvents();
                    if (window.savedContractorAssignments?.length) {
                        window.savedContractorAssignments.forEach((as, i) => this.addSavedAssignment(i, as));
                        AIZ.uploader.previewGenerate();
                    } else {
                        this.addAssignment(0);
                    }
                },

                // 4) VAT calculation
                initVAT: function () {
                    const vatType = '{{ old('vat_type', $repairIssue->vat_type) }}';
                    const vatPct = '{{ old('vat_percentage', $repairIssue->vat_percentage) }}';

                    function calculateVAT() {
                        const price = parseFloat($('#estimated_price').val()) || 0;
                        const pct = parseFloat($('#vat_percentage').val()) || 0;
                        if (price > 0 && pct > 0) {
                            const amt = price * (pct / 100), total = price + amt;
                            $('#vat_calculation').html(`VAT Amount: $${amt.toFixed(2)}<br>Total Price (including VAT): $${total.toFixed(2)}`);
                        } else {
                            $('#vat_calculation').empty();
                        }
                    }

                    if (vatType === 'exclusive') {
                        $('#vat_type_exclusive').prop('checked', true);
                        $('#exclusive_vat_fields').removeClass('d-none');
                        if (vatPct > 0) {
                            $('#vat_percentage').val(vatPct);
                            $('#vat_calculation_preview').removeClass('d-none');
                            calculateVAT();
                        }
                        $('#vat_percentage').prop('required', true);
                    } else {
                        $('#vat_type_inclusive').prop('checked', true);
                        $('#exclusive_vat_fields, #vat_calculation_preview').addClass('d-none');
                        $('#vat_percentage').prop('required', false).val('');
                    }

                    $('input[name="vat_type"]').off('change').on('change', function () {
                        if ($('#vat_type_exclusive').is(':checked')) {
                            $('#exclusive_vat_fields').removeClass('d-none');
                            $('#vat_percentage').prop('required', true);
                            if ($('#vat_percentage').val() > 0) $('#vat_calculation_preview').removeClass('d-none');
                            calculateVAT();
                        } else {
                            $('#exclusive_vat_fields, #vat_calculation_preview').addClass('d-none');
                            $('#vat_percentage').prop('required', false).val('');
                        }
                    });

                    $('#vat_percentage').off('input').on('input', function () {
                        if ($(this).val() > 0) $('#vat_calculation_preview').removeClass('d-none');
                        else $('#vat_calculation_preview').addClass('d-none');
                        calculateVAT();
                    });
                },

                // Master init
                init: function () {
                    this.initSelect2();
                    this.initValidation();
                    this.initAssignments();
                    this.initVAT();
                }
            };

            $(function () {
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