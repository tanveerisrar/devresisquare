<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\BankAccount;
use Illuminate\Http\Request;

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
        $properties = Property::pluck('name', 'id');
        $users      = User::pluck('name', 'id');
        $methods    = PaymentMethod::pluck('name', 'id');
        $accounts   = BankAccount::pluck('account_name', 'id');

        return view('backend.transactions.create', compact('categories', 'invoices', 'properties', 'users', 'methods', 'accounts'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'                   => 'required|date',
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
            'tax_amount'             => 'nullable|numeric|min:0',
            'total_amount'           => 'nullable|numeric|min:0',
            'transaction_reference'  => 'nullable|string|max:255',
            'status'                 => 'required|string|in:pending,completed,cancelled',
            'notes'                  => 'nullable|string',
        ]);

        $transaction = Transaction::create($validated);

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
        $properties = Property::pluck('name', 'id');
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
