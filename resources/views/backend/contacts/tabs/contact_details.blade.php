{{-- Hidden div for contact ID --}}
<div id="hidden-contact-id" class="d-none" data-contact-id="{{ $contactId }}">
    @php
        // Debugging the contactId
        echo '<pre>';
        echo 'Contact ID: ';
        var_dump($contactId);
        echo '</pre>';
    @endphp
</div>
<div>
    {{-- CATEGORY --}}
    <div><strong>Category:</strong> {{ $contact->category->name ?? '—' }}</div>

    {{-- NAMES --}}
    <div>
        <strong>Full Name:</strong>
        {{
            $contact->full_name ? $contact->full_name : implode(' ', array_filter([
                $contact->first_name,
                $contact->middle_name,
                $contact->last_name
            ]))
        }}
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
        @forelse(optional($contact->detail)->emails ?? [] as $e)
            <li>{{ $e }}</li>
        @empty
            <li>—</li>
        @endforelse
    </ul>

    <strong>Phones:</strong>
    <ul>
        @forelse(optional($contact->detail)->phones ?? [] as $p)
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
        {{ $contact->created_at
    ? $contact->created_at->format('Y-m-d H:i:s')
    : '—' }}
    </div>

    @php
    // Normalize category name
    $cat = strtolower($contact->category->name ?? '');
    // Get the contact’s detail object (or null if it doesn’t exist)
    $d = $contact->detail ?? null;

    // Lettings & Sales
    $budget          = isset($d->budget)          && !empty($d->budget)           ? $d->budget          : '';
    $area            = isset($d->area)            && !empty($d->area)             ? $d->area            : '';
    $tentative_move  = isset($d->tentative_move_in) && !empty($d->tentative_move_in) ? $d->tentative_move_in : '';
    $beds            = isset($d->no_of_beds)      && !empty($d->no_of_beds)       ? $d->no_of_beds      : '';
    $tenants         = isset($d->no_of_tenants)   && !empty($d->no_of_tenants)    ? $d->no_of_tenants   : 1;

    // Contractor
    $specialisations = isset($d->specialisations) && is_array($d->specialisations) && count($d->specialisations)
                        ? $d->specialisations : [];
    $cover_areas     = isset($d->cover_areas)     && !empty($d->cover_areas)      ? $d->cover_areas     : '';
    $pi_insurance    = isset($d->pi_insurance)    && !empty($d->pi_insurance)     ? (bool)$d->pi_insurance : false;
    $pi_ref          = isset($d->pi_reference_number) && !empty($d->pi_reference_number)
                        ? $d->pi_reference_number : '';
    $pi_cert_path    = isset($d->pi_certificate_path) && !empty($d->pi_certificate_path)
                        ? $d->pi_certificate_path : null;
    @endphp
    
{{-- LETTINGS & SALES APPLICANTS --}}
    @if(in_array($cat, ['letting applicant', 'sales applicant']))
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
    @if($cat === 'contractor')
        <div class="mb-4">
            <strong>Specialisations:</strong>
            @if(count($specialisations))
                <ul class="list-disc list-inside">
                    @foreach($specialisations as $spec)
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

        @if($pi_insurance)
            <div class="mb-4">
                <strong>Insurance Reference #:</strong>
                <p>{{ $pi_ref ?: '—' }}</p>
            </div>

            <div class="mb-4">
                <strong>Certificate:</strong>
                @if($pi_cert_path)
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

    {{-- LETTINGS & SALES APPLICANTS --}}
    {{-- @if(in_array($cat, ['letting applicant', 'sales applicant']))
        <div class="mb-4">
            <label for="budget">Budget (rent per month):</label>
            <input
                type="number" step="0.01" name="budget" id="budget"
                value="{{ old('budget', $budget) }}"
                class="form-input"
            >
        </div>

        <div class="mb-4">
            <label for="area">Area:</label>
            <input
                type="text" name="area" id="area"
                value="{{ old('area', $area) }}"
                class="form-input"
            >
        </div>

        <div class="mb-4">
            <label for="tentative_move_in">Tentative move-in:</label>
            <input
                type="date" name="tentative_move_in" id="tentative_move_in"
                value="{{ old('tentative_move_in', $tentative_move) }}"
                class="form-input"
            >
        </div>

        <div class="mb-4">
            <label for="no_of_beds">No. of Beds:</label>
            <input
                type="number" name="no_of_beds" id="no_of_beds"
                value="{{ old('no_of_beds', $beds) }}"
                class="form-input"
            >
        </div>

        <div class="mb-4">
            <label for="no_of_tenants">No. of Tenants (incl. applicant):</label>
            <input
                type="number" name="no_of_tenants" id="no_of_tenants"
                value="{{ old('no_of_tenants', $tenants) }}"
                class="form-input"
            >
        </div>
    @endif --}}


    {{-- CONTRACTOR --}}
    {{-- @if($cat === 'contractor')
        <div class="mb-4">
            <label for="specialisations">Specialisation:</label>
            <select
                name="specialisations[]" id="specialisations"
                multiple class="form-multiselect"
            >
                @foreach([
                    'Handyman','Plumber','Electrician','Inventory Clerk',
                    'Maintenance','Gas Engineer','EICR Engineer','EPC Engineer'
                ] as $spec)
                    <option value="{{ $spec }}"
                        {{ in_array($spec, old('specialisations', $specialisations)) ? 'selected' : '' }}>
                        {{ $spec }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="cover_areas">Cover Areas:</label>
            <input
                type="text" name="cover_areas" id="cover_areas"
                value="{{ old('cover_areas', $cover_areas) }}"
                class="form-input"
            >
        </div>

        <div class="mb-4">
            <label>
                <input type="hidden" name="pi_insurance" value="0">
                <input
                    type="checkbox" name="pi_insurance" value="1"
                    {{ old('pi_insurance', $pi_insurance) ? 'checked' : '' }}
                >
                PI Insurance?
            </label>
        </div>

        @if(old('pi_insurance', $pi_insurance))
            <div class="mb-4">
                <label for="pi_reference_number">Insurance Ref #:</label>
                <input
                    type="text" name="pi_reference_number" id="pi_reference_number"
                    value="{{ old('pi_reference_number', $pi_ref) }}"
                    class="form-input"
                >
            </div>

            <div class="mb-4">
                <label for="pi_certificate">Certificate (PDF/JPG/PNG):</label>
                <input type="file" name="pi_certificate" id="pi_certificate" class="form-input">
                @if($pi_cert_path)
                    <p><small>
                        Current: <a
                            href="{{ asset('storage/' . $pi_cert_path) }}"
                            target="_blank"
                        >View certificate</a>
                    </small></p>
                @endif
            </div>
        @endif
    @endif --}}



    {{-- Bank info from details: --}}
    {{-- {{ $contact->details->bank_account_number ?? 'n/a' }} --}}
    {{-- Tenancies: --}}
    {{-- @foreach($contact->tenancies as $t)
    {{ $t->address }}
    @endforeach --}}
</div>