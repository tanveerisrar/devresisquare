<?php

namespace App\Http\Controllers\Backend;

use App\Models\Notes;
use Illuminate\Http\Request;

class NotesController 
{   
    /**
     * Create or update a note
     * Expected input: noteable_type, noteable_id, type, content, optional note_id for update
     */
    // Add a method to handle logic, returning Note model
    public function saveNoteData(array $data)
    {
        if (!empty($data['note_id'])) {
            $note = Notes::where('noteable_type', $data['noteable_type'])
                        ->where('noteable_id', $data['noteable_id'])
                        ->findOrFail($data['note_id']);
            $note->update([
                'note_type_id' => $data['note_type_id'],
                'content' => $data['content'],
            ]);
        } else {
            $note = Notes::create([
                'noteable_type' => $data['noteable_type'],
                'noteable_id'   => $data['noteable_id'],
                'note_type_id'  => $data['note_type_id'],
                'content'       => $data['content'],
            ]);
        }
        return $note;
    }

    // Keep storeOrUpdate as an API endpoint
    public function storeOrUpdate(Request $request)
    {
        $data = $request->validate([
            'noteable_type' => 'required|string',
            'noteable_id'   => 'required|integer',
            'note_type_id'   => 'required|exists:note_types,id',
            'content'       => 'required|string',
            'note_id'       => 'nullable|exists:notes,id',
        ]);

        $note = $this->saveNoteData($data);

        return response()->json([
            'status'  => true,
            'message' => $data['note_id'] ? 'Note updated' : 'Note created',
            'note'    => $note->load('noteType'),
        ]);
    }
        

    // Get list of notes for a property (and optional single note for edit)
    /**
     * List notes optionally filtered by noteable_type and noteable_id.
     * You can also pass note_id to get a single note separately if needed.
     */
    public function listNotes(Request $request)
    {
        $data = $request->validate([
            'noteable_type' => 'required|string',
            'noteable_id'   => 'required|integer',
            'note_id'       => 'nullable|integer|exists:notes,id',
        ]);

        // Get all notes for this noteable entity
        $notes = Notes::with('noteType')->where('noteable_type', $data['noteable_type'])
                    ->where('noteable_id', $data['noteable_id'])
                    ->orderByDesc('updated_at')
                    ->get();

        // If a single note id provided, get that note, else null
        $note = null;
        if (!empty($data['note_id'])) {
            $note = $notes->firstWhere('id', $data['note_id']);
            // Optional: if not found in list, fallback to querying directly:
            if (!$note) {
                $note = Notes::with('noteType')->where('noteable_type', $data['noteable_type'])
                            ->where('noteable_id', $data['noteable_id'])
                            ->find($data['note_id']);
            }
        }

        return response()->json(compact('notes', 'note'));
    }


    // Show single note content (for popup)
    /**
     * Show a single note by ID
     */
    public function showNote($id)
    {
        $note = Notes::with('noteType')->findOrFail($id);

        return response()->json([
            'id'            => $note->id,
            'type'          => $note->noteType->name ?? '',
            'content'       => $note->content,
            'created_at'    => $note->created_at,
            'updated_at'    => $note->updated_at,
        ]);
    }

    /**
     * Delete a note by ID
     */
    public function deleteNote($id)
    {
        $note = Notes::findOrFail($id);
        $note->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Note deleted successfully!',
        ]);
    }
}
