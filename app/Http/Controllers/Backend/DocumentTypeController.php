<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::latest()->paginate(10);
        return view('backend.document_types.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $documentType = new DocumentType();
        return view('backend.document_types.form', compact('documentType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|unique:document_types,name',
            'description' => 'nullable|string',
        ]);

        DocumentType::create($request->only('name', 'description'));

        flash("Document type created!")->success();
        return redirect()->route('admin.document-types.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $documentType = DocumentType::findOrFail($id);
        return view('backend.document_types.show', compact('documentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $documentType = DocumentType::findOrFail($id);
        return view('backend.document_types.form', compact('documentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'        => 'required|string|unique:document_types,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $documentType = DocumentType::findOrFail($id);
        $documentType->update($request->only('name', 'description'));

        flash("Document type updated!")->success();
        return redirect()->route('admin.document-types.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $documentType = DocumentType::findOrFail($id);
        $documentType->delete();

        flash("Document type deleted.")->success();
        return redirect()->route('admin.document-types.index');
    }
}
