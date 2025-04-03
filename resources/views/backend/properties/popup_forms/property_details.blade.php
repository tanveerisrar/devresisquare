@php
    if (isset($property)) {
        $epcRating = $property->epc_rating ?? '';
        $isGas = $property->is_gas ?? '';
        $gas_safe_acknowledged = $property->gas_safe_acknowledged ?? 0;
    } else {
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

@else
    <form id="propertyDetailsForm">

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
            <div class="rs_radio_btns">
                <div>
                    <input type="radio" name="is_gas" id="is_gas_no" value="0" {{ $isGas == '0' ? 'checked' : '' }}
                        required />
                    <label for="is_gas_no"> No</label>
                </div>
                <div>
                    <input type="radio" name="is_gas" id="is_gas_yes" value="1" {{ $isGas == '1' ? 'checked' : '' }}
                        required />
                    <label for="is_gas_yes"> Yes</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>
@endif