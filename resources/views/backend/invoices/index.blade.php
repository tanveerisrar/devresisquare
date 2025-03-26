@extends('backend.layout.app')

@section('content')
<div class="container">
    <h2>Invoices</h2>

    <!-- Filters Section -->
    <form method="GET" action="{{ route('admin.invoices.index') }}" class="mb-3">
        <div class="row justify-content-end">
            {{-- <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search Invoice No, Contact, Property..." 
                       value="{{ request('search') }}">
            </div> --}}
            <div class="col-md-4">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <!-- Invoices Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Work Order</th>
                <th>Property</th>
                <th>Contact</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ optional($invoice->workOrder)->works_order_no }}</td>
                <td>{{ getPropertyDetails($invoice->property_id, ['prop_ref_no', 'prop_name', 'line_1', 'line_2', 'city', 'country']) }}</td>
                <td>{{ optional($invoice->contact)->full_name ?? 'N/A' }}</td>
                <td>£{{ number_format($invoice->total_amount, 2) }}</td>
                <td>
                    @php
                        $statusColors = [
                            1 => 'bg-warning',  // Pending
                            2 => 'bg-success',  // Paid
                            3 => 'bg-danger',   // Overdue
                            4 => 'bg-secondary' // Cancelled
                        ];
                        $statusText = [
                            1 => 'Pending',
                            2 => 'Paid',
                            3 => 'Overdue',
                            4 => 'Cancelled'
                        ];
                    @endphp
                    <span class="badge {{ $statusColors[$invoice->status_id] ?? 'bg-secondary' }}">
                        {{ $statusText[$invoice->status_id] ?? 'Unknown' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $invoices->appends(request()->query())->links() }}
    </div>
</div>
@endsection
