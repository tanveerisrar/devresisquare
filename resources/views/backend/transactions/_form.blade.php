@csrf

@if(isset($transaction))
    <input type="hidden" name="id" value="{{ $transaction->id }}">
@endif

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Transaction Date</label>
        <input type="date" name="transaction_date" id="transaction_date" class="form-control"
            value="{{ old('transaction_date', $transaction->transaction_date ?? $transaction->date ?? \Carbon\Carbon::now()->toDateString()) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Transaction Number</label>
        <input type="text" name="transaction_number" id="transaction_number" class="form-control"
            value="{{ old('transaction_number', $transaction->transaction_number ?? $transaction_number ?? '') }}"
            placeholder="Auto-generated if left blank">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Transaction Type</label>
        <select name="transaction_type" id="transaction_type" class="form-control" required>
            <option value="credit" {{ old('transaction_type', $transaction->transaction_type ?? '') === 'credit' ? 'selected' : '' }}>Credit</option>
            <option value="debit" {{ old('transaction_type', $transaction->transaction_type ?? '') === 'debit' ? 'selected' : '' }}>Debit</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Category</label>
        <select name="transaction_category_id" class="form-control" required>
            <option value="">-- Select Category --</option>
            @foreach($categories as $id => $name)
                <option value="{{ $id }}" {{ old('transaction_category_id', $transaction->transaction_category_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Invoice (optional)</label>
        <select name="invoice_id" class="form-control">
            <option value="">-- Select Invoice --</option>
            @if(!empty($invoices))
                @foreach($invoices as $id => $label)
                    <option value="{{ $id }}" {{ old('invoice_id', $transaction->invoice_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Property (optional)</label>
        <select name="property_id" class="form-control">
            <option value="">-- Select Property --</option>
            @foreach($properties as $id => $label)
                <option value="{{ $id }}" {{ old('property_id', $transaction->property_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Payer (optional)</label>
        <select name="payer_id" class="form-control">
            <option value="">-- Select Payer --</option>
            @foreach($users as $id => $label)
                <option value="{{ $id }}" {{ old('payer_id', $transaction->payer_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Payee (optional)</label>
        <select name="payee_id" class="form-control">
            <option value="">-- Select Payee --</option>
            @foreach($users as $id => $label)
                <option value="{{ $id }}" {{ old('payee_id', $transaction->payee_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Payment Method</label>
        <select name="payment_method_id" id="payment_method_id" class="form-control">
            <option value="">-- Select Method --</option>
            @foreach($methods as $id => $label)
                <option value="{{ $id }}" {{ old('payment_method_id', $transaction->payment_method_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Bank Account</label>
        <select name="bank_account_id" id="bank_account_id" class="form-control">
            <option value="">-- Select Bank Account --</option>
            @foreach($accounts as $id => $label)
                <option value="{{ $id }}" {{ old('bank_account_id', $transaction->bank_account_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Reference</label>
        <input type="text" name="transaction_reference" class="form-control" value="{{ old('transaction_reference', $transaction->transaction_reference ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount', $transaction->amount ?? '') }}" required>
    </div>

    <div class="col-md-4 mb-3 d-none">
        <label>Total Amount</label>
        <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" value="{{ old('total_amount', $transaction->total_amount ?? '') }}" readonly>
    </div>

    <div class="col-md-4 mb-3">
        <label>Status</label>
        <select name="status" class="form-control" required>
            <option value="pending" {{ old('status', $transaction->status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ old('status', $transaction->status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ old('status', $transaction->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
</div>

<div class="mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control">{{ old('notes', $transaction->notes ?? '') }}</textarea>
</div>

<script>
(function () {
    function parseFloatSafe(v){ v = parseFloat(v); return isNaN(v) ? 0 : v; }
    function updateTotal() {
        const amount = parseFloatSafe(document.getElementById('amount').value);
        document.getElementById('total_amount').value = amount.toFixed(2);
    }
    ['amount'].forEach(id=>{
        const el = document.getElementById(id);
        if(!el) return;
        el.addEventListener('input', updateTotal);
        el.addEventListener('change', updateTotal);
    });
    document.addEventListener('DOMContentLoaded', updateTotal);
})();
</script>
