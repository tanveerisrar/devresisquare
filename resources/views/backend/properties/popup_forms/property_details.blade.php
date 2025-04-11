@php
    if (isset($property)) {
        $epcRequired = booleanToYesNo($property->epc_required) ?? '';
        $epcRating = $property->epc_rating ?? '';
        $isGas = booleanToYesNo($property->is_gas) ?? '';
        $gas_safe_acknowledged = $property->gas_safe_acknowledged ?? 0;
    } else {
        $epcRequired = '';
        $epcRating = '';
        $isGas = '';
        $gas_safe_acknowledged = 0;
    }
@endphp
@if(!isset($editMode) || !$editMode)
    <!-- Display View Mode -->
    <strong>Valid EPC</strong><br>
    <div class="d-flex gap-3">
        <p><strong>EPC Required:</strong> {{ $epcRequired }}</p>
        <p><strong>EPC Rating:</strong> {{ $epcRating }}</p>
        <p><strong>Gas:</strong> {{ $isGas }}</p>
    </div>

    @if ($property->photos)
        <h6>Property Photos</h6>
        @foreach (explode(',', $property->photos) as $photo_id)
            <img class="mb-2 d-block preview-img" role="button" title="Preview" width="100"
                src="{{ uploaded_asset(trim($photo_id)) }}" 
                alt="Property Photo" 
                onclick="openImageModal('{{ uploaded_asset(trim($photo_id)) }}')">
        @endforeach
    @else
        <p><strong>Property Photos: </strong>N/A</p>
    @endif

    @if ($property->floor_plan)
        <h6>Floor Plans</h6>
        @foreach (explode(',', $property->floor_plan) as $floor_plan_id)
            <img class="mb-2 d-block preview-img" role="button" title="Preview" width="100"
                src="{{ uploaded_asset(trim($floor_plan_id)) }}" 
                alt="Floor Plan" 
                onclick="openImageModal('{{ uploaded_asset(trim($floor_plan_id)) }}')">
        @endforeach
    @else
        <p><strong>Floor Plans: </strong>N/A</p>
    @endif

    @if ($property->view_360)
        <h6>360° Views</h6>
        @foreach (explode(',', $property->view_360) as $view_360_id)
            <img class="mb-2 d-block preview-img" role="button" title="Preview" width="100"
                src="{{ uploaded_asset(trim($view_360_id)) }}" 
                alt="360 View" 
                onclick="openImageModal('{{ uploaded_asset(trim($view_360_id)) }}')">
        @endforeach
    @else
        <p><strong>360° Views: </strong>N/A</p>
    @endif
    @if ($property->market_on)
        <h6>Market On</h6>
        <p>{{ implode(', ', $property->market_on) }}</p>
    @else
        <p><strong>Market On: </strong>N/A</p>
    @endif
    
