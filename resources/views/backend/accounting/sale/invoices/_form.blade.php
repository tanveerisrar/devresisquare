@csrf
@if(isset($item))
    @method('PUT')
@endif

@php
    $oldVal = fn($key, $default = null) => old(
        $key,
        data_get($item ?? null, $key, data_get($defaults ?? [], $key, $default))
    );
    $users = $selectOptions['user_id'] ?? [];
    $statuses = ['draft' => 'Draft', 'issued' => 'Issued', 'paid' => 'Paid', 'partial' => 'Partial', 'cancelled' => 'Cancelled'];
    $taxes = $selectOptions['tax_id'] ?? [];
    $taxRatesMap = $selectOptions['tax_rates'] ?? [];
    $existingItems = old('items', isset($item) ? $item->items->map(function($it){
        return [
            'item_name' => $it->item_name,
            'description' => $it->description,
            'quantity' => $it->quantity,
            'rate' => $it->rate,
            'discount' => $it->discount,
            'tax_id' => $it->tax_id,
            'tax_rate' => $it->tax_rate,
            'tax_amount' => $it->tax_amount,
            'line_total' => $it->line_total,
            'notes' => $it->notes,
        ];
    })->toArray() : []);
    if (empty($existingItems)) {
        $existingItems = [
            ['item_name' => '', 'description' => '', 'quantity' => 1, 'rate' => 0, 'discount' => 0, 'tax_id' => null, 'tax_rate' => 0, 'tax_amount' => 0, 'line_total' => 0, 'notes' => '']
        ];
    }
