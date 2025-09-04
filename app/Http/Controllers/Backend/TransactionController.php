<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionCategory;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::with([
            'invoice',
            'property',
            'payer',
            'payee',
            'category',
            'paymentMethod',
            'bankAccount'
        ])
        ->latest()
        ->paginate(20);

        return view('backend.transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create()
    {
        $categories = TransactionCategory::where('is_active', true)->pluck('name', 'id');
        $invoices   = Invoice::pluck('invoice_number', 'id');
        $properties = Property::optionsForSelect();
        $users      = User::optionsForSelect();
        $methods    = PaymentMethod::pluck('name', 'id');
        $accounts   = BankAccount::pluck('account_name', 'id');

        // Pre-generate a transaction number (if you want a prefix specific to transactions)
        // adjust prefix as needed
        try {
            $transaction_number = generateReferenceNumber(Transaction::class, 'transaction_number', 'RESISQRETXN');
        } catch (\Throwable $e) {
            // fallback if helper fails for any reason
            $transaction_number = null;
        }

        // Prepare last balances per bank account for client-side preview
        // Grab latest transaction balance per bank_account_id
        $lastBalances = Transaction::whereNotNull('bank_account_id')
            ->orderBy('id', 'desc')
            ->get()
            ->unique('bank_account_id')
            ->pluck('balance', 'bank_account_id')
            ->toArray();

        return view('backend.transactions.create', compact(
            'categories', 'invoices', 'properties', 'users', 'methods', 'accounts', 'transaction_number', 'lastBalances'
        ));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date'       => 'nullable|date',
            'payment_method_id'      => 'nullable|exists:payment_methods,id',
            'bank_account_id'        => 'nullable|exists:bank_accounts,id',
            'transaction_number'     => 'nullable|string|max:255|unique:transactions,transaction_number',
            'transaction_type'       => 'required|string|in:credit,debit',
            'invoice_id'             => 'nullable|exists:invoices,id',
            'transaction_category_id'=> 'required|exists:transaction_categories,id',
            'property_id'            => 'nullable|exists:properties,id',
            'payer_id'               => 'nullable|exists:users,id',
            'payee_id'               => 'nullable|exists:users,id',
            'amount'                 => 'required|numeric|min:0',
            'total_amount'           => 'nullable|numeric|min:0',
            'transaction_reference'  => 'nullable|string|max:255',
            'status'                 => 'required|string|in:pending,completed,cancelled',
            'notes'                  => 'nullable|string',
        ]);

        // total is just amount (user removed tax); keep defensive server-side calculation
        $amount = (float) $validated['amount'];
        $total = isset($validated['total_amount']) && $validated['total_amount'] > 0
            ? (float) $validated['total_amount']
            : $amount;

        // ensure transaction number
        if (empty($validated['transaction_number'])) {
            $validated['transaction_number'] = generateReferenceNumber(Transaction::class, 'transaction_number', 'RESISQRETXN');
        }

        // determine credit/debit server-side (kept for ledger)
        if ($validated['transaction_type'] === 'credit') {
            $credit = $total;
            $debit = 0.0;
        } else {
            $debit = $total;
            $credit = 0.0;
        }

        $bankAccountId = $validated['bank_account_id'] ?? null;

        // compute last balance if bank account provided (optional)
        $lastBalance = 0.0;
        if ($bankAccountId) {
            $lastBalance = Transaction::where('bank_account_id', $bankAccountId)
                ->orderBy('id', 'desc')
                ->value('balance') ?? 0.0;
        }

        $newBalance = $lastBalance + $credit - $debit;

        $transaction = DB::transaction(function () use ($validated, $amount, $total, $credit, $debit, $newBalance) {
            $payload = $validated;
            $payload['amount'] = $amount;
            $payload['total_amount'] = $total;
            $payload['credit'] = $credit;
            $payload['debit'] = $debit;
            $payload['balance'] = $newBalance;
            if (empty($payload['transaction_date'])) {
                $payload['transaction_date'] = $payload['date'] ?? now()->toDateString();
            }

            return Transaction::create($payload);
        });

        // If transaction is linked to an invoice and the transaction is completed -> apply payment
        if (!empty($validated['invoice_id']) && $validated['status'] === 'completed') {
            $invoice = Invoice::find($validated['invoice_id']);
            if ($invoice) {
                // you can pass the transaction total_amount (or amount). We used total_amount.
                $invoice->applyPayment((float)$transaction->total_amount);
            }
        }

        return redirect()
            ->route('backend.transactions.index')
            ->with('success', 'Transaction created successfully.');
    }


    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load([
            'invoice',
            'property',
            'payer',
            'payee',
            'category',
            'paymentMethod',
            'bankAccount'
        ]);

        return view('backend.transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        $categories = TransactionCategory::where('is_active', true)->pluck('name', 'id');
        $invoices   = Invoice::pluck('invoice_number', 'id');
        $properties = Property::pluck('prop_name', 'id');
        $users      = User::pluck('name', 'id');
        $methods    = PaymentMethod::pluck('name', 'id');
        $accounts   = BankAccount::pluck('account_name', 'id');

        return view('backend.transactions.edit', compact('transaction', 'categories', 'invoices', 'properties', 'users', 'methods', 'accounts'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'date'                   => 'required|date',
            'payment_method_id'      => 'nullable|exists:payment_methods,id',
            'bank_account_id'        => 'nullable|exists:bank_accounts,id',
            'transaction_number'     => 'nullable|string|max:255|unique:transactions,transaction_number,' . $transaction->id,
            'transaction_type'       => 'required|string|in:credit,debit',
            'invoice_id'             => 'nullable|exists:invoices,id',
            'transaction_category_id'=> 'required|exists:transaction_categories,id',
            'property_id'            => 'nullable|exists:properties,id',
            'payer_id'               => 'nullable|exists:users,id',
            'payee_id'               => 'nullable|exists:users,id',
            'amount'                 => 'required|numeric|min:0',
            'tax_amount'             => 'nullable|numeric|min:0',
            'total_amount'           => 'nullable|numeric|min:0',
            'transaction_reference'  => 'nullable|string|max:255',
            'status'                 => 'required|string|in:pending,completed,cancelled',
            'notes'                  => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()
            ->route('backend.transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()
            ->route('backend.transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
}
