@extends('backend.layout.app')

@section('content')
<div class="container py-4">
    <h3>Receipt #{{ $receipt->reference_number }}</h3>
    <a href="{{ url()->previous() }}" class="btn btn-link mb-3">&larr; Back</a>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Invoice</dt>
                <dd class="col-sm-9">{{ $receipt->invoice->invoice_number ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Amount</dt>
                <dd class="col-sm-9">£{{ number_format($receipt->amount,2) }}</dd>

                <dt class="col-sm-3">Date</dt>
                <dd class="col-sm-9">{{ $receipt->receipt_date?->format('Y-m-d') }}</dd>

                <dt class="col-sm-3">Mode</dt>
                <dd class="col-sm-9">{{ $receipt->mode_of_payment ?? '—' }}</dd>

                <dt class="col-sm-3">Notes</dt>
                <dd class="col-sm-9">{{ $receipt->notes ?? '—' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
