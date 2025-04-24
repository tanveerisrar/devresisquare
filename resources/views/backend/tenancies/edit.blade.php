@php
    // echo'<pre>';
    // var_dump($tenancy);
    // echo'</pre>';

    // Check if the properties are set before storing them in variables
    $status = isset($tenancy->status) ? $tenancy->status : null;
    $move_in = isset($tenancy->move_in) ? $tenancy->move_in : null;
    $move_out = isset($tenancy->move_out) ? $tenancy->move_out : null;
    $tenancy_renewal_confirm_date = isset($tenancy->tenancy_renewal_confirm_date)
        ? $tenancy->tenancy_renewal_confirm_date
        : null;
    $extension_date = isset($tenancy->extension_date) ? $tenancy->extension_date : null;
    $rent = isset($tenancy->rent) ? $tenancy->rent : null;
    $deposit = isset($tenancy->deposit) ? $tenancy->deposit : null;
    $deposit_type = isset($tenancy->deposit_type) ? $tenancy->deposit_type : '';
    $deposit_number = isset($tenancy->deposit_number) ? $tenancy->deposit_number : '';
    $deposit_held_by = isset($tenancy->deposit_held_by) ? $tenancy->deposit_held_by : '';
    $deposit_service = isset($tenancy->deposit_service) ? $tenancy->deposit_service : '';
    $periodic = isset($tenancy->periodic) && $tenancy->periodic === 1 ? 1 : 0;
    $rolling_contract = isset($tenancy->rolling_contract) && $tenancy->rolling_contract === 1 ? 1 : 0;
    $renewal_exempt = isset($tenancy->renewal_exempt) && $tenancy->renewal_exempt === 1 ? 1 : 0;
    $term_months = isset($tenancy->term_months) ? $tenancy->term_months : '';
    $term_days = isset($tenancy->term_days) ? $tenancy->term_days : '';

@endphp

<div id="mainForm">
    <form id="editTenancyForm" action="{{ route('admin.tenancies.update', $tenancy->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="property_id" class="form-control" value="">

        <div class="form-group">
            <button type="button" class="btn btn-outline-primary btn-sm" id="addContactBtn">
                Quick Add New Tenant
            </button>
            <label for="tenant_id">Select Tenants</label>
            <select name="contact_id[]" id="tenant_id" multiple class="form-control select2" required>
                @foreach ($tenants as $contact)
                    <option value="{{ $contact->id }}"
                        {{ in_array($contact->id, $tenancy->tenantMembers->pluck('contact_id')->toArray()) ? 'selected' : '' }}>
                        {{ $contact->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="tenant-options" class="mt-3">
            @foreach ($tenancy->tenantMembers as $tenantMembersContact)
                <div class="form-check">
                    <input type="radio" name="is_main_person" value="{{ $tenantMembersContact->contact->id }}"
                        id="is_main_person{{ $tenantMembersContact->contact->id }}" class="form-check-input"
                        @if ($tenantMembersContact->is_main_person) checked @endif>
                    <label for="is_main_person{{ $tenantMembersContact->contact->id }}"
                        class="form-check-label">{{ $tenantMembersContact->contact->full_name }}</label>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col">
                <div class="mb-3">
                    <div class="form-group field-tenancies-status required">
                        <label class="control-label" for="tenancies-status">Status</label>
                        <select id="tenancies-status" class="form-control" name="status" aria-required="true">
                            <option value="Active" {{ isset($status) && $status == 'Active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="Archive" {{ isset($status) && $status == 'Archive' ? 'selected' : '' }}>
                                Archive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Tenancy Type</label>
                    <select name="tenancy_type_id" class="form-control" required>
                        <option value="" disabled>Select Tenancy Type</option>
                        @foreach ($tenancyTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('tenancy_type_id', $tenancy->tenancy_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <div class="form-group field-tenancies-sub_status required has-error">
                        <label class="control-label" for="tenancies-sub_status">Sub Status</label>
                        <select id="tenancies-sub_status" class="form-control" name="tenancy_sub_status_id"
                            aria-required="true" required>
                            <option value="" disabled>Select Sub Status</option>
                            @foreach ($tenancySubStatuses as $subStatus)
                                <option value="{{ $subStatus->id }}" @if ($tenancy->tenancy_sub_status_id == $subStatus->id) selected @endif>
                                    {{ $subStatus->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <label>
                <input type="checkbox" name="periodic" {{ $periodic === 1 ? 'checked' : '' }}>
                Periodic
            </label><br>

            <label>
                <input type="checkbox" name="rolling_contract" {{ $rolling_contract === 1 ? 'checked' : '' }}>
                Rolling Contract
            </label><br>

            <label>
                <input type="checkbox" name="renewal_exempt" {{ $renewal_exempt === 1 ? 'checked' : '' }}>
                Renewal Exempt
            </label><br>

        </div>

        <div class="row mt-3">
            <div class="col">
                <div class="mb-3">
                    <label class="control-label" for="tenancies-move_in">Move In</label>
                    <input type="date" id="tenancies-move_in" class="form-control" name="move_in"
                        value="{{ $move_in }}" required>
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label class="control-label" for="tenancies-term_months">Term (Months)</label>
                    <input type="number" id="tenancies-term_months" class="form-control" name="term_months"
                        min="0" pattern="^[0-9]+$" value="{{ $term_months }}">
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label class="control-label" for="tenancies-term_days">Term (Days)</label>
                    <input type="number" id="tenancies-term_days" class="form-control" name="term_days" min="0"
                        pattern="^[0-9]+$" value="{{ $term_days }}">
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label class="control-label" for="tenancies-move_out">Move Out</label>
                    <input type="date" id="tenancies-move_out" class="form-control" name="move_out"
                        value="{{ $move_out }}">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="mb-3">
                    <div class="form-group field-tenancies-tenancy_renewal_confirm_date">
                        <label class="control-label" for="tenancies-tenancy_renewal_confirm_date">Renewal Confirm
                            Date</label>
                        <input type="date" id="tenancies-tenancy_renewal_confirm_date" class="form-control"
                            name="tenancy_renewal_confirm_date" min="{{ todayDate() }}"
                            value="{{ $tenancy_renewal_confirm_date }}">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <div class="form-group field-tenancies-extension_date">
                        <label class="control-label" for="tenancies-extension_date">Extension Date</label>
                        <input type="date" id="tenancies-extension_date" class="form-control"
                            name="extension_date" min="{{ tomorrowDate() }}" value="{{ $extension_date }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="mb-3">
                    <div class="form-group field-tenancies-rent">
                        <label class="control-label" for="tenancies-rent">Rent</label>
                        <input type="number" inputmode="numeric" pattern="[0-9]" id="tenancies-rent"
                            class="form-control" name="rent" value="{{ $rent }}">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <div class="form-group field-tenancies-deposit">
                        <label class="control-label" for="tenancies-deposit">Deposit</label>
                        <input type="number" inputmode="numeric" pattern="[0-9]" id="tenancies-deposit"
                            class="form-control" name="deposit" value="{{ $deposit }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col">
                <div class="mb-3">
                    <label for="depositType" class="form-label">Deposit Type</label>
                    <select class="form-select" id="depositType" name="deposit_type">
                        <option value="weeks_deposit" @if ($deposit_type == 'weeks_deposit') selected @endif>No of Weeks
                            Deposit</option>
                        <option value="months_deposit" @if ($deposit_type == 'months_deposit') selected @endif>No of Months
                            Deposit</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="depositNumber" class="form-label">Number of deposit type (Weeks/Months)</label>
                    <input value="{{ $deposit_number }}" type="number" class="form-control" id="depositNumber"
                        name="deposit_number" min="1" placeholder="Enter number of weeks or months" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="mb-3">
                    <label for="depositService" class="form-label">Deposit Held By</label>
                    @php
                        $depositHeldByOptions = [
                            'landlord_holding' => 'Landlord Holding',
                            'deposit_protection_service' => 'Deposit Protection Service',
                            'deposit_replacement_scheme' => 'Deposit Replacement Scheme',
                            'agent_holding_as_stakeholder' => 'Agent Holding As Stakeholder',
                        ];
                    @endphp

                    <select class="form-select" id="depositService" name="deposit_held_by">
                        @foreach ($depositHeldByOptions as $value => $label)
                            <option value="{{ $value }}" @if ($deposit_held_by == $value) selected @endif>
                                {{ $label }}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="depositService" class="form-label">Deposit Service</label>
                    <select class="form-select" id="depositService" name="deposit_service">
                        <option value="tds_dps_number" @if ($deposit_service == 'tds_dps_number') selected @endif>*TDS or DPS
                            Number</option>
                        <option value="number_of_scheme_or_number_of_reference"
                            @if ($deposit_service == 'number_of_scheme_or_number_of_reference') selected @endif>Name of the scheme Reference | Number of
                            Scheme</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <div class="form-group field-property_manager required has-success">
                <label class="control-label" for="property_manager">Property Manager</label>
                <select name="property_manager[]" id="property_manager" multiple class="form-control select2"
                    required>
                    @foreach ($property_managers as $property_manager)
                        <option value="{{ $property_manager->id }}" @if (in_array($property_manager->id, $currentPropertyManagers)) selected @endif>
                            {{ $property_manager->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

</div>
<button type="submit" form="editTenancyForm" class="float-end mt-3 btn btn_secondary">Save</button>
</form>
</div>

<!-- Add Contact Form (Step 2) -->
<div id="addContactFormContainer" style="display: none;">
    <form id="addContactForm">
        @csrf
        <input type="hidden" class="form-control" id="category_id" name="category_id" value="3">
        <div class="mb-3">
            <label for="contact_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="contact_name" name="full_name" required>
        </div>
        <div class="mb-3">
            <label for="contact_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="contact_email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="contact_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="contact_phone" name="phone" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Contact</button>
        <button type="button" class="btn btn-secondary" id="backToMainForm">Back</button>
    </form>
</div>

<script>
    initSelect3('.select2');

    // Form submission validation
    $('form').on('submit', function(e) {
        if ($('input[name="is_main_person"]:checked').length === 0) {
            e.preventDefault(); // Prevent form submission
            alert('Please select a main contact.'); // Show alert message
        }
    });

    function initializeForm() {
        const moveInInput = document.getElementById('tenancies-move_in');
        const termMonthsInput = document.getElementById('tenancies-term_months');
        const termDaysInput = document.getElementById('tenancies-term_days');
        const moveOutInput = document.getElementById('tenancies-move_out');

        if (!moveInInput || !termMonthsInput || !termDaysInput || !moveOutInput) return;

        // Function to calculate the "Move Out" date
        function calculateMoveOutDate() {
            const moveInDate = new Date(moveInInput.value);
            const termMonths = parseInt(termMonthsInput.value, 10) || 0; // Default to 0 if empty
            const termDays = parseInt(termDaysInput.value, 10) || 0; // Default to 0 if empty

            if (isNaN(moveInDate.getTime())) {
                moveOutInput.value = '';
                return;
            }

            // Add months and days to the "Move In" date
            const resultDate = new Date(moveInDate);
            if (termMonths > 0) {
                resultDate.setMonth(resultDate.getMonth() + termMonths);
                // Subtract 1 day for tenancy default behavior
                resultDate.setDate(resultDate.getDate() - 1);
            }
            if (termDays > 0) resultDate.setDate(resultDate.getDate() + termDays);

            // Set the calculated "Move Out" date
            moveOutInput.value = resultDate.toISOString().split('T')[0];
        }
        // Function to calculate the term in months and days based on Move In and Move Out dates
        function recalculateTerm() {
            const moveInDate = new Date(moveInInput.value);
            const moveOutDate = new Date(moveOutInput.value);

            if (isNaN(moveInDate.getTime()) || isNaN(moveOutDate.getTime())) return;

            const timeDifference = moveOutDate - moveInDate; // Difference in milliseconds
            const daysDifference = timeDifference / (1000 * 3600 * 24); // Convert to days

            const months = Math.floor(daysDifference / 30); // Approximate months
            const remainingDays = daysDifference % 30; // Remainder days

            // Update the term input fields
            termMonthsInput.value = months;
            termDaysInput.value = remainingDays;
        }

        // Function to validate the "Move Out" date and ensure it's after the "Move In" date
        function validateMoveOutDate() {
            const moveInDate = new Date(moveInInput.value);
            const moveOutDate = new Date(moveOutInput.value);

            if (isNaN(moveInDate.getTime()) || isNaN(moveOutDate.getTime())) return;

            if (moveOutDate <= moveInDate) {
                // Show error message or reset the Move Out date if it's invalid
                alert("Move Out Date must be greater than Move In Date.");
                moveOutInput.value = ''; // Reset Move Out Date
            }
        }

        // Function to ensure that the Term fields cannot have negative values
        function validateTermInputs() {
            const termMonths = parseInt(termMonthsInput.value, 10);
            const termDays = parseInt(termDaysInput.value, 10);

            if (termMonths < 0) termMonthsInput.value = 0;
            if (termDays < 0) termDaysInput.value = 0;
        }

        // Add event listeners to update the "Move Out" date on input change
        moveInInput.addEventListener('change', () => {
            // Only calculate Move Out if either termMonths or termDays is set
            if (termMonthsInput.value > 0 || termDaysInput.value > 0) {
                calculateMoveOutDate();
            }
        });

        // moveInInput.addEventListener('change', calculateMoveOutDate);
        termMonthsInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            validateTermInputs();
            calculateMoveOutDate();
        });
        termDaysInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            validateTermInputs();
            calculateMoveOutDate();
        });
        moveOutInput.addEventListener('change', () => {
            calculateMoveOutDate();
            recalculateTerm();
            validateMoveOutDate(); // Validate if Move Out Date is after Move In Date
        });

        // Initial calculation if values are already filled
        calculateMoveOutDate();
    }

    // Initialize the form only once the content is fully loaded
    initializeForm();
    // });
</script>