@endphp

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Invoice No <span class="text-danger">*</span></label>
                <input type="text" name="invoice_no" class="form-control" value="{{ $oldVal('invoice_no') }}" required readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Customer <span class="text-danger">*</span></label>
                <select name="user_id" class="form-select" required>
                    <option value="">Select</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ (string)$oldVal('user_id') === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ $oldVal('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                <input type="date" name="invoice_date" id="invoice-date" class="form-control" value="{{ $oldVal('invoice_date') ?? now()->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" id="due-date" class="form-control" value="{{ $oldVal('due_date') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Discount Type</label>
                <select name="discount_type" id="discount-type" class="form-select">
                    <option value="" {{ $oldVal('discount_type') === '' ? 'selected' : '' }}>No discount</option>
                    <option value="before_tax" {{ $oldVal('discount_type') === 'before_tax' ? 'selected' : '' }}>Before Tax</option>
                    <option value="after_tax" {{ $oldVal('discount_type') === 'after_tax' ? 'selected' : '' }}>After Tax</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3 d-none">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="total_amount" class="form-control" value="{{ $oldVal('total_amount') }}" required readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Balance Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="balance_amount" class="form-control" value="{{ $oldVal('balance_amount') }}" required readonly>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 d-flex align-items-center gap-2"><span class="bi bi-list"></span> Invoice Items</h5>
            <button type="button" class="btn btn-sm btn-success" id="add-item-row">+ Add Item</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" id="items-table">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width:220px;">Item Name</th>
                        <th>Description</th>
                        <th style="width:120px;">Unit Price</th>
                        <th style="width:100px;">Quantity</th>
                        <th style="width:150px;">Tax</th>
                        <th style="width:110px;">Tax Rate (%)</th>
                        <th style="width:120px;">Tax Amount</th>
                        <th style="width:140px;">Total (Incl. Tax)</th>
                        <th style="width:70px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($existingItems as $index => $row)
                    <tr>
                        <td>
                            <input type="text" name="items[{{ $index }}][item_name]" class="form-control item-name" value="{{ $row['item_name'] }}" required>
                            <input type="hidden" name="items[{{ $index }}][discount]" class="discount" value="{{ $row['discount'] }}">
                        </td>
                        <td><input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $row['description'] }}"></td>
                        <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][rate]" class="form-control rate text-end" value="{{ $row['rate'] }}"></td>
                        <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]" class="form-control qty text-end" value="{{ $row['quantity'] }}"></td>
                        <td>
                            <select name="items[{{ $index }}][tax_id]" class="form-select tax-id">
                                <option value="">None</option>
                                @foreach($taxes as $id => $name)
                                    <option value="{{ $id }}" data-rate="{{ $taxRatesMap[$id] ?? 0 }}" {{ (string)$row['tax_id'] === (string)$id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][tax_rate]" class="form-control tax-rate text-end" value="{{ $row['tax_rate'] }}"></td>
                        <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][tax_amount]" class="form-control tax-amount text-end" value="{{ $row['tax_amount'] }}" readonly></td>
                        <td><input type="number" step="0.01" min="0" name="items[{{ $index }}][line_total]" class="form-control line-total text-end" value="{{ $row['line_total'] }}" readonly></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="6" class="text-end">Subtotal:</th>
                        <th colspan="2"><input type="text" class="form-control text-end" id="subtotal-display" readonly></th>
                    </tr>
                    <tr class="table-light">
                        <th colspan="6" class="text-end">Tax Total:</th>
                        <th colspan="2"><input type="text" class="form-control text-end" id="taxtotal-display" readonly></th>
                    </tr>
                    <tr class="table-light">
                        <th colspan="6" class="text-end align-middle">Discount:</th>
                        <th colspan="2">
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="discount_value" id="discount-value" class="form-control text-end" value="{{ $oldVal('discount_value', 0) }}">
                                <select name="discount_mode" id="discount-mode" class="form-select" style="max-width: 150px;">
                                    <option value="percent" {{ $oldVal('discount_mode', 'percent') === 'percent' ? 'selected' : '' }}>%</option>
                                    <option value="fixed" {{ $oldVal('discount_mode', 'percent') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                            </div>
                            <input type="hidden" name="discount_amount" id="discount-amount" value="{{ $oldVal('discount_amount', 0) }}">
                        </th>
                    </tr>
                    <tr class="table-light">
                        <th colspan="6" class="text-end">Discount Amount:</th>
                        <th colspan="2"><input type="text" class="form-control text-end" id="discount-amount-display" readonly></th>
                    </tr>
                    <tr class="table-primary">
                        <th colspan="6" class="text-end">Grand Total:</th>
                        <th colspan="2"><input type="text" class="form-control text-end fw-bold" id="grandtotal-display" readonly></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3">{{ $oldVal('notes') }}</textarea>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route($routeName . '.index') }}" class="btn btn-secondary">Cancel</a>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const invoiceDateInput = document.getElementById('invoice-date');
    const dueDateInput = document.getElementById('due-date');

    function setDueDate() {
        if (!invoiceDateInput.value) return;
        const d = new Date(invoiceDateInput.value);
        d.setDate(d.getDate() + 30);
        dueDateInput.value = d.toISOString().split('T')[0];
    }

    invoiceDateInput.addEventListener('change', setDueDate);

    if (!dueDateInput.value && invoiceDateInput.value) {
        setDueDate();
    }

    const tableBody = document.querySelector('#items-table tbody');
    const addBtn = document.querySelector('#add-item-row');
    const totalField = document.querySelector('input[name="total_amount"]');
    const balanceField = document.querySelector('input[name="balance_amount"]');
    const subTotalDisplay = document.getElementById('subtotal-display');
    const taxTotalDisplay = document.getElementById('taxtotal-display');
    const grandTotalDisplay = document.getElementById('grandtotal-display');
    const discountType = document.getElementById('discount-type');
    const discountValue = document.getElementById('discount-value');
    const discountMode = document.getElementById('discount-mode');
    const discountAmount = document.getElementById('discount-amount');
    const discountAmountDisplay = document.getElementById('discount-amount-display');

    function recalcRow(tr) {
        const qty = parseFloat(tr.querySelector('.qty').value) || 0;
        const rate = parseFloat(tr.querySelector('.rate').value) || 0;
        const discount = parseFloat(tr.querySelector('.discount').value) || 0;
        const taxRate = parseFloat(tr.querySelector('.tax-rate').value) || 0;
        const base = Math.max(0, (qty * rate) - discount);
        const tax = taxRate > 0 ? base * taxRate / 100 : 0;
        const total = base + tax;
        tr.querySelector('.tax-amount').value = tax.toFixed(2);
        tr.querySelector('.line-total').value = total.toFixed(2);
        recalcTotals();
    }

    function recalcTotals() {
        let baseSum = 0, taxSum = 0;
        tableBody.querySelectorAll('tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.qty')?.value) || 0;
            const rate = parseFloat(tr.querySelector('.rate')?.value) || 0;
            const discount = parseFloat(tr.querySelector('.discount')?.value) || 0;
            const base = Math.max(0, (qty * rate) - discount);
            const tax = parseFloat(tr.querySelector('.tax-amount')?.value) || 0;
            baseSum += base;
            taxSum += tax;
        });
        const preGrand = baseSum + taxSum;
        const dtype = discountType?.value || '';
        const dval = parseFloat(discountValue?.value) || 0;
        const dmode = discountMode?.value || 'percent';
        const discountBase = dtype === 'before_tax' ? baseSum : preGrand;
        let dAmount = 0;
        if (dtype && dval > 0 && discountBase > 0) {
            dAmount = dmode === 'percent' ? (discountBase * (dval / 100)) : dval;
            dAmount = Math.min(dAmount, discountBase);
        }

        const grand = Math.max(0, preGrand - dAmount);
        subTotalDisplay.value = baseSum.toFixed(2);
        taxTotalDisplay.value = taxSum.toFixed(2);
        discountAmountDisplay.value = dAmount.toFixed(2);
        discountAmount.value = dAmount.toFixed(2);
        grandTotalDisplay.value = grand.toFixed(2);
        totalField.value = grand.toFixed(2);
        balanceField.value = grand.toFixed(2);
    }

    function ensureDiscountTypeIfValue() {
        const dtype = discountType?.value || '';
        const dval = parseFloat(discountValue?.value) || 0;
        if (!dtype && dval > 0) {
            alert('Please select Discount Type before entering a discount.');
            discountValue.value = 0;
            discountValue.focus();
            return false;
        }
        return true;
    }

    function bindRow(tr) {
        ['qty','rate','discount','tax-rate'].forEach(cls => {
            tr.querySelectorAll('.' + cls).forEach(input => {
                input.addEventListener('input', () => recalcRow(tr));
            });
        });
        tr.querySelectorAll('.tax-id').forEach(select => {
            select.addEventListener('change', () => {
                const rate = parseFloat(select.selectedOptions[0]?.dataset.rate || 0);
                tr.querySelector('.tax-rate').value = rate;
                recalcRow(tr);
            });
        });
        tr.querySelector('.remove-row').addEventListener('click', () => {
            if (tableBody.rows.length > 1) tr.remove();
            recalcTotals();
        });
    }

    tableBody.querySelectorAll('tr').forEach(bindRow);
    tableBody.querySelectorAll('tr').forEach(recalcRow);

    discountType?.addEventListener('change', recalcTotals);
    discountMode?.addEventListener('change', recalcTotals);
    discountValue?.addEventListener('input', () => {
        if (ensureDiscountTypeIfValue()) recalcTotals();
    });

    addBtn.addEventListener('click', () => {
        const index = tableBody.rows.length;
        const tpl = `
        <tr>
            <td>
                <input type="text" name="items[${index}][item_name]" class="form-control item-name" required>
                <input type="hidden" name="items[${index}][discount]" class="discount" value="0">
            </td>
            <td><input type="text" name="items[${index}][description]" class="form-control"></td>
            <td><input type="number" step="0.01" min="0" name="items[${index}][rate]" class="form-control rate text-end" value="0"></td>
            <td><input type="number" step="0.01" min="0" name="items[${index}][quantity]" class="form-control qty text-end" value="1"></td>
            <td>
                <select name="items[${index}][tax_id]" class="form-select tax-id">
                    <option value="">None</option>
                    @foreach($taxes as $id => $name)
                        <option value="{{ $id }}" data-rate="{{ $taxRatesMap[$id] ?? 0 }}">{{ $name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" step="0.01" min="0" name="items[${index}][tax_rate]" class="form-control tax-rate text-end" value="0"></td>
            <td><input type="number" step="0.01" min="0" name="items[${index}][tax_amount]" class="form-control tax-amount text-end" value="0" readonly></td>
            <td><input type="number" step="0.01" min="0" name="items[${index}][line_total]" class="form-control line-total text-end" value="0" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
            </td>
        </tr>`;
        const temp = document.createElement('tbody');
        temp.innerHTML = tpl.trim();
        const tr = temp.firstElementChild;
        tableBody.appendChild(tr);
        bindRow(tr);
        recalcRow(tr);
    });
});
</script>
@endpush
