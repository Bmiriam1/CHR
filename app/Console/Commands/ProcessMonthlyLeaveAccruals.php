<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveAccrual;
use Carbon\Carbon;

class ProcessMonthlyLeaveAccruals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:process-monthly-accruals {--date= : Specific date to process (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process monthly leave accruals for all active learners according to SA employment law';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing monthly leave accruals...');
        
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $this->info("Processing accruals for: {$date->format('Y-m-d')}");

        try {
            $processed = LeaveAccrual::processMonthlyAccruals();
            
            if (empty($processed)) {
                $this->warn('No accruals processed. This may indicate:');
                $this->warn('- All accruals for this month already exist');
                $this->warn('- No active learners found');
                $this->warn('- No eligible leave types found');
                return Command::SUCCESS;
            }

            $this->info("Successfully processed {count($processed)} accruals:");
            
            // Group by user for better display
            $groupedByUser = [];
            foreach ($processed as $accrual) {
                $groupedByUser[$accrual['user']][] = $accrual;
            }

            foreach ($groupedByUser as $userName => $userAccruals) {
                $this->line("\n{$userName}:");
                foreach ($userAccruals as $accrual) {
                    $this->line("  - {$accrual['leave_type']}: +{$accrual['days_accrued']} days (Balance: {$accrual['running_balance']})");
                }
            }

            $this->info("\nMonthly leave accruals processed successfully!");
            
        } catch (\Exception $e) {
            $this->error("Error processing monthly accruals: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}