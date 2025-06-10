{{-- resources/views/partials/calendar.blade.php --}}
<div id="calendar"></div>

<!-- Recurrence Rule Builder Modal -->
<div class="modal fade" id="rruleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Recurrence Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Frequency -->
                <div class="mb-3">
                    <label class="form-label">Frequency</label>
                    <select id="freqSelect" class="form-select">
                        <option value="DAILY">Daily</option>
                        <option value="WEEKLY">Weekly</option>
                        <option value="MONTHLY">Monthly</option>
                        <option value="YEARLY">Yearly</option>
                    </select>
                </div>

                <!-- Interval -->
                <div class="mb-3">
                    <label class="form-label">Repeat Every</label>
                    <div class="input-group">
                        <input type="number" id="intervalInput" class="form-control" min="1" value="1">
                        <span class="input-group-text" id="intervalLabel">day(s)</span>
                    </div>
                </div>

                <!-- By Day (for WEEKLY) -->
                <div class="mb-3 d-none" id="byDayContainer">
                    <label class="form-label">On Days of Week</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="MO" id="chkMO">
                        <label class="form-check-label" for="chkMO">Mon</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="TU" id="chkTU">
                        <label class="form-check-label" for="chkTU">Tue</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="WE" id="chkWE">
                        <label class="form-check-label" for="chkWE">Wed</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="TH" id="chkTH">
                        <label class="form-check-label" for="chkTH">Thu</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="FR" id="chkFR">
                        <label class="form-check-label" for="chkFR">Fri</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="SA" id="chkSA">
                        <label class="form-check-label" for="chkSA">Sat</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="SU" id="chkSU">
                        <label class="form-check-label" for="chkSU">Sun</label>
                    </div>
                </div>

                <!-- By Month Day vs. By Ordinal Day of Month (example for “2nd Tuesday”) -->
                <div class="mb-3 d-none" id="byOrdinalContainer">
                    <label class="form-label">Monthly On</label>
                    <div class="row">
                        <div class="col-4">
                            <select id="bySetPos" class="form-select">
                                <option value="1">First</option>
                                <option value="2">Second</option>
                                <option value="3">Third</option>
                                <option value="4">Fourth</option>
                                <option value="-1">Last</option>
                            </select>
                        </div>
                        <div class="col-8">
                            <select id="byDayOrdinal" class="form-select">
                                <option value="MO">Monday</option>
                                <option value="TU">Tuesday</option>
                                <option value="WE">Wednesday</option>
                                <option value="TH">Thursday</option>
                                <option value="FR">Friday</option>
                                <option value="SA">Saturday</option>
                                <option value="SU">Sunday</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- End Conditions -->
                <div class="mb-3">
                    <label class="form-label">End</label>
                    <select id="endTypeSelect" class="form-select">
                        <option value="NEVER">Never</option>
                        <option value="AFTER">After N Occurrences</option>
                        <option value="BYDATE">By Date</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="endAfterContainer">
                    <label class="form-label">Occurrences</label>
                    <input type="number" id="endAfterCount" class="form-control" min="1" value="1">
                </div>

                <div class="mb-3 d-none" id="endByDateContainer">
                    <label class="form-label">End Date</label>
                    <input type="date" id="endByDateInput" class="form-control">
                </div>

                <!-- Exclusion Dates (e.g. public holidays) -->
                <div class="mb-3">
                    <label class="form-label">Exclude Specific Dates</label>
                    <div id="exdateList" class="mb-2">
                        <!-- We'll dynamically add date inputs here -->
                    </div>
                    <button type="button" id="addExdateBtn" class="btn btn-sm btn-outline-secondary">
                        + Add Exclusion Date
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveRRuleBtn">
                    Save Recurrence
                </button>
            </div>
        </div>
    </div>
</div>


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

                        <div class="mb-3">
                            <label class="form-label">Recurrence</label>
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="editRRuleBtn">
                                Set Recurrence…
                            </button>
                            <div id="rruleSummary" class="mt-2 text-muted"></div>
                            <!-- Hidden field to store the serialized RRULE string -->
                            <textarea name="rrule" id="rruleInput" class="d-none"></textarea>
                        </div>

                        <!-- We’ll also keep a hidden JSON field for exdates -->
                        <textarea name="exdates" id="exdatesInput" class="d-none">
                        </textarea>

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
                    <input type="hidden" name="original_end" value="">
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
    <script src="https://cdn.jsdelivr.net/npm/rrule@2.7.1/dist/es5/rrule.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {



            $(function () {
                // Shortcuts to elements:
                const $rruleModal = $('#rruleModal');
                const $freqSelect = $('#freqSelect');
                const $intervalInput = $('#intervalInput');
                const $intervalLabel = $('#intervalLabel');
                const $byDayC = $('#byDayContainer');
                const $byOrdinalC = $('#byOrdinalContainer');
                const $endType = $('#endTypeSelect');
                const $endAfterC = $('#endAfterContainer');
                const $endByDateC = $('#endByDateContainer');
                const $endAfterCnt = $('#endAfterCount');
                const $endByDate = $('#endByDateInput');
                const $exdateList = $('#exdateList');

                // Utility: render the human‐readable summary in #rruleSummary
                function renderRRuleSummary(rruleString, exdatesArray) {
                    if (!rruleString) {
                        $('#rruleSummary').text('No recurrence');
                        return;
                    }
                    try {
                        const rule = RRule.fromString(rruleString);
                        const human = rule.toText(); // e.g. “Every week on Monday, Wednesday until December 31, 2025”
                        $('#rruleSummary').text(human);
                    }
                    catch (e) {
                        $('#rruleSummary').text('Invalid recurrence rule');
                    }
                }

                // When the user clicks “Set Recurrence…”
                $('#editRRuleBtn').on('click', function () {
                    // 1) If there is already an rrule string in the hidden <textarea>, parse it and fill the fields
                    const existingRRule = $('#rruleInput').val().trim();
                    const existingExdates = $('#exdatesInput').val().trim()
                        ? JSON.parse($('#exdatesInput').val())
                        : [];

                    if (existingRRule) {
                        try {
                            const rule = RRule.fromString(existingRRule);
                            // Fill frequency & interval
                            $freqSelect.val(rule.options.freq === RRule.YEARLY ? 'YEARLY'
                                : rule.options.freq === RRule.MONTHLY ? 'MONTHLY'
                                    : rule.options.freq === RRule.WEEKLY ? 'WEEKLY'
                                        : 'DAILY');
                            $intervalInput.val(rule.options.interval);

                            // Show/hide relevant sections
                            onFrequencyChange();

                            // For “WEEKLY” → check appropriate weekdays
                            if (rule.options.freq === RRule.WEEKLY && rule.options.byweekday) {
                                const days = rule.options.byweekday;
                                // rule.options.byweekday is an array of Weekday instances (e.g. [RRule.MO, RRule.WE])
                                $('input[type="checkbox"][id^="chk"]').prop('checked', false);
                                days.forEach(d => {
                                    // day.weekday returns 0=MO,1=TU,…6=SU
                                    const idMap = ['chkMO', 'chkTU', 'chkWE', 'chkTH', 'chkFR', 'chkSA', 'chkSU'];
                                    const chkId = idMap[d.weekday];
                                    $('#' + chkId).prop('checked', true);
                                });
                            }

                            // For “MONTHLY” → check if bymonthday (e.g. day 15) or bysetpos/byday
                            if (rule.options.freq === RRule.MONTHLY) {
                                if (rule.options.bymonthday) {
                                    // You’d need another UI control to let user pick day-of-month directly
                                    // (not shown above), e.g. <input type="number" id="bymonthday" min="1" max="31">
                                    $('#bymonthday').val(rule.options.bymonthday[0]);
                                    $('#bymonthdayContainer').show();
                                    $byOrdinalC.hide();
                                }
                                else if (rule.options.bysetpos && rule.options.byweekday) {
                                    $byOrdinalC.show();
                                    $('#bymonthdayContainer').hide();
                                    $('#bySetPos').val(rule.options.bysetpos[0]);        // e.g. 2 for “Second”
                                    $('#byDayOrdinal').val(rule.options.byweekday[0].weekday); // e.g. “WE” → 2
                                }
                            }

                            // End conditions:
                            if (rule.options.count) {
                                $endType.val('AFTER');
                                $endAfterC.show();
                                $endByDateC.hide();
                                $endAfterCnt.val(rule.options.count);
                            }
                            else if (rule.options.until) {
                                $endType.val('BYDATE');
                                $endAfterC.hide();
                                $endByDateC.show();
                                // rule.options.until is a JS Date object → format to "YYYY-MM-DD"
                                const u = rule.options.until;
                                const y = u.getFullYear();
                                const m = String(u.getMonth() + 1).padStart(2, '0');
                                const d = String(u.getDate()).padStart(2, '0');
                                $endByDate.val(`${y}-${m}-${d}`);
                            }
                            else {
                                $endType.val('NEVER');
                                $endAfterC.hide();
                                $endByDateC.hide();
                            }
                        }
                        catch (e) {
                            console.warn('Failed to parse existing RRule:', e);
                        }
                    }
                    else {
                        // No existing rrule → reset UI
                        $freqSelect.val('DAILY');
                        $intervalInput.val(1);
                        onFrequencyChange();
                        $endType.val('NEVER');
                        $endAfterC.hide();
                        $endByDateC.hide();
                        $exdateList.empty();
                    }

                    // 2) Populate exdates UI
                    $exdateList.empty();
                    if (existingExdates.length) {
                        existingExdates.forEach(d => {
                            addExdateRow(d);
                        });
                    }

                    $rruleModal.modal('show');
                });

                // Show/hide parts of the modal based on frequency
                function onFrequencyChange() {
                    const freq = $freqSelect.val();
                    // Update the “interval” label
                    let unitLabel = 'day(s)';
                    if (freq === 'WEEKLY') unitLabel = 'week(s)';
                    else if (freq === 'MONTHLY') unitLabel = 'month(s)';
                    else if (freq === 'YEARLY') unitLabel = 'year(s)';
                    $intervalLabel.text(unitLabel);

                    // Show/hide “byDay” for WEEKLY
                    if (freq === 'WEEKLY') {
                        $byDayC.removeClass('d-none');
                    } else {
                        $byDayC.addClass('d-none');
                        $('input[type="checkbox"][id^="chk"]').prop('checked', false);
                    }

                    // Show/hide “byOrdinal” for MONTHLY
                    if (freq === 'MONTHLY') {
                        $byOrdinalC.removeClass('d-none');
                    } else {
                        $byOrdinalC.addClass('d-none');
                        $('#bySetPos').val('1');
                        $('#byDayOrdinal').val('MO');
                    }
                }

                $freqSelect.on('change', onFrequencyChange);

                // Show/hide end condition fields
                $endType.on('change', function () {
                    const val = $(this).val();
                    if (val === 'AFTER') {
                        $endAfterC.removeClass('d-none');
                        $endByDateC.addClass('d-none');
                    } else if (val === 'BYDATE') {
                        $endAfterC.addClass('d-none');
                        $endByDateC.removeClass('d-none');
                    } else {
                        $endAfterC.addClass('d-none');
                        $endByDateC.addClass('d-none');
                    }
                });

                // Add a new Exclusion Date row
                $('#addExdateBtn').on('click', function () {
                    addExdateRow();
                });

                function addExdateRow(initialValue = '') {
                    const idx = $exdateList.children().length;
                    const html = `
                                              <div class="input-group mb-2" data-idx="${idx}">
                                                <input type="date" class="form-control exdateInput" value="${initialValue}">
                                                <button class="btn btn-outline-danger removeExdateBtn" type="button">
                                                  &times;
                                                </button>
                                              </div>`;
                    $exdateList.append(html);
                }

                // Remove a specific exdate row
                $exdateList.on('click', '.removeExdateBtn', function () {
                    $(this).closest('.input-group').remove();
                });

                // 3) When user clicks “Save Recurrence”
                $('#saveRRuleBtn').on('click', function () {
                    // Build options for RRule
                    const freq = $freqSelect.val(); // DAILY, WEEKLY, MONTHLY, YEARLY
                    const interval = parseInt($intervalInput.val()) || 1;
                    const options = {
                        freq: RRule[freq],
                        interval: interval,
                    };

                    // If WEEKLY → collect byweekday
                    if (freq === 'WEEKLY') {
                        const days = [];
                        $('input[id^="chk"]').each(function () {
                            if ($(this).prop('checked')) {
                                days.push(RRule[$(this).val()]);
                            }
                        });
                        if (days.length) {
                            options.byweekday = days;
                        }
                    }

                    // If MONTHLY and user picked an ordinal day
                    if (freq === 'MONTHLY') {
                        const setpos = parseInt($('#bySetPos').val());     // e.g. 2
                        const bydayVal = $('#byDayOrdinal').val();         // e.g. "TU"
                        options.bysetpos = setpos;
                        options.byweekday = [RRule[bydayVal]];
                    }

                    // End conditions
                    const endType = $endType.val();
                    if (endType === 'AFTER') {
                        options.count = parseInt($endAfterCnt.val()) || 1;
                    }
                    else if (endType === 'BYDATE') {
                        const untilRaw = $endByDate.val(); // "YYYY-MM-DD"
                        if (untilRaw) {
                            // Convert to JS date at 23:59:59 local time
                            const ut = new Date(untilRaw + 'T23:59:59');
                            options.until = ut;
                        }
                    }
                    // else “NEVER” → we leave options.count & options.until undefined

                    // Build the actual RRule
                    let rruleString = '';
                    try {
                        const rule = new RRule(options);
                        rruleString = rule.toString(); // e.g. "FREQ=WEEKLY;INTERVAL=1;BYDAY=MO,WE;COUNT=10"
                    } catch (e) {
                        alert('Failed to build recurrence rule: ' + e);
                        return;
                    }

                    // Collect exdates from UI
                    const exdates = [];
                    $exdateList.find('.exdateInput').each(function () {
                        const val = $(this).val();
                        if (val) {
                            exdates.push(val); // e.g. "2025-07-04"
                        }
                    });

                    // Write them back to the hidden form fields
                    $('#rruleInput').val(rruleString);
                    $('#exdatesInput').val(JSON.stringify(exdates));

                    // Also render the summary:
                    renderRRuleSummary(rruleString, exdates);

                    // Close the modal
                    $rruleModal.modal('hide');
                });
            });

            // Initialize Bootstrap 5 modal instance once
            var modalEl = document.getElementById('eventModal');
            var eventModal = new bootstrap.Modal(modalEl);

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
                const year = dt.getFullYear();
                const month = String(dt.getMonth() + 1).padStart(2, '0');
                const day = String(dt.getDate()).padStart(2, '0');
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
                const $end = $('input[name="end_datetime"]');
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

            // 2) Fire when the start_datetime changes
            // Whenever the user edits Start Date & Time, re-apply the rule:
            $('input[name="start_datetime"]').on('change', function () {
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
                    updateEndMin();
                    updateRepeatUntilMin();

                    $('#rruleInput').val('');
                    $('#exdatesInput').val('');
                    $('#rruleSummary').text('No recurrence');

                    $('#freqSelect').val('DAILY');
                    $('#intervalInput').val(1);
                    onFrequencyChange();
                    $endType.val('NEVER');
                    $endAfterC.hide();
                    $endByDateC.hide();
                    $exdateList.empty();

                    // Clear Type/Sub‐Type
                    $('#type_id').val('');
                    $('#sub_type_id').html('<option value="">— Select Sub-Type —</option>');

                    eventModal.show();
                },
                eventClick: function (info) {
                    // When clicking an existing instance, load data into modal to “Edit Instance”
                    var inst = info.event.extendedProps;
                    // If no recurrence → treat as a single
                    if (!inst.rrule) {
                        openSingleInstanceModal(info);
                        return;
                    }

                    // Otherwise, ask:
                    const choice = prompt(
                        'This is a recurring series. Choose:\n' +
                        '1 → Edit only this occurrence\n' +
                        '2 → Edit entire series\n' +
                        '3 → Edit this & future occurrences\n' +
                        '4 → Delete only this occurrence\n' +
                        '5 → Delete entire series\n' +
                        '6 → Delete this & future occurrences',
                        '1'
                    );

                    switch (choice) {
                        case '1':
                            openSingleInstanceModal(info);
                            break;
                        case '2':
                            openSeriesModal(info);
                            break;
                        case '3':
                            openSplitSeriesModal(info);
                            break;
                        case '4':
                            if (confirm('Cancel only this occurrence?')) {
                                $.post(
                                    '{{ route("backend.events.destroyInstance", "") }}/' + info.event.id,
                                    { _token: '{{ csrf_token() }}' },
                                    function () { calendar.refetchEvents(); }
                                );
                            }
                            break;
                        case '5':
                            if (confirm('Cancel the entire series?')) {
                                $.post(
                                    '{{ route("backend.events.cancelSeries", "") }}/' + inst.master_id,
                                    { _token: '{{ csrf_token() }}' },
                                    function () { calendar.refetchEvents(); }
                                );
                            }
                            break;
                        case '6':
                            if (confirm('Cancel this & future occurrences?')) {
                                $.post(
                                    '{{ route("backend.events.splitSeries", "") }}/' + info.event.id,
                                    $(/* serialize form for the “split” defaults… */).serialize(),
                                    function () { calendar.refetchEvents(); }
                                );
                            }
                            break;
                        default:
                            break;
                    }
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
        });

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
            var originalStart = $('input[name="original_start"]').val();
            var originalEnd = $('input[name="original_end"]').val();


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
                        original_start: originalStart,
                        original_end: originalEnd
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
            else if (formAction === 'splitSeries' && instanceId) {
                // Edit this & all following occurrences (split)
                $.ajax({
                    url: '{{ route("backend.events.splitSeries", "") }}/' + instanceId,
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
                            alert('Error splitting series.');
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

        // -------------------------------------------------
        // Helper to open the “Single Instance” modal
        // -------------------------------------------------
        function openSingleInstanceModal(info) {
            const inst = info.event.extendedProps;
            $('input[name="instance_id"]').val(info.event.id);
            $('input[name="master_id"]').val(inst.master_id);
            $('input[name="form_action"]').val('updateInstance');
            $('#eventForm')[0].reset();
            $('.text-danger').remove();

            $('input[name="title"]').val(info.event.title);
            $('input[name="office"]').val(inst.office);
            $('select[name="status"]').val(inst.status);
            $('input[name="diary_owner"]').val(inst.diary_owner);
            $('input[name="on_behalf_of"]').val(inst.on_behalf_of);
            $('input[name="location"]').val(inst.location);
            $('input[name="reminder"]').val(inst.reminder);
            $('textarea[name="description"]').val(inst.description);

            $('input[name="start_datetime"]').val(
                moment(info.event.start).format('YYYY-MM-DDTHH:mm')
            );
            $('input[name="end_datetime"]').val(
                moment(info.event.end).format('YYYY-MM-DDTHH:mm')
            );
            updateEndMin();
            updateRepeatUntilMin();

            // Fill recurrence fields from this instance’s master
            $('#rruleInput').val(inst.rrule || '');
            $('#exdatesInput').val(inst.exdates || '[]');
            renderRRuleSummary(inst.rrule, inst.exdates ? JSON.parse(inst.exdates) : []);

            // Hide editing recurrence in single mode
            $('#editRRuleBtn').hide();

            // Type / Sub‐Type
            $('#type_id').val(inst.type_id);
            $('#sub_type_id').html('<option value="">— Select Sub‐Type —</option>');
            if (inst.type_id) {
                $.getJSON('/admin/api/event-sub-types/' + inst.type_id, function (data) {
                    $.each(data, function (id, name) {
                        $('#sub_type_id').append(
                            $('<option>', { value: id }).text(name)
                        );
                    });
                    $('#sub_type_id').val(inst.sub_type_id);
                });
            }

            eventModal.show();
        }

        // -------------------------------------------------
        // Helper to open the “Entire Series” modal
        // -------------------------------------------------
        function openSeriesModal(info) {
            const inst = info.event.extendedProps;
            $('input[name="instance_id"]').val('');
            $('input[name="master_id"]').val(inst.master_id);
            $('input[name="form_action"]').val('updateMaster');
            $('#eventForm')[0].reset();
            $('.text-danger').remove();

            // Fill master‐level fields (title, office, etc.) from extendedProps
            $('input[name="title"]').val(inst.title);
            $('input[name="office"]').val(inst.office);
            $('select[name="status"]').val(inst.status);
            $('input[name="diary_owner"]').val(inst.diary_owner);
            $('input[name="on_behalf_of"]').val(inst.on_behalf_of);
            $('input[name="location"]').val(inst.location);
            $('input[name="reminder"]').val(inst.reminder);
            $('textarea[name="description"]').val(inst.description);

            // Use clicked instance’s datetime as the new “dtstart”
            $('input[name="start_datetime"]').val(
                moment(info.event.start).format('YYYY-MM-DDTHH:mm')
            );
            $('input[name="end_datetime"]').val(
                moment(info.event.end).format('YYYY-MM-DDTHH:mm')
            );
            updateEndMin();
            updateRepeatUntilMin();

            // Fill recurrence fields from master
            $('#rruleInput').val(inst.rrule || '');
            $('#exdatesInput').val(inst.exdates || '[]');
            renderRRuleSummary(inst.rrule, inst.exdates ? JSON.parse(inst.exdates) : []);

            $('#editRRuleBtn').show();

            // Type / Sub‐Type
            $('#type_id').val(inst.type_id);
            $('#sub_type_id').html('<option value="">— Select Sub‐Type —</option>');
            if (inst.type_id) {
                $.getJSON('/admin/api/event-sub-types/' + inst.type_id, function (data) {
                    $.each(data, function (id, name) {
                        $('#sub_type_id').append(
                            $('<option>', { value: id }).text(name)
                        );
                    });
                    $('#sub_type_id').val(inst.sub_type_id);
                });
            }

            eventModal.show();
        }

        // -------------------------------------------------
        // Helper to open the “Split Series” modal (edit this & future)
        // -------------------------------------------------
        function openSplitSeriesModal(info) {
            const inst = info.event.extendedProps;
            $('input[name="instance_id"]').val(info.event.id);
            $('input[name="master_id"]').val(inst.master_id);
            $('input[name="form_action"]').val('splitSeries');
            $('#eventForm')[0].reset();
            $('.text-danger').remove();

            // Copy most fields from the old master, but use clicked datetime as new dtstart
            $('input[name="title"]').val(inst.title);
            $('input[name="office"]').val(inst.office);
            $('select[name="status"]').val(inst.status);
            $('input[name="diary_owner"]').val(inst.diary_owner);
            $('input[name="on_behalf_of"]').val(inst.on_behalf_of);
            $('input[name="location"]').val(inst.location);
            $('input[name="reminder"]').val(inst.reminder);
            $('textarea[name="description"]').val(inst.description);

            $('input[name="start_datetime"]').val(
                moment(info.event.start).format('YYYY-MM-DDTHH:mm')
            );
            $('input[name="end_datetime"]').val(
                moment(info.event.end).format('YYYY-MM-DDTHH:mm')
            );
            updateEndMin();
            updateRepeatUntilMin();

            // We need to let them define a brand-new recurrence rule for the split series
            $('#rruleInput').val(inst.rrule || '');
            $('#exdatesInput').val(inst.exdates || '[]');
            renderRRuleSummary(inst.rrule, inst.exdates ? JSON.parse(inst.exdates) : []);

            $('#editRRuleBtn').show();

            // Type / Sub‐Type
            $('#type_id').val(inst.type_id);
            $('#sub_type_id').html('<option value="">— Select Sub‐Type —</option>');
            if (inst.type_id) {
                $.getJSON('/admin/api/event-sub-types/' + inst.type_id, function (data) {
                    $.each(data, function (id, name) {
                        $('#sub_type_id').append(
                            $('<option>', { value: id }).text(name)
                        );
                    });
                    $('#sub_type_id').val(inst.sub_type_id);
                });
            }

            eventModal.show();
        }

    </script>

@endpush