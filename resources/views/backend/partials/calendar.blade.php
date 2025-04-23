{{-- resources/views/partials/calendar.blade.php --}}
<div id="calendar"></div>

<!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="eventForm">
                <div class="modal-header">
                    <h5 class="modal-title">Create / Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Subject" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <input type="text" name="type" class="form-control" placeholder="e.g. Meeting">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub-Type</label>
                            <input type="text" name="sub_type" class="form-control" placeholder="e.g. Planning">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Office</label>
                            <input type="text" name="office" class="form-control" placeholder="Office name">
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
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Diary Owner</label>
                            <input type="text" name="diary_owner" class="form-control" placeholder="Owner name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">On Behalf Of</label>
                            <input type="text" name="on_behalf_of" class="form-control" placeholder="e.g. Client">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Meeting location">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reminder</label>
                            <input type="text" name="reminder" class="form-control" placeholder="e.g. 30 minutes">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date &amp; Time</label>
                            <input type="datetime-local" name="start_datetime" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date &amp; Time</label>
                            <input type="datetime-local" name="end_datetime" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Repeat</label>
                            <select name="repeat" class="form-select">
                                <option value="" selected>None</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Occurrences</label>
                            <input type="number" name="repeat_until" class="form-control" min="1"
                                placeholder="How many times">
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
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: '{{ route("backend.events.index") }}',
                editable: true,
                selectable: true,
                select: function (info) {
                    $("input[name='start_datetime']").val(info.startStr + 'T09:00');
                    $("input[name='end_datetime']").val(info.startStr + 'T10:00');
                    new bootstrap.Modal($('#eventModal')).show();
                },
                eventDrop: function (info) {
                    $.post(`/calendar/events/update/${info.event.id}`, {
                        _token: '{{ csrf_token() }}',
                        start_datetime: moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
                        end_datetime: moment(info.event.end).format('YYYY-MM-DD HH:mm:ss')
                    });
                },
                eventClick: function (info) {
                    if (confirm('Delete this event?')) {
                        $.post(`/calendar/events/delete/${info.event.id}`, {
                            _token: '{{ csrf_token() }}'
                        }, function () {
                            calendar.refetchEvents();
                        });
                    }
                }
            });
            calendar.render();

            $('#eventForm').on('submit', function (e) {
                e.preventDefault();
                $.post('/calendar/store', $(this).serialize() + '&_token={{ csrf_token() }}', function () {
                    calendar.refetchEvents();
                    bootstrap.Modal.getInstance($('#eventModal')).hide();
                    $('#eventForm')[0].reset();
                });
            });
        });
    </script>
@endpush