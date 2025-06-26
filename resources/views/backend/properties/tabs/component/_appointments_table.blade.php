@if ($events->isEmpty())
    <p class="text-muted">No appointments found for this property.</p>
@else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Start</th>
                <th>End</th>
                <th>Diary Of</th>
                <th>Booked By</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ formatDateTime($event->start_datetime) }}</td>
                    <td>{{ formatDateTime($event->end_datetime) }}</td>
                    <td>{{ optional($event->diaryOwner)->name ?? '—' }}</td>
                    <td>{{ optional($event->onBehalfOf)->name ?? '—' }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                {{ ucfirst($event->status) }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach (['confirmed', 'pending', 'cancelled'] as $status)
                                    @if ($status !== $event->status)
                                        <li>
                                            <a class="dropdown-item change-status-btn" href="#"
                                                data-id="{{ $event->id }}" data-status="{{ $status }}">
                                                Mark as {{ ucfirst($status) }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info btn-edit" data-id="{{ $event->id }}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('backend.events.deleteInstance', $event->id) }}" data-id="{{ $event->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Laravel pagination --}}
    <div class="mt-3">
        {!! $events->appends(request()->query())->links() !!}
    </div>
@endif
