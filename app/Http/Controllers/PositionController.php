<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\PositionResource;
use App\Models\Position;
// use Illuminate\Http\Request; // Removed duplicate import

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PositionResource::collection(Position::with('department')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'level' => 'nullable|string',
        ]);

        $position = Position::create($validated);
        return new PositionResource($position->load('department'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $position = Position::with(['department', 'employees'])->findOrFail($id);
        return new PositionResource($position);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'department_id' => 'sometimes|exists:departments,id',
            'level' => 'nullable|string',
        ]);

        $position->update($validated);
        return new PositionResource($position->load('department'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
        return response()->noContent();
    }
}
