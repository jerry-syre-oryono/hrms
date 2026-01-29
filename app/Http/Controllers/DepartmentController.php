<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
// use Illuminate\Http\Request; // Removed duplicate import

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DepartmentResource::collection(Department::with(['manager', 'positions'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $department = Department::create($validated);
        return new DepartmentResource($department->load(['manager', 'positions']));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $department = Department::with(['manager', 'positions', 'employees'])->findOrFail($id);
        return new DepartmentResource($department);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $department->update($validated);
        return new DepartmentResource($department->load(['manager', 'positions']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        $department->delete();
        return response()->noContent();
    }
}
