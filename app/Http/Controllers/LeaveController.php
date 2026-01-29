<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\LeaveResource;
use App\Models\Leave;
// use Illuminate\Http\Request; // Removed duplicate import
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('employee') && $user->employee) {
            $leaves = Leave::where('employee_id', $user->employee->id)->with('leaveType')->latest()->paginate(10);
        } else {
            $leaves = Leave::with(['employee.user', 'leaveType'])->latest()->paginate(10);
        }

        return LeaveResource::collection($leaves);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['message' => 'User is not an employee.'], 403);
        }

        $leave = Leave::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return new LeaveResource($leave->load('leaveType'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new LeaveResource(Leave::with(['employee.user', 'leaveType'])->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $leave = Leave::findOrFail($id);

        // Simple approval flow for now: 'approve' or 'reject' action
        $action = $request->input('action'); // approve, reject

        if ($action === 'approve') {
            // Check role to determine level of approval
            if ($request->user()->hasRole('hr') || $request->user()->hasRole('admin')) {
                $leave->update([
                    'status' => 'approved',
                    'hr_approved_at' => Carbon::now(),
                    'manager_approved_at' => $leave->manager_approved_at ?? Carbon::now(), // Auto approve manager step if HR does it
                ]);
            } elseif ($request->user()->hasRole('manager')) {
                $leave->update([
                    'status' => 'approved_manager',
                    'manager_approved_at' => Carbon::now(),
                ]);
            }
        } elseif ($action === 'reject') {
            $leave->update([
                'status' => 'rejected',
                'rejected_at' => Carbon::now(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);
        }

        return new LeaveResource($leave);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $leave = Leave::findOrFail($id);
        if ($leave->status === 'pending') {
            $leave->delete();
            return response()->noContent();
        }
        return response()->json(['message' => 'Cannot delete processed leave.'], 403);
    }
}
