@extends('backend.layout.app')

@section('content')
<div class="container py-4">
    <h3>Create Invoice</h3>
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

    <form method="POST" action="{{ route('admin.invoices.store') }}" class="card shadow-sm p-4">
        @csrf

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Invoice Type</label>
                <select name="invoice_type" class="form-select">
                    <option value="standard">Standard</option>
                    <option value="rent">Rent</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Charge To</label>
                <select name="user_id" class="form-select" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Link To</label>
                <input type="text" name="link_to" class="form-control" placeholder="Search/Link ref">
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label">Select Bank Account</label>
                <input type="text" name="bank_account" class="form-control" placeholder="Bank account">
            </div>
            <div class="col-md-4">
                <label class="form-label d-block">Charge on Rent</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="charge_on_rent" value="yes">
                    <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="charge_on_rent" value="no" checked>
                    <label class="form-check-label">No</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label d-block">Tax Included</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tax_included" value="1">
                    <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tax_included" value="0" checked>
                    <label class="form-check-label">No</label>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ old('tax_rate', 0) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Funds Goes To</label>
                <input type="text" name="funds_goes_to" class="form-control" placeholder="Account / entity">
            </div>
            <div class="col-md-3">
                <label class="form-label">Frequency</label>
                <input type="text" name="frequency" class="form-control" placeholder="e.g. Monthly">
            </div>
            <div class="col-md-3">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label">Account Header</label>
                <select name="account_header_id" class="form-select">
                    <option value="">-- Optional: Link header --</option>
                    @foreach($accountHeaders as $header)
                        <option value="{{ $header->id }}">{{ $header->name }} ({{ $header->reference_number }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Reference Number</label>
                <input type="text" class="form-control" value="Auto on save" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="{{ old('due_date', now()->addDays(14)->toDateString()) }}" required>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label">Penalty / Late Fee</label>
                <input type="number" step="0.01" name="penalty_late_fee" class="form-control" value="{{ old('penalty_late_fee') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Commission Type</label>
                <select name="commission_type" class="form-select">
                    <option value="">None</option>
                    <option value="percent">% Percentage</option>
                    <option value="flat">Flat</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Commission Value</label>
                <input type="number" step="0.01" name="commission_value" class="form-control" value="{{ old('commission_value') }}">
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <label class="form-label">Commission Charged To</label>
                <input type="text" name="commission_charged_to" class="form-control" value="{{ old('commission_charged_to') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Commission Payable To</label>
                <input type="text" name="commission_payable_to" class="form-control" value="{{ old('commission_payable_to') }}">
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Process Invoice</button>
        </div>
    </form>
</div>
@endsection
