{{-- Hidden div for property ID --}}
<div id="hidden-property-id" class="d-none" data-property-id="{{ $propertyId }}">
    @php
        // Debugging the propertyId (for development purposes)
        echo '<pre>';
        var_dump($propertyId);
        echo '</pre>';
    @endphp
</div>

{{-- Show table only if there is data --}}
@if($ownerGroups->isNotEmpty())

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Sr No.</th>
            <th>Group Name</th>
            <th>Purchase Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ownerGroups as $index => $ownerGroup)
            <!-- Main Row -->
            <tr>
                <!-- Sr No -->
                <td>{{ $index + 1 }}</td>

                <!-- Group Name -->
                <td>
                    @php
                        $contacts = $ownerGroup->ownerGroupContacts->pluck('contact.full_name')->toArray();
                        $groupName = count($contacts) > 2
                            ? implode(' & ', array_slice($contacts, 0, 2)) . ' and others'
                            : implode(' & ', $contacts);
                    @endphp
                    <span class="group-name" style="cursor: pointer;" data-toggle="collapse" data-target="#details-{{ $ownerGroup->id }}" aria-expanded="false" aria-controls="details-{{ $ownerGroup->id }}">
                        {{ $groupName }}
                    </span>
                </td>

                <!-- Purchase Date -->
                <td>{{ $ownerGroup->purchased_date ?? 'N/A' }}</td>

                <!-- Status -->
                <td>{{ ucfirst($ownerGroup->status ?? 'unknown') }}</td>

                <!-- Action -->
                <td>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-outline-warning editNote editForm me-1" title="Edit Owner Group" data-url="{{ route('admin.owner-groups.edit', $ownerGroup->id) }}">
                            <i class="bi bi-pencil">Edit</i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger me-1" title="Delete Owner Group" onclick="confirmModal('{{ route('admin.owner-groups.delete_group', $ownerGroup->id) }}', responseHandler)">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    </div>
                </td>
            </tr>

            <!-- Expandable Row for Contact Details -->
            <tr class="collapse" id="details-{{ $ownerGroup->id }}">
                <td colspan="5">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ownerGroup->ownerGroupContacts as $contactIndex => $contact)
                                <tr>
                                    <!-- Sr No -->
                                    <td>{{ $contactIndex + 1 }}</td>

                                    <!-- Name -->
                                    <td>
                                        {{ $contact->contact->full_name }}
                                        @if($contact->is_main)
                                            <span class="badge text-bg-success">Main</span>
                                        @endif
                                    </td>

                                    <!-- Position -->
                                    <td>
                                        {{ optional($contact->contact->category)->name ?? 'N/A' }}
                                    </td>

                                    <!-- Phone -->
                                    <td>{{ $contact->contact->phone }}</td>

                                    <!-- Email -->
                                    <td>{{ $contact->contact->email }}</td>

                                    <!-- City -->
                                    <td>{{ $contact->contact->city }}</td>

                                    <!-- Actions -->
                                    <td>
                                        @if(!$contact->is_main)
                                        <button class="btn btn-sm btn_secondary" onclick="setAsMain({{ $contact->id }}, {{ $ownerGroup->id }})">
                                            Set as Main
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- JavaScript functions -->
<script>

    function setAsMain(contactId, groupId) {
        if (confirm('Are you sure you want to set this contact as the main contact?')) {
            // The URL for the update
            var actionUrlTemplate = "{{ route('admin.owner-groups.updateMain', ['id' => ':groupId']) }}";

            var actionUrl = actionUrlTemplate.replace(':groupId', groupId);

            // Get the CSRF token dynamically
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Prepare the form data
            var formData = new FormData();
            formData.append('owner_group_id', groupId);
            formData.append('contact_id', contactId);
            formData.append('_token', csrfToken); // CSRF token for security

            // Perform the AJAX request directly with the URL and form data
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: formData,
                processData: false, // Important: don't process the data
                contentType: false, // Important: let jQuery handle the content type
                success: function(response) {
                    // Handle the response upon success or failure
                    if (response.status) {
                        toastr.success(response.notification, 'Success');
                        // Optionally reload the page or update the UI here
                        setTimeout(function() {
                            location.reload(); // Reload the page after 1 second
                        }, 1000);
                    } else {
                        toastr.error(response.notification, 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error); // Handle any errors that may occur
                }
            });
        }
    }



    // function setAsMain(contactId, groupId) {
    //     if (confirm('Are you sure you want to set this contact as the main contact?')) {
    //         // Perform the action via AJAX or redirect
    //         alert('Contact ID: ' + contactId + ' set as Main for Group ID: ' + groupId);
    //     }
    // }
</script>



@else
    <p>No data available</p>
@endif
