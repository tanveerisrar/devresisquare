@php
    $propertyType = $property->property_type ?? '';
    $transactionType = $property->transaction_type ?? '';
    $specificPropertyType = $property->specific_property_type ?? '';
@endphp
@if(!isset($editMode) || !$editMode)
<div class="accordion_inner">
    <!-- Display View Mode -->
    <div class="accordion_property_info_item">
        <span class="left_item">Property Type:</span>
        <span class="right_item capitalize"> {{ $propertyType }} </span>
    </div>
    <div class="accordion_property_info_item">
        <span class="left_item">Transaction Type:</span>
        <span class="right_item capitalize"> {{ $transactionType }}  </span>
    </div>
    <div class="accordion_property_info_item">
        <span class="left_item">Specific Property Type:</span> 
        <span class="right_item capitalize">{{ $specificPropertyType }} </span>
    </div>
</div>
@else
    <form id="propertyInfoForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="form_type" value="property_info">

        <div class="info_edit_form">
            <div class="form-group pt_wrapper">
                <div class="accordion_inner_heading mb-2">Proeperty Type</div>
                <div class="rounded_radio_btn">
                    <div class="rs_radio_btns">
                        <div>
                            <input class="hidden" type="radio" name="property_type" id="property_type_sales" value="sales" {{ (isset($property) && $propertyType == 'sales') ? 'checked' : '' }} required />
                            <label class="checkbox_btn" for="property_type_sales">Sales</label>
                        </div>
                        <div>
                            <input class="hidden" type="radio" name="property_type" id="property_type_lettings" value="lettings" {{ (isset($property) && $propertyType == 'lettings') ? 'checked' : '' }} required />
                            <label class="checkbox_btn" for="property_type_lettings">Lettings</label>
                        </div>
                        <div>
                            <input class="hidden" type="radio" name="property_type" id="property_type_both" value="both" {{ (isset($property) && $propertyType == 'both') ? 'checked' : '' }} required />
                            <label class="checkbox_btn" for="property_type_both">Both</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="accordion_inner_heading mb-2">Transaction Type</div>
                <div class="rounded_radio_btn">
                    <div class="rs_radio_btns">
                        <div>
                            <input class="hidden" type="radio" name="transaction_type" id="transaction_type_residential" value="residential" {{ (isset($property) && $transactionType == 'residential') ? 'checked' : '' }} required />
                            <label class="checkbox_btn" for="transaction_type_residential">Residential</label>
                        </div>
                        <div>
                            <input class="hidden" type="radio" name="transaction_type" id="transaction_type_commercial" value="commercial" {{ (isset($property) && $transactionType == 'commercial') ? 'checked' : '' }} required />
                            <label class="checkbox_btn" for="transaction_type_commercial">Commercial</label>
                        </div>
                    </div>
                    @error('transaction_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="accordion_inner_heading mb-2">Specific Property Type </div>
                <div class="rounded_radio_btn">
                    <div class="rs_radio_btns">
                        <div>
                            <input class="hidden" type="radio" name="specific_property_type" id="specific_property_type_appartment"
                                value="appartment" {{ (isset($property) && $specificPropertyType == 'appartment') ? 'checked' : '' }}
                                required>
                            <label class="checkbox_btn" for="specific_property_type_appartment">Appartment</label>
                        </div>
                        <div>
                            <input class="hidden" type="radio" name="specific_property_type" id="specific_property_type_flat" value="flat" {{ (isset($property) && $specificPropertyType == 'flat') ? 'checked' : '' }} required>
                            <label class="checkbox_btn" for="specific_property_type_flat">Flat</label>
                        </div>
                        <div>
                            <input class="hidden" type="radio" name="specific_property_type" id="specific_property_type_bunglow" value="bunglow" {{ (isset($property) && $specificPropertyType == 'bunglow') ? 'checked' : '' }} required>
                            <label class="checkbox_btn" for="specific_property_type_bunglow">Bunglow</label>
                        </div>
                        <div>
                            <input class="hidden" type="radio" name="specific_property_type" id="specific_property_type_house" value="house" {{ (isset($property) && $specificPropertyType == 'house') ? 'checked' : '' }} required>
                            <label class="checkbox_btn" for="specific_property_type_house">House</label>
                        </div>
                    </div>
                    @error('specific_property_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3 float-end">Save Changes</button>
    </form>
@endif