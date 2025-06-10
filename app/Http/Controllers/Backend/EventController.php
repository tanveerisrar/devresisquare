<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\EventInstance;
use App\Models\EventInstanceChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RRule\RRule;

class EventController
{

    /**
     * 1) INDEX: Return all event_instances in a given date range for FullCalendar.
     *
     * FullCalendar will call ?start=YYYY-MM-DD&end=YYYY-MM-DD.
     */
    public function index(Request $request)
    {
        $start = Carbon::parse($request->query('start'));
        $end = Carbon::parse($request->query('end'));

        // Fetch instances whose start_datetime is between $start and $end
        $instances = EventInstance::whereBetween('start_datetime', [$start, $end])
            ->where('instance_status', '!=', 'Cancelled')
            ->with('event')   // eager-load master to get titles/colors if needed
            ->get();

        // Map to FullCalendar’s JSON format
        $data = $instances->map(function ($inst) {
            return [
                'id' => $inst->id,
                'title' => $inst->event->title,
                'start' => $inst->start_datetime->format('Y-m-d H:i:s'),
                'end' => $inst->end_datetime->format('Y-m-d H:i:s'),
                // These two lines are the critical additions:
                'type_id' => $inst->event->type_id,
                'sub_type_id' => $inst->event->sub_type_id,
                'backgroundColor' => $inst->event->color, // from master’s getColorAttribute()
                'extendedProps' => [
                    'master_id' => $inst->event_id,
                    'instance_status' => $inst->instance_status,
                    'type_id' => $inst->event->type_id,
                    'sub_type_id' => $inst->event->sub_type_id,

                    // We can keep these for display or other purposes:
                    'type_label' => $inst->event->type,       // old string
                    'sub_type_label' => $inst->event->sub_type,   // old string

                    // 'type' => $inst->event->type,
                    // 'sub_type' => $inst->event->sub_type,
                    'office' => $inst->event->office,
                    'status' => $inst->event->status,
                    'diary_owner' => $inst->event->diary_owner,
                    'on_behalf_of' => $inst->event->on_behalf_of,
                    'location' => $inst->event->location,
                    'description' => $inst->event->description,
                    'reminder' => $inst->event->reminder,
                    'repeat' => $inst->event->repeat,
                    'repeat_interval' => $inst->event->repeat_interval,
                    'repeat_until_date' => $inst->event->repeat_until_date,
                    // 'repeat_until_count' => $inst->event->repeat_until_count,
                ],
            ];
        });

        return response()->json($data);
    }

    /**
     * 2) STORE: Create a new master event + generate its instances immediately (up to repeat count).
     *    Request payload: (title, type, sub_type, office, status, diary_owner, on_behalf_of,
     *    location, description, reminder, repeat, repeat_interval, repeat_until_count,
     *    start_datetime, end_datetime)
     */
    public function store(Request $request)
    {
        // 2.1. VALIDATION
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type_id' => 'required|exists:event_types,id',
            'sub_type_id' => 'required|exists:event_sub_types,id',
            'office' => 'nullable|string|max:100',
            'status' => 'nullable|in:Confirmed,Pending,Cancelled,Rescheduled',
            'diary_owner' => 'nullable|string|max:255',
            'on_behalf_of' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'reminder' => ['nullable', 'string', 'regex:/^\d+(\s?(minutes|hours|days))?$/'],
            // 'repeat' => 'required|in:none,daily,weekly,monthly',
            // 'repeat_interval' => 'nullable|integer|min:1|max:100',
            // // 'repeat_until_count' => 'nullable|integer|min:0|max:1000',
            // 'repeat_until_date' => 'nullable|date|after:start_datetime',

            // The first occurrence:
            'start_datetime' => 'required|date|after_or_equal:today',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',


            'rrule' => 'nullable|string', // RFC-5545 string
            'exdates' => 'nullable|string', // JSON array of dates
        ]);

        // 2.2. WRAP IN TRANSACTION
        DB::beginTransaction();
        try {
            // 2.3. CREATE MASTER EVENT
            $master = Event::create([
                'title' => $validated['title'],
                'type_id' => $validated['type_id'] ?? null,
                'sub_type_id' => $validated['sub_type_id'] ?? null,
                'office' => $validated['office'] ?? null,
                'status' => $validated['status'] ?? 'Pending',
                'diary_owner' => $validated['diary_owner'] ?? null,
                'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'reminder' => $validated['reminder'] ?? null,
                // 'repeat' => $validated['repeat'] ?? 'none',
                // 'repeat_interval' => $validated['repeat_interval'] ?? 1,
                // // 'repeat_until_count' => $validated['repeat_until_count'] ?? 0,
                // 'repeat_until_date' => $validated['repeat_until_date'] ?? null,

                // Recurrence fields:
                'rrule' => $validated['rrule'] ?? null,
                'exdates' => $validated['exdates'] ?? null,
            ]);

            // 2.3) ALWAYS create the first instance row
            $firstStart = Carbon::parse($validated['start_datetime']);
            $firstEnd = Carbon::parse($validated['end_datetime']);

            // Always insert the first instance (i = 0)
            EventInstance::create([
                'event_id' => $master->id,
                'start_datetime' => $firstStart,
                'end_datetime' => $firstEnd,
                'instance_status' => 'Scheduled',
                'notified' => false,
            ]);

            // 3) If there’s an rrule string, generate all future occurrences
            if (!empty($validated['rrule'])) {
                // Build an RRule object. We need to set “dtstart” to the original start datetime:
                $rruleArr = [
                    'rrule' => $validated['rrule'],
                    'dtstart' => $firstStart->toAtomString(), // e.g. “2025-06-05T09:00:00+00:00”
                ];

                $rule = new RRule($rruleArr);

                // Convert exdates JSON string into an array of Y-m-d strings (if any):
                $exdates = [];
                if (!empty($validated['exdates'])) {
                    $exdates = json_decode($validated['exdates'], true);
                }

                // Loop through each occurrence generated by RRule
                foreach ($rule as $occ) {
                    // $occ is a DateTime instance in UTC. Compare to the “first” we already saved.
                    if ($occ->getTimestamp() === $firstStart->getTimestamp()) {
                        continue; // skip the original we've already inserted
                    }
                    // If the date part (Y-m-d) is in exdates, skip
                    $dateOnly = $occ->format('Y-m-d');
                    if (in_array($dateOnly, $exdates, true)) {
                        // Create a placeholder marked “Canceled” so we never regenerate it:
                        EventInstance::create([
                            'event_id' => $master->id,
                            'start_datetime' => Carbon::instance($occ),
                            'end_datetime' => Carbon::instance($occ)->addSeconds(
                                $firstEnd->diffInSeconds($firstStart)
                            ),
                            'instance_status' => 'Canceled',
                            'is_exception' => true,
                            'notified' => false,
                        ]);
                        continue;
                    }

                    // Otherwise, insert a normal scheduled instance
                    EventInstance::create([
                        'event_id' => $master->id,
                        'start_datetime' => Carbon::instance($occ),
                        'end_datetime' => Carbon::instance($occ)->addSeconds(
                            $firstEnd->diffInSeconds($firstStart)
                        ),
                        'instance_status' => 'Scheduled',
                        'is_exception' => false,
                        'notified' => false,
                    ]);
                }
            }
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while saving.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * 3) UPDATE AN INSTANCE (drag/drop or per-instance edit).
     *    Payload: start_datetime, end_datetime (plus any instance-level fields you want)
     */
    public function updateInstance(Request $request, EventInstance $instance)
    {
        // Validate incoming data
        $data = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'original_start' => 'nullable|date',
            'original_end' => 'nullable|date',
            'form_action' => 'required|string|in:updateInstance'
        ]);

        // Keep track of old values
        $oldStart = $instance->start_datetime->format('Y-m-d H:i:s');
        $oldEnd = $instance->end_datetime->format('Y-m-d H:i:s');
        $oldStatus = $instance->instance_status;

        // Update the instance
        $instance->update([
            'start_datetime' => $data['start_datetime'],
            'end_datetime' => $data['end_datetime'],
            'instance_status' => 'Rescheduled',
            'is_exception' => true,
        ]);

        // Record two change entries: one for start, one for end
        $userId = auth()->id(); // or null if guests allowed


        // Only log start_datetime if it actually changed
        if ($oldStart !== $data['start_datetime']) {
            EventInstanceChange::create([
                'event_instance_id' => $instance->id,
                'changed_field' => 'start_datetime',
                'old_value' => $oldStart,
                'new_value' => $data['start_datetime'],
                'changed_by' => $userId,
                'changed_at' => Carbon::now(),
                'comment' => 'Start time changed',
            ]);
        }

        // Only log end_datetime if it changed
        if ($oldEnd !== $data['end_datetime']) {
            EventInstanceChange::create([
                'event_instance_id' => $instance->id,
                'changed_field' => 'end_datetime',
                'old_value' => $oldEnd,
                'new_value' => $data['end_datetime'],
                'changed_by' => $userId,
                'changed_at' => Carbon::now(),
                'comment' => 'End time changed',
            ]);
        }

        // If you also changed status, log that too
        if ($oldStatus !== 'Rescheduled') {
            EventInstanceChange::create([
                'event_instance_id' => $instance->id,
                'changed_field' => 'instance_status',
                'old_value' => $oldStatus,
                'new_value' => 'Rescheduled',
                'changed_by' => $userId,
                'changed_at' => Carbon::now(),
                'comment' => 'Status changed to Rescheduled',
            ]);
        }

        return response()->json(['success' => true]);
    }


    /**
     * 4) DELETE / CANCEL A SINGLE INSTANCE
     */
    public function destroyInstance(EventInstance $instance)
    {
        // Mark as cancelled
        $oldStatus = $instance->instance_status;
        $instance->update(['instance_status' => 'Cancelled', 'is_exception' => true]);

        EventInstanceChange::create([
            'event_instance_id' => $instance->id,
            'changed_field' => 'instance_status',
            'old_value' => $oldStatus,
            'new_value' => 'Cancelled',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
            'comment' => 'Instance cancelled by user',
        ]);

        return response()->json(['success' => true]);
    }
    
    public function destroySeries(Request $request, Event $event)
    {
        // Mark the master as Canceled
        $event->update(['status' => 'Canceled']);

        // Mark all future instances as canceled
        $event->instances()
            ->where('start_datetime', '>=', Carbon::now())
            ->update([
                'instance_status' => 'Canceled',
                'is_exception' => true,
            ]);

        return response()->json(['success' => true]);
    }
    public function splitSeriesFromInstance(Request $request, $instanceId)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type_id' => 'required|exists:event_types,id',
            // … all other master fields + new rrule/exdates …
            'form_action' => 'required|string|in=splitSeries',
        ]);

        $instance = EventInstance::with('event')->findOrFail($instanceId);
        $oldMaster = $instance->event;
        $pivotTime = Carbon::parse($validated['start_datetime']);

        \DB::beginTransaction();
        try {
            // 1) Cancel this and all future instances of old series
            EventInstance::where('event_id', $oldMaster->id)
                ->where('start_datetime', '>=', $pivotTime)
                ->update([
                    'instance_status' => 'Canceled',
                    'is_exception' => true
                ]);

            // 2) Create a new master event starting at $pivotTime
            $newMaster = Event::create([
                'title' => $validated['title'],
                'type_id' => $validated['type_id'],
                // … copy all other fields from $validated or $oldMaster as needed …
                'rrule' => $validated['rrule'],      // new rule
                'exdates' => $validated['exdates'],    // new exceptions
            ]);

            // 3) Create the first occurrence for newMaster at pivotTime
            $firstEnd = Carbon::parse($validated['end_datetime']);
            EventInstance::create([
                'event_id' => $newMaster->id,
                'start_datetime' => $pivotTime,
                'end_datetime' => $firstEnd,
                'instance_status' => 'Scheduled',
                'is_exception' => false,
                'notified' => false,
            ]);

            // 4) If the newMaster has its own rrule, generate subsequent instances
            if (!empty($validated['rrule'])) {
                $rule = new RRule([
                    'rrule' => $validated['rrule'],
                    'dtstart' => $pivotTime->toAtomString()
                ]);
                $exdates = json_decode($validated['exdates'] ?? '[]', true);
                foreach ($rule as $occ) {
                    if ($occ->getTimestamp() === $pivotTime->getTimestamp()) {
                        continue;
                    }
                    $dateOnly = $occ->format('Y-m-d');
                    if (in_array($dateOnly, $exdates, true)) {
                        continue;
                    }
                    EventInstance::create([
                        'event_id' => $newMaster->id,
                        'start_datetime' => Carbon::instance($occ),
                        'end_datetime' => Carbon::instance($occ)->addSeconds(
                            $firstEnd->diffInSeconds($pivotTime)
                        ),
                        'instance_status' => 'Scheduled',
                        'is_exception' => false,
                        'notified' => false,
                    ]);
                }
            }

            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Error splitting series.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateMaster(Request $request, Event $event)
    {
        // 5.1) VALIDATION
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type_id' => 'required|exists:event_types,id',
            'sub_type_id' => 'required|exists:event_sub_types,id',
            'office' => 'nullable|string|max:100',
            'status' => 'nullable|in:Confirmed,Pending,Cancelled,Rescheduled',
            'diary_owner' => 'nullable|string|max:255',
            'on_behalf_of' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'reminder' => ['nullable', 'string', 'regex:/^\d+(\s?(minutes|hours|days))?$/'],

            // The “new original” start/end for the first instance:
            'start_datetime' => 'required|date|after_or_equal:today',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',

            // 'repeat'              => 'required|in:none,daily,weekly,monthly',
            // 'repeat_interval'     => 'nullable|integer|min:1|max:100',
            // 'repeat_until_date'   => 'nullable|date|after:start_datetime',

            'rrule' => 'nullable|string',
            'exdates' => 'nullable|string',

            'form_action' => 'required|string|in:updateMaster',
        ]);

        \DB::beginTransaction();
        try {
            // 5.2) UPDATE only the master event’s own columns (not start/end):
            $event->update([
                'title' => $validated['title'],
                'type_id' => $validated['type_id'],
                'sub_type_id' => $validated['sub_type_id'],
                'office' => $validated['office'] ?? null,
                'status' => $validated['status'] ?? null,
                'diary_owner' => $validated['diary_owner'] ?? null,
                'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'reminder' => $validated['reminder'] ?? null,

                'rrule' => $validated['rrule'] ?? null,
                'exdates' => $validated['exdates'] ?? null,

                // 'repeat'            => $validated['repeat'],
                // 'repeat_interval'   => (int) ($validated['repeat_interval'] ?? 1),
                // 'repeat_until_date' => $validated['repeat_until_date'] ?? null,
            ]);

            // 5.3) COLLECT all existing instances (past & future) for lookup
            $now = Carbon::now();
            $existingInstances = $event->instances()
                ->orderBy('start_datetime', 'asc')
                ->get([
                    'id',
                    'start_datetime',
                    'end_datetime',
                ]);

            // Map of existing start‐times for quick "exists" check
            $existingStarts = $existingInstances->pluck('start_datetime')
                ->map(function ($dt) {
                    return $dt->format('Y-m-d H:i:s');
                })
                ->toArray();

            // 5.4) UPDATE or CREATE the earliest (original) instance
            $originalStart = Carbon::parse($validated['start_datetime']);
            $originalEnd = Carbon::parse($validated['end_datetime']);

            if ($existingInstances->isEmpty()) {
                // No instances at all → create the first one
                EventInstance::create([
                    'event_id' => $event->id,
                    'start_datetime' => $originalStart,
                    'end_datetime' => $originalEnd,
                    'instance_status' => 'Scheduled',
                    'is_exception' => false,
                    'notified' => false,
                ]);
                $existingStarts[] = $originalStart->format('Y-m-d H:i:s');
            } else {
                $firstInstance = $existingInstances->first();
                $firstStart = $firstInstance->start_datetime->format('Y-m-d H:i:s');
                $newStartKey = $originalStart->format('Y-m-d H:i:s');

                if ($firstStart !== $newStartKey) {
                    $firstInstance->update([
                        'start_datetime' => $originalStart,
                        'end_datetime' => $originalEnd,
                        'instance_status' => 'Scheduled',
                        'is_exception' => false,
                        'notified' => false,
                    ]);
                    $existingStarts[] = $newStartKey;
                }
            }

            // 5.5) If no RRULE, we’re done
            if (empty($validated['rrule'])) {
                \DB::commit();
                return response()->json(['success' => true]);
            }

            // 5.6) Parse exdates (if any)
            $exdates = [];
            if (!empty($validated['exdates'])) {
                $exdates = json_decode($validated['exdates'], true);
            }

            // 5.7) Use RRULE to generate future instances
            $rruleArr = [
                'rrule' => $validated['rrule'],
                'dtstart' => $originalStart->toAtomString(),
            ];
            $rrule = new RRule($rruleArr);

            $durationInSeconds = $originalEnd->diffInSeconds($originalStart);

            foreach ($rrule as $occurrence) {
                $startCarbon = Carbon::instance($occurrence);
                $startKey = $startCarbon->format('Y-m-d H:i:s');

                // Skip if it's the first one (already created/updated above)
                if ($startKey === $originalStart->format('Y-m-d H:i:s')) {
                    continue;
                }

                // Skip if already exists
                if (in_array($startKey, $existingStarts, true)) {
                    continue;
                }

                // Check if it's in the exclusion list
                if (in_array($startCarbon->format('Y-m-d'), $exdates, true)) {
                    EventInstance::create([
                        'event_id' => $event->id,
                        'start_datetime' => $startCarbon,
                        'end_datetime' => $startCarbon->copy()->addSeconds($durationInSeconds),
                        'instance_status' => 'Canceled',
                        'is_exception' => true,
                        'notified' => false,
                    ]);
                    continue;
                }

                // Normal future instance
                if ($startCarbon->greaterThan($now)) {
                    EventInstance::create([
                        'event_id' => $event->id,
                        'start_datetime' => $startCarbon,
                        'end_datetime' => $startCarbon->copy()->addSeconds($durationInSeconds),
                        'instance_status' => 'Scheduled',
                        'is_exception' => false,
                        'notified' => false,
                    ]);
                }
            }

            // 5.8) Commit the transaction
            \DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Error updating recurrence rule.',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * 6) DELETE (MASTER): Delete entire series (all instances).
     */
    public function destroyMaster(Event $event)
    {
        $event->delete();
        return response()->json(['success' => true]);
    }

    public function revertInstanceField(Request $request, EventInstance $instance)
    {
        // 1) Validate that ‘field’ is provided and is a valid column
        $data = $request->validate([
            'field' => 'required|string|in:start_datetime,end_datetime,instance_status',
        ]);

        $field = $data['field'];

        // 2) Find the most recent change for this instance & field
        $lastChange = EventInstanceChange::where('event_instance_id', $instance->id)
            ->where('changed_field', $field)
            ->orderBy('changed_at', 'desc')
            ->first();

        if (!$lastChange) {
            return response()->json([
                'message' => 'No previous change found to revert.'
            ], 404);
        }

        // 3) Grab old and current values
        $oldValue = $lastChange->old_value;
        $currentValue = $instance->{$field};

        // 4) Update the instance, reverting that field
        $instance->update([
            $field => $oldValue
        ]);

        // 5) Log a new change that we reverted
        EventInstanceChange::create([
            'event_instance_id' => $instance->id,
            'changed_field' => $field,
            'old_value' => $currentValue,
            'new_value' => $oldValue,
            'changed_by' => Auth::id(),
            'changed_at' => Carbon::now(),
            'comment' => "Reverted {$field} to previous value",
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$field} reverted to previous value",
        ]);
    }
}