@else
    <form id="propertyDetailsForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="form_type" value="property_details">
        <div class="form-group">
            <label>EPC Required</label>
            <div class="radio_bts_square">
                <input type="radio" name="epc_required" id="epc_required1" value="1" {{ (isset($property) && $property->epc_required == '1') ? 'checked' : '' }} required />
                <label for="epc_required1"> Yes </label>
                <input type="radio" name="epc_required" id="epc_required0" value="0" {{ (isset($property) && $property->epc_required == '0') ? 'checked' : '' }} required />
                <label for="epc_required0"> No </label>
            </div>
        </div>
        <div class="form-group">
            <label for="epc_rating">EPC Rating</label>
            <select name="epc_rating" id="epc_rating" class="form-control">
                <option value="A" {{ $epcRating == 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ $epcRating == 'B' ? 'selected' : '' }}>B</option>
                <option value="C" {{ $epcRating == 'C' ? 'selected' : '' }}>C</option>
                <option value="D" {{ $epcRating == 'D' ? 'selected' : '' }}>D</option>
            </select>
            @error('epc_rating')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" id="gas_section">
            <input type="hidden" id="gas_safe_acknowledged" name="gas_safe_acknowledged"
                value="{{ $gas_safe_acknowledged }}">

            <label>Does the property have gas?</label>
            <div class="radio_bts_square">
                <input type="radio" name="is_gas" id="is_gas_no" value="1" {{ (isset($property) && $property->is_gas == '1') ? 'checked' : '' }} required />
                <label for="is_gas_no"> Yes </label>
                <input type="radio" name="is_gas" id="is_gas_yes" value="0" {{ (isset($property) && $property->is_gas == '0') ? 'checked' : '' }} required />
                <label for="is_gas_yes"> No </label>
            </div>            
        </div>

        <div class="form-group rs_upload_btn">
            <h5 class="sub_title mt-4">Photos <small>(Living Room, Reception, Bed Room, Bath Room, Garden, Hallway, Exterior - Cover Image)</small></h5>
            <div class="media_wrapper">
                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                    <label class="col-form-label" for="photos">Photos</label>
                    <div class="d-none input-group-prepend">
                        <div class="input-group-text bg-soft-secondary font-weight-medium">Browse</div>
                    </div>
                    <div class="d-none form-control file-amount">Choose File</div>
                    <input id="photos" id="photos" type="hidden" name="photos" value="{{ isset($property) && isset($property->photos) ? $property->photos : '' }}" class="selected-files">
                </div>
                <div class="d-flex gap-3 file-preview box sm">
                </div>
            </div>
        </div>

        <div class="form-group rs_upload_btn">
            <h5 class="sub_title mt-4">Floor Plan</h5>
            <div class="media_wrapper">
                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                    <label for="floor_plan">Upload Floor Plan Photos</label>
                    <div class="d-none input-group-prepend">
                        <div class="input-group-text bg-soft-secondary font-weight-medium">Browse</div>
                    </div>
                    <div class="d-none form-control file-amount">Choose File</div>
                    <input id="floor_plan" type="hidden" name="floor_plan" value="{{ isset($property) && isset($property->floor_plan) ? $property->floor_plan : '' }}" class="selected-files">
                </div>
                <div class="d-flex gap-3 file-preview box sm">
                </div>
            </div>
        </div>

        <div class="form-group rs_upload_btn">
            <h5 class="sub_title mt-4">View 360</h5>
            <div class="media_wrapper">
                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                    <label for="view_360">Upload 360 View Photos</label>
                    <div class="d-none input-group-prepend">
                        <div class="input-group-text bg-soft-secondary font-weight-medium">Browse</div>
                    </div>
                    <div class="d-none form-control file-amount">Choose File</div>
                    <input type="hidden" id="view_360" name="view_360" value="{{ isset($property) && isset($property->view_360) ? $property->view_360 : '' }}" class="selected-files">
                </div>
                <div class="d-flex gap-3 file-preview box sm">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="market_on">Market On</label>
            <select name="market_on[]" id="market_on" class="form-control select2" multiple>
                <option value="resisquare" {{ (isset($property) && is_array($property->market_on) && in_array('resisquare', $property->market_on)) || (is_array(old('market_on')) && in_array('resisquare', old('market_on'))) ? 'selected' : '' }}>Resisquare</option>
                <option value="rightmove" {{ (isset($property) && is_array($property->market_on) && in_array('rightmove', $property->market_on)) || (is_array(old('market_on')) && in_array('rightmove', old('market_on'))) ? 'selected' : '' }}>Rightmove</option>
                <option value="zoopla" {{ (isset($property) && is_array($property->market_on) && in_array('zoopla', $property->market_on)) || (is_array(old('market_on')) && in_array('zoopla', old('market_on'))) ? 'selected' : '' }}>Zoopla</option>
                <option value="onthemarket" {{ (isset($property) && is_array($property->market_on) && in_array('onthemarket', $property->market_on)) || (is_array(old('market_on')) && in_array('onthemarket', old('market_on'))) ? 'selected' : '' }}>OnTheMarket</option>
            </select>
            @error('market_on')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-success mt-3 float-end">Save Changes</button>
    </form>
@endif