<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use Illuminate\Http\Request;

class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * AJAX endpoint to list users for a select dropdown.
     */
    public function ajaxList(Request $request)
    {
        $term = $request->input('q');

        $query = User::query();

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%$term%")
                ->orWhere('email', 'like', "%$term%");
            });
        }

        $users = $query
            ->select('id', 'name', 'email')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $results = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => "{$user->name} - {$user->email}",
            ];
        });

        return response()->json(['results' => $results]);
    }
}
