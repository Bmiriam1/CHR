<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\LeaveManagementSeeder;

class SeedLeaveManagement extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'leave:seed 
                            {--fresh : Drop and recreate all leave-related tables}
                            {--force : Force seed without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Seed leave management system with sample data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if ($this->option('fresh')) {
                $this->info('Fresh seeding selected - this will drop and recreate leave tables...');

                if (!$this->option('force') && !$this->confirm('Are you sure you want to drop existing leave data?')) {
                    $this->info('Seeding cancelled.');
                    return 0;
                }

                $this->call('migrate:fresh', [
                    '--path' => 'database/migrations/2025_01_15_100000_create_leave_types_table.php',
                    '--path' => 'database/migrations/2025_01_15_100001_create_leave_balances_table.php',
                    '--path' => 'database/migrations/2025_01_15_100002_create_leave_requests_table.php',
                    '--path' => 'database/migrations/2025_01_15_100003_create_leave_accruals_table.php',
                    '--path' => 'database/migrations/2025_01_15_100004_create_leave_carry_overs_table.php',
                ]);
            }

            $this->info('Running Leave Management Seeder...');

            $seeder = new LeaveManagementSeeder();
            $seeder->run();

            $this->info('Leave management seeding completed successfully!');

            // Display summary
            $this->displaySummary();

            return 0;
        } catch (\Exception $e) {
            $this->error('Leave management seeding failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Display seeding summary
     */
    private function displaySummary(): void
    {
        $this->newLine();
        $this->info('📊 Leave Management System Summary:');
        $this->newLine();

        // Count records
        $leaveTypes = \App\Models\LeaveType::count();
        $leaveBalances = \App\Models\LeaveBalance::count();
        $leaveRequests = \App\Models\LeaveRequest::count();
        $leaveAccruals = \App\Models\LeaveAccrual::count();
        $leaveCarryOvers = \App\Models\LeaveCarryOver::count();

        $this->table(
            ['Component', 'Count'],
            [
                ['Leave Types', $leaveTypes],
                ['Leave Balances', $leaveBalances],
                ['Leave Requests', $leaveRequests],
                ['Leave Accruals', $leaveAccruals],
                ['Leave Carry Overs', $leaveCarryOvers],
            ]
        );

        $this->newLine();
        $this->info('🎯 Available API Endpoints:');
        $this->line('  • GET  /api/leave/balance - Get user leave balance');
        $this->line('  • GET  /api/leave/types - Get available leave types');
        $this->line('  • GET  /api/leave/programs - Get user programs');
        $this->line('  • GET  /api/leave/requests - Get leave requests');
        $this->line('  • POST /api/leave/requests - Submit leave request');
        $this->line('  • GET  /api/leave/requests/{id} - Get specific request');
        $this->line('  • PATCH /api/leave/requests/{id}/cancel - Cancel request');

        $this->newLine();
        $this->info('🔧 Available Commands:');
        $this->line('  • php artisan leave:process-accruals - Process monthly accruals');
        $this->line('  • php artisan leave:process-accruals --year-end - Process year-end carry overs');
        $this->line('  • php artisan leave:seed --fresh - Fresh seed leave data');

        $this->newLine();
        $this->info('✅ Leave management system is ready to use!');
    }
}
