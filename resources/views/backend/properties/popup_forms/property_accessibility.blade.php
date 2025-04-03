@if(!isset($editMode) || !$editMode)
    <!-- Display View Mode -->
    <strong>Parking:</strong> {{ $parking }} <br>
    <strong>Balcony:</strong> {{ $balcony }} <br>
    <strong>Garden:</strong> {{ $garden }} <br>
    @if ($parking == 'Yes' && !empty($parkingLocation))
        <strong>Parking Location:</strong> {{ $parkingLocation }} <br>    
    @endif

@else
    <form id="propertyInfoForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="form_type" value="property_info">


        <div class="form-group">
            <label>Balcony</label>
            <div class="rs_radio_btns">
                <div>
                    <input type="radio" name="balcony" id="balcony_no" value="0" {{ (isset($property) && $property->balcony == '0') ? 'checked' : '' }} required />
                    <label for="balcony_no"> No</label>
                </div>
                <div>
                    <input type="radio" name="balcony" id="balcony_yes" value="1" {{ (isset($property) && $property->balcony == '1') ? 'checked' : '' }} required />
                    <label for="balcony_yes"> Yes</label>
                </div>
                @error('balcony')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <div class="form-group">
            <label>Garden</label>
            <div class="rs_radio_btns">
                <div>
                    <input type="radio" name="garden" id="garden_no" value="0" {{ (isset($property) && $property->garden == '0') ? 'checked' : '' }} required />
                    <label for="garden_no"> No</label>
                </div>
                <div>
                    <input type="radio" name="garden" id="garden_yes" value="1" {{ (isset($property) && $property->garden == '1') ? 'checked' : '' }} required />
                    <label for="garden_yes"> Yes</label>
                </div>
                @error('garden')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>


@endif