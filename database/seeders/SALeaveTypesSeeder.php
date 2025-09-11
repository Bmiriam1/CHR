<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class SALeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create standard South African leave types according to BCEA and employment law
        LeaveType::createSAStandardTypes();
        
        $this->command->info('South African standard leave types created successfully.');
        
        // Display created leave types
        $leaveTypes = LeaveType::all();
        $this->command->info("Created {$leaveTypes->count()} leave types:");
        
        foreach ($leaveTypes as $leaveType) {
            $this->command->line("- {$leaveType->code}: {$leaveType->name} ({$leaveType->annual_entitlement_days} days)");
        }
    }
}