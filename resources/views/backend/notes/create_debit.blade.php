@extends('backend.layout.app')

@section('content')
<div class="container py-4">
    <h3>Create Debit Note</h3>
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

    <form method="POST" action="{{ route('backend.debit_notes.store') }}" class="card shadow-sm p-4">
        @csrf

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Party <span class="text-danger">*</span></label>
                <input type="number" name="party_id" class="form-control" value="{{ old('party_id') }}" placeholder="User ID" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Party Role <span class="text-danger">*</span></label>
                <select name="party_role" class="form-select" required>
                    <option value="">-- Select --</option>
                    @foreach(['client','vendor'] as $role)
                        <option value="{{ $role }}" {{ old('party_role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Note Date <span class="text-danger">*</span></label>
                <input type="date" name="note_date" class="form-control" value="{{ old('note_date', now()->toDateString()) }}" required>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="total_amount" class="form-control" value="{{ old('total_amount') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Account Header</label>
                <select name="account_header_id" class="form-select">
                    <option value="">-- Optional: link header --</option>
                    @foreach($accountHeaders as $header)
                        <option value="{{ $header->id }}" {{ old('account_header_id') == $header->id ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_',' ', $header->header_type)) }} — {{ $header->name }} ({{ $header->reference_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Reference Number</label>
                <input type="text" class="form-control" value="Auto on save" readonly>
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Optional">{{ old('notes') }}</textarea>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Process Debit Note</button>
        </div>
    </form>
</div>
@endsection
