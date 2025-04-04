@if(!isset($editMode) || !$editMode)
    <!-- Display View Mode -->

    <div class="rs_sub_title">Access Arrangement:</div>
    <p>{{ isset($property) && $property->access_arrangement ? $property->access_arrangement : 'N/A' }}</p>

    <div class="rs_sub_title">Key Highlights:</div>
    <p>{{ isset($property) && $property->key_highlights ? $property->key_highlights : 'N/A' }}</p>
    
    <div class="rs_sub_title">Nearest Station:</div>
    <p>
        @if($stations->isNotEmpty())
            {{ implode(', ', $stations->toArray()) }}
        @else
            N/A
        @endif
    </p>

    <div class="rs_sub_title">Nearest School:</div>
    <p>
        @if($schools->isNotEmpty())
            {{ implode(', ', $schools->toArray()) }}
        @else
            N/A
        @endif
    </p>

    <div class="rs_sub_title">Nearest Religious Places (Distance in KM):</div>
    @php
        $religiousPlaces =
            isset($property) && $property->nearest_religious_places
                ? json_decode($property->nearest_religious_places)
                : ['masjid' => 'N/A', 'church' => 'N/A', 'mandir' => 'N/A'];
    @endphp
    <p><strong>Masjid:</strong> {{ $religiousPlaces->masjid ?? 'N/A' }} km</p>
    <p><strong>Church:</strong> {{ $religiousPlaces->church ?? 'N/A' }} km</p>
    <p><strong>Mandir:</strong> {{ $religiousPlaces->mandir ?? 'N/A' }} km</p>

    <div class="rs_sub_title">Useful Information:</div>
    <p>{{ isset($property) && $property->useful_information ? $property->useful_information : 'N/A' }}</p>
   

@else
    <form id="propertyAccessiblityForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="form_type" value="property_accessibility">


        <div class="form-group">
            <div class="rs_sub_title">Access Arrangement</div>
            <textarea name="access_arrangement" required>{{ isset($property) && $property->access_arrangement ? $property->access_arrangement : '' }}</textarea>
            @error('access_arrangement')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="rs_sub_title">Key Highlights</div>
            <textarea name="key_highlights" required>{{ isset($property) && $property->key_highlights ? $property->key_highlights : '' }}</textarea>
            @error('key_highlights')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nearest Station -->
        <div class="form-group">
            <div class="rs_sub_title">Nearest Station</div>
            <input id="station_name" type="text" class="tagify-input" placeholder="Station Name"
                data-source="stations"
                data-values="{{ $property->nearest_school }}"
                data-id-value="{{ json_encode($allstations) }}"
                data-options="{{ json_encode(['maxTags' => 5, 'dropdownEnabled' => 1, 'maxItems' => 10, 'searchKeys' => ['name'], 'closeOnSelect' => false])  }}">
            <input type="hidden" name="nearest_station" value="{{ $property->nearest_station }}" class="hidden-input" required>
            @error('nearest_station')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nearest School -->
        <div class="form-group">
            <div class="rs_sub_title">Nearest School</div>
            <input id="school_name" type="text" class="tagify-input" placeholder="School Name"
                data-source="schools"
                data-values="{{ $property->nearest_school }}"
                data-id-value="{{ json_encode($allschools) }}"
                data-options="{{ json_encode(['maxTags' => 5, 'dropdownEnabled' => 1, 'maxItems' => 10, 'searchKeys' => ['name'], 'closeOnSelect' => false])  }}">
            <input type="hidden" name="nearest_school" value="{{ $property->nearest_schools }}" class="hidden-input" required>
            @error('nearest_school')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group">
            <div class="rs_sub_title">Nearest Religious Places (Distance in KM)</div>
            @php
                // If nearest_religious_places is not null, decode the JSON and use it; otherwise, use empty values
                $religiousPlaces =
                    isset($property) && $property->nearest_religious_places
                        ? json_decode($property->nearest_religious_places)
                        : ['masjid' => '', 'church' => '', 'mandir' => ''];
            @endphp
            <input type="number" pattern="[0-9]" class="form-control mt-2" inputmode="numeric"
                name="nearest_religious_places[masjid]" value="{{ $religiousPlaces->masjid ?? '' }}"
                placeholder="Masjid" required>
            <input type="number" pattern="[0-9]" class="form-control mt-2" inputmode="numeric"
                name="nearest_religious_places[church]" value="{{ $religiousPlaces->church ?? '' }}"
                placeholder="Church" required>
            <input type="number" pattern="[0-9]" class="form-control mt-2" inputmode="numeric"
                name="nearest_religious_places[mandir]" value="{{ $religiousPlaces->mandir ?? '' }}"
                placeholder="Mandir" required>
            @error('nearest_religious_places')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group">
            <label for="useful_information">Useful Information</label>
            <input type="text" name="useful_information" id="useful_information" class="form-control"
                value="{{ isset($property) && $property->useful_information ? $property->useful_information : '' }}"
                required>
            @error('useful_information')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>


@endif