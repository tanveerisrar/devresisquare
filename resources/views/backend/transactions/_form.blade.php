@csrf

<div class="mb-3">
    <label>Date</label>
    <input type="date" name="date" class="form-control" value="{{ old('date', $transaction->date ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Transaction Type</label>
    <select name="transaction_type" class="form-control" required>
        <option value="credit" {{ old('transaction_type', $transaction->transaction_type ?? '') === 'credit' ? 'selected' : '' }}>Credit</option>
        <option value="debit" {{ old('transaction_type', $transaction->transaction_type ?? '') === 'debit' ? 'selected' : '' }}>Debit</option>
    </select>
</div>

<div class="mb-3">
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

<div class="mb-3">
    <label>Amount</label>
    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $transaction->amount ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Tax Amount</label>
    <input type="number" step="0.01" name="tax_amount" class="form-control" value="{{ old('tax_amount', $transaction->tax_amount ?? '') }}">
</div>

<div class="mb-3">
    <label>Total Amount</label>
    <input type="number" step="0.01" name="total_amount" class="form-control" value="{{ old('total_amount', $transaction->total_amount ?? '') }}">
</div>

<div class="mb-3">
    <label>Transaction Reference</label>
    <input type="text" name="transaction_reference" class="form-control" value="{{ old('transaction_reference', $transaction->transaction_reference ?? '') }}">
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control" required>
        <option value="pending" {{ old('status', $transaction->status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="completed" {{ old('status', $transaction->status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="cancelled" {{ old('status', $transaction->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
</div>

<div class="mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control">{{ old('notes', $transaction->notes ?? '') }}</textarea>
</div>
