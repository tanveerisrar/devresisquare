@php
    $propertyType = $property->property_type ?? '';
    $availableFrom = formatDate($property->available_from) ?? '';
    $salePrice = $property->price ?? '';
    $lettingPrice = $property->letting_price ?? '';
    $groundRent = $property->ground_rent ?? '';
    $serviceCharge = $property->service_charge ?? '';
    $annualCouncilTax = $property->annual_council_tax ?? '';
    $councilTaxBand = $property->council_tax_band ?? '';
    $estateCharge = $property->estate_charge ?? '';
    $miscellaneousCharge = $property->miscellaneous_charge ?? '';
    $localAuthority = $property->local_authority ?? '';
    $lengthOfLease = $property->length_of_lease ?? '';
@endphp

@if (!isset($editMode) || !$editMode)
    <!-- Marketing Details -->
    <p class="fw-bold h4 mb-2">Marketing Details</p>
    <div class="row mb-2">
        <div class="col"><span class="text-muted">Move-in Date : </span><strong>{{ $availableFrom }}</strong></div>
    </div>

    @if ($propertyType == 'sales' || $propertyType == 'both')
        <div class="row mb-2">
            <div class="col"><span class="text-muted">Length of Lease : </span><strong>{{ $lengthOfLease }}</strong>
            </div>
        </div>
    @endif

    <div class="row mb-2">
        <div class="col"><span class="text-muted">Local Authority : </span><strong>{{ $localAuthority }}</strong></div>
    </div>

    <div class="row mb-2">
        <div class="col"><span class="text-muted">Tenure : </span>
            <strong>
                @switch($property->tenure)
                    @case('leasehold')
                        Leasehold
                    @break

                    @case('freehold')
                        Freehold
                    @break

                    @case('commonhold')
                        Commonhold
                    @break

                    @case('feudal')
                        Feudal
                    @break

                    @case('share_of_freehold')
                        Share of Freehold
                    @break

                    @default
                        N/A
                @endswitch
            </strong>
        </div>
    </div>

    <!-- Price Section -->
    <div class="mt-md-4 mt-3">
        <p class="fw-bold h4 mb-2">Price</p>

        <div class="row mb-2">
            @if ($propertyType == 'sales' || $propertyType == 'both')
                <div class="col-4"><span class="text-muted">Estate Charges : </span><strong>{{ getPoundSymbol() }}
                        {{ $estateCharge }}</strong></div>
            @endif
            <div class="col-6"><span class="text-muted">Miscellaneous Charge (annual) :
                </span><strong>{{ getPoundSymbol() }} {{ $miscellaneousCharge }}</strong></div>
        </div>

        @if ($propertyType == 'sales' || $propertyType == 'both')
            <div class="row mb-2">
                <div class="col-4"><span class="text-muted">Ground Rent : </span><strong>{{ getPoundSymbol() }}
                        {{ $groundRent }}</strong></div>
                <div class="col-6"><span class="text-muted">Service Charge (annual) :
                    </span><strong>{{ getPoundSymbol() }} {{ $serviceCharge }}</strong></div>
            </div>
        @endif

        <div class="row mb-2">
            <div class="col-4"><span class="text-muted">Sales Price : </span><strong>{{ getPoundSymbol() }}
                    {{ $salePrice }}</strong></div>
            <div class="col-6"><span class="text-muted">Letting Price : </span><strong>{{ getPoundSymbol() }}
                    {{ $lettingPrice }}</strong></div>
        </div>
    </div>


    <!-- Council Tax Section -->
    <div class="mt-md-4 mt-3">
        <h5 class="fw-bold h4 mb-2">Council Tax</h5>
        <div class="row mb-2">
            <div class="col"><span class="text-muted">Annual Council Tax : </span><strong>{{ getPoundSymbol() }}
                    {{ $annualCouncilTax }}</strong></div>
        </div>
        <div class="row mb-2">
            <div class="col"><span class="text-muted">Council Tax Band :
                </span><strong>{{ $councilTaxBand }}</strong></div>
        </div>
    </div>
