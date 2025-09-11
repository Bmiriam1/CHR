<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Program Management
            'view programs',
            'create programs',
            'edit programs',
            'delete programs',
            'activate programs',
            'deactivate programs',

            // Schedule Management
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
            'start schedules',
            'complete schedules',
            'cancel schedules',

            // Attendance Management
            'view attendance',
            'create attendance',
            'edit attendance',
            'validate attendance',
            'approve attendance',
            'check-in',
            'check-out',

            // Payslip Management
            'view payslips',
            'create payslips',
            'edit payslips',
            'approve payslips',
            'process payslips',
            'download payslips',
            'generate payslips',
            'bulk generate payslips',

            // Payment Schedule Management
            'view payment schedules',
            'create payment schedules',
            'edit payment schedules',
            'approve payment schedules',
            'export payment schedules',
            'generate payment schedules',

            // Device Management
            'view devices',
            'edit devices',
            'approve devices',
            'block devices',
            'unblock devices',
            'register devices',

            // Host Management
            'view hosts',
            'create hosts',
            'edit hosts',
            'delete hosts',

            // Compliance & Reporting
            'view compliance dashboard',
            'generate sars reports',
            'generate emp201',
            'generate emp501',
            'generate tax certificates',
            'generate uif reports',
            'generate eti claims',
            'generate sdl returns',
            'view audit reports',
            'run compliance checks',

            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Profile Management
            'view profile',
            'edit profile',
            'delete profile',

            // Dashboard Access
            'view learner dashboard',
            'view company dashboard',
            'view admin dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Learner Role
        $learnerRole = Role::firstOrCreate(['name' => 'learner']);
        $learnerRole->givePermissionTo([
            'view schedules',
            'view attendance',
            'check-in',
            'check-out',
            'view payslips',
            'download payslips',
            'view devices',
            'register devices',
            'view profile',
            'edit profile',
            'view learner dashboard',
        ]);

        // Company Admin Role
        $companyAdminRole = Role::firstOrCreate(['name' => 'company_admin']);
        $companyAdminRole->givePermissionTo([
            // Program Management
            'view programs',
            'create programs',
            'edit programs',
            'delete programs',
            'activate programs',
            'deactivate programs',

            // Schedule Management
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
            'start schedules',
            'complete schedules',
            'cancel schedules',

            // Attendance Management
            'view attendance',
            'create attendance',
            'edit attendance',
            'validate attendance',
            'approve attendance',

            // Payslip Management
            'view payslips',
            'create payslips',
            'edit payslips',
            'approve payslips',
            'process payslips',
            'download payslips',
            'generate payslips',
            'bulk generate payslips',

            // Payment Schedule Management
            'view payment schedules',
            'create payment schedules',
            'edit payment schedules',
            'approve payment schedules',
            'export payment schedules',
            'generate payment schedules',

            // Device Management
            'view devices',
            'edit devices',
            'approve devices',
            'block devices',
            'unblock devices',

            // Compliance & Reporting
            'view compliance dashboard',
            'generate sars reports',
            'generate emp201',
            'generate emp501',
            'generate tax certificates',
            'generate uif reports',
            'generate eti claims',
            'generate sdl returns',
            'view audit reports',
            'run compliance checks',

            // User Management
            'view users',
            'create users',
            'edit users',

            // Profile Management
            'view profile',
            'edit profile',

            // Dashboard Access
            'view company dashboard',
        ]);

        // HR Manager Role
        $hrManagerRole = Role::firstOrCreate(['name' => 'hr_manager']);
        $hrManagerRole->givePermissionTo([
            // Schedule Management
            'view schedules',
            'create schedules',
            'edit schedules',
            'start schedules',
            'complete schedules',

            // Attendance Management
            'view attendance',
            'create attendance',
            'edit attendance',
            'validate attendance',
            'approve attendance',

            // Payslip Management
            'view payslips',
            'create payslips',
            'edit payslips',
            'approve payslips',
            'process payslips',
            'download payslips',
            'generate payslips',
            'bulk generate payslips',

            // Payment Schedule Management
            'view payment schedules',
            'create payment schedules',
            'edit payment schedules',
            'approve payment schedules',
            'export payment schedules',
            'generate payment schedules',

            // Device Management
            'view devices',
            'edit devices',
            'approve devices',

            // User Management
            'view users',
            'create users',
            'edit users',

            // Profile Management
            'view profile',
            'edit profile',

            // Dashboard Access
            'view company dashboard',
        ]);

        // Super Admin Role (for system administration)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        echo "Roles and permissions created successfully!\n";
        echo "Created roles: learner, company_admin, hr_manager, admin\n";
        echo "Created " . count($permissions) . " permissions\n";
    }
}
