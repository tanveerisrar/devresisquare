@php
    $propertyType = $property->property_type ?? '';
    $transactionType = $property->transaction_type ?? '';
    $specificPropertyType = $property->specific_property_type ?? '';
@endphp
@if(!isset($editMode) || !$editMode)
<div class="accordion_inner">
    <!-- Display View Mode -->
    <strong>Property Type:</strong> {{ $propertyType }} <br>
    <strong>Transaction Type:</strong> {{ $transactionType }} <br>
    <strong>Specific Property Type:</strong> {{ $specificPropertyType }} <br>
</div>
@else
    <form id="propertyInfoForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="form_type" value="property_info">

        <div class="form-group pt_wrapper">
            <div class="rs_radio_btns">
                <div>
                    <input type="radio" name="property_type" id="property_type_sales" value="sales" {{ (isset($property) && $propertyType == 'sales') ? 'checked' : '' }} required />
                    <label for="property_type_sales">Sales</label>
                </div>
                <div>
                    <input type="radio" name="property_type" id="property_type_lettings" value="lettings" {{ (isset($property) && $propertyType == 'lettings') ? 'checked' : '' }} required />
                    <label for="property_type_lettings">Lettings</label>
                </div>
                <div>
                    <input type="radio" name="property_type" id="property_type_both" value="both" {{ (isset($property) && $propertyType == 'both') ? 'checked' : '' }} required />
                    <label for="property_type_both">Both</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Transaction Type</label>
            <div class="rs_radio_btns">
                <div>
                    <input type="radio" name="transaction_type" id="transaction_type_residential" value="residential" {{ (isset($property) && $transactionType == 'residential') ? 'checked' : '' }} required />
                    <label for="transaction_type_residential">Residential</label>
                </div>
                <div>
                    <input type="radio" name="transaction_type" id="transaction_type_commercial" value="commercial" {{ (isset($property) && $transactionType == 'commercial') ? 'checked' : '' }} required />
                    <label for="transaction_type_commercial">Commercial</label>
                </div>
            </div>
            @error('transaction_type')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Specific Property Type</label>
            <div class="rs_radio_btns">
                <div>
                    <input type="radio" name="specific_property_type" id="specific_property_type_appartment"
                        value="appartment" {{ (isset($property) && $specificPropertyType == 'appartment') ? 'checked' : '' }}
                        required>
                    <label for="specific_property_type_appartment">Appartment</label>
                </div>
                <div>
                    <input type="radio" name="specific_property_type" id="specific_property_type_flat" value="flat" {{ (isset($property) && $specificPropertyType == 'flat') ? 'checked' : '' }} required>
                    <label for="specific_property_type_flat">Flat</label>
                </div>
                <div>
                    <input type="radio" name="specific_property_type" id="specific_property_type_bunglow" value="bunglow" {{ (isset($property) && $specificPropertyType == 'bunglow') ? 'checked' : '' }} required>
                    <label for="specific_property_type_bunglow">Bunglow</label>
                </div>
                <div>
                    <input type="radio" name="specific_property_type" id="specific_property_type_house" value="house" {{ (isset($property) && $specificPropertyType == 'house') ? 'checked' : '' }} required>
                    <label for="specific_property_type_house">House</label>
                </div>
            </div>
            @error('specific_property_type')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>
@endif