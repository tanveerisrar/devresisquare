<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Invoice;
use App\Models\DocumentSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ReceiptController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $invoices = Invoice::orderBy('invoice_number', 'desc')->limit(50)->get();
        return view('backend.receipts.create', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'receipt_date' => 'required|date',
            'mode_of_payment' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'send_email' => 'nullable|boolean',
            'send_sms' => 'nullable|boolean',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        $reference = function_exists('generateDocumentNumber')
            ? generateDocumentNumber('receipt', 'RCPT')
            : (Schema::hasTable('document_sequences')
                ? DocumentSequence::generate('receipt', 'RCPT')
                : 'RCPT-' . str_pad((Receipt::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT));

        $receipt = Receipt::create([
            'invoice_id' => $data['invoice_id'],
            'amount' => $data['amount'],
            'receipt_date' => $data['receipt_date'],
            'mode_of_payment' => $data['mode_of_payment'] ?? null,
            'notes' => $data['notes'] ?? null,
            'send_email' => $request->boolean('send_email'),
            'send_sms' => $request->boolean('send_sms'),
            'send_whatsapp' => $request->boolean('send_whatsapp'),
            'reference_number' => $reference,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.receipts.show', $receipt->id)
            ->with('success', 'Receipt created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Receipt $receipt)
    {
        $receipt->load('invoice');
        return view('backend.receipts.show', compact('receipt'));
    }
}
