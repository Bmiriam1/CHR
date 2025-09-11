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
        $this->call([
            RolePermissionSeeder::class,
            ComplianceTestSeeder::class,
            SALeaveTypesSeeder::class,
            LearnerIRP5Seeder::class,
            SimCardAllocationSeeder::class,
            TestCompanySeeder::class,
            TestRolesSeeder::class,
            LearnerIRP5Seeder::class,
        ]);
    }
}