@else
    <form id="availabilityPricingForm">
        @csrf
        <input type="hidden" name="repair_id" value="{{ $repairIssue->id }}">
        <input type="hidden" name="form_type" value="availability_pricing">

        <div class="mb-3">
            <label>Availability</label>
            <input type="date" name="available_from" class="form-control" value="{{ $property->available_from }}">
        </div>
        <div class="form-group">
            <label for="local_authority">Local Authority</label>
            <input type="text" name="local_authority" id="local_authority" class="form-control"
                value="{{ $localAuthority }}">
        </div>

        <div class="form-group">
            <label for="tenure">Tenure</label>
            <select name="tenure" id="tenure" class="form-control">
                <option value="leasehold" {{ isset($property) && $property->tenure == 'leasehold' ? 'selected' : '' }}>
                    Leasehold</option>
                <option value="freehold" {{ isset($property) && $property->tenure == 'freehold' ? 'selected' : '' }}>
                    Freehold
                </option>
                <option value="commonhold"
                    {{ isset($property) && $property->tenure == 'commonhold' ? 'selected' : '' }}>
                    Commonhold</option>
                <option value="feudal" {{ isset($property) && $property->tenure == 'feudal' ? 'selected' : '' }}>
                    Feudal
                </option>
                <option value="share_of_freehold"
                    {{ isset($property) && $property->tenure == 'share_of_freehold' ? 'selected' : '' }}>Share of
                    Freehold</option>
            </select>

        </div>

        @if ($propertyType == 'sales' || $propertyType == 'both')
            <div class="form-group">
                <label for="length_of_lease">Length of Lease (in year)</label>
                <input type="text" name="length_of_lease" id="length_of_lease" class="form-control"
                    value="{{ $lengthOfLease }}">
            </div>

            <div class="form-group">
                <label for="estate_charge">Estate Charge (annual)</label>
                <div class="price_input_wrapper">
                    <div class="pound_sign">{{ getPoundSymbol() }}</div>
                    <input type="text" name="estate_charge" id="estate_charge" class="form-control"
                        value="{{ $estateCharge }}">
                </div>
            </div>


            <div class="form-group">
                <label for="ground_rent">Ground Rent (annual)</label>
                <div class="price_input_wrapper">
                    <div class="pound_sign">{{ getPoundSymbol() }}</div>
                    <input type="text" name="ground_rent" id="ground_rent" class="form-control"
                        value="{{ $groundRent }}">
                </div>
            </div>

            <div class="form-group">
                <label for="service_charge">Service Charge (annual)</label>
                <div class="price_input_wrapper">
                    <div class="pound_sign">{{ getPoundSymbol() }}</div>
                    <input type="text" name="service_charge" id="service_charge" class="form-control"
                        value="{{ $serviceCharge }}">
                </div>
            </div>


            {{-- <div class="form-group">
            <label for="estate_charges[amount]">Estate Charge</label>
            <div class="price_input_wrapper">
                <div class="pound_sign">{{ getPoundSymbol() }}</div>
                <input required type="text" name="estate_charges[amount]" id="estate_charges" class="form-control"
                    value="{{ old('estate_charges.amount', $property->estateCharge->amount ?? '') }}">
            </div>
            @error('estate_charges.amount')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div> --}}
        @endif

        <div class="form-group">
            <label for="miscellaneous_charge">Miscellaneous Charge (annual)</label>
            <div class="price_input_wrapper">
                <div class="pound_sign">{{ getPoundSymbol() }}</div>
                <input type="text" name="miscellaneous_charge" id="miscellaneous_charge" class="form-control"
                    value="{{ $miscellaneousCharge }}">
            </div>
        </div>

        <!-- Listing Sale Price Input (Show only if type is sales or both) -->
        @if ($propertyType == 'sales' || $propertyType == 'both')
            <div class="form-group">
                <label for="lprice">Listing Sale Price</label>
                <div class="price_input_wrapper">
                    <div class="pound_sign">{{ getPoundSymbol() }}</div>
                    <input type="text" name="price" id="price" class="form-control"
                        value="{{ $salePrice }}">
                </div>
            </div>
        @endif

        <!-- Letting Price Input (Show only if type is letting or both) -->
        @if ($propertyType == 'lettings' || $propertyType == 'both')
            <div class="form-group">
                <label for="letting_price">Letting Price</label>
                <div class="price_input_wrapper">
                    <div class="pound_sign">{{ getPoundSymbol() }}</div>
                    <input type="text" name="letting_price" id="letting_price" class="form-control"
                        value="{{ $lettingPrice }}">
                </div>
            </div>
        @endif


        <div class="form-group">
            <label for="annual_council_tax">Annual Council Tax (annual)</label>
            <div class="price_input_wrapper">
                <div class="pound_sign">{{ getPoundSymbol() }}</div>
                <input type="text" name="annual_council_tax" id="annual_council_tax" class="form-control"
                    value="{{ $annualCouncilTax }}">
            </div>
        </div>

        <div class="form-group">
            <label for="council_tax_band">Council Tax Band</label>
            <select name="council_tax_band" id="council_tax_band" class="form-control">
                <option value="" disabled selected>Select a band</option>
                <option value="A" {{ old('council_tax_band', $councilTaxBand) == 'A' ? 'selected' : '' }}>A
                </option>
                <option value="B" {{ old('council_tax_band', $councilTaxBand) == 'B' ? 'selected' : '' }}>B
                </option>
                <option value="C" {{ old('council_tax_band', $councilTaxBand) == 'C' ? 'selected' : '' }}>C
                </option>
                <option value="D" {{ old('council_tax_band', $councilTaxBand) == 'D' ? 'selected' : '' }}>D
                </option>
                <option value="E" {{ old('council_tax_band', $councilTaxBand) == 'E' ? 'selected' : '' }}>E
                </option>
                <option value="F" {{ old('council_tax_band', $councilTaxBand) == 'F' ? 'selected' : '' }}>F
                </option>
                <option value="G" {{ old('council_tax_band', $councilTaxBand) == 'G' ? 'selected' : '' }}>G
                </option>
                <option value="H" {{ old('council_tax_band', $councilTaxBand) == 'H' ? 'selected' : '' }}>H
                </option>
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-3 float-end">Save Changes</button>
    </form>

@endif
