<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Reference and dates
            $table->string('reference_number')->unique();
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->date('payment_date');
            
            // Earnings
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('overtime', 12, 2)->default(0);
            $table->decimal('bonuses', 12, 2)->default(0);
            $table->decimal('gross_pay', 12, 2);
            
            // Deductions
            $table->decimal('paye', 12, 2)->default(0);
            $table->decimal('uif', 12, 2)->default(0);
            $table->decimal('pension', 12, 2)->default(0);
            $table->decimal('medical_aid', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2);
            
            // Net pay
            $table->decimal('net_pay', 12, 2);
            
            // Additional info
            $table->text('description')->nullable();
            $table->json('breakdown')->nullable(); // Detailed breakdown if needed
            $table->string('status')->default('paid'); // paid, pending, cancelled
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'pay_period_start']);
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};