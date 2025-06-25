@if($events->isEmpty())
    <p class="text-muted">No appointments found for this property.</p>
@else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Start</th>
                <th>End</th>
                <th>Diary Owner</th>
                <th>Booked By</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ formatDateTime($event->start_datetime) }}</td>
                    <td>{{ formatDateTime($event->end_datetime) }}</td>
                    <td>{{ optional($event->diaryOwner)->name ?? '—' }}</td>
                    <td>{{ optional($event->onBehalfOf)->name ?? '—' }}</td>
                    <td>{{ $event->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Laravel pagination --}}
    <div class="mt-3">
        {!! $events->appends(request()->query())->links() !!}
    </div>
@endif
