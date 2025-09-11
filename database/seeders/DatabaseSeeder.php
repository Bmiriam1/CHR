<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run all seeders in the required order
        $this->call([
            RolePermissionSeeder::class,
            ComplianceTestSeeder::class,
            SALeaveTypesSeeder::class,
            TestCompanySeeder::class,
            ConnectHRPayslipSeeder::class,
            SimCardAllocationSeeder::class,
            TestRolesSeeder::class,
        ]);
    }
}
