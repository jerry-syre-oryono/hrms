<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Annual Leave', 'days_allowed' => 21, 'description' => 'Standard annual leave'],
            ['name' => 'Sick Leave', 'days_allowed' => 14, 'description' => 'Medical leave'],
            ['name' => 'Maternity Leave', 'days_allowed' => 90, 'description' => 'Mothers who have given birth'],
            ['name' => 'Paternity Leave', 'days_allowed' => 14, 'description' => 'Fathers who have a newborn'],
            ['name' => 'Unpaid Leave', 'days_allowed' => 0, 'description' => 'Leave without pay'],
        ];

        foreach ($types as $type) {
            LeaveType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
