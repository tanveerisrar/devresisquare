<?php

namespace App\Http\Controllers\Backend;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentsController 
{   
    /**
     * Create or update a document record
     * Expected input: documentable_type, documentable_id, upload_ids, optional document_id for update, optional document_type_id
     */
    public function saveDocumentData(array $data)
    {
        if (!empty($data['document_id'])) {
            // Update existing
            $document = Document::where('documentable_type', $data['documentable_type'])
                                ->where('documentable_id', $data['documentable_id'])
                                ->findOrFail($data['document_id']);
            $document->update([
                'upload_ids'       => $data['upload_ids'],
                'document_type_id' => $data['document_type_id'] ?? null,
            ]);
        } else {
            // Create new
            $document = Document::create([
                'documentable_type'   => $data['documentable_type'],
                'documentable_id'     => $data['documentable_id'],
                'upload_ids'          => $data['upload_ids'],
                'document_type_id'    => $data['document_type_id'] ?? null,
            ]);
        }
        return $document;
    }

    /**
     * API endpoint to store or update document
     */
    public function storeOrUpdate(Request $request)
    {
        $data = $request->validate([
            'documentable_type'   => ['required', 'string'],
            'documentable_id'     => ['required', 'integer'],
            'upload_ids'          => ['required', 'string'], // comma-separated IDs
            'document_type_id'    => ['nullable', 'integer', Rule::exists('document_types', 'id')],
            'document_id'         => ['nullable', 'integer', Rule::exists('documents', 'id')],
        ]);

        $document = $this->saveDocumentData($data);

        return response()->json([
            'status'   => true,
            'message'  => !empty($data['document_id']) ? 'Document updated' : 'Document created',
            'document' => $document->load('type'),
        ]);
    }

    /**
     * List documents for a given parent entity; optional single document for edit
     */
    public function listDocuments(Request $request)
    {
        $data = $request->validate([
            'documentable_type' => ['required', 'string'],
            'documentable_id'   => ['required', 'integer'],
            'document_id'       => ['nullable', 'integer', Rule::exists('documents', 'id')],
        ]);

        $query = Document::with('type')
                    ->where('documentable_type', $data['documentable_type'])
                    ->where('documentable_id', $data['documentable_id']);

        $documents = $query->orderByDesc('updated_at')->get();

        $document = null;
        if (!empty($data['document_id'])) {
            $document = $documents->firstWhere('id', $data['document_id']);
            if (! $document) {
                $document = $query->find($data['document_id']);
            }
        }

        return response()->json(compact('documents', 'document'));
    }

    /**
     * Show a specific Document by ID
     */
    public function showDocument($id)
    {
        $document = Document::with('type')->findOrFail($id);

        return response()->json([
            'id'               => $document->id,
            'upload_ids'       => $document->upload_ids,
            'document_type'    => optional($document->type)->name,
            'created_at'       => $document->created_at,
            'updated_at'       => $document->updated_at,
        ]);
    }

    /**
     * Delete a Document record by ID
     */
    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Document deleted successfully!',
        ]);
    }
}
