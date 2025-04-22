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
    $religiousPlacesView = $property->nearest_religious_places;

    if (is_string($religiousPlacesView)) {
        $religiousPlacesView = json_decode($religiousPlacesView, true);
    } elseif (is_object($religiousPlacesView)) {
        $religiousPlacesView = (array) $religiousPlacesView;
    }

    $religiousPlacesView = $religiousPlacesView ?? ['masjid' => 'N/A', 'church' => 'N/A', 'mandir' => 'N/A'];
    
    @endphp
    <p><strong>Masjid:</strong> {{ $religiousPlacesView['masjid'] ?? 'N/A' }} km</p>
    <p><strong>Church:</strong> {{ $religiousPlacesView['church'] ?? 'N/A' }} km</p>
    <p><strong>Mandir:</strong> {{ $religiousPlacesView['mandir'] ?? 'N/A' }} km</p>


    <div class="rs_sub_title">Useful Information:</div>
    <p>{{ isset($property) && $property->useful_information ? $property->useful_information : 'N/A' }}</p>
   

@else
    <form id="propertyAccessiblityForm">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <input type="hidden" name="form_type" value="property_accessibility">

        <div class="form-group">
            <label for="access_arrangement">Access Arrangement Description</label>
            <textarea name="access_arrangement" id="access_arrangement" rows="6" placeholder="Write Description" class="form-control">{{ isset($property) && $property->access_arrangement ? $property->access_arrangement : '' }}</textarea>
        </div>

        <div class="form-group">
            <label for="key_highlights">Key Highlights Description</label>
            <textarea name="key_highlights" id="key_highlights" rows="6" placeholder="Write Description" class="form-control">{{ isset($property) && $property->key_highlights ? $property->key_highlights : '' }}</textarea>
        </div>

        <!-- Nearest Station -->
        <div class="form-group">
            <div class="rs_sub_title">Nearest Station</div>
            <input id="station_name" type="text" class="tagify-input" placeholder="Station Name"
                data-source="stations"
                data-values="{{ $property->nearest_station }}"
                data-id-value="{{ json_encode($allstations) }}"
                data-options="{{ json_encode(['maxTags' => 5, 'dropdownEnabled' => 1, 'maxItems' => 10, 'searchKeys' => ['name'], 'closeOnSelect' => false])  }}">
            <input type="hidden" name="nearest_station" value="{{ $property->nearest_station }}" class="hidden-input" required>
        </div>

        <!-- Nearest School -->
        <div class="form-group">
            <div class="rs_sub_title">Nearest School</div>
            <input id="school_name" type="text" class="tagify-input" placeholder="School Name"
                data-source="schools"
                data-values="{{ $property->nearest_school }}"
                data-id-value="{{ json_encode($allschools) }}"
                data-options="{{ json_encode(['maxTags' => 5, 'dropdownEnabled' => 1, 'maxItems' => 10, 'searchKeys' => ['name'], 'closeOnSelect' => false])  }}">
            <input type="hidden" name="nearest_school" value="{{ $property->nearest_school }}" class="hidden-input" required>
        </div>

        <div class="form-group">
            <div class="rs_sub_title mb-2">Nearest Religious Places (Distance in KM)</div>
            @php
                $religiousPlaces =
                    isset($property) && $property->nearest_religious_places
                        ? json_decode($property->nearest_religious_places)
                        : (object)['masjid' => '', 'church' => '', 'mandir' => ''];
            @endphp
        
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-mosque"></i></span>
                <div class="form-floating">
                    <input type="number" class="form-control" id="floatingMasjid" inputmode="numeric"
                        name="nearest_religious_places[masjid]" value="{{ $religiousPlaces->masjid ?? '' }}"
                        placeholder="Mosque(Masjid)" required>
                    <label for="floatingMasjid">Mosque(Masjid)</label>
                </div>
            </div>
        
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-church"></i></span>
                <div class="form-floating">
                    <input type="number" class="form-control" id="floatingChurch" inputmode="numeric"
                        name="nearest_religious_places[church]" value="{{ $religiousPlaces->church ?? '' }}"
                        placeholder="Church" required>
                    <label for="floatingChurch">Church(Girja Ghar)</label>
                </div>
            </div>
        
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-gopuram"></i></span> <!-- Use fa-hindu-temple if available -->
                <div class="form-floating">
                    <input type="number" class="form-control" id="floatingMandir" inputmode="numeric"
                        name="nearest_religious_places[mandir]" value="{{ $religiousPlaces->mandir ?? '' }}"
                        placeholder="Temple(Mandir)" required>
                    <label for="floatingMandir">Temple(Mandir)</label>
                </div>
            </div>
        </div>
        

        <div class="form-group">
            <label for="useful_information">Useful Information</label>
            <input type="text" name="useful_information" id="useful_information" class="form-control"
                value="{{ isset($property) && $property->useful_information ? $property->useful_information : '' }}"
                required>
        </div>
        <button type="submit" class="btn btn-success mt-3 float-end">Save Changes</button>
    </form>


@endif