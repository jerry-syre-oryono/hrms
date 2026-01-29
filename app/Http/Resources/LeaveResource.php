<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LeaveTypeResource;

class LeaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => $this->employee ? [
                'id' => $this->employee->id,
                'name' => $this->employee->user->name,
                'employee_number' => $this->employee->employee_number,
            ] : null,
            'leave_type' => new LeaveTypeResource($this->leaveType),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
