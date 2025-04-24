{{-- Hidden div for property ID --}}
<div id="hidden-property-id" class="d-none" data-property-id="{{ $propertyId }}"></div>


{{-- Compliance Types and Records --}}
<div class="dynamic_div">
    @foreach ($complianceTypes as $type)
        @php $heading = strtoupper(str_replace('_', ' ', $type->alias)); @endphp

        {{-- Compliance Type and Add Button --}}
        <div class="flex justify_between align_center my-3 w-100">
            <div class="sub_title">{{ $heading }}</div>
            <x-backend.forms.button
                name="Add {{ $heading }}"
                type="secondary"
                size="sm"
                isOutline="false"
                isLinkBtn="true"
                link="#"
                onClick="openComplianceModal({{ $type->id }})"
            />
        </div>


        {{-- Compliance Records Table --}}
        @if ($complianceRecords->has($type->id))
            <div class="desktop_only">
                <h4>{{ $type->name }}</h4>
                <table class="table rs_table">
                    <thead>
                        <tr>
                            @php
                                // Collect unique headers from compliance details
                                $headers = $complianceRecords[$type->id]->flatMap(function ($record) {
                                    return $record->complianceDetails->pluck('key');
                                })->unique();
                            @endphp
                            @foreach ($headers as $header)
                                <th>{{ ucfirst(str_replace('_', ' ', $header)) }}</th>
                            @endforeach
                            <th>Expiry Date</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complianceRecords[$type->id] as $record)
                            @php $photo_ids = explode(',', $record->photos); @endphp
                            <tr>
                                @foreach ($headers as $header)
                                    @php
                                        $detail = $record->complianceDetails->firstWhere('key', $header);
                                    @endphp
                                    <td>{{ $detail ? $detail->value : '' }}</td>
                                @endforeach
                                <td>{{ formatDate($record->expiry_date) }}</td>
                                <td class="img_thumb">
                                    @if ($photo_ids)
                                        @foreach ($photo_ids as $photo_id)
                                            <img class="mb-2" width="100" src="{{ uploaded_asset($photo_id) }}" alt="image">
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="openComplianceModal({{ $type->id }}, {{ $record->id }})">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $record->id }})">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach
</div>


{{-- Modal for Adding Compliance --}}
<div class="modal" id="complianceModal" tabindex="-1" aria-labelledby="complianceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="complianceModalLabel">Add Compliance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="complianceModalBody">
                <!-- Dynamic form content will be loaded here via Ajax -->
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-auto">
                        <button type="submit" form="" class="btn btn_secondary" id="submitComplianceForm">Save</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn_outline_secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this compliance record?
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-auto">
                        <button type="button" class="btn btn_outline_secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
