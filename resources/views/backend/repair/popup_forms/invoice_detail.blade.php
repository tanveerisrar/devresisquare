@if(!isset($editMode) || !$editMode)
    <!-- Display View Mode -->
    <strong>Parking:</strong> {{ $property->parking == '1' ? 'Yes' : 'No' }} <br>
    @if($property->parking == '1' && !empty($property->parking_location))
        <strong>Parking Location:</strong> {{ $property->parking_location }} <br>
    @endif
    <strong>Service:</strong> {{ $property->service ?? 'N/A' }} <br>
    @if(isset($property) && $property->property_type == 'lettings' || $property->property_type == 'both')
        <strong>Pets Allow?:</strong> {{ $petsAllowed }} <br>
    @endif


@else
    <form id="propertyServiceForm">
        @csrf
        <input type="hidden" name="repair_id" value="{{ $repairIssue->id }}">
        <input type="hidden" name="form_type" value="property_services">

        <div class="form-group">
            <label>Parking</label>
            <div class="rs_radio_btns">
                <label><input type="radio" name="parking" value="0" {{ (isset($property) && $property->parking == '0') ? 'checked' : '' }} required /> No</label>
                <label><input type="radio" name="parking" value="1" {{ (isset($property) && $property->parking == '1') ? 'checked' : '' }} required /> Yes</label>
                @error('parking')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group" id="parking_location_group" style="display: none;">
            <label for="parking_location">Parking Location</label>
            <input type="text" name="parking_location" class="form-control"
                value="{{ $property->parking_location ?? '' }}" />
            @error('parking_location')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Service</label>
            <div class="row">
                <div class="col-lg-6 col-12">
                    <select name="service" class="form-control" required>
                        <option value="" disabled {{ (isset($property) && $property->service == '') ? 'selected' : '' }}>
                            Select a service</option>
                        <option value="Comprehensive Management" {{ (isset($property) && $property->service == 'Comprehensive Management') ? 'selected' : '' }}>Comprehensive Management </option>
                        <option value="Standard Management" {{ (isset($property) && $property->service == 'Standard Management') ? 'selected' : '' }}>Standard Management</option>
                        <option value="fully manged" {{ (isset($property) && $property->service == 'fully manged') ? 'selected' : '' }}>Fully Manged</option>
                        <option value="let and rent collect" {{ (isset($property) && $property->service == 'let and rent collect') ? 'selected' : '' }}>Let And Rent Collect</option>
                        <option value="let only" {{ (isset($property) && $property->service == 'let only') ? 'selected' : '' }}>Let Only</option>
                    </select>
                    @error('service')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        @if(isset($property) && $property->property_type == 'lettings' || $property->property_type == 'both')
            <div class="form-group">
                <input type="checkbox" name="pets_allow" id="pets_allow" style="width: 5%;"
                    value="{{ isset($property) && $property->pets_allow == 1 ? 1 : 0 }}" {{ isset($property) && $property->pets_allow == 1 ? 'checked' : '' }} />
                <label for="pets_allow">Pets Allowed</label>
            </div>
        @endif

        <button type="submit" class="btn btn-success mt-3 float-end">Save Changes</button>
    </form>

    <script>
        function initializeParkingRadios() {
            // Declare the variable for radio buttons if it's not already declared
            let parkingRadios = document.querySelectorAll('input[name="parking"]');
            const parkingLocationGroup = document.getElementById('parking_location_group');

            // Add event listener to handle changes
            parkingRadios.forEach(radio => {
                radio.addEventListener('change', () => {
                    // Show or hide the parking location group based on the selected value
                    parkingLocationGroup.style.display = (radio.value === '1') ? 'block' : 'none';
                });
            });

            // Check if any radio button is already selected, and show the field accordingly
            const selectedRadio = Array.from(parkingRadios).find(radio => radio.checked);
            if (selectedRadio && selectedRadio.value === '1') {
                parkingLocationGroup.style.display = 'block'; // Show the field if selected value is '1'
            } else {
                parkingLocationGroup.style.display = 'none';  // Hide the field if '0' or none selected
            }
        }

        // Call the function to initialize
        initializeParkingRadios();

    </script>
@endif