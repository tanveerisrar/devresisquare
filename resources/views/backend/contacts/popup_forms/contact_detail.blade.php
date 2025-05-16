@php
    $email = $contact->email ?? '';
    $phone = $contact->phone ?? '';
    // Normalize category name
    $cat = strtolower($contact->category->name ?? '');
    // Get the contact’s detail object (or null if it doesn’t exist)
    $d = $contact->detail ?? null;

    // Lettings & Sales
    $budget = isset($d->budget) && !empty($d->budget) ? $d->budget : '';
    $area = isset($d->area) && !empty($d->area) ? $d->area : '';
    $tentative_move = isset($d->tentative_move_in) && !empty($d->tentative_move_in) ? $d->tentative_move_in : '';
    $beds = isset($d->no_of_beds) && !empty($d->no_of_beds) ? $d->no_of_beds : '';
    $tenants = isset($d->no_of_tenants) && !empty($d->no_of_tenants) ? $d->no_of_tenants : 1;

    // Contractor
    $specialisations =
        isset($d->specialisations) && is_array($d->specialisations) && count($d->specialisations)
            ? $d->specialisations
            : [];
    $cover_areas = isset($d->cover_areas) && !empty($d->cover_areas) ? $d->cover_areas : '';
    $pi_insurance = isset($d->pi_insurance) && !empty($d->pi_insurance) ? (bool) $d->pi_insurance : false;
    $pi_ref = isset($d->pi_reference_number) && !empty($d->pi_reference_number) ? $d->pi_reference_number : '';
    $pi_cert_path = isset($d->pi_certificate_path) && !empty($d->pi_certificate_path) ? $d->pi_certificate_path : null;

    // Start with the primary contact fields if set
    $allEmails = [];
    if (!empty($contact->email)) {
        $allEmails[] = $contact->email;
    }
    // Append any detail‑emails (make sure cast/json_decode is working)
    if (!empty($contact->detail->emails) && is_array($contact->detail->emails)) {
        $allEmails = array_merge($allEmails, $contact->detail->emails);
    }

    $allPhones = [];
    if (!empty($contact->phone)) {
        $allPhones[] = $contact->phone;
    }
    if (!empty($contact->detail->phones) && is_array($contact->detail->phones)) {
        $allPhones = array_merge($allPhones, $contact->detail->phones);
    }
@endphp

@if (!isset($editMode) || !$editMode)

    <!-- Display View Mode -->

    {{-- CATEGORY --}}
    <div><strong>Category:</strong> {{ $contact->category->name ?? '—' }}</div>

    {{-- NAMES --}}
    <div>
        <strong>Full Name:</strong>
        {{ $contact->full_name
            ? $contact->full_name
            : implode(' ', array_filter([$contact->first_name, $contact->middle_name, $contact->last_name])) }}
    </div>

    {{-- ADDRESS --}}
    <div>
        <strong>Address:</strong>
        {{ $contact->address_line_1 }}<br>
        {{ $contact->address_line_2 }}<br>
        {{ $contact->city }}, {{ $contact->postcode }}<br>
        {{ $contact->country }}
    </div>

    {{-- CORRESPONDENCE ADDRESS --}}
    <div>
        <strong>Correspondence Address:</strong><br>
        {{ $contact->detail->correspondence_address ?? '—' }}
    </div>

    <strong>Emails:</strong>
    <ul>
        @forelse($allEmails as $e)
            <li>{{ $e }}</li>
        @empty
            <li>—</li>
        @endforelse
    </ul>

    <strong>Phones:</strong>
    <ul>
        @forelse($allPhones as $p)
            <li>{{ $p }}</li>
        @empty
            <li>—</li>
        @endforelse
    </ul>
    
    {{-- OTHER free‑text --}}
    <div>
        <strong>Other:</strong><br>
        {{ $contact->detail->other ?? '—' }}
    </div>

    {{-- CONSENTS --}}
    <div>
        <strong>Allow:</strong>
        Email: {{ optional($contact->detail)->allow_email === 1 ? 'Yes' : 'No' }} |
        Post: {{ optional($contact->detail)->allow_post === 1 ? 'Yes' : 'No' }} |
        Text: {{ optional($contact->detail)->allow_text === 1 ? 'Yes' : 'No' }} |
        Call: {{ optional($contact->detail)->allow_call === 1 ? 'Yes' : 'No' }}
    </div>

    {{-- OCCUPATION & COMPANY --}}
    <div>
        <strong>Occupation:</strong> {{ $contact->detail->occupation ?? '—' }}<br>
        <strong>Company Name:</strong> {{ $contact->detail->business_name ?? '—' }}
    </div>

    {{-- REGISTERED ADDRESS & VAT --}}
    <div>
        <strong>Registered Address:</strong><br>
        {{ $contact->detail->registered_address ?? '—' }}<br>
        <strong>VAT Number:</strong> {{ $contact->detail->vat_number ?? '—' }}
    </div>
    {{-- CREATED BY & CREATED AT --}}
    <div>
        <strong>Created By:</strong> {{ $contact->creator->name ?? '—' }}
    </div>
    <div>
        <strong>Created At:</strong>
        {{ $contact->created_at ? $contact->created_at->format('Y-m-d H:i:s') : '—' }}
    </div>

    {{-- LETTINGS & SALES APPLICANTS --}}
    @if (in_array($cat, ['letting applicant', 'sales applicant']))
        <div class="mb-4">
            <strong>Budget (rent per month):</strong>
            <p>{{ $budget !== '' ? number_format($budget, 2) : '—' }}</p>
        </div>

        <div class="mb-4">
            <strong>Area:</strong>
            <p>{{ $area ?: '—' }}</p>
        </div>

        <div class="mb-4">
            <strong>Tentative move-in:</strong>
            <p>{{ $tentative_move ? \Carbon\Carbon::parse($tentative_move)->toFormattedDateString() : '—' }}</p>
        </div>

        <div class="mb-4">
            <strong>No. of Beds:</strong>
            <p>{{ $beds }}</p>
        </div>

        <div class="mb-4">
            <strong>No. of Tenants (incl. applicant):</strong>
            <p>{{ $tenants }}</p>
        </div>
    @endif


    {{-- CONTRACTOR --}}
    @if ($cat === 'contractor')
        <div class="mb-4">
            <strong>Specialisations:</strong>
            @if (count($specialisations))
                <ul class="list-disc list-inside">
                    @foreach ($specialisations as $spec)
                        <li>{{ $spec }}</li>
                    @endforeach
                </ul>
            @else
                <p>—</p>
            @endif
        </div>

        <div class="mb-4">
            <strong>Cover Areas:</strong>
            <p>{{ $cover_areas ?: '—' }}</p>
        </div>

        <div class="mb-4">
            <strong>PI Insurance?</strong>
            <p>{{ $pi_insurance ? 'Yes' : 'No' }}</p>
        </div>

        @if ($pi_insurance)
            <div class="mb-4">
                <strong>Insurance Reference #:</strong>
                <p>{{ $pi_ref ?: '—' }}</p>
            </div>

            <div class="mb-4">
                <strong>Certificate:</strong>
                @if ($pi_cert_path)
                    <p>
                        <a href="{{ asset('storage/' . $pi_cert_path) }}" target="_blank">
                            View uploaded certificate
                        </a>
                    </p>
                @else
                    <p>—</p>
                @endif
            </div>
        @endif
    @endif
@else
    <form id="contactDetailForm">
        @csrf
        <input type="hidden" name="contact_id" value="{{ $contact->id }}">
        <input type="hidden" name="form_type" value="contact_detail">

        {{-- CATEGORY --}}
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-select">
                @foreach ($categories as $catOption)
                    <option value="{{ $catOption->id }}"
                        {{ old('category_id', $contact->category_id) == $catOption->id ? 'selected' : '' }}>
                        {{ $catOption->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- NAMES --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control"
                    value="{{ old('first_name', $contact->first_name) }}">
            </div>
            <div class="col-md-4">
                <label for="middle_name" class="form-label">Middle Name</label>
                <input type="text" name="middle_name" id="middle_name" class="form-control"
                    value="{{ old('middle_name', $contact->middle_name) }}">
            </div>
            <div class="col-md-4">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control"
                    value="{{ old('last_name', $contact->last_name) }}">
            </div>
        </div>

        {{-- ADDRESS --}}
        <div class="mb-3">
            <label for="address_line_1" class="form-label">Address Line 1</label>
            <input type="text" name="address_line_1" id="address_line_1" class="form-control"
                value="{{ old('address_line_1', $contact->address_line_1) }}">
        </div>
        <div class="mb-3">
            <label for="address_line_2" class="form-label">Address Line 2</label>
            <input type="text" name="address_line_2" id="address_line_2" class="form-control"
                value="{{ old('address_line_2', $contact->address_line_2) }}">
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="city" class="form-label">City</label>
                <input type="text" name="city" id="city" class="form-control"
                    value="{{ old('city', $contact->city) }}">
            </div>
            <div class="col-md-6">
                <label for="postcode" class="form-label">Postcode</label>
                <input type="text" name="postcode" id="postcode" class="form-control"
                    value="{{ old('postcode', $contact->postcode) }}">
            </div>
        </div>
        <div class="mb-3">
            <label for="country" class="form-label">Country</label>
            <input type="text" name="country" id="country" class="form-control"
                value="{{ old('country', $contact->country) }}">
        </div>

        {{-- CORRESPONDENCE ADDRESS --}}
        <div class="mb-3">
            <label for="correspondence_address" class="form-label">Correspondence Address</label>
            <textarea name="correspondence_address" id="correspondence_address" class="form-control" rows="3">{{ old('correspondence_address', $d->correspondence_address ?? '') }}</textarea>
        </div>

        {{-- EMAILS --}}
        <div class="my-3 my-md-4">
            <label class="form-label">Emails</label>
            <div class="emails-target">
                <div class="col-sm-10">
                    <div class="form-floating">
                        <input type="email" name="email" class="form-control" id="email"
                            placeholder="Email Address" value="{{ $email }}">
                        <label for="email">Email Address</label>
                    </div>
                </div>
                @php $emails = old('emails', $d->emails ?? ['']); @endphp
                @foreach ($emails as $i => $email)
                    <div class="row g-2 align-items-center email-entry my-2">
                        <div class="col-sm-10">
                            <div class="form-floating">
                                <input type="email" name="emails[]" class="form-control"
                                    id="emailInput{{ $loop->index }}" placeholder="Email Address (optional)"
                                    value="{{ $email }}">
                                <label for="emailInput{{ $loop->index }}">Email Address (optional)</label>
                            </div>
                        </div>
                        <div class="col-sm-2 text-end">
                            <button class="btn btn-outline-danger remove-email" data-toggle="remove-parent"
                                data-parent=".email-entry" type="button">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-outline-success btn-sm" data-toggle="add-more"
                    data-target=".emails-target"
                    data-content='
                <div class="row g-2 align-items-center email-entry my-2">
                <div class="col-sm-10">
                    <div class="form-floating">
                    <input
                        type="email"
                        name="emails[]"
                        class="form-control"
                        placeholder="Email Address (optional)">
                    <label>Email Address (optional)</label>
                    </div>
                </div>
                <div class="col-sm-2 text-end">
                    <button
                    class="btn btn-outline-danger remove-email"
                    data-toggle="remove-parent"
                    data-parent=".email-entry"
                    type="button"
                    >
                    <i class="fa fa-minus"></i>
                    </button>
                </div>
                </div>'>
                    <i class="fa fa-plus me-1"></i> Add New
                </button>
            </div>
        </div>


        {{-- PHONES --}}
        <div class="my-3 my-md-4">
            <label class="form-label">Phones</label>
            <div class="phones-target">
                <div class="col-sm-10">
                    <div class="form-floating">
                        <input type="text" name="phone" class="form-control" id="phone"
                            placeholder="Phone Number" value="{{ $phone }}" required>
                        <label for="phone">Phone Number</label>
                    </div>
                </div>
                @php $phones = old('phones', $d->phones ?? ['']); @endphp
                @foreach ($phones as $i => $phone)
                    <div class="row g-2 align-items-center phone-entry my-2">
                        <div class="col-sm-10">
                            <div class="form-floating">
                                <input type="text" name="phones[]" class="form-control"
                                    id="phoneInput{{ $loop->index }}" placeholder="Phone Number (optional)"
                                    value="{{ $phone }}">
                                <label for="phoneInput{{ $loop->index }}">Phone Number (optional)</label>
                            </div>
                        </div>
                        <div class="col-sm-2 text-end">
                            <button class="btn btn-outline-danger remove-phone" data-toggle="remove-parent"
                                data-parent=".phone-entry" type="button">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-outline-success btn-sm" data-toggle="add-more"
                    data-target=".phones-target"
                    data-content='
                <div class="row g-2 align-items-center phone-entry my-2">
                <div class="col-sm-10">
                    <div class="form-floating">
                    <input
                        type="text"
                        name="phones[]"
                        class="form-control"
                        placeholder="Phone Number (optional)"
                        
                    >
                    <label>Phone Number (optional)</label>
                    </div>
                </div>
                <div class="col-sm-2 text-end">
                    <button
                    class="btn btn-outline-danger remove-phone"
                    data-toggle="remove-parent"
                    data-parent=".phone-entry"
                    type="button"
                    >
                    <i class="fa fa-minus"></i>
                    </button>
                </div>
                </div>'>
                    <i class="fa fa-plus me-1"></i> Add New
                </button>
            </div>
        </div>


        {{-- OTHER --}}
        <div class="mb-3">
            <label for="other" class="form-label">Other</label>
            <textarea name="other" id="other" class="form-control" rows="2">{{ old('other', $d->other ?? '') }}</textarea>
        </div>

        {{-- CONSENTS --}}
        <div class="mb-3">
            <label class="form-label d-block">Allow:</label>
            @foreach (['email' => 'Email', 'post' => 'Post', 'text' => 'Text', 'call' => 'Call'] as $field => $label)
                <div class="form-check form-check-inline">
                    <input type="hidden" name="allow_{{ $field }}" value="0">
                    <input class="form-check-input" type="checkbox" name="allow_{{ $field }}" value="1"
                        {{ old("allow_{$field}", $d["allow_{$field}"] ?? 0) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        {{-- OCCUPATION & COMPANY --}}
        <div class="mb-3">
            <label for="occupation" class="form-label">Occupation</label>
            <input type="text" name="occupation" id="occupation" class="form-control"
                value="{{ old('occupation', $d->occupation ?? '') }}">
        </div>
        <div class="mb-3">
            <label for="business_name" class="form-label">Company Name</label>
            <input type="text" name="business_name" id="business_name" class="form-control"
                value="{{ old('business_name', $d->business_name ?? '') }}">
        </div>

        {{-- REGISTERED ADDRESS & VAT --}}
        <div class="mb-3">
            <label for="registered_address" class="form-label">Registered Address</label>
            <textarea name="registered_address" id="registered_address" class="form-control" rows="2">{{ old('registered_address', $d->registered_address ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="vat_number" class="form-label">VAT Number</label>
            <input type="text" name="vat_number" id="vat_number" class="form-control"
                value="{{ old('vat_number', $d->vat_number ?? '') }}">
        </div>


        {{-- CREATED BY & CREATED AT (readonly) --}}
        <div class="mb-4">
            <label class="block font-medium">Created By:</label>
            <input type="text" class="form-input w-full" value="{{ $contact->creator->name ?? '—' }}" disabled>
        </div>
        <div class="mb-4">
            <label class="block font-medium">Created At:</label>
            <input type="text" class="form-input w-full"
                value="{{ optional($contact->created_at)->format('Y-m-d H:i:s') }}" disabled>
        </div>

        {{-- LETTINGS & SALES APPLICANTS --}}
        @if (in_array($cat, ['letting applicant', 'sales applicant']))
            <div class="mb-4">
                <label for="budget">Budget (rent per month):</label>
                <input type="number" step="0.01" name="budget" id="budget"
                    value="{{ old('budget', $budget) }}" class="form-input">
            </div>

            <div class="mb-4">
                <label for="area">Area:</label>
                <input type="text" name="area" id="area" value="{{ old('area', $area) }}"
                    class="form-input">
            </div>

            <div class="mb-4">
                <label for="tentative_move_in">Tentative move-in:</label>
                <input type="date" name="tentative_move_in" id="tentative_move_in"
                    value="{{ old('tentative_move_in', $tentative_move) }}" class="form-input">
            </div>

            <div class="mb-4">
                <label for="no_of_beds">No. of Beds:</label>
                <input type="number" name="no_of_beds" id="no_of_beds" value="{{ old('no_of_beds', $beds) }}"
                    class="form-input">
            </div>

            <div class="mb-4">
                <label for="no_of_tenants">No. of Tenants (incl. applicant):</label>
                <input type="number" name="no_of_tenants" id="no_of_tenants"
                    value="{{ old('no_of_tenants', $tenants) }}" class="form-input">
            </div>
        @endif


        {{-- CONTRACTOR --}}
        @if ($cat === 'contractor')
            <div class="mb-4">
                <label for="specialisations">Specialisation:</label>
                <select name="specialisations[]" id="specialisations" multiple class="form-multiselect">
                    @foreach (['Handyman', 'Plumber', 'Electrician', 'Inventory Clerk', 'Maintenance', 'Gas Engineer', 'EICR Engineer', 'EPC Engineer'] as $spec)
                        <option value="{{ $spec }}"
                            {{ in_array($spec, old('specialisations', $specialisations)) ? 'selected' : '' }}>
                            {{ $spec }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="cover_areas">Cover Areas:</label>
                <input type="text" name="cover_areas" id="cover_areas"
                    value="{{ old('cover_areas', $cover_areas) }}" class="form-input">
            </div>

            <div class="mb-4">
                <label>
                    <input type="hidden" name="pi_insurance" value="0">
                    <input type="checkbox" name="pi_insurance" value="1"
                        {{ old('pi_insurance', $pi_insurance) ? 'checked' : '' }}>
                    PI Insurance?
                </label>
            </div>

            @if (old('pi_insurance', $pi_insurance))
                <div class="mb-4">
                    <label for="pi_reference_number">Insurance Ref #:</label>
                    <input type="text" name="pi_reference_number" id="pi_reference_number"
                        value="{{ old('pi_reference_number', $pi_ref) }}" class="form-input">
                </div>

                <div class="mb-4">
                    <label for="pi_certificate">Certificate (PDF/JPG/PNG):</label>
                    <input type="file" name="pi_certificate" id="pi_certificate" class="form-input">
                    @if ($pi_cert_path)
                        <p><small>
                                Current: <a href="{{ asset('storage/' . $pi_cert_path) }}" target="_blank">View
                                    certificate</a>
                            </small></p>
                    @endif
                </div>
            @endif
        @endif

        <button type="submit" class="btn btn_secondary mt-3 float-end">Save Changes</button>
    </form>
@endif
