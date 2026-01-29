<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
// use Illuminate\Http\Request; // Removed duplicate import
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // If employee, only show their attendance
        if ($user->hasRole('employee') && $user->employee) {
            $attendances = Attendance::where('employee_id', $user->employee->id)->latest()->paginate(15);
        } else {
            // Admin/HR/Manager can see all (filtering can be added later)
            $attendances = Attendance::with('employee.user')->latest()->paginate(15);
        }

        return AttendanceResource::collection($attendances);
    }

    /**
     * Store a newly created resource in storage (Clock In).
     */
    public function store(Request $request)
    {
        // Assume authenticated user is clocking in
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['message' => 'User is not an employee.'], 403);
        }

        // Check if already clocked in today
        $existing = Attendance::where('employee_id', $employee->id)
            ->where('date', Carbon::today())
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Already checked in for today.'], 400);
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => Carbon::today(),
            'check_in' => Carbon::now(),
            'status' => 'present', // Logic for 'late' can be added here based on time
        ]);

        return new AttendanceResource($attendance);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new AttendanceResource(Attendance::findOrFail($id));
    }

    /**
     * Update the specified resource in storage (Clock Out).
     */
    public function update(Request $request, string $id)
    {
        // In this simplified flow, we use update to Clock Out.
        // Ideally, we might use a dedicated endpoint like /attendance/clock-out

        // If passing ID is tricky for current user, we can look up open attendance
        if ($id === 'clock-out') {
            $employee = $request->user()->employee;
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', Carbon::today())
                ->whereNull('check_out')
                ->firstOrFail();
        } else {
            $attendance = Attendance::findOrFail($id);
        }

        $attendance->update([
            'check_out' => Carbon::now(),
        ]);

        return new AttendanceResource($attendance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Attendance::findOrFail($id)->delete();
        return response()->noContent();
    }
}
