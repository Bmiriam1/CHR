<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies (clients).
     */
    public function index()
    {
        $user = auth()->user();

        // Get the user's primary company and all child companies
        $primaryCompany = $user->company;

        if (!$primaryCompany) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        // Get the root parent company
        $rootCompany = $primaryCompany->getRootParent();

        // Get all companies in the group (parent + children) with proper counts
        $companies = $rootCompany->getCompanyGroup();

        // Load relationships for each company with all fields
        $companies->load([
            'programs',
            'users' => function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'learner');
                });
            }
        ]);

        return view('companies.index', compact('companies', 'rootCompany'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        $user = auth()->user();
        $parentCompany = null;

        // Only get parent company if user has a company assigned
        if ($user->hasCompany() && $user->company) {
            $parentCompany = $user->company->getRootParent();
        }

        return view('companies.create', compact('parentCompany'));
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'company_registration_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'physical_address_line1' => 'nullable|string|max:255',
            'physical_address_line2' => 'nullable|string|max:255',
            'physical_city' => 'nullable|string|max:100',
            'physical_postal_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();
        $parentCompany = null;

        // Get parent company if user has one, otherwise this will be a root company
        if ($user->hasCompany() && $user->company) {
            $parentCompany = $user->company->getRootParent();
        }

        $company = Company::create([
            'parent_company_id' => $parentCompany?->id,
            'name' => $request->name,
            'display_name' => $request->display_name,
            'trading_name' => $request->trading_name,
            'company_registration_number' => $request->company_registration_number,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'physical_address_line1' => $request->physical_address_line1,
            'physical_address_line2' => $request->physical_address_line2,
            'physical_city' => $request->physical_city,
            'physical_postal_code' => $request->physical_postal_code,
            'is_active' => $request->boolean('is_active', true),
            // Inherit some settings from parent
            'postal_country_code' => $parentCompany->postal_country_code ?? 'ZA',
            'default_pay_frequency' => $parentCompany->default_pay_frequency,
            'subscription_tier' => $parentCompany->subscription_tier,
            'billing_active' => true,
            'max_learners' => 100, // Default limit for sub-companies
            'max_programs' => 10,
        ]);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        // Ensure user can access this company
        $user = auth()->user();
        $userCompany = $user->company;

        if (!$userCompany) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        $allowedCompanies = $userCompany->getCompanyGroup()->pluck('id');

        if (!$allowedCompanies->contains($company->id)) {
            abort(403, 'Access denied to this client.');
        }

        $company->load([
            'parentCompany',
            'childCompanies',
        ]);

        // Load programs without global scope to avoid tenant filtering
        // Only show programs that the user has access to (same company or parent company)
        $userCompany = $user->company;

        if (!$userCompany) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        $allowedCompanyIds = $userCompany->getCompanyGroup()->pluck('id');

        $programs = Program::withoutGlobalScopes()
            ->whereIn('company_id', $allowedCompanyIds)
            ->with(['schedules', 'coordinator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $company->setRelation('programs', $programs);

        // Load users with learner role
        $learners = User::where('company_id', $company->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'learner');
            })
            ->get();

        $company->setRelation('users', $learners);

        // Get statistics with proper counts
        $stats = [
            'total_programs' => $programs->count(),
            'active_programs' => $programs->where('status', 'active')->count(),
            'total_learners' => $learners->count(),
            'remaining_program_capacity' => max(0, ($company->max_programs ?? 999) - $programs->count()),
            'remaining_learner_capacity' => max(0, ($company->max_learners ?? 999) - $learners->count()),
        ];

        return view('companies.show', compact('company', 'stats'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        // Ensure user can access this company
        $user = auth()->user();
        $userCompany = $user->company;

        if (!$userCompany) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        $allowedCompanies = $userCompany->getCompanyGroup()->pluck('id');

        if (!$allowedCompanies->contains($company->id)) {
            abort(403, 'Access denied to this client.');
        }

        $parentCompany = $company->parentCompany;

        return view('companies.edit', compact('company', 'parentCompany'));
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, Company $company)
    {
        // Ensure user can access this company
        $user = auth()->user();
        $userCompany = $user->company;

        if (!$userCompany) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        $allowedCompanies = $userCompany->getCompanyGroup()->pluck('id');

        if (!$allowedCompanies->contains($company->id)) {
            abort(403, 'Access denied to this client.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'company_registration_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'physical_address_line1' => 'nullable|string|max:255',
            'physical_address_line2' => 'nullable|string|max:255',
            'physical_city' => 'nullable|string|max:100',
            'physical_postal_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $company->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'trading_name' => $request->trading_name,
            'company_registration_number' => $request->company_registration_number,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'physical_address_line1' => $request->physical_address_line1,
            'physical_address_line2' => $request->physical_address_line2,
            'physical_city' => $request->physical_city,
            'physical_postal_code' => $request->physical_postal_code,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified company.
     */
    public function destroy(Company $company)
    {
        // Ensure user can access this company
        $user = auth()->user();
        $userCompany = $user->company;

        if (!$userCompany) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        $allowedCompanies = $userCompany->getCompanyGroup()->pluck('id');

        if (!$allowedCompanies->contains($company->id)) {
            abort(403, 'Access denied to this client.');
        }

        // Prevent deletion of parent company
        if ($company->isParentCompany()) {
            return redirect()->back()->with('error', 'Cannot delete the primary client.');
        }

        // Check if company has active programs
        if ($company->programs()->where('status', '!=', 'completed')->exists()) {
            return redirect()->back()->with('error', 'Cannot delete client with active programs.');
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Toggle company active status.
     */
    public function toggleStatus(Company $company)
    {
        // Ensure user can access this company
        $user = auth()->user();
        $userCompany = $user->company;

        if (!$userCompany) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        $allowedCompanies = $userCompany->getCompanyGroup()->pluck('id');

        if (!$allowedCompanies->contains($company->id)) {
            abort(403, 'Access denied to this client.');
        }

        $company->update([
            'is_active' => !$company->is_active
        ]);

        $status = $company->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Client {$status} successfully.");
    }

    /**
     * Show programs for a specific company.
     */
    public function programs(Company $company)
    {
        // Ensure user can access this company
        $user = auth()->user();
        $userCompany = $user->company;

        if (!$userCompany) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        $allowedCompanies = $userCompany->getCompanyGroup()->pluck('id');

        if (!$allowedCompanies->contains($company->id)) {
            abort(403, 'Access denied to this client.');
        }

        $programs = $company->programs()
            ->with(['programType', 'schedules', 'coordinator'])
            ->orderBy('title')
            ->paginate(20);

        return view('companies.programs', compact('company', 'programs'));
    }
}
