{{-- resources/views/partials/calendar.blade.php --}}
<div id="calendar"></div>

<!-- Modal -->
<div class="modal fade" id="eventModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="eventForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create / Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Master fields (updated) --}}
                        <div class="col-md-6">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="Subject" required>
                            <div class="text-danger" data-error-for="title"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type *</label>
                            <select name="type_id" id="type_id" class="form-select" required>
                                <option value="">— Select Type —</option>
                                @foreach(\App\Models\EventType::orderBy('name')->get() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger" data-error-for="type_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub-Type *</label>
                            <select name="sub_type_id" id="sub_type_id" class="form-select" required>
                                <option value="">— Select Sub-Type —</option>
                                {{-- Options will be filled via AJAX when a Type is chosen --}}
                            </select>
                            <div class="text-danger" data-error-for="sub_type_id"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Office</label>
                            <input type="text" name="office" class="form-control" placeholder="Office name">
                            <div class="text-danger" data-error-for="office"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="" selected>— Select —</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Pending">Pending</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Rescheduled">Rescheduled</option>
                            </select>
                            <div class="text-danger" data-error-for="status"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Diary Owner</label>
                            <input type="text" name="diary_owner" class="form-control" placeholder="Owner name">
                            <div class="text-danger" data-error-for="diary_owner"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">On Behalf Of</label>
                            <input type="text" name="on_behalf_of" class="form-control" placeholder="e.g. Client">
                            <div class="text-danger" data-error-for="on_behalf_of"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Meeting location">
                            <div class="text-danger" data-error-for="location"></div>
                        </div>                        

                        {{-- Instance fields (unchanged except error placeholders) --}}
                        <div class="col-md-6">
                            <label class="form-label">Start Date &amp; Time *</label>
                            <input type="datetime-local" name="start_datetime" class="form-control" required>
                            <div class="text-danger" data-error-for="start_datetime"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date &amp; Time *</label>
                            <input type="datetime-local" name="end_datetime" class="form-control" required>
                            <div class="text-danger" data-error-for="end_datetime"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reminder</label>
                            <input type="text" name="reminder" class="form-control" placeholder="e.g. 30 minutes">
                            <div class="text-danger" data-error-for="reminder"></div>
                        </div>

                        {{-- Recurrence fields (master) with error placeholders --}}
                        <div class="col-md-6">
                            <label class="form-label">Repeat</label>
                            <select id="repeatSelect" name="repeat" class="form-select">
                                <option value="none" selected>None</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                            <div class="text-danger" data-error-for="repeat"></div>
                        </div>
                        <!-- INTERVAL (Every N days/weeks/months) -->
                        <div class="col-md-6" id="intervalContainer">
                            <label class="form-label">Interval</label>
                            <input type="number" name="repeat_interval" class="form-control" min="1" value="1"
                                placeholder="Every N days/weeks/months">
                            <div class="text-danger" data-error-for="repeat_interval"></div>
                        </div>

                        <!-- REPEAT UNTIL DATE -->
                        <div class="col-md-6" id="repeatUntilDateContainer">
                            <label class="form-label">Repeat Until</label>
                            <input type="date" name="repeat_until_date" class="form-control">
                            <div class="text-danger" data-error-for="repeat_until_date"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Add any notes…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Hidden for update; fill in via JS if needed -->
                    <input type="hidden" name="event_id" value="">
                    <input type="hidden" name="instance_id" value="">
                    <input type="hidden" name="master_id" value="">
                    <input type="hidden" name="form_action" value="">      
                    <input type="hidden" name="original_start" value="">
                    <input type="hidden" name="original_end"   value="">
                    {{-- Submit button --}} 
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let repeatSelect         = document.getElementById('repeatSelect');
            let intervalContainer    = document.getElementById('intervalContainer');
            let repeatUntilContainer = document.getElementById('repeatUntilDateContainer');

            // Initialize Bootstrap 5 modal instance once
            var modalEl = document.getElementById('eventModal');
            var eventModal = new bootstrap.Modal(modalEl);
            
            // 1) The helper that enforces repeat_until_date ≥ start_datetime
            /*function updateRepeatUntilMin() {
                const $start = $('input[name="start_datetime"]');
                const $until = $('input[name="repeat_until_date"]');
                const startVal = $start.val(); // e.g. "2025-06-05T09:00"

                if (!startVal) {
                $until.removeAttr('min');
                return;
                }

                // Extract just the date portion ("YYYY-MM-DD")
                const minDate = startVal.split('T')[0];

                // Set min= on the “Repeat Until” field
                $until.attr('min', minDate);

                // If the current value is earlier, bump it up
                if ($until.val() && $until.val() < minDate) {
                $until.val(minDate);
                }
                // console.log('✏️[updateRepeatUntilMin] Set min date for repeat_until_date:', minDate);
            }*/

            function updateRepeatUntilMin() {
                const $start = $('input[name="start_datetime"]');
                const $until = $('input[name="repeat_until_date"]');
                const startVal = $start.val(); // e.g. "2025-06-05T09:00"

                if (!startVal) {
                // No start → no restriction
                $until.removeAttr('min');
                return;
                }

                // Extract the "YYYY-MM-DD" portion
                const [datePart] = startVal.split('T'); // e.g. "2025-06-05"

                // Convert to a Date object, add one day, then format back to "YYYY-MM-DD"
                const dt = new Date(datePart);
                dt.setDate(dt.getDate() + 1);
                const year  = dt.getFullYear();
                const month = String(dt.getMonth() + 1).padStart(2, '0');
                const day   = String(dt.getDate()).padStart(2, '0');
                const minDatePlusOne = `${year}-${month}-${day}`;

                // Set that as the minimum selectable date
                $until.attr('min', minDatePlusOne);

                // If the existing “Repeat Until” is earlier than that, bump it up
                if ($until.val() && $until.val() < minDatePlusOne) {
                $until.val(minDatePlusOne);
                }
                console.log('✏️[updateRepeatUntilMin] Set min date for repeat_until_date:', minDatePlusOne);
            }

            function updateEndMin() {
                const $start = $('input[name="start_datetime"]');
                const $end   = $('input[name="end_datetime"]');
                const startVal = $start.val(); // e.g. "2025-06-05T09:00"

                if (!startVal) {
                // If no start, clear any min restriction on End
                $end.removeAttr('min');
                return;
                }

                // Set the same string as min on End (so user cannot pick earlier)
                $end.attr('min', startVal);

                // If current End < Start, bump it to equal Start
                if ($end.val() && $end.val() < startVal) {
                $end.val(startVal);
                }
                console.log('✏️[updateEndMin] Set min date for end_datetime:', startVal);
            }

            function toggleRepeatFields() {
                if (repeatSelect.value === 'none') {
                    intervalContainer.style.display    = 'none';
                    repeatUntilContainer.style.display = 'none';
                    // Optionally clear their values:
                    $('input[name="repeat_interval"]').val(1);
                    $('input[name="repeat_until_date"]').val('');
                } else {
                    intervalContainer.style.display    = 'block';
                    repeatUntilContainer.style.display = 'block';
                    // Make sure the repeat until date can't be before the start date
                    updateRepeatUntilMin();
                }
            }

            // Initialize on page load
            toggleRepeatFields();

            // Whenever the user changes “Repeat” (None/Daily/Weekly/Monthly)
            repeatSelect.addEventListener('change', toggleRepeatFields);

            // 2) Fire when the start_datetime changes
            // Whenever the user edits Start Date & Time, re-apply the rule:
            $('input[name="start_datetime"]').on('change', function() {
                updateEndMin();
                updateRepeatUntilMin();
            });

            
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    right: 'prev,next today',
                    center: 'title',
                    left: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '{{ route("backend.events.index") }}',
                editable: true,
                selectable: true,
                select: function (info) {
                    // Prepare modal for “Create New”:
                    $('input[name="instance_id"]').val('');
                    $('input[name="master_id"]').val('');
                    $('input[name="form_action"]').val('create'); 
                    $('#eventForm')[0].reset();
                    $('.text-danger').remove();

                    // Pre-fill start/end times
                    $("input[name='start_datetime']").val(info.startStr + 'T09:00');
                    $("input[name='end_datetime']").val(info.startStr + 'T10:00');

                    // Default recurrence to none
                    $('select[name="repeat"]').val('none');
                    toggleRepeatFields(); // call to sync visibility
                    $('input[name="repeat_interval"]').val(1);
                    $('input[name="repeat_until_date"]').val(null);
                    $('#sub_type_id').html('<option value="">— Select Sub-Type —</option>');
                    eventModal.show();
                },
                eventClick: function (info) {
                    // When clicking an existing instance, load data into modal to “Edit Instance”
                    var inst = info.event.extendedProps;
                    var evID = info.event.id; // instance_id
                    var typeId     = inst.type_id;              // numeric (because we returned type_id at top level)
                    var subTypeId  = inst.sub_type_id;          // numeric (because we returned sub_type_id at top level)

                    // Clear old error messages
                    $('.text-danger').remove();

                    // Fill master/instance hidden fields
                    $('input[name="instance_id"]').val(evID);
                    $('input[name="master_id"]').val(inst.master_id);

                    // 2d) Because you clicked an existing instance, we’ll be at least in “updateInstance” mode.
                    //     But if you want the user to be able to switch to “edit series,” the form_action can become “updateMaster.”
                    //     For simplicity, let’s assume that opening via click means “updateInstance” by default.
                    $('input[name="form_action"]').val('updateMaster');
                    
                    // 2e) Store original start/end for comparison
                    $('input[name="original_start"]').val(
                        moment(info.event.start).format('YYYY-MM-DD HH:mm:ss')
                    );
                    $('input[name="original_end"]').val(
                        moment(info.event.end).format('YYYY-MM-DD HH:mm:ss')
                    );
                        
                    // Load master fields into form
                    $('input[name="title"]').val(info.event.title);
                    // $('input[name="type"]').val(inst.type);
                    // $('input[name="sub_type"]').val(inst.sub_type);
                    $('input[name="office"]').val(inst.office);
                    $('select[name="status"]').val(inst.status);
                    $('input[name="diary_owner"]').val(inst.diary_owner);
                    $('input[name="on_behalf_of"]').val(inst.on_behalf_of);
                    $('input[name="location"]').val(inst.location);
                    $('input[name="reminder"]').val(inst.reminder);
                    $('textarea[name="description"]').val(inst.description);

                    // Instance fields
                    $("input[name='start_datetime']").val(moment(info.event.start)
                        .format('YYYY-MM-DDTHH:mm'));
                    $("input[name='end_datetime']").val(moment(info.event.end)
                        .format('YYYY-MM-DDTHH:mm'));

                    // Recurrence fields (master)
                    // $('select[name="repeat"]').val(inst.repeat || 'none');
                    // var repeatSelect = document.getElementById('repeatSelect'); // assuming you gave it this ID
                    // repeatSelect.value = inst.repeat || 'none'; // set the value
                    document.getElementById('repeatSelect').value = inst.repeat || 'none';
                    toggleRepeatFields(); // call to sync visibility
                    updateEndMin(); // Ensure end time is valid
                    $('input[name="repeat_interval"]').val(inst.repeat_interval || 1);
                    $('input[name="repeat_until_date"]').val(inst.repeat_until_date || null);

                    // 5) ————————————————————————————————
                    //     PRESELECT “Type” USING type_id
                    //  ————————————————————————————————
                    // Clear old Sub-Type options (so we don’t stack them)
                    $('#sub_type_id').html('<option value="">— Select Sub-Type —</option>');

                    // If there is a valid typeId, set it & then load its sub-types
                    if (typeId) {
                        // (A) Preselect Type dropdown
                        // $('#type_id').val(typeId);

                        // Fill the Type dropdown and then load SubTypes
                        $('#type_id').val(typeId).trigger('change');

                        // After subtypes load, set the selected subtype
                        // Because AJAX is async, we wait for the callback:
                        $.getJSON('/admin/api/event-sub-types/' + typeId, function (data) {
                            $.each(data, function (id, name) {
                                $('#sub_type_id').append(`<option value="${id}">${name}</option>`);
                            });
                            $('#sub_type_id').val(subTypeId);
                        });
                    } else {
                        // If no typeId, just clear the Type dropdown entirely
                        $('#type_id').val('');
                    }
                    
                    eventModal.show();
                },
                eventDrop: function (info) {
                    // When user drags to reschedule an instance
                    var instId = info.event.id;
                    var newStart = moment(info.event.start).format('YYYY-MM-DD HH:mm:ss');
                    var newEnd = moment(info.event.end).format('YYYY-MM-DD HH:mm:ss');

                    $.ajax({
                        url: '{{ route("backend.events.updateInstance", "") }}/' + instId,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            start_datetime: newStart,
                            end_datetime: newEnd,
                            form_action: 'updateInstance'
                        },
                        success: function () {
                            calendar.refetchEvents();
                        },
                        error: function () {
                            alert('Unable to update instance.');
                            info.revert();
                        }
                    });
                },
                events: '{{ route("backend.events.index") }}'
            });

            calendar.render();

            $('#type_id').on('change', function () {
                var typeId = $(this).val();
                var $sub = $('#sub_type_id');

                // Clear existing options
                $sub.html('<option value="">— Select Sub-Type —</option>');

                if (!typeId) {
                    return; // no type chosen
                }

                $.getJSON('/admin/api/event-sub-types/' + typeId, function (data) {
                    // data is an object {id: name, ...}
                    $.each(data, function (id, name) {
                        $sub.append(`<option value="${id}">${name}</option>`);
                    });
                });
            });

            // Handle form submission: could be “new master + instances” or “update instance + maybe update master”
            $('#eventForm').on('submit', function (e) {
                e.preventDefault();
                $('.text-danger').remove();

                // Read hidden IDs
                var instanceId = $('input[name="instance_id"]').val();
                var masterId = $('input[name="master_id"]').val();
                var formActionMode = $('input[name="form_action"]').val();
                var originalStart  = $('input[name="original_start"]').val();
                var originalEnd    = $('input[name="original_end"]').val();

                
                console.log('✏️[formSubmit] form_action=', formActionMode,
                            'instanceId=', instanceId,
                            'masterId=', masterId,
                            'original_start=', originalStart,
                            'original_end=', originalEnd);
                            
                // Collect form data
                var formData = $(this).serializeArray();
                var payload = {};
                formData.forEach(function (f) { payload[f.name] = f.value; });

                // If instanceId is present → update that single instance (drag/drop or manual edit)
                if (instanceId && formActionMode === 'updateInstance') {
                    // Only update instance’s start/end (we can allow editing other details if desired)
                    $.ajax({
                        url: '{{ route("backend.events.updateInstance", "") }}/' + instanceId,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            start_datetime: payload.start_datetime,
                            end_datetime: payload.end_datetime,
                            // You could also send originalStart/originalEnd for auditing:
                            original_start:  originalStart,
                            original_end:    originalEnd
                        },
                        success: function () {
                            eventModal.hide();
                            $('#eventForm')[0].reset();
                            calendar.refetchEvents();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let errs = xhr.responseJSON.errors;
                                $.each(errs, function (key, msgs) {
                                    var $input = $('[name="' + key + '"]');
                                    if ($input.length) {
                                        $input.after('<div class="text-danger">' + msgs[0] + '</div>');
                                    }
                                });
                            } else {
                                alert('Error updating instance.');
                            }
                        }
                    });
                }
                // Else if masterId is present → user clicked an existing instance but may have changed recurrence or master data
                else if (masterId && formActionMode === 'updateMaster') {
                    $.ajax({
                        url: '{{ route("backend.events.updateMaster", "") }}/' + masterId,
                        method: 'PUT',
                        data: payload,
                        success: function () {
                            eventModal.hide();
                            $('#eventForm')[0].reset();
                            calendar.refetchEvents();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let errs = xhr.responseJSON.errors;
                                $.each(errs, function (key, msgs) {
                                    var $input = $('[name="' + key + '"]');
                                    if ($input.length) {
                                        $input.after('<div class="text-danger">' + msgs[0] + '</div>');
                                    }
                                });
                            } else {
                                alert('Error updating series.');
                            }
                        }
                    });
                }
                // Otherwise → new master + instances
                else {
                    $.ajax({
                        url: '{{ route("backend.events.store") }}',
                        method: 'POST',
                        data: payload,
                        success: function () {
                            eventModal.hide();
                            $('#eventForm')[0].reset();
                            calendar.refetchEvents();
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let errs = xhr.responseJSON.errors;
                                $.each(errs, function (key, msgs) {
                                    var $input = $('[name="' + key + '"]');
                                    if ($input.length) {
                                        $input.after('<div class="text-danger">' + msgs[0] + '</div>');
                                    }
                                });
                            } else {
                                alert('Error saving event.');
                            }
                        }
                    });
                }
            });
        });

        // $('#eventForm').on('submit', function (e) {
        //     e.preventDefault();
        //     $.post('{{ route("backend.events.store") }}', $(this).serialize() + '&_token={{ csrf_token() }}', function () {
        //         calendar.refetchEvents();
        //         bootstrap.Modal.getInstance($('#eventModal')).hide();
        //         $('#eventForm')[0].reset();
        //     });
        // });

    </script>

@endpush