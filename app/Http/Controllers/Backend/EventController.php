<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\EventInstance;
use App\Models\EventInstanceChange;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RRule\RRule;

class EventController
{

    public function index(Request $request)
    {
        $start = Carbon::parse($request->query('start'));
        $end = Carbon::parse($request->query('end'));

        // Fetch events directly
        $events = Event::whereBetween('start_datetime', [$start, $end])
            ->where('status', '!=', 'Cancelled')
            ->with('reminders')
            ->get();

        $data = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_datetime->format('Y-m-d H:i:s'),
                'end' => $event->end_datetime->format('Y-m-d H:i:s'),
                'type_id' => $event->type_id,
                'sub_type_id' => $event->sub_type_id,
                'backgroundColor' => $event->color, // assuming getColorAttribute() exists in model
                'status' => $event->status,
                'remindersCount' => $event->reminders->count(),
                'reminders' => $event->reminders->map(fn($r) => [
                    'minutes_before' => $r->minutes_before,
                    'channel' => $r->channel,
                ]),

                'extendedProps' => [
                    // 'start' => $event->start_datetime->toIso8601String(),
                    // 'end' => $event->end_datetime->toIso8601String(),
                    'start' => $event->start_datetime->format('Y-m-d H:i:s'),
                    'end' => $event->end_datetime->format('Y-m-d H:i:s'),
                    'event_id' => $event->id, // use parent_id if exists, else self
                    'parent_id' => $event->parent_id,
                    'master_id' => $event->parent_id,
                    'event_status' => $event->status,
                    'office' => $event->office,
                    'diary_owner' => $event->diary_owner,
                    'on_behalf_of' => $event->on_behalf_of,
                    'location' => $event->location,
                    'description' => $event->description,
                    'repeat_until_date' => $event->repeat_until_date,
                    'rrule' => $event->rrule,
                    'exdates' => $event->exdates,
                    'type_id' => $event->type_id,
                    'sub_type_id' => $event->sub_type_id,
                    'type_label' => $event->type,           // assuming string fallback or relationship
                    'sub_type_label' => $event->sub_type,
                    'reminders' => $event->reminders->map(fn($r) => [
                        'minutes_before' => $r->minutes_before,
                        'channel' => $r->channel,
                    ]),
                ],
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type_id' => 'required|exists:event_types,id',
            'sub_type_id' => 'required|exists:event_sub_types,id',
            'office' => 'nullable|string|max:100',
            'status' => 'nullable|in:Confirmed,Pending,Cancelled,Rescheduled,Scheduled',
            'diary_owner' => 'nullable|string|max:255',
            'on_behalf_of' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'rrule' => 'nullable|string',
            'exdates' => 'nullable|string',
            'reminders' => 'nullable|array',
            'reminders.*.minutes_before' => 'nullable|integer|min:0',
            'reminders.*.channel' => 'nullable|in:email,in_app,sms,push',
        ]);

        \DB::beginTransaction();
        try {
            $start = Carbon::parse($validated['start_datetime']);
            $end = Carbon::parse($validated['end_datetime']);
            $duration = abs($end->diffInSeconds($start));

            // 1. Create master event
            $master = Event::create([
                'title' => $validated['title'],
                'type_id' => $validated['type_id'],
                'sub_type_id' => $validated['sub_type_id'],
                'office' => $validated['office'] ?? null,
                'status' => $validated['status'] ?? 'Pending',
                'diary_owner' => $validated['diary_owner'] ?? null,
                'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'start_datetime' => $start,
                'end_datetime' => $end,
                'rrule' => $validated['rrule'] ?? null,
                'exdates' => $validated['exdates'] ?? null,
            ]);

            // 2. Attach reminders to master
            if (!empty($validated['reminders'])) {
                foreach ($validated['reminders'] as $r) {
                    if (
                        (!isset($r['minutes_before']) || $r['minutes_before'] === '' || $r['minutes_before'] === null)
                        && (empty($r['channel']))
                    ) {
                        continue;
                    }
                    $master->reminders()->create([
                        'minutes_before' => $r['minutes_before'] ?? 0,
                        'channel' => $r['channel'] ?? 'email',
                    ]);
                }
            }

            // 3. If rrule exists, generate child events with parent_id
            if (!empty($validated['rrule'])) {
                $rruleString = preg_replace('/^RRULE:/i', '', trim($validated['rrule']));
                $rule = new RRule($rruleString, $start);
                $exdates = json_decode($validated['exdates'] ?? '[]', true);
                $endLimit = now()->addYear();
                $reminders = $master->reminders()->get();

                foreach ($rule as $occurrence) {
                    $occ = Carbon::instance($occurrence);
                    if ($occ->equalTo($start) || $occ->greaterThan($endLimit))
                        continue;

                    $dateOnly = $occ->toDateString();
                    if (in_array($dateOnly, $exdates, true))
                        continue;

                    $child = Event::create([
                        'parent_id' => $master->id,
                        'title' => $master->title,
                        'type_id' => $master->type_id,
                        'sub_type_id' => $master->sub_type_id,
                        'office' => $master->office,
                        'status' => $master->status,
                        'diary_owner' => $master->diary_owner,
                        'on_behalf_of' => $master->on_behalf_of,
                        'location' => $master->location,
                        'description' => $master->description,
                        'reminder' => $master->reminder,
                        'start_datetime' => $occ,
                        'end_datetime' => $occ->copy()->addSeconds($duration),
                        'rrule' => $validated['rrule'] ?? null,
                        'exdates' => $validated['exdates'] ?? null,
                    ]);

                    // Copy reminders to each child event
                    foreach ($reminders as $r) {
                        $child->reminders()->create([
                            'minutes_before' => $r->minutes_before,
                            'channel' => $r->channel,
                        ]);
                    }
                }
            }

            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while saving.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateInstance(Request $request, $id)
    {
        $event = Event::findOrFail($id); // now $event won't be null

        $data = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'form_action' => 'required|string|in:updateInstance'
        ]);

        $userId = auth()->id();

        $oldStart = optional($event->start_datetime)->format('Y-m-d H:i:s');
        $oldEnd = optional($event->end_datetime)->format('Y-m-d H:i:s');
        $oldStatus = $event->status;

        $event->update([
            'start_datetime' => $data['start_datetime'],
            'end_datetime' => $data['end_datetime'],
            'status' => 'Rescheduled',
            'instance_status' => 'Rescheduled',
            'is_exception' => true,
        ]);

        if ($oldStart !== $data['start_datetime']) {
            DB::table('event_instance_changes')->insert([
                'event_id' => $event->id,
                'changed_field' => 'start_datetime',
                'old_value' => $oldStart,
                'new_value' => $data['start_datetime'],
                'changed_by' => $userId,
                'changed_at' => now(),
                'comment' => 'Start time changed',
            ]);
        }

        if ($oldEnd !== $data['end_datetime']) {
            DB::table('event_instance_changes')->insert([
                'event_id' => $event->id,
                'changed_field' => 'end_datetime',
                'old_value' => $oldEnd,
                'new_value' => $data['end_datetime'],
                'changed_by' => $userId,
                'changed_at' => now(),
                'comment' => 'End time changed',
            ]);
        }

        if ($oldStatus !== 'Rescheduled') {
            DB::table('event_instance_changes')->insert([
                'event_id' => $event->id,
                'changed_field' => 'status',
                'old_value' => $oldStatus,
                'new_value' => 'Rescheduled',
                'changed_by' => $userId,
                'changed_at' => now(),
                'comment' => 'Status changed to Rescheduled',
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyInstance($eventID)
    {
        $event = Event::findOrFail($eventID); // Ensure the event exists

        // Cancel the instance
        if ($event->status === 'Cancelled' || $event->instance_status === 'Cancelled') {
            return response()->json(['error' => 'Instance already cancelled'], 400);
        }

        // Record the change
        $oldStatus = $event->instance_status;

        $event->update([
            'status' => 'Cancelled',
            'instance_status' => 'Cancelled',
            'is_exception' => true,
        ]);

        \DB::table('event_instance_changes')->insert([
            'event_id' => $event->id,
            'changed_field' => 'instance_status',
            'old_value' => $oldStatus,
            'new_value' => 'Cancelled',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
            'comment' => 'Single event instance cancelled by user',
        ]);

        return response()->json(['success' => true, 'message' => 'Event instance cancelled successfully']);
    }

    public function updateMaster(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type_id' => 'required|exists:event_types,id',
            'sub_type_id' => 'required|exists:event_sub_types,id',
            'office' => 'nullable|string|max:100',
            'status' => 'nullable|in:Confirmed,Pending,Cancelled,Rescheduled,Scheduled',
            'diary_owner' => 'nullable|string|max:255',
            'on_behalf_of' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'reminder' => ['nullable', 'string', 'regex:/^\d+(\s?(minutes|hours|days))?$/'],
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'rrule' => 'nullable|string',
            'exdates' => 'nullable|string',
            'reminders' => 'nullable|array',
            'reminders.*.minutes_before' => 'nullable|integer|min:0',
            'reminders.*.channel' => 'nullable|in:email,in_app,sms,push',
            'form_action' => 'required|string|in:updateMaster',
            'choice_action' => 'required|in:single,series,future',
        ]);

        \DB::beginTransaction();

        try {

            $choice = $request->input('choice_action', 'single'); // default to updateInstance

            $instanceId = $request->input('instance_id');
            $masterId = $request->input('master_id');
            $instance = Event::findOrFail($instanceId); // if instance_id is not provided, use the master event
            $newRrule = $validated['rrule'] ?? null;
            $rruleChanged = $instance->rrule !== $newRrule;

            switch ($choice) {
                case 'single':
                    // Update single instance

                    if ($rruleChanged && $newRrule) {
                        // Convert this instance into a new master
                        $instance->update([
                            'title' => $validated['title'],
                            'type_id' => $validated['type_id'],
                            'sub_type_id' => $validated['sub_type_id'],
                            'office' => $validated['office'] ?? null,
                            'status' => $validated['status'] ?? 'Confirmed',
                            'diary_owner' => $validated['diary_owner'] ?? null,
                            'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                            'location' => $validated['location'] ?? null,
                            'description' => $validated['description'] ?? null,
                            'reminder' => $validated['reminder'] ?? null,
                            'start_datetime' => $validated['start_datetime'],
                            'end_datetime' => $validated['end_datetime'],
                            'rrule' => $newRrule,
                            'exdates' => $validated['exdates'] ?? '[]',
                            'parent_id' => null,
                            'is_exception' => false,
                            'instance_status' => 'Scheduled',
                        ]);

                        $instance->reminders()->delete();
                        if ($request->filled('reminders')) {
                            foreach ($validated['reminders'] as $r) {
                                if (!empty($r['minutes_before']) && !empty($r['channel'])) {
                                    $instance->reminders()->create($r);
                                }
                            }
                        }

                        // Generate children from this new master
                        $this->generateChildInstances($instance, $validated);

                        \DB::commit();
                        return response()->json(['success' => true, 'message' => 'New recurring event created from single instance.']);
                    }

                    // Else, just update single instance without recurrence
                    $instance->update([
                        'title' => $validated['title'],
                        'type_id' => $validated['type_id'],
                        'sub_type_id' => $validated['sub_type_id'],
                        'office' => $validated['office'] ?? null,
                        'status' => $validated['status'] ?? 'Confirmed',
                        'diary_owner' => $validated['diary_owner'] ?? null,
                        'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                        'location' => $validated['location'] ?? null,
                        'description' => $validated['description'] ?? null,
                        'reminder' => $validated['reminder'] ?? null,
                        'start_datetime' => $validated['start_datetime'],
                        'end_datetime' => $validated['end_datetime'],
                        'rrule' => $validated['rrule'] ?? null,
                        'exdates' => $validated['exdates'] ?? null,
                        'is_exception' => false,
                        'instance_status' => 'Scheduled',
                        'parent_id' => $instance->parent_id, // keep it if it had one
                    ]);

                    $instance->reminders()->delete();
                    if ($request->filled('reminders')) {
                        foreach ($validated['reminders'] as $r) {
                            if (!empty($r['minutes_before']) && !empty($r['channel'])) {
                                $instance->reminders()->create($r);
                            }
                        }
                    }

                    \DB::commit();
                    return response()->json(['success' => true, 'message' => 'Single occurrence updated.']);
                case 'series':
                    // Always update the master event
                    $event->update([
                        'title' => $validated['title'],
                        'type_id' => $validated['type_id'],
                        'sub_type_id' => $validated['sub_type_id'],
                        'office' => $validated['office'] ?? null,
                        'status' => $validated['status'] ?? 'Confirmed',
                        'diary_owner' => $validated['diary_owner'] ?? null,
                        'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                        'location' => $validated['location'] ?? null,
                        'description' => $validated['description'] ?? null,
                        'reminder' => $validated['reminder'] ?? null,
                        'start_datetime' => $validated['start_datetime'],
                        'end_datetime' => $validated['end_datetime'],
                        'rrule' => $validated['rrule'], // updated or same
                        'exdates' => $validated['exdates'] ?? '[]',
                        'is_exception' => false,
                        'instance_status' => 'Scheduled',
                    ]);

                    // Update reminders
                    $event->reminders()->delete();
                    if ($request->filled('reminders')) {
                        foreach ($validated['reminders'] as $r) {
                            if (!empty($r['minutes_before']) && !empty($r['channel'])) {
                                $event->reminders()->create($r);
                            }
                        }
                    }

                    if ($rruleChanged) {
                        // Recurrence rule changed: delete and regenerate children
                        $event->children()->delete();
                        $this->generateChildInstances($event, $validated);
                    } else {
                        // RRule not changed: update existing children
                        foreach ($event->children as $child) {
                            $child->update([
                                'title' => $validated['title'],
                                'type_id' => $validated['type_id'],
                                'sub_type_id' => $validated['sub_type_id'],
                                'office' => $validated['office'] ?? null,
                                'status' => $validated['status'] ?? 'Confirmed',
                                'diary_owner' => $validated['diary_owner'] ?? null,
                                'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                                'location' => $validated['location'] ?? null,
                                'description' => $validated['description'] ?? null,
                                'reminder' => $validated['reminder'] ?? null,
                                'rrule' => $validated['rrule'], // same as master
                                'exdates' => $validated['exdates'] ?? '[]',
                                'is_exception' => false,
                                'instance_status' => 'Scheduled',
                                // Do not update `start_datetime` and `end_datetime`
                                // because each child has its own instance time
                            ]);
                        }
                    }

                    \DB::commit();
                    return response()->json(['success' => true, 'message' => 'Series updated successfully.']);


                case 'future':
                    // Update all future instances

                    if ($rruleChanged && $newRrule) {
                        $oldMaster = $instance->parent ?? $instance;

                        // Step 1: Add current instance's start_datetime to old master exdates
                        $existingExdates = json_decode($oldMaster->exdates ?? '[]', true);
                        $existingExdates[] = $instance->start_datetime->format('Y-m-d\TH:i:s');
                        $oldMaster->update([
                            'exdates' => json_encode(array_unique($existingExdates)),
                        ]);

                        // Step 2: Make current instance a new master with updated data
                        $instance->update([
                            'title' => $validated['title'],
                            'type_id' => $validated['type_id'],
                            'sub_type_id' => $validated['sub_type_id'],
                            'office' => $validated['office'] ?? null,
                            'status' => $validated['status'] ?? 'Confirmed',
                            'diary_owner' => $validated['diary_owner'] ?? null,
                            'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                            'location' => $validated['location'] ?? null,
                            'description' => $validated['description'] ?? null,
                            'reminder' => $validated['reminder'] ?? null,
                            'start_datetime' => $validated['start_datetime'],
                            'end_datetime' => $validated['end_datetime'],
                            'rrule' => $newRrule,
                            'exdates' => $validated['exdates'] ?? '[]',
                            'is_exception' => false,
                            'instance_status' => 'Scheduled',
                            'parent_id' => null, // Make it a new master
                        ]);

                        // Step 3: Generate children from this new master
                        $this->generateChildInstances($instance, $validated);

                        \DB::commit();
                        return response()->json(['success' => true, 'message' => 'Future instances updated with new series.']);
                    }

                    // rrule not changed, just update this and future children
                    $instance->update([
                        'title' => $validated['title'],
                        'type_id' => $validated['type_id'],
                        'sub_type_id' => $validated['sub_type_id'],
                        'office' => $validated['office'] ?? null,
                        'status' => $validated['status'] ?? 'Confirmed',
                        'diary_owner' => $validated['diary_owner'] ?? null,
                        'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                        'location' => $validated['location'] ?? null,
                        'description' => $validated['description'] ?? null,
                        'reminder' => $validated['reminder'] ?? null,
                        'start_datetime' => $validated['start_datetime'],
                        'end_datetime' => $validated['end_datetime'],
                        'is_exception' => false,
                        'instance_status' => 'Scheduled',
                    ]);

                    $instance->reminders()->delete();
                    if ($request->filled('reminders')) {
                        foreach ($validated['reminders'] as $r) {
                            if (!empty($r['minutes_before']) && !empty($r['channel'])) {
                                $instance->reminders()->create($r);
                            }
                        }
                    }

                    // Update all future siblings (after current)
                    Event::where('parent_id', $instance->parent_id)
                        ->where('start_datetime', '>', $instance->start_datetime)
                        ->update([
                            'title' => $validated['title'],
                            'type_id' => $validated['type_id'],
                            'sub_type_id' => $validated['sub_type_id'],
                            'office' => $validated['office'] ?? null,
                            'status' => $validated['status'] ?? 'Confirmed',
                            'diary_owner' => $validated['diary_owner'] ?? null,
                            'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                            'location' => $validated['location'] ?? null,
                            'description' => $validated['description'] ?? null,
                            'reminder' => $validated['reminder'] ?? null,
                            'is_exception' => false,
                            'instance_status' => 'Scheduled',
                        ]);

                    \DB::commit();
                    return response()->json(['success' => true, 'message' => 'Future occurrences updated successfully.']);

                default:
                    \DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
            }

        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    protected function generateChildInstances(Event $master, array $validated, ?Carbon $startFrom = null)
    {
        $rruleStr = $validated['rrule'];
        if (str_starts_with($rruleStr, 'RRULE:')) {
            $rruleStr = substr($rruleStr, 6);
        }

        $start = Carbon::parse($validated['start_datetime']);
        $end = Carbon::parse($validated['end_datetime']);
        $duration = abs($end->diffInSeconds($start));

        $rrule = new RRule($rruleStr, $start);
        $exdates = json_decode($validated['exdates'] ?? '[]', true);

        foreach ($rrule as $occurrence) {
            $occurrenceStart = Carbon::instance($occurrence);
            if ($occurrenceStart->eq($start)) {
                continue; // skip master
            }

            if ($startFrom && $occurrenceStart->lt($startFrom)) {
                continue; // skip past for "future" mode
            }

            $occurrenceEnd = $occurrenceStart->copy()->addSeconds($duration);
            $isException = in_array($occurrenceStart->format('Y-m-d'), $exdates, true);

            Event::create([
                'title' => $master->title,
                'type_id' => $master->type_id,
                'sub_type_id' => $master->sub_type_id,
                'office' => $master->office,
                'status' => $master->status,
                'diary_owner' => $master->diary_owner,
                'on_behalf_of' => $master->on_behalf_of,
                'location' => $master->location,
                'description' => $master->description,
                'reminder' => $master->reminder,
                'start_datetime' => $occurrenceStart,
                'end_datetime' => $occurrenceEnd,
                'rrule' => $validated['rrule'],
                'exdates' => $validated['exdates'] ?? '[]',
                'is_exception' => $isException,
                'instance_status' => $isException ? 'Cancelled' : 'Scheduled',
                'parent_id' => $master->id,
            ]);
        }
    }

    public function cancelInstance(Request $request, $id)
    {
        $request->validate([
            'choice_action' => 'in:single,series,future',
            'occurrence_start' => 'required|date',
        ]);

        $choice = $request->input('choice_action', 'single');
        $pivot = Carbon::parse($request->occurrence_start)
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');

        $event = Event::findOrFail($id);

        switch ($choice) {
            case 'single':
                // Cancel only this instance
                $event->update([
                    'status' => 'Cancelled',
                    'instance_status' => 'Cancelled',
                    'is_exception' => true,
                ]);
                break;

            case 'series':
                // Determine the master event
                $master = $event->parent ?: $event; // if current event has no parent, it's master

                // Cancel the master event
                $master->update([
                    'status' => 'Cancelled',
                    'instance_status' => 'Cancelled',
                    'is_exception' => true,
                ]);

                // Cancel all its children (instances)
                $master->children()
                    ->where('status', '!=', 'Cancelled')
                    ->update([
                        'status' => 'Cancelled',
                        'instance_status' => 'Cancelled',
                        'is_exception' => true,
                    ]);
                break;

            case 'future':
                // Determine the master event
                $master = $event->parent ?: $event;

                // Cancel the master if it starts at or after the pivot
                if ($master->start_datetime >= $pivot) {
                    $master->update([
                        'status' => 'Cancelled',
                        'instance_status' => 'Cancelled',
                        'is_exception' => true,
                    ]);
                }

                // Cancel children starting from pivot onward
                $master->children()
                    ->where('start_datetime', '>=', $pivot)
                    ->where('status', '!=', 'Cancelled')
                    ->update([
                        'status' => 'Cancelled',
                        'instance_status' => 'Cancelled',
                        'is_exception' => true,
                    ]);
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Cancellation successful.']);
    }



    /**
     * 6) DELETE (MASTER): Delete entire series (all instances).
     */
    public function destroyMaster(Event $event)
    {
        $event->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Cancel this and all future occurrences of a series.
     */
    public function cancelSeries(Request $request, $seriesId)
    {
        $request->validate([
            'occurrence_start' => 'required|date',
        ]);

        // Normalize the pivot to match your DB format
        $pivot = Carbon::parse($request->occurrence_start)
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');

        // Fetch the Event and its instances relation
        $event = Event::findOrFail($seriesId);

        // Use the relation to scope & update future instances
        // $event->instances()
        $event->where('start_datetime', '>=', $pivot)
            ->where('instance_status', '!=', 'Cancelled')
            ->update([
                'instance_status' => 'Cancelled',
                'is_exception' => true,
            ]);

        return response()->json(['success' => true]);
    }



}