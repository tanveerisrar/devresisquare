@php
    $frunishingType = $property->frunishing_type ?? '';
    $propertyType = $property->property_type ?? '';
    $transactionType = $property->transaction_type ?? '';
    $specificPropertyType = $property->specific_property_type ?? '';

    $salesStatusDescription = $property->sales_status_description ?? '';
    $lettingStatusDescription = $property->letting_status_description ?? '';
@endphp
@if(!isset($editMode) || !$editMode)
<style>
    .propertyStatusDescription {
      position: relative;
      line-height: 1.5;            /* make sure you know your line-height */
      max-height: calc(1.5em * 5);  /* clamp to 5 lines */
      overflow: hidden;
      transition: max-height 0.3s ease;
    }
    
    .propertyStatusDescription.expanded {
      max-height: none;
    }
    
    .view-more {
      display: block;
      margin-top: 0.5em;
      color: #007bff;
      cursor: pointer;
      user-select: none;
    }
    
</style>
<div class="accordion_inner">
    <!-- Display View Mode -->
    <div class="accordion_property_info_item">
        <span class="left_item">Frunishing Type:</span>
        <span class="right_item capitalize"> {{ $frunishingType }} </span>
    </div>
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
    <div class="accordion_property_info_item row mb-3">
        <div class="col-12 mb-3">
            <div class="left_item">Sales Status Description :</div>
            <div class="propertyStatusDescription right_item">{{ $salesStatusDescription }}</div>
        </div>
        <div class="col-12 mb-3">
            <div class="left_item">Letting Status Description :</div>
            <div class="propertyStatusDescription2 right_item">{{ $lettingStatusDescription }}</div>
        </div>
    </div>
</div>
<script>
    $(function(){
      // target all elements with this class [ $ is not a function error from here]
      $('.propertyStatusDescription').each(function(){
        var $desc = $(this),
            lineHeight = parseFloat($desc.css('line-height')),
            maxHeight = lineHeight * 5;
    
        // only proceed if there's overflow beyond 5 lines
        if ($desc.length && $desc[0].scrollHeight > maxHeight) {
          // append the view‐more link
          var $link = $('<span class="view-more">View more</span>');
          $desc.after($link);
    
          // toggle on click
          $link.on('click', function(){
            if ($desc.hasClass('expanded')) {
              $desc.removeClass('expanded');
              $link.text('View more');
            } else {
              $desc.addClass('expanded');
              $link.text('View less');
            }
          });
        }
      });
    });
</script>
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
            @if(isset($property) && ($property->property_type == 'sales' || $property->property_type == 'both'))
            <div class="form-group sales_description">
                <label for="sales_status_description">Sales Description</label>
                <textarea name="sales_status_description" id="sales_status_description" rows="6"
                    class="form-control">{{ isset($property) && $property->sales_status_description ? $property->sales_status_description : '' }}</textarea>
            </div>
            @endif
            @if(isset($property) && ($property->property_type == 'lettings' || $property->property_type == 'both'))
            <div class="form-group lettings_description">
                <label for="letting_status_description">Letting Description</label>
                <textarea name="letting_status_description" id="letting_status_description" rows="6"
                    class="form-control">{{ isset($property) && $property->letting_status_description ? $property->letting_status_description : '' }}</textarea>
            </div>
            @endif
            
        </div>

        <button type="submit" class="btn btn_secondary mt-3 float-end">Save Changes</button>
    </form>
@endif