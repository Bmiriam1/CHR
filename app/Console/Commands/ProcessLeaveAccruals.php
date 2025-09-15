<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveAccrual;
use App\Models\LeaveCarryOver;
use App\Services\LeaveManagementService;
use Carbon\Carbon;

class ProcessLeaveAccruals extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'leave:process-accruals 
                            {--date= : Date to process accruals for (Y-m-d format, defaults to current month)}
                            {--year-end : Process year-end carry overs}';

    /**
     * The console command description.
     */
    protected $description = 'Process monthly leave accruals and year-end carry overs';

    protected $leaveService;

    public function __construct(LeaveManagementService $leaveService)
    {
        parent::__construct();
        $this->leaveService = $leaveService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting leave accrual processing...');

        try {
            if ($this->option('year-end')) {
                $this->processYearEndCarryOvers();
            } else {
                $this->processMonthlyAccruals();
            }

            $this->info('Leave accrual processing completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Leave accrual processing failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Process monthly accruals
     */
    private function processMonthlyAccruals(): void
    {
        $date = $this->option('date')
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'))
            : Carbon::now()->startOfMonth();

        $this->info("Processing monthly accruals for {$date->format('Y-m-d')}...");

        $processed = LeaveAccrual::processMonthlyAccruals();

        if (empty($processed)) {
            $this->warn('No accruals were processed. This might be normal if:');
            $this->warn('- No users have active leave balances');
            $this->warn('- Accruals for this month have already been processed');
            $this->warn('- Users are not eligible for accruals yet');
        } else {
            $this->info("✓ Processed " . count($processed) . " accrual records");

            // Display summary
            $summary = collect($processed)->groupBy('leave_type')->map(function ($items, $leaveType) {
                $totalDays = collect($items)->sum('days_accrued');
                $userCount = collect($items)->unique('user')->count();
                return "{$leaveType}: {$totalDays} days for {$userCount} users";
            });

            $this->table(['Leave Type', 'Summary'], $summary->map(function ($summary, $leaveType) {
                return [$leaveType, $summary];
            })->toArray());
        }
    }

    /**
     * Process year-end carry overs
     */
    private function processYearEndCarryOvers(): void
    {
        $year = $this->option('date')
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'))->year
            : Carbon::now()->year - 1; // Default to previous year

        $this->info("Processing year-end carry overs from {$year}...");

        $processed = LeaveCarryOver::processYearEndCarryOvers($year);

        if (empty($processed)) {
            $this->warn('No carry overs were processed. This might be normal if:');
            $this->warn('- No users have unused leave balances');
            $this->warn('- Carry overs for this year have already been processed');
            $this->warn('- Leave types do not allow carry overs');
        } else {
            $this->info("✓ Processed " . count($processed) . " carry over records");

            // Display summary
            $summary = collect($processed)->groupBy('leave_type')->map(function ($items, $leaveType) {
                $totalCarriedOver = collect($items)->sum('carried_over');
                $totalForfeited = collect($items)->sum('forfeited');
                $userCount = collect($items)->unique('user')->count();
                return "{$leaveType}: {$totalCarriedOver} days carried over, {$totalForfeited} days forfeited for {$userCount} users";
            });

            $this->table(['Leave Type', 'Summary'], $summary->map(function ($summary, $leaveType) {
                return [$leaveType, $summary];
            })->toArray());
        }
    }
}



