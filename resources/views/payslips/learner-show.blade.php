@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-5">
        <div class="py-4 lg:py-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                        Payslip Details
                    </h2>
                    <p class="mt-0.5 text-slate-500 dark:text-navy-200">
                        {{ $payslip->pay_period_formatted }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('payslips.download', ['payslip' => $payslip, 'format' => 'pdf']) }}"
                        class="btn bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90">
                        <i class="fa fa-file-pdf mr-2"></i>
                        Download PDF
                    </a>
                    <a href="{{ route('payslips.download', ['payslip' => $payslip, 'format' => 'csv']) }}"
                        class="btn bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90">
                        <i class="fa fa-file-csv mr-2"></i>
                        Download CSV
                    </a>
                    <a href="{{ route('payslips.index') }}"
                        class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus