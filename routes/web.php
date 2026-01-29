<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('/employees', function () {
        $employees = \App\Models\Employee::with(['user', 'department', 'position'])->paginate(10);
        return Inertia::render('employees/index', [
            'employees' => \App\Http\Resources\EmployeeResource::collection($employees),
        ]);
    })->name('employees.index');

    Route::get('/departments', function () {
        $departments = \App\Models\Department::with(['manager', 'positions'])->get();
        return Inertia::render('departments/index', [
            'departments' => \App\Http\Resources\DepartmentResource::collection($departments),
        ]);
    })->name('departments.index');

    Route::get('/positions', function () {
        $positions = \App\Models\Position::with('department')->get();
        return Inertia::render('positions/index', [
            'positions' => \App\Http\Resources\PositionResource::collection($positions),
        ]);
    })->name('positions.index');

    Route::get('/attendance', function () {
        // Mock request for resource using current user
        $request = request();
        $controller = new \App\Http\Controllers\AttendanceController();
        // We can call the controller method directly or just replicate logic
        // For simplicity and to reuse resource logic:
        $resource = $controller->index($request);
        return Inertia::render('attendance/index', [
            'attendances' => $resource
        ]);
    })->name('attendance.index');

    Route::get('/leaves', function () {
        $leaves = \App\Models\Leave::with(['employee.user', 'leaveType'])->latest()->paginate(10);
        $leaveTypes = \App\Models\LeaveType::all();

        return Inertia::render('leaves/index', [
            'leaves' => \App\Http\Resources\LeaveResource::collection($leaves),
            'leaveTypes' => $leaveTypes
        ]);
    })->name('leaves.index');
});

require __DIR__ . '/settings.php';
