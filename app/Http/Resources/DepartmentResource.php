<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'name' => $this->name,
            'manager' => $this->manager ? [
                'id' => $this->manager->id,
                'name' => $this->manager->name,
            ] : null,
            'positions_count' => $this->positions ? $this->positions->count() : 0,
            'positions' => $this->whenLoaded('positions'), // Optional loading
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
