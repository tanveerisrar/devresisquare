@php
    $lettingCurrentStatus = $property->letting_current_status ?? '';
    $salesCurrentStatus = $property->sales_current_status ?? '';
    $statusDescription = $property->status_description ?? '';
@endphp

@if(!isset($editMode) || !$editMode)
    <!-- Display View Mode -->

    <div class="mt-md-4 mt-3">
        <p class="fw-bold h4 mb-2">Status</p>

        <div class="row mb-2">
            <div class="col-4"><span class="text-muted">Sales Status : </span><strong>{{ $salesCurrentStatus }}</strong>
            </div>
            
        @if(isset($property) && ($property->property_type == 'lettings' || $property->property_type == 'both'))
            <div class="col-6"><span class="text-muted">Letting Status : </span><strong>{{ $lettingCurrentStatus }}</strong>
            </div>
        @endif
        </div>

        <div class="row mb-2">
            <div class="col-4"><span class="text-muted">Status Description :
                </span><strong>{{ $statusDescription }}</strong></div>
        </div>
    </div>

@else
    <form id="propertyStatusForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="form_type" value="property_status">

        @if(isset($property) && ($property->property_type == 'sales' || $property->property_type == 'both'))
            <div class="form-group">
                <label for="sales_current_status">Sales Status</label>
                <select name="sales_current_status" id="sales_current_status" class="form-control" required>
                    <option value="" disabled {{ (isset($property) && $property->sales_current_status == '') ? 'selected' : ''  }}>Select a Status</option>
                    <option value="for sale" {{ (isset($property) && $property->sales_current_status == 'for sale') ? 'selected' : '' }}>For Sale</option>
                    <option value="on hold" {{ (isset($property) && $property->sales_current_status == 'on hold') ? 'selected' : '' }}>On Hold</option>
                    <option value="under offer" {{ (isset($property) && $property->sales_current_status == 'under offer') ? 'selected' : '' }}>Under Offer</option>
                    <option value="sold" {{ (isset($property) && $property->sales_current_status == 'sold') ? 'selected' : '' }}>Sold</option>
                    <option value="sold STC" {{ (isset($property) && $property->sales_current_status == 'sold STC') ? 'selected' : '' }}>Sold STC</option>
                    <option value="sold by other" {{ (isset($property) && $property->sales_current_status == 'sold by other') ? 'selected' : '' }}>Sold By Other</option>
                    <option value="exchanged" {{ (isset($property) && $property->sales_current_status == 'exchanged') ? 'selected' : '' }}>Exchanged</option>
                    <option value="available" {{ (isset($property) && $property->sales_current_status == 'available') ? 'selected' : '' }}>Available</option>
                    <option value="let agreed" {{ (isset($property) && $property->sales_current_status == 'let agreed') ? 'selected' : '' }}>Let Agreed</option>
                </select>
                @error('sales_current_status')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif

        @if(isset($property) && ($property->property_type == 'lettings' || $property->property_type == 'both'))
            <div class="form-group">
                <label for="letting_current_status">Letting Status</label>
                <select name="letting_current_status" id="letting_current_status" class="form-control" required>
                    <option value="" disabled {{ (isset($property) && $property->letting_current_status == '') ? 'selected' : ''  }}>Select a Status</option>
                    <option value="not available" {{ (isset($property) && $property->letting_current_status == 'not available') ? 'selected' : '' }}>Not Available</option>
                    <option value="available" {{ (isset($property) && $property->letting_current_status == 'available') ? 'selected' : '' }}>Available</option>
                    <option value="let agreed" {{ (isset($property) && $property->letting_current_status == 'let agreed') ? 'selected' : '' }}>Let Agreed</option>
                    <option value="let by other" {{ (isset($property) && $property->letting_current_status == 'let by other') ? 'selected' : '' }}>Let By Other</option>
                </select>
                @error('letting_current_status')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif

        <div class="form-group">
            <label for="status_description">Description</label>
            <textarea name="status_description" id="status_description" rows="6"
                class="form-control">{{ isset($property) && $property->status_description ? $property->status_description : '' }}</textarea>
                <div class="input_tag">0/5000 words</div>
            @error('status_description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3 float-end">Save Changes</button>
    </form>
@endif