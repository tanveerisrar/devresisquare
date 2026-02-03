<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AccountHeader;
use App\Models\DocumentSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AccountHeaderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accountHeaders = AccountHeader::latest()->paginate(10);
        return view('backend.account_headers.index', compact('accountHeaders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $headerTypes = ['invoice', 'credit_note', 'debit_note'];
        return view('backend.account_headers.create', compact('headerTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'header_type' => 'required|in:invoice,credit_note,debit_note',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $referenceNumber = function_exists('generateDocumentNumber')
            ? generateDocumentNumber('account_header', 'HDR')
            : (Schema::hasTable('document_sequences')
                ? DocumentSequence::generate('account_header', 'HDR')
                : 'HDR-' . str_pad((AccountHeader::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT));

        AccountHeader::create([
            'header_type' => $validated['header_type'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? true,
            'reference_number' => $referenceNumber,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('backend.account_headers.index')
                         ->with('success', 'Account Header created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountHeader $accountHeader)
    {
        $headerTypes = ['invoice', 'credit_note', 'debit_note'];
        return view('backend.account_headers.edit', compact('accountHeader', 'headerTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountHeader $accountHeader)
    {
        $validated = $request->validate([
            'header_type' => 'required|in:invoice,credit_note,debit_note',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $accountHeader->update([
            'header_type' => $validated['header_type'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? false,
            'updated_by' => Auth::id(),
        ]);

        flash('Account Header updated successfully.')->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountHeader $accountHeader)
    {
        $accountHeader->delete();
        return redirect()->route('backend.account_headers.index')
                         ->with('success', 'Account Header deleted successfully.');
    }
}
