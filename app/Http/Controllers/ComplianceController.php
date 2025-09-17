<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Program;
use App\Models\Payslip;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf; // Add this import for PDF generation

class ComplianceController extends Controller
{
    /**
     * Show the compliance dashboard.
     */
    public function dashboard(): View
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $taxYear = now()->month >= 3 ? now()->year : now()->year - 1;

        // Compliance Overview Stats
        $stats = [
            'total_companies' => Company::count(),
            'active_programs' => Program::where('status', 'active')->count(),
            'monthly_payslips' => Payslip::whereYear('pay_date', $currentYear)
                ->whereMonth('pay_date', $currentMonth)
                ->count(),
            'pending_submissions' => $this->getPendingSubmissions(),
            'total_learners' => User::whereHas('attendanceRecords')->count(),
            'monthly_paye' => Payslip::whereYear('pay_date', $currentYear)
                ->whereMonth('pay_date', $currentMonth)
                ->sum('paye_tax'),
            'monthly_uif' => Payslip::whereYear('pay_date', $currentYear)
                ->whereMonth('pay_date', $currentMonth)
                ->sum('uif_employee') * 2, // Employee + Employer
            'monthly_eti_benefit' => Payslip::whereYear('pay_date', $currentYear)
                ->whereMonth('pay_date', $currentMonth)
                ->sum('eti_benefit'),
        ];

        // Recent submissions
        $recentSubmissions = $this->getRecentSubmissions();

        // Compliance alerts
        $alerts = $this->getComplianceAlerts();

        // Monthly trends
        $monthlyTrends = $this->getMonthlyTrends();

        return view('compliance.dashboard', compact(
            'stats',
            'recentSubmissions',
            'alerts',
            'monthlyTrends',
            'taxYear'
        ));
    }

    /**
     * Show EMP201 form.
     */
    public function emp201Form(): View
    {
        $companies = Company::select('id', 'name')->get();
        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'value' => $month,
                'label' => Carbon::create(null, $month)->format('F'),
            ];
        });

        return view('compliance.sars.emp201-form', compact('companies', 'months'));
    }

    /**
     * Generate EMP201 return.
     */
    public function generateEmp201(Request $request): Response
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'year' => 'required|integer|min:2020|max:' . (now()->year + 1),
            'month' => 'required|integer|min:1|max:12',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $startDate = Carbon::create($validated['year'], $validated['month'], 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get payslip data for the period
        $payslips = Payslip::where('company_id', $company->id)
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'processed'])
            ->get();

        // Calculate EMP201 totals
        $emp201Data = [
            'company' => $company,
            'period' => $startDate->format('F Y'),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_employees' => $payslips->unique('user_id')->count(),
            'total_remuneration' => $payslips->sum('taxable_earnings'),
            'total_paye' => $payslips->sum('paye_tax'),
            'total_uif_employee' => $payslips->sum('uif_employee'),
            'total_uif_employer' => $payslips->sum('uif_employer'),
            'total_sdl' => $payslips->sum('sdl_contribution'),
            'total_eti_benefit' => $payslips->sum('eti_benefit'),
            'net_paye_due' => $payslips->sum('paye_tax') - $payslips->sum('eti_benefit'),
            'generated_at' => now(),
        ];

        // Generate Excel format for submission
        $excel = $this->generateEmp201Excel($emp201Data);

        return response($excel, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="EMP201_' . $company->paye_reference_number . '_' . $startDate->format('Y_m') . '.xlsx"',
        ]);
    }

    /**
     * Show EMP501 form.
     */
    public function emp501Form(): View
    {
        $companies = Company::select('id', 'name')->get();
        $years = collect(range(now()->year - 5, now()->year + 1));

        return view('compliance.sars.emp501-form', compact('companies', 'years'));
    }

    /**
     * Generate EMP501 annual reconciliation.
     */
    public function generateEmp501(Request $request): Response
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'tax_year' => 'required|integer|min:2020|max:' . now()->year,
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $taxYear = $validated['tax_year'];

        // Tax year runs from March to February
        $startDate = Carbon::create($taxYear, 3, 1);
        $endDate = Carbon::create($taxYear + 1, 2, 28)->endOfMonth();

        // Get all payslips for the tax year
        $payslips = Payslip::where('company_id', $company->id)
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'processed'])
            ->with('user')
            ->get();

        // Group by employee for individual certificates
        $employeeData = $payslips->groupBy('user_id')->map(function ($userPayslips) {
            $user = $userPayslips->first()->user;
            return [
                'employee' => $user,
                'total_remuneration' => $userPayslips->sum('taxable_earnings'),
                'total_paye' => $userPayslips->sum('paye_tax'),
                'total_uif_employee' => $userPayslips->sum('uif_employee'),
                'months_worked' => $userPayslips->count(),
                'sars_codes' => [
                    '3601' => $userPayslips->sum('sars_3601'),
                    '3605' => $userPayslips->sum('sars_3605'),
                    '3615' => $userPayslips->sum('sars_3615'),
                    '3617' => $userPayslips->sum('sars_3617'),
                    '3627' => $userPayslips->sum('sars_3627'),
                    '3699' => $userPayslips->sum('sars_3699'),
                ],
            ];
        });

        $emp501Data = [
            'company' => $company,
            'tax_year' => "{$taxYear}/" . ($taxYear + 1),
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
            'total_employees' => $employeeData->count(),
            'total_remuneration' => $payslips->sum('taxable_earnings'),
            'total_paye' => $payslips->sum('paye_tax'),
            'total_uif_employee' => $payslips->sum('uif_employee'),
            'total_uif_employer' => $payslips->sum('uif_employer'),
            'total_sdl' => $payslips->sum('sdl_contribution'),
            'total_eti_benefit' => $payslips->sum('eti_benefit'),
            'employees' => $employeeData,
            'generated_at' => now(),
        ];

        // Generate Excel format for submission
        $excel = $this->generateEmp501Excel($emp501Data);

        return response($excel, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="EMP501_' . $company->paye_reference_number . '_' . $taxYear . '.xlsx"',
        ]);
    }

    /**
     * Show tax certificates index.
     */
    public function taxCertificatesIndex(): View
    {
        // Set session for tenant scope to work properly
        $company = Company::first();
        if ($company) {
            session(['current_company_id' => $company->id]);
        }

        $companies = Company::select('id', 'name')->get();
        $years = collect(range(now()->year - 5, now()->year + 1));

        return view('compliance.tax-certificates.index', compact('companies', 'years'));
    }

    /**
     * Generate IT3(a) certificates - MISSING METHOD FIXED
     */
    public function generateIt3a(Request $request): Response
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'tax_year' => 'required|integer',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $taxYear = $validated['tax_year'];

        $startDate = Carbon::create($taxYear, 3, 1);
        $endDate = Carbon::create($taxYear + 1, 2, 28)->endOfMonth();

        $query = Payslip::where('company_id', $company->id)
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'processed'])
            ->with('user');

        if (!empty($validated['user_ids'])) {
            $query->whereIn('user_id', $validated['user_ids']);
        }

        $payslips = $query->get();
        
        // Group by employee
        $employeeData = $payslips->groupBy('user_id')->map(function ($userPayslips) {
            $user = $userPayslips->first()->user;
            return [
                'employee' => $user,
                'total_remuneration' => $userPayslips->sum('taxable_earnings'),
                'total_paye' => $userPayslips->sum('paye_tax'),
                'total_uif_employee' => $userPayslips->sum('uif_employee'),
                'months_worked' => $userPayslips->count(),
            ];
        });

        $data = [
            'company' => $company,
            'tax_year' => $taxYear,
            'employees' => $employeeData,
            'generated_at' => now(),
        ];

        // Generate PDF using a view
        $pdf = PDF::loadView('compliance.certificates.it3a', $data);
        
        return $pdf->download('IT3A_Certificates_' . $company->name . '_' . $taxYear . '.pdf');
    }

    /**
     * Generate IRP5 certificates.
     */
    public function generateIrp5(Request $request): Response
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'tax_year' => 'required|integer',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $taxYear = $validated['tax_year'];

        $startDate = Carbon::create($taxYear, 3, 1);
        $endDate = Carbon::create($taxYear + 1, 2, 28)->endOfMonth();

        $query = Payslip::where('company_id', $company->id)
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'processed'])
            ->with('user');

        if (!empty($validated['user_ids'])) {
            $query->whereIn('user_id', $validated['user_ids']);
        }

        $payslips = $query->get();
        
        // Group by employee for individual certificates
        $employeeData = $payslips->groupBy('user_id')->map(function ($userPayslips) {
            $user = $userPayslips->first()->user;
            return [
                'employee' => $user,
                'total_remuneration' => $userPayslips->sum('taxable_earnings'),
                'total_paye' => $userPayslips->sum('paye_tax'),
                'total_uif_employee' => $userPayslips->sum('uif_employee'),
                'months_worked' => $userPayslips->count(),
                'sars_codes' => [
                    '3601' => $userPayslips->sum('sars_3601'),
                    '3605' => $userPayslips->sum('sars_3605'),
                    '3615' => $userPayslips->sum('sars_3615'),
                    '3617' => $userPayslips->sum('sars_3617'),
                    '3627' => $userPayslips->sum('sars_3627'),
                    '3699' => $userPayslips->sum('sars_3699'),
                ],
            ];
        });

        $data = [
            'company' => $company,
            'tax_year' => $taxYear,
            'employees' => $employeeData,
            'generated_at' => now(),
        ];

        // Generate PDF using a view
        $pdf = PDF::loadView('compliance.certificates.irp5', $data);
        
        return $pdf->download('IRP5_Certificates_' . $company->name . '_' . $taxYear . '.pdf');
    }

    /**
     * Show ETI dashboard.
     */
    public function etiDashboard(): View
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // ETI eligible learners
        $etiLearners = User::whereHas('programs', function ($query) {
            $query->where('eti_eligible_program', true);
        })
            ->with(['programs' => function ($query) {
                $query->where('eti_eligible_program', true);
            }])
            ->get();

        // Monthly ETI claims
        $monthlyClaims = Payslip::whereYear('pay_date', $currentYear)
            ->where('eti_benefit', '>', 0)
            ->selectRaw('MONTH(pay_date) as month, SUM(eti_benefit) as total_benefit')
            ->groupBy('month')
            ->get();

        $stats = [
            'eligible_learners' => $etiLearners->count(),
            'monthly_benefit' => Payslip::whereYear('pay_date', $currentYear)
                ->whereMonth('pay_date', $currentMonth)
                ->sum('eti_benefit'),
            'ytd_benefit' => Payslip::whereYear('pay_date', $currentYear)
                ->sum('eti_benefit'),
            'average_benefit_per_learner' => $etiLearners->count() > 0
                ? Payslip::whereYear('pay_date', $currentYear)->sum('eti_benefit') / $etiLearners->count()
                : 0,
        ];

        return view('compliance.eti.dashboard', compact('etiLearners', 'monthlyClaims', 'stats'));
    }

    /**
     * Show UIF forms.
     */
    public function uifForm(): View
    {
        $companies = Company::select('id', 'name')->get();

        return view('compliance.uif.form', compact('companies'));
    }

    /**
     * Generate UIF monthly declaration.
     */
    public function generateUifDeclaration(Request $request): Response
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $startDate = Carbon::create($validated['year'], $validated['month'], 1);
        $endDate = $startDate->copy()->endOfMonth();

        $payslips = Payslip::where('company_id', $company->id)
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'processed'])
            ->get();

        $uifData = [
            'company' => $company,
            'period' => $startDate->format('F Y'),
            'total_employees' => $payslips->unique('user_id')->count(),
            'total_remuneration' => $payslips->sum('uif_contribution_base'),
            'total_uif_employee' => $payslips->sum('uif_employee'),
            'total_uif_employer' => $payslips->sum('uif_employer'),
            'total_uif_contribution' => $payslips->sum('uif_employee') + $payslips->sum('uif_employer'),
            'generated_at' => now(),
        ];

        $csv = $this->generateUifCsv($uifData);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="UIF_Declaration_' . $company->uif_reference_number . '_' . $startDate->format('Y_m') . '.csv"',
        ]);
    }

    /**
     * Show compliance checks.
     */
    public function complianceChecks(): View
    {
        $checks = $this->runComplianceValidation();

        return view('compliance.checks.index', compact('checks'));
    }

    /**
     * Run compliance validation.
     */
    public function runValidation(): array
    {
        return $this->runComplianceValidation();
    }

    /**
     * Get pending submissions.
     */
    private function getPendingSubmissions(): int
    {
        $currentDate = now();
        $submissions = 0;

        // EMP201 submissions (monthly, due by 7th of following month)
        if ($currentDate->day > 7) {
            $lastMonth = $currentDate->copy()->subMonth();
            $submitted = Payslip::whereYear('pay_date', $lastMonth->year)
                ->whereMonth('pay_date', $lastMonth->month)
                ->whereNotNull('exported_to_sars_at')
                ->exists();
            if (!$submitted) $submissions++;
        }

        // Add other submission checks...

        return $submissions;
    }

    /**
     * Get recent submissions.
     */
    private function getRecentSubmissions(): array
    {
        return [
            [
                'type' => 'EMP201',
                'period' => now()->subMonth()->format('F Y'),
                'submitted_at' => now()->subDays(5),
                'status' => 'submitted',
            ],
            // Add more recent submissions...
        ];
    }

    /**
     * Get compliance alerts.
     */
    private function getComplianceAlerts(): array
    {
        $alerts = [];

        // Check for overdue EMP201
        if (now()->day > 7) {
            $lastMonth = now()->subMonth();
            $submitted = Payslip::whereYear('pay_date', $lastMonth->year)
                ->whereMonth('pay_date', $lastMonth->month)
                ->whereNotNull('exported_to_sars_at')
                ->exists();

            if (!$submitted) {
                $alerts[] = [
                    'type' => 'overdue',
                    'title' => 'EMP201 Overdue',
                    'message' => "EMP201 for {$lastMonth->format('F Y')} is overdue",
                    'action_url' => route('compliance.sars.emp201.form'),
                ];
            }
        }

        return $alerts;
    }

    /**
     * Get monthly trends.
     */
    private function getMonthlyTrends(): array
    {
        $currentYear = now()->year;

        return Payslip::whereYear('pay_date', $currentYear)
            ->selectRaw('MONTH(pay_date) as month')
            ->selectRaw('SUM(paye_tax) as paye')
            ->selectRaw('SUM(uif_employee + uif_employer) as uif')
            ->selectRaw('SUM(eti_benefit) as eti')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    /**
     * Generate EMP201 Excel.
     */
    private function generateEmp201Excel(array $data): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setTitle('EMP201 Monthly PAYE Return');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']]
        ];

        // Data styling
        $dataStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ];

        // Company Details Section
        $sheet->setCellValue('A1', 'EMP201 Monthly PAYE Return');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray($headerStyle);

        $sheet->setCellValue('A3', 'Company Details');
        $sheet->getStyle('A3')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);

        $sheet->setCellValue('A4', 'Company Name:');
        $sheet->setCellValue('B4', $data['company']->name);
        $sheet->setCellValue('A5', 'PAYE Reference:');
        $sheet->setCellValue('B5', $data['company']->paye_reference_number);
        $sheet->setCellValue('A6', 'Period:');
        $sheet->setCellValue('B6', $data['period']);
        $sheet->setCellValue('A7', 'Generated:');
        $sheet->setCellValue('B7', $data['generated_at']->format('Y-m-d H:i:s'));

        // Summary Section
        $sheet->setCellValue('A9', 'Summary');
        $sheet->getStyle('A9')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);

        $summaryData = [
            ['Total Employees', $data['total_employees']],
            ['Total Remuneration', 'R ' . number_format($data['total_remuneration'], 2)],
            ['Total PAYE', 'R ' . number_format($data['total_paye'], 2)],
            ['Total UIF (Employee)', 'R ' . number_format($data['total_uif_employee'], 2)],
            ['Total UIF (Employer)', 'R ' . number_format($data['total_uif_employer'], 2)],
            ['Total UIF (Combined)', 'R ' . number_format($data['total_uif_employee'] + $data['total_uif_employer'], 2)],
            ['Total SDL', 'R ' . number_format($data['total_sdl'], 2)],
            ['ETI Benefit', 'R ' . number_format($data['total_eti_benefit'], 2)],
            ['Net PAYE Due', 'R ' . number_format($data['net_paye_due'], 2)]
        ];

        $row = 10;
        foreach ($summaryData as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($dataStyle);
            $row++;
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        // Generate Excel file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'emp201_');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return $content;
    }

    /**
     * Generate EMP501 Excel.
     */
    private function generateEmp501Excel(array $data): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setTitle('EMP501 Annual Reconciliation');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']]
        ];

        // Data styling
        $dataStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ];

        // Company Details Section
        $sheet->setCellValue('A1', 'EMP501 Annual Reconciliation');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray($headerStyle);

        $sheet->setCellValue('A3', 'Company Details');
        $sheet->getStyle('A3')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);

        $sheet->setCellValue('A4', 'Company Name:');
        $sheet->setCellValue('B4', $data['company']->name);
        $sheet->setCellValue('A5', 'PAYE Reference:');
        $sheet->setCellValue('B5', $data['company']->paye_reference_number);
        $sheet->setCellValue('A6', 'Tax Year:');
        $sheet->setCellValue('B6', $data['tax_year']);
        $sheet->setCellValue('A7', 'Period:');
        $sheet->setCellValue('B7', $data['period_start'] . ' to ' . $data['period_end']);
        $sheet->setCellValue('A8', 'Generated:');
        $sheet->setCellValue('B8', $data['generated_at']->format('Y-m-d H:i:s'));

        // Summary Section
        $sheet->setCellValue('A10', 'Annual Summary');
        $sheet->getStyle('A10')->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);

        $summaryData = [
            ['Total Employees', $data['total_employees']],
            ['Total Remuneration', 'R ' . number_format($data['total_remuneration'], 2)],
            ['Total PAYE', 'R ' . number_format($data['total_paye'], 2)],
            ['Total UIF (Employee)', 'R ' . number_format($data['total_uif_employee'], 2)],
            ['Total UIF (Employer)', 'R ' . number_format($data['total_uif_employer'], 2)],
            ['Total SDL', 'R ' . number_format($data['total_sdl'], 2)],
            ['ETI Benefit', 'R ' . number_format($data['total_eti_benefit'], 2)]
        ];

        $row = 11;
        foreach ($summaryData as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($dataStyle);
            $row++;
        }

        // Employee Details Section
        $sheet->setCellValue('A' . ($row + 1), 'Employee Details');
        $sheet->getStyle('A' . ($row + 1))->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);

        // Employee headers
        $employeeHeaders = ['Employee Number', 'Name', 'ID Number', 'Total Remuneration', 'Total PAYE', 'Total UIF'];
        $headerRow = $row + 3;
        $col = 'A';
        foreach ($employeeHeaders as $header) {
            $sheet->setCellValue($col . $headerRow, $header);
            $sheet->getStyle($col . $headerRow)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $col++;
        }

        // Employee data
        $dataRow = $headerRow + 1;
        foreach ($data['employees'] as $employee) {
            $sheet->setCellValue('A' . $dataRow, $employee['employee']->employee_number);
            $sheet->setCellValue('B' . $dataRow, $employee['employee']->first_name . ' ' . $employee['employee']->last_name);
            $sheet->setCellValue('C' . $dataRow, $employee['employee']->id_number);
            $sheet->setCellValue('D' . $dataRow, 'R ' . number_format($employee['total_remuneration'], 2));
            $sheet->setCellValue('E' . $dataRow, 'R ' . number_format($employee['total_paye'], 2));
            $sheet->setCellValue('F' . $dataRow, 'R ' . number_format($employee['total_uif_employee'], 2));

            $sheet->getStyle('A' . $dataRow . ':F' . $dataRow)->applyFromArray($dataStyle);
            $dataRow++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate Excel file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'emp501_');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return $content;
    }

    /**
     * Generate IRP5 CSV for SARS e@syfile.
     */
    public function generateIrp5Csv(Request $request): Response
    {
        // Debug logging
        \Log::info('IRP5 CSV Request received', [
            'request_data' => $request->all(),
            'session_company_id' => session('current_company_id'),
            'user_id' => auth()->id()
        ]);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'tax_year' => 'required|integer',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $taxYear = $validated['tax_year'];

        // Set session for tenant scope to work properly
        session(['current_company_id' => $company->id]);

        $startDate = Carbon::create($taxYear, 3, 1);
        $endDate = Carbon::create($taxYear + 1, 2, 28)->endOfMonth();

        $query = Payslip::where('company_id', $company->id)
            ->whereBetween('pay_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'processed'])
            ->whereHas('user', function($q) {
                $q->where('is_employee', true)
                  ->where('employment_status', 'active')
                  ->whereNotNull('employee_number')
                  ->where('employee_number', 'NOT LIKE', 'NAT%') // Exclude admin users like NAT001
                  ->where('employee_number', 'LIKE', 'CHR%'); // Include only CHR employees
            })
            ->with('user');

        if (!empty($validated['user_ids'])) {
            $query->whereIn('user_id', $validated['user_ids']);
        }

        $payslips = $query->get();

        // Debug logging
        \Log::info('IRP5 CSV Debug', [
            'company_id' => $company->id,
            'tax_year' => $taxYear,
            'session_company_id' => session('current_company_id'),
            'payslips_count' => $payslips->count(),
            'date_range' => [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]
        ]);

        $csv = $this->generateIrp5CsvContent($payslips, $company, $taxYear);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="IRP5_' . $company->paye_reference_number . '_' . $taxYear . '.csv"',
        ]);
    }

    /**
     * Generate IRP5 CSV content for SARS e@syfile.
     */
    private function generateIrp5CsvContent($payslips, $company, $taxYear): string
    {
        // Group payslips by user for annual totals
        $employeeData = $payslips->groupBy('user_id')->map(function ($userPayslips) {
            $user = $userPayslips->first()->user;
            $totalPayslips = $userPayslips->count();

            return [
                'employee' => $user,
                'total_remuneration' => $userPayslips->sum('taxable_earnings'),
                'total_paye' => $userPayslips->sum('paye_tax'),
                'total_uif_employee' => $userPayslips->sum('uif_employee'),
                'total_uif_employer' => $userPayslips->sum('uif_employer'),
                'total_sdl' => $userPayslips->sum('sdl_contribution'),
                'months_worked' => $totalPayslips,
                'sars_codes' => [
                    '3601' => $userPayslips->sum('sars_3601'),
                    '3605' => $userPayslips->sum('sars_3605'),
                    '3615' => $userPayslips->sum('sars_3615'),
                    '3617' => $userPayslips->sum('sars_3617'),
                    '3627' => $userPayslips->sum('sars_3627'),
                    '3699' => $userPayslips->sum('sars_3699'),
                ],
            ];
        });

        $csv = '';

        // Generate SARS e@syfile format
        // Header record (2010) - Company information
        $csv .= $this->generateCompanyHeaderRecord($company, $taxYear);

        // Employee records (3010) - Individual IRP5 data
        $employeeCounter = 1;
        foreach ($employeeData as $employee) {
            $csv .= $this->generateEmployeeRecord($employee, $company, $taxYear, $employeeCounter);
            $employeeCounter++;
        }

        // Trailer record (6010) - Required by SARS e@syfile
        $csv .= $this->generateTrailerRecord($employeeData->count(), $company, $taxYear);

        return $csv;
    }

    /**
     * Generate company header record (2010) for SARS e@syfile.
     */
    private function generateCompanyHeaderRecord($company, $taxYear): string
    {
        $record = '';

        // 2010 - Record type (Company header) - Just the record identifier
        $record .= '2010,';

        // 2015 - Status (LIVE)
        $record .= '2015,"LIVE",';

        // 2020 - PAYE Reference
        $record .= '2020,' . $this->escapeCsvField($company->paye_reference_number) . ',';

        // 2022 - PAYE Reference (duplicate)
        $record .= '2022,"' . $this->escapeCsvField($company->paye_reference_number) . '",';

        // 2024 - UIF Reference (if available)
        $record .= '2024,"' . $this->escapeCsvField($company->uif_reference_number ?? '') . '",';

        // 2025 - Contact person name
        $record .= '2025,"' . $this->escapeCsvField($company->contact_person ?? '') . '",';

        // 2036 - Contact person surname
        $record .= '2036,' . $this->escapeCsvField($company->contact_surname ?? '') . ',';

        // 2038 - Contact person title/position
        $record .= '2038,' . $this->escapeCsvField($company->contact_title ?? '') . ',';

        // 2026 - Phone number
        $record .= '2026,' . $this->escapeCsvField($company->phone ?? '') . ',';

        // 2039 - Phone number (duplicate)
        $record .= '2039,' . $this->escapeCsvField($company->phone ?? '') . ',';

        // 2028 - Company name (duplicate)
        $record .= '2028,"' . $this->escapeCsvField($company->name) . '",';

        // 2029 - Company name (duplicate)
        $record .= '2029,"' . $this->escapeCsvField($company->name) . '",';

        // 2030 - Tax year
        $record .= '2030,' . $taxYear . ',';

        // 2031 - Tax period (YYYYMM format)
        $record .= '2031,' . $taxYear . '09,'; // Assuming September for annual submission

        // 2082 - Company registration number
        $record .= '2082,' . $this->escapeCsvField($company->company_registration_number ?? '') . ',';

        // 2037 - Company type indicator
        $record .= '2037,N,'; // N for normal company

        // 2027 - Email address
        $record .= '2027,"' . $this->escapeCsvField($company->email ?? '') . '",';

        // 2063 - Address line 1
        $record .= '2063,"' . $this->escapeCsvField($company->physical_address_line1 ?? '') . '",';

        // 2064 - Address line 2
        $record .= '2064,"' . $this->escapeCsvField($company->physical_address_line2 ?? '') . '",';

        // 2065 - Suburb
        $record .= '2065,"' . $this->escapeCsvField($company->physical_suburb ?? '') . '",';

        // 2066 - City
        $record .= '2066,"' . $this->escapeCsvField($company->physical_city ?? '') . '",';

        // 2080 - Postal code
        $record .= '2080,' . $this->escapeCsvField($company->physical_postal_code ?? '') . ',';

        // 2081 - Country code
        $record .= '2081,"ZA",';

        // 9999 - End of record marker
        $record .= '9999' . "\n";

        return $record;
    }

    /**
     * Generate employee record (3010) for SARS e@syfile.
     */
    private function generateEmployeeRecord($employee, $company, $taxYear, $employeeCounter): string
    {
        $user = $employee['employee'];
        $record = '';

        // 3010 - Record type (Employee IRP5) - Just the record identifier
        $record .= '3010,';

        // 3015 - Certificate type
        $record .= '3015,"IRP5",';

        // 3020 - Certificate status
        $record .= '3020,"A",'; // A for Active
        
        // 3021 - Certificate reference number
        $record .= '3021,"' . $company->paye_reference_number . $taxYear . '09' . str_pad($employeeCounter, 6, '0', STR_PAD_LEFT) . '",';

        // 3025 - Tax year
        $record .= '3025,' . $taxYear . ',';

        // 3026 - Tax year indicator
        $record .= '3026,Y,'; // Y for Yes

        // 3030 - Employee surname
        $record .= '3030,"' . $this->escapeCsvField($user->last_name) . '",';

        // 3040 - Employee first name
        $record .= '3040,"' . $this->escapeCsvField($user->first_name) . '",';

        // 3050 - Employee initial
        $record .= '3050,"' . $this->escapeCsvField($user->initials ?? substr($user->first_name, 0, 1)) . '",';

        // 3060 - ID Number
        $record .= '3060,' . $this->escapeCsvField($user->id_number) . ',';

        // 3080 - Birth date (YYYYMMDD format)
        $record .= '3080,' . ($user->birth_date ? Carbon::parse($user->birth_date)->format('Ymd') : '') . ',';

        // 3100 - Tax number
        $record .= '3100,' . $this->escapeCsvField($user->tax_number ?? '') . ',';

        // 3263 - PAYE Reference
        $record .= '3263,"' . $company->paye_reference_number . '",';

        // 3136 - Phone number
        $record .= '3136,"' . $this->escapeCsvField($user->phone ?? '') . '",';

        // 3146 - Address line 1
        $record .= '3146,"' . $this->escapeCsvField($user->res_addr_line1 ?? '') . '",';

        // 3147 - Address line 2
        $record .= '3147,"' . $this->escapeCsvField($user->res_addr_line2 ?? '') . '",';

        // 3148 - Suburb
        $record .= '3148,"' . $this->escapeCsvField($user->res_suburb ?? '') . '",';

        // 3149 - City
        $record .= '3149,"' . $this->escapeCsvField($user->res_city ?? '') . '",';

        // 3150 - Postal code
        $record .= '3150,"' . $this->escapeCsvField($user->res_postcode ?? '') . '",';

        // 3151 - Country code
        $record .= '3151,"ZA",';

        // 3160 - Employee number
        $record .= '3160,"' . $this->escapeCsvField($user->employee_number) . '",';

        // 3170 - Employment start date (YYYYMMDD)
        $record .= '3170,' . ($user->employment_start_date ? Carbon::parse($user->employment_start_date)->format('Ymd') : '') . ',';

        // 3180 - Employment end date (YYYYMMDD)
        $record .= '3180,' . ($user->employment_end_date ? Carbon::parse($user->employment_end_date)->format('Ymd') : '') . ',';

        // 3195 - Disability indicator
        $record .= '3195,"N",'; // N for No disability

        // 3200 - Tax rate
        $record .= '3200,006.0000,'; // 6% tax rate

        // 3210 - Tax rebate
        $record .= '3210,001.0000,'; // Primary rebate

        // Address fields (duplicates for employee)
        $record .= '3213,"' . $this->escapeCsvField($user->res_addr_line1 ?? '') . '",';
        $record .= '3214,"' . $this->escapeCsvField($user->res_addr_line2 ?? '') . '",';
        $record .= '3215,"' . $this->escapeCsvField($user->res_suburb ?? '') . '",';
        $record .= '3216,"' . $this->escapeCsvField($user->res_city ?? '') . '",';
        $record .= '3217,"' . $this->escapeCsvField($user->res_postcode ?? '') . '",';

        // 3220 - Residency indicator
        $record .= '3220,"N",'; // N for Non-resident (adjust as needed)

        // 3285 - Country code
        $record .= '3285,"ZA",';

        // 3279 - Pension fund indicator
        $record .= '3279,"N",'; // N for No pension fund

        // 3288 - Months worked
        $record .= '3288,' . $employee['months_worked'] . ',';

        // 3240 - Additional income
        $record .= '3240,0,';

        // SARS Codes
        $record .= '3601,' . str_pad(number_format($employee['sars_codes']['3601'], 0, '', ''), 12, '0', STR_PAD_LEFT) . ',';
        $record .= '3699,' . str_pad(number_format($employee['sars_codes']['3699'], 0, '', ''), 12, '0', STR_PAD_LEFT) . ',';

        // UIF and SDL
        $record .= '4102,0,'; // UIF exempt indicator
        $record .= '4141,' . str_pad(number_format($employee['total_uif_employee'], 2, '', ''), 12, '0', STR_PAD_LEFT) . ',';
        $record .= '4142,' . str_pad(number_format($employee['total_uif_employer'], 2, '', ''), 12, '0', STR_PAD_LEFT) . ',';
        $record .= '4149,' . str_pad(number_format($employee['total_uif_employee'] + $employee['total_uif_employer'], 2, '', ''), 12, '0', STR_PAD_LEFT) . ',';
        $record .= '4118,' . str_pad(number_format($employee['total_paye'], 0, '', ''), 12, '0', STR_PAD_LEFT) . ',';

        // Additional fields
        $record .= '7006,08,'; // Tax month
        $record .= '7005,1,'; // Tax period
        $record .= '7007,160,'; // Tax rebate amount
        $record .= '7002,' . str_pad(number_format($employee['total_remuneration'], 0, '', ''), 12, '0', STR_PAD_LEFT) . ',';
        $record .= '7003,' . str_pad(number_format($employee['total_remuneration'], 0, '', ''), 12, '0', STR_PAD_LEFT) . ',';
        $record .= '7008,' . str_pad(number_format($employee['total_remuneration'], 0, '', ''), 12, '0', STR_PAD_LEFT) . ',';
        $record .= '7004,' . str_pad(number_format($employee['total_paye'], 0, '', ''), 12, '0', STR_PAD_LEFT) . ',';

        // 9999 - End of record marker
        $record .= '9999' . "\n";

        return $record;
    }

    /**
     * Generate trailer record (6010) for SARS e@syfile.
     */
    private function generateTrailerRecord($totalEmployees, $company, $taxYear): string
    {
        $record = '';
        
        // 6010 - Record type (Trailer) - Just the record identifier
        $record .= '6010,';
        
        // 6015 - Status (LIVE)
        $record .= '6015,"LIVE",';
        
        // 6020 - PAYE Reference
        $record .= '6020,"' . $company->paye_reference_number . '",';
        
        // 6025 - Tax year
        $record .= '6025,' . $taxYear . ',';
        
        // 6030 - Total number of employees
        $record .= '6030,' . $totalEmployees . ',';
        
        // 6035 - File generation date (YYYYMMDD)
        $record .= '6035,' . now()->format('Ymd') . ',';
        
        // 6040 - File generation time (HHMMSS)
        $record .= '6040,' . now()->format('His') . ',';
        
        // 9999 - End of record marker
        $record .= '9999' . "\n";
        
        return $record;
    }

    /**
     * Escape CSV field to handle commas, quotes, and newlines.
     */
    private function escapeCsvField($field): string
    {
        if ($field === null) {
            return '';
        }

        $field = (string) $field;

        // If field contains comma, quote, or newline, wrap in quotes and escape quotes
        if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
            return '"' . str_replace('"', '""', $field) . '"';
        }

        return $field;
    }

    /**
     * Generate UIF CSV.
     */
    private function generateUifCsv(array $data): string
    {
        $csv = "UIF Monthly Declaration - {$data['period']}\n\n";
        $csv .= "Company: {$data['company']->name}\n";
        $csv .= "UIF Reference: {$data['company']->uif_reference_number}\n";
        $csv .= "Period: {$data['period']}\n";
        $csv .= "Total Employees: {$data['total_employees']}\n";
        $csv .= "Total Remuneration: {$data['total_remuneration']}\n";
        $csv .= "Employee UIF: {$data['total_uif_employee']}\n";
        $csv .= "Employer UIF: {$data['total_uif_employer']}\n";
        $csv .= "Total UIF: {$data['total_uif_contribution']}\n";

        return $csv;
    }

    /**
     * Run comprehensive compliance validation.
     */
    private function runComplianceValidation(): array
    {
        $checks = [];

        // Check for missing SARS references
        $companiesWithoutSarsRef = Company::whereNull('paye_reference_number')->count();
        $checks[] = [
            'category' => 'Company Setup',
            'check' => 'SARS PAYE Reference',
            'status' => $companiesWithoutSarsRef === 0 ? 'pass' : 'fail',
            'message' => $companiesWithoutSarsRef === 0
                ? 'All companies have SARS PAYE references'
                : "{$companiesWithoutSarsRef} companies missing SARS PAYE references",
        ];

        // Check for unprocessed payslips
        $unprocessedPayslips = Payslip::where('is_final', false)->count();
        $checks[] = [
            'category' => 'Payroll',
            'check' => 'Unprocessed Payslips',
            'status' => $unprocessedPayslips === 0 ? 'pass' : 'warning',
            'message' => $unprocessedPayslips === 0
                ? 'All payslips processed'
                : "{$unprocessedPayslips} payslips pending processing",
        ];

        // Check for missing attendance records
        $orphanedPayslips = Payslip::whereHas('user', function ($query) {
            $query->whereDoesntHave('attendanceRecords');
        })->count();
        $checks[] = [
            'category' => 'Attendance',
            'check' => 'Payslips without Attendance',
            'status' => $orphanedPayslips === 0 ? 'pass' : 'warning',
            'message' => $orphanedPayslips === 0
                ? 'All payslips have corresponding attendance'
                : "{$orphanedPayslips} payslips without attendance records",
        ];

        return $checks;
    }
}