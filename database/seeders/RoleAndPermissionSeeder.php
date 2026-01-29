<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage_employees',
            'approve_leave',
            'view_payroll',
            'manage_departments',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign created permissions

        // 1. Employee (Base Role)
        $role = Role::create(['name' => 'employee']);
        // Employees might have basic permissions like viewing their own profile, which isn't listed yet.
        // For now, we leave them with no specific administrative permissions.

        // 2. Manager
        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo('approve_leave');

        // 3. HR
        $role = Role::create(['name' => 'hr']);
        $role->givePermissionTo(['manage_employees', 'approve_leave', 'view_payroll']);

        // 4. Admin
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
