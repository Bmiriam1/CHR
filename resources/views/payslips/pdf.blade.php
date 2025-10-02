<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payslip->user->employee_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f1f5f9;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 13px;
            color: #334155;
            margin-bottom: 10px;
            border-left: 4px solid #2563eb;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 6px 10px;
            font-weight: 600;
            width: 40%;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-value {
            display: table-cell;
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .breakdown-table th {
            background-color: #f8fafc;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #cbd5e1;
        }
        .breakdown-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .breakdown-table .amount {
            text-align: right;
            font-weight: 500;
        }
        .subtotal-row {
            background-color: #f8fafc;
            font-weight: 600;
        }
        .total-row {
            background-color: #dbeafe;
            font-weight: bold;
            font-size: 14px;
        }
        .net-pay-box {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 8px;
        }
        .net-pay-box .label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .net-pay-box .amount {
            font-size: 32px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #64748b;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-approved {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-calculated {
            background-color: #fef3c7;
            color: #92400e;
        }
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PAYSLIP</h1>
            @if($payslip->company)
                <p><strong>{{ $payslip->company->company_name }}</strong></p>
                @if($payslip->company->company_address)
                    <p>{{ $payslip->company->company_address }}</p>
                @endif
            @endif
            <p style="margin-top: 10px;">
                <span class="status-badge status-{{ $payslip->status }}">{{ strtoupper($payslip->status) }}</span>
            </p>
        </div>

        <!-- Employee Information -->
        <div class="section">
            <div class="section-title">Employee Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Employee Name:</div>
                    <div class="info-value">{{ $payslip->user->first_name }} {{ $payslip->user->last_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Employee Code:</div>
                    <div class="info-value">{{ $payslip->user->employee_code }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">ID Number:</div>
                    <div class="info-value">{{ $payslip->user->id_number }}</div>
                </div>
                @if($payslip->program)
                    <div class="info-row">
                        <div class="info-label">Program:</div>
                        <div class="info-value">{{ $payslip->program->title }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pay Period Information -->
        <div class="section">
            <div class="section-title">Pay Period Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Pay Period:</div>
                    <div class="info-value">{{ $payslip->payroll_period_start->format('d M Y') }} - {{ $payslip->payroll_period_end->format('d M Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Pay Date:</div>
                    <div class="info-value">{{ $payslip->pay_date->format('d M Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Days Worked:</div>
                    <div class="info-value">{{ $payslip->days_worked }} days</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Daily Rate:</div>
                    <div class="info-value">R{{ number_format($payslip->daily_rate_used, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Earnings Breakdown -->
        <div class="section">
            <div class="section-title">Earnings</div>
            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Amount (R)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Stipend</td>
                        <td class="amount">{{ number_format($payslip->basic_earnings, 2) }}</td>
                    </tr>
                    @if($payslip->transport_allowance > 0)
                        <tr>
                            <td>Transport Allowance</td>
                            <td class="amount">{{ number_format($payslip->transport_allowance, 2) }}</td>
                        </tr>
                    @endif
                    @if($payslip->meal_allowance > 0)
                        <tr>
                            <td>Meal Allowance</td>
                            <td class="amount">{{ number_format($payslip->meal_allowance, 2) }}</td>
                        </tr>
                    @endif
                    @if($payslip->accommodation_allowance > 0)
                        <tr>
                            <td>Accommodation Allowance</td>
                            <td class="amount">{{ number_format($payslip->accommodation_allowance, 2) }}</td>
                        </tr>
                    @endif
                    @if($payslip->other_allowances > 0)
                        <tr>
                            <td>Other Allowances</td>
                            <td class="amount">{{ number_format($payslip->other_allowances, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="subtotal-row">
                        <td><strong>Gross Earnings</strong></td>
                        <td class="amount"><strong>{{ number_format($payslip->gross_earnings, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Deductions Breakdown -->
        <div class="section">
            <div class="section-title">Deductions</div>
            <table class="breakdown-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Amount (R)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($payslip->uif_employee > 0)
                        <tr>
                            <td>UIF Contribution (1%)</td>
                            <td class="amount">{{ number_format($payslip->uif_employee, 2) }}</td>
                        </tr>
                    @endif
                    @if($payslip->paye_tax > 0)
                        <tr>
                            <td>PAYE Tax</td>
                            <td class="amount">{{ number_format($payslip->paye_tax, 2) }}</td>
                        </tr>
                    @endif
                    @if($payslip->other_deductions > 0)
                        <tr>
                            <td>Other Deductions</td>
                            <td class="amount">{{ number_format($payslip->other_deductions, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="subtotal-row">
                        <td><strong>Total Deductions</strong></td>
                        <td class="amount"><strong>{{ number_format($payslip->total_deductions, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Net Pay -->
        <div class="net-pay-box">
            <div class="label">NET PAY</div>
            <div class="amount">R{{ number_format($payslip->net_pay, 2) }}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This payslip is generated electronically and does not require a signature.  
            <br>© {{ date('Y') }} {{ $payslip->company->company_name ?? 'Skills Panda' }}. All rights reserved.
        </div>
    </div>
</body>
</html>
