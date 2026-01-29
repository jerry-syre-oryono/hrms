<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|exists:users,id|unique:employees,user_id,' . $this->route('employee'),
            'employee_number' => 'sometimes|string|unique:employees,employee_number,' . $this->route('employee'),
            'department_id' => 'sometimes|exists:departments,id',
            'position_id' => 'sometimes|exists:positions,id',
            'hire_date' => 'sometimes|date',
            'status' => 'sometimes|in:active,suspended,terminated',
            'salary' => 'sometimes|numeric|min:0',
            'contract_type' => 'sometimes|in:permanent,contract',
        ];
    }
}
