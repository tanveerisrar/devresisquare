@extends('backend.layout.app')

@php
    $status = strtoupper($invoice->status ?? 'draft');
@endphp

@push('styles')
<style>
    .invoice-shell { max-width: 900px; margin: 20px auto; background: #fff; border: 1px solid #e6e9ed; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
    .invoice-hero { background: #eceff4; padding: 28px; }
    .invoice-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; align-items: center; }
    .invoice-meta { text-align: right; }
    .billto { margin-top: 16px; font-size: 13px; }
    .billto strong { display:block; margin-bottom:4px; }
    .table-items { width: 100%; border-collapse: collapse; margin-top: 12px; }
    .table-items th, .table-items td { border: 1px solid #dfe3e8; padding: 8px 10px; }
    .table-items th { background: #111; color: #fff; font-weight: 600; font-size: 12px; }
    .totals { width: 320px; margin-left: auto; margin-top: 12px; border-collapse: collapse; }
    .totals th, .totals td { padding: 6px 10px; border: 1px solid #dfe3e8; }
    .totals th { background: #f7f8fa; text-align: left; }
    .mt-12 { margin-top: 12px; }
    .text-right { text-align: right; }
    .badge-status { font-weight: 700; font-size: 12px; text-transform: uppercase; }
    .status-PAID { color: #0f9d58; }
    .status-PARTIAL { color: #ff8c00; }
    .status-DRAFT { color: #3b82f6; }
    .status-CANCELLED { color: #6b7280; text-decoration: line-through; }
    .btn-stripe { padding: 10px 16px; border: none; background: #635bff; color: #fff; border-radius: 4px; cursor: pointer; }
    .notes-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 12px; }
</style>
@endpush

@section('content')
<div class="mt-md-4 me-md-4 me-3 mt-3">
    <div class="invoice-shell">
        <div class="invoice-hero">
            <div class="invoice-grid">
                <div>
                    <img loading="lazy" src="https://laravel.resisquare.co.uk/asset/images/resisquare-logo.svg" height="44" alt="Resisquare">
                    <div style="margin-top:14px; font-style: italic; color:#444;">
                        {{ get_setting('company_name') ?: 'Resisquare' }}<br>
                        {{ get_setting('contact_address') ?: 'UK, London' }}<br>
                        {{ get_setting('contact_email') ?: '' }}<br>
                        {{ get_setting('contact_phone') ?: '' }}
                    </div>
                </div>
                <div class="invoice-meta">
                    <h2 style="margin:0;">Invoice</h2>
                    <div># {{ $invoice->invoice_no }}</div>
                    <div class="badge-status status-{{ $status }}">{{ $status }}</div>
                    <div style="margin-top:10px; font-size: 12px;">
                        <div>Invoice Date: {{ $invoice->invoice_date }}</div>
                        <div>Invoice Due Date: {{ $invoice->due_date ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="billto">
                <strong>Bill To:</strong>
                <div>{{ optional($customer)->name ?? 'N/A' }}</div>
                <div style="font-size:12px;color:#4b5563;">{{ optional($customer)->email }}</div>
            </div>
        </div>

        <div style="padding: 18px 24px;">
            <table class="table-items">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Item</th>
                        <th>Description</th>
                        <th style="width:70px;" class="text-right">Qty</th>
                        <th style="width:90px;" class="text-right">Rate</th>
                        <th style="width:90px;" class="text-right">Discount</th>
                        <th style="width:90px;" class="text-right">Tax</th>
                        <th style="width:110px;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $idx => $row)
                        @php
                            $lineBase = max(0, ($row->quantity * $row->rate) - ($row->discount ?? 0));
                            $lineTotal = $lineBase + ($row->tax_amount ?? 0);
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $row->item_name }}</td>
                            <td>{{ $row->description }}</td>
                            <td class="text-right">{{ number_format($row->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($row->rate, 2) }}</td>
                            <td class="text-right">{{ number_format($row->discount ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($row->tax_amount ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($lineTotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="totals mt-12">
                <tr><th>Subtotal</th><td class="text-right">{{ number_format($subtotal, 2) }}</td></tr>
                <tr><th>Tax Total</th><td class="text-right">{{ number_format($taxTotal, 2) }}</td></tr>
                <tr><th>Grand Total</th><td class="text-right">{{ number_format($total, 2) }}</td></tr>
                <tr><th>Paid</th><td class="text-right">{{ number_format($paid, 2) }}</td></tr>
                <tr><th>Balance</th><td class="text-right">{{ number_format($balance, 2) }}</td></tr>
            </table>

            <div class="notes-box mt-12">
                <strong>Notes:</strong>
                <div style="margin-top:4px;">{{ $invoice->notes ?? 'N/A' }}</div>
            </div>

            <div class="mt-12">
                <h6 style="margin-bottom:6px;">Payments</h6>
                <table class="table-items">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Bank</th>
                            <th class="text-right">Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payments as $pidx => $pay)
                            <tr>
                                <td>{{ $pidx + 1 }}</td>
                                <td>{{ $pay->payment_date }}</td>
                                <td>{{ optional($pay->paymentMethod)->name ?? '-' }}</td>
                                <td>{{ optional($pay->bankAccount)->account_name ?? '-' }}</td>
                                <td class="text-right">{{ number_format($pay->amount, 2) }}</td>
                                <td>{{ $pay->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No payments recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(($balance ?? 0) > 0)
                <form action="{{ route($routeName . '.pay', $invoice->id) }}" method="POST" style="margin-top:14px;">
                    @csrf
                    <button type="submit" class="btn-stripe">Pay with Stripe (Test)</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
