<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class TestRolesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $hrManagerRole = Role::firstOrCreate(['name' => 'hr_manager']);
        $companyAdminRole = Role::firstOrCreate(['name' => 'company_admin']);
        $learnerRole = Role::firstOrCreate(['name' => 'learner']);

        // Create basic permissions
        $permissions = [
            'view programs',
            'create programs',
            'edit programs',
            'delete programs',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view companies',
            'create companies',
            'edit companies',
            'delete companies',
            'view payslips',
            'create payslips',
            'edit payslips',
            'delete payslips',
            'view compliance',
            'export compliance',
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
            'view attendance',
            'create attendance',
            'edit attendance',
            'view leave requests',
            'create leave requests',
            'approve leave requests',
            'manage leave requests',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->syncPermissions(Permission::all());
        
        $hrManagerRole->syncPermissions([
            'view programs', 'create programs', 'edit programs',
            'view users', 'create users', 'edit users',
            'view payslips', 'create payslips', 'edit payslips',
            'view compliance', 'export compliance',
            'view schedules', 'create schedules', 'edit schedules',
            'view attendance', 'create attendance', 'edit attendance',
            'view leave requests', 'approve leave requests', 'manage leave requests',
        ]);
        
        $companyAdminRole->syncPermissions([
            'view programs', 'edit programs',
            'view users', 'edit users',
            'view payslips',
            'view compliance',
            'view schedules',
            'view attendance',
            'view leave requests', 'approve leave requests',
        ]);
        
        $learnerRole->syncPermissions([
            'view programs',
            'view schedules',
            'view attendance',
            'view leave requests',
            'create leave requests',
        ]);

        // Assign roles to test users
        $admin = User::where('email', 'mmathabo@skillspanda.co.za')->first();
        if ($admin) {
            $admin->assignRole('admin');
        }

        $learner = User::where('email', 'learner@connecthr.co.za')->first();
        if ($learner) {
            $learner->assignRole('learner');
        }

        // Assign learner role to IRP5 learners from Connect HR
        $irp5Learners = User::whereIn('email', [
            'andile.mdlankomo@connecthr.co.za',
            'ayanda.thabethe@connecthr.co.za',
            'bongiwe.nkosi@connecthr.co.za',
            'kelebogile.pelo@connecthr.co.za',
            'kwazusomandla.ndaba@connecthr.co.za',
        ])->get();
        
        foreach ($irp5Learners as $learnerUser) {
            $learnerUser->assignRole('learner');
        }

        $this->command->info('Roles and permissions created and assigned successfully!');
    }
}