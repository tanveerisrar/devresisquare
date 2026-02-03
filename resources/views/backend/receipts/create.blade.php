@extends('backend.layout.app')

@section('content')
<div class="container py-4">
    <h3>Create Receipt</h3>
    <a href="{{ url()->previous() }}" class="btn btn-link mb-3">&larr; Back</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('backend.receipts.store') }}" class="card shadow-sm p-4">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Search Invoice</label>
                <select name="invoice_id" class="form-select">
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->invoice_number }} — £{{ number_format($inv->total_amount,2) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Receipt Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Receipt Date</label>
                <input type="date" name="receipt_date" class="form-control" value="{{ old('receipt_date', now()->toDateString()) }}" required>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <label class="form-label">Reference Number</label>
                <input type="text" class="form-control" value="Auto on save" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Mode of Payment</label>
                <input type="text" name="mode_of_payment" class="form-control" value="{{ old('mode_of_payment') }}" placeholder="e.g. Bank Transfer">
            </div>
            <div class="col-md-4">
                <label class="form-label d-block">Notifications</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="send_email" value="1" {{ old('send_email') ? 'checked' : '' }}>
                    <label class="form-check-label">Email</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="send_sms" value="1" {{ old('send_sms') ? 'checked' : '' }}>
                    <label class="form-check-label">SMS</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="send_whatsapp" value="1" {{ old('send_whatsapp') ? 'checked' : '' }}>
                    <label class="form-check-label">WhatsApp</label>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Optional">{{ old('notes') }}</textarea>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Process Receipt</button>
        </div>
    </form>
</div>
@endsection
