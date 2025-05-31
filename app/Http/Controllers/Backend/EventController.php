<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\EventInstance;
use App\Models\EventInstanceChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                'type_id'     => $inst->event->type_id,
                'sub_type_id' => $inst->event->sub_type_id,
                'backgroundColor' => $inst->event->color, // from master’s getColorAttribute()
                'extendedProps' => [
                    'master_id' => $inst->event_id,
                    'instance_status' => $inst->instance_status,
                        
                    // We can keep these for display or other purposes:
                    'type_label'           => $inst->event->type,       // old string
                    'sub_type_label'       => $inst->event->sub_type,   // old string
                    
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
                    'repeat_until_count' => $inst->event->repeat_until_count,
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
            'repeat' => 'required|in:none,daily,weekly,monthly',
            'repeat_interval' => 'nullable|integer|min:1|max:100',
            'repeat_until_count' => 'nullable|integer|min:0|max:1000',

            // The first occurrence:
            'start_datetime' => 'required|date|after_or_equal:today',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
        ]);

        // 2.2. WRAP IN TRANSACTION
        DB::beginTransaction();
        try {
            // 2.3. CREATE MASTER EVENT
            $master = Event::create([
                'title' => $validated['title'],
                'type' => $validated['type_id'] ?? null,
                'sub_type' => $validated['sub_type_id'] ?? null,
                'office' => $validated['office'] ?? null,
                'status' => $validated['status'] ?? 'Pending',
                'diary_owner' => $validated['diary_owner'] ?? null,
                'on_behalf_of' => $validated['on_behalf_of'] ?? null,
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'reminder' => $validated['reminder'] ?? null,
                'repeat' => $validated['repeat'] ?? 'none',
                'repeat_interval' => $validated['repeat_interval'] ?? 1,
                'repeat_until_count' => $validated['repeat_until_count'] ?? 0,
            ]);

            // 2.4. INSERT INSTANCES
            $originalStart = Carbon::parse($validated['start_datetime']);
            $originalEnd = Carbon::parse($validated['end_datetime']);

            // Always insert the first instance (i = 0)
            EventInstance::create([
                'event_id' => $master->id,
                'start_datetime' => $originalStart,
                'end_datetime' => $originalEnd,
                'instance_status' => 'Scheduled',
                'notified' => false,
            ]);

            // If repeat != 'none' AND repeat_until_count > 0, generate additional
            if ($master->repeat !== 'none' && ($master->repeat_until_count ?? 0) > 0) {
                for ($i = 1; $i <= $master->repeat_until_count; $i++) {
                    $start = $originalStart->copy();
                    $end = $originalEnd->copy();

                    switch ($master->repeat) {
                        case 'daily':
                            $start->addDays($i * $master->repeat_interval);
                            $end->addDays($i * $master->repeat_interval);
                            break;
                        case 'weekly':
                            $start->addWeeks($i * $master->repeat_interval);
                            $end->addWeeks($i * $master->repeat_interval);
                            break;
                        case 'monthly':
                            $start->addMonths($i * $master->repeat_interval);
                            $end->addMonths($i * $master->repeat_interval);
                            break;
                    }

                    EventInstance::create([
                        'event_id' => $master->id,
                        'start_datetime' => $start,
                        'end_datetime' => $end,
                        'instance_status' => 'Scheduled',
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
        $instance->update(['instance_status' => 'Cancelled']);

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


    /**
     * 5) UPDATE MASTER RULE (e.g. change from daily→weekly, or change repeat count).
     *    We must:
     *      • Update event fields
     *      • Delete/cancel all future instances (after “now”)
     *      • Regenerate future instances based on new rule
     */
    public function updateMaster(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'sub_type' => 'nullable|string|max:100',
            'office' => 'nullable|string|max:100',
            'status' => 'nullable|in:Confirmed,Pending,Cancelled,Rescheduled',
            'diary_owner' => 'nullable|string|max:255',
            'on_behalf_of' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'reminder' => ['nullable', 'string', 'regex:/^\d+(\s?(minutes|hours|days))?$/'],
            'repeat' => 'nullable|in:none,daily,weekly,monthly',
            'repeat_interval' => 'nullable|integer|min:1|max:100',
            'repeat_until_count' => 'nullable|integer|min:0|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // 5.1. Update master fields
            $event->update($validated);

            // 5.2. Cancel/delete all future instances (keep past ones intact)
            $now = Carbon::now();
            $event->instances()
                ->where('start_datetime', '>', $now)
                ->update(['instance_status' => 'Cancelled']);

            // 5.3. Regenerate new instances from now forward
            // Determine “last kept” instance time: can be now or next slot
            $nextBase = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $now->format('Y-m-d ') . $event->instances()
                    ->where('instance_status', 'Scheduled')
                    ->orderBy('start_datetime', 'desc')
                    ->value('start_datetime')
            );

            // For simplicity, start from “today at original start time” if $nextBase < original start
            $originalStart = Carbon::parse($event->instances()->min('start_datetime'));
            $originalEnd = Carbon::parse($event->instances()->min('end_datetime'));

            $countExisting = $event->instances()
                ->where('start_datetime', '<=', $now)
                ->count();

            // Number of remaining occurrences needed = repeat_until_count - existingCount
            $remaining = max(0, ($event->repeat_until_count + 1) - $countExisting) - 1;
            // +1 because original counts as 1, minus 1 because we will offset starting at i=1.

            for ($i = 1; $i <= $remaining; $i++) {
                // Offset from original first date by i intervals
                $start = $originalStart->copy();
                $end = $originalEnd->copy();

                switch ($event->repeat) {
                    case 'daily':
                        $start->addDays($i * $event->repeat_interval);
                        $end->addDays($i * $event->repeat_interval);
                        break;
                    case 'weekly':
                        $start->addWeeks($i * $event->repeat_interval);
                        $end->addWeeks($i * $event->repeat_interval);
                        break;
                    case 'monthly':
                        $start->addMonths($i * $event->repeat_interval);
                        $end->addMonths($i * $event->repeat_interval);
                        break;
                }

                // Only insert if it's in the future
                if ($start > $now) {
                    $event->instances()->create([
                        'start_datetime' => $start,
                        'end_datetime' => $end,
                        'instance_status' => 'Scheduled',
                        'notified' => false,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
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
