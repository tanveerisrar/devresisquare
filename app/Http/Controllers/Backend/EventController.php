<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Arr;
use App\Models\EventSubType;
use Illuminate\Http\Request;

class EventController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        // Filter events within the range
        $events = Event::whereBetween('start_datetime', [$start, $end])->get();

        return response()->json($events);
    }

    /**
     * Fetch all events as JSON for FullCalendar.
     */
    public function fetch()
    {
        $events = Event::all();
        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_datetime->format('Y-m-d H:i:s'),
                'end' => $event->end_datetime->format('Y-m-d H:i:s'),
                'backgroundColor' => $this->getColor($event->status),
            ];
        }
        return response()->json($data);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate every field
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'sub_type' => 'nullable|string|max:100',
            'office' => 'nullable|string|max:100',
            'status' => 'nullable|in:Cancelled,Confirmed,Pending,Rescheduled',
            'diary_owner' => 'nullable|string|max:255',
            'on_behalf_of' => 'nullable|string|max:255',
            'start_datetime' => 'required|date|after_or_equal:now',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'reminder' => 'nullable|string|regex:/^\d+\s?(minutes|hours|days)$/',
            'repeat' => 'nullable|in:daily,weekly,monthly',
            'repeat_until' => 'nullable|integer|min:1|max:1000',
        ]);

        // 2. Create one or multiple events
        if (!empty($validated['repeat']) && !empty($validated['repeat_until'])) {
            for ($i = 0; $i < $validated['repeat_until']; $i++) {
                $start = Carbon::parse($validated['start_datetime']);
                $end = Carbon::parse($validated['end_datetime']);

                $start = $this->addInterval($start, $validated['repeat'], $i);
                $end = $this->addInterval($end, $validated['repeat'], $i);

                Event::create(array_merge(
                    Arr::except($validated, ['start_datetime', 'end_datetime']),
                    [
                        'start_datetime' => $start,
                        'end_datetime' => $end,
                    ]
                ));
            }
        } else {
            // Single event
            Event::create($validated);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Update start/end when an event is dragged or resized.
     */
    public function updateTime(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update([
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Event::destroy($id);
        return response()->json(['success' => true]);
    }

    public function subtypes($typeId)
    {
        $subs = EventSubType::where('event_type_id', $typeId)->pluck('name', 'id');
        return response()->json($subs);
    }


    /**
     * Get the color based on the status.
     *
     * @param  string  $status
     * @return string
     */
    private function getColor($status)
    {
        return match ($status) {
            'Cancelled' => '#dc3545',
            'Confirmed' => '#28a745',
            default => '#007bff',
        };
    }


    /**
     * Add the proper interval (daily/weekly/monthly).
     *
     * @param  Carbon  $date
     * @param  string  $repeat
     * @param  int     $i      zero-based repetition index
     * @return Carbon
     */
    private function addInterval(Carbon $date, string $repeat, int $i): Carbon
    {
        return match ($repeat) {
            'daily' => $date->addDays($i),
            'weekly' => $date->addWeeks($i),
            'monthly' => $date->addMonths($i),
            default => $date,
        };
    }
}
