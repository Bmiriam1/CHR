<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HostController extends Controller
{
    /**
     * Display a listing of hosts.
     */
    public function index()
    {
        $user = auth()->user();
        $userCompany = $user->company;
        
        // Get all companies in the group for admin users
        if ($user->hasRole(['admin', 'hr_manager', 'company_admin'])) {
            $allowedCompanyIds = $userCompany->getCompanyGroup()->pluck('id');
        } else {
            $allowedCompanyIds = collect([$userCompany->id]);
        }

        // Load hosts with relationships and analytics
        $hosts = Host::with(['program', 'company', 'attendanceRecords'])
            ->whereIn('company_id', $allowedCompanyIds)
            ->orderBy('name')
            ->get();

        // Calculate analytics
        $analytics = [
            'total_hosts' => $hosts->count(),
            'active_hosts' => $hosts->where('is_active', true)->count(),
            'inactive_hosts' => $hosts->where('is_active', false)->count(),
            'hosts_with_gps' => $hosts->where('requires_gps_validation', true)->count(),
            'hosts_with_time_validation' => $hosts->where('requires_time_validation', true)->count(),
            'total_check_ins_today' => $hosts->sum(function($host) {
                return $host->attendanceRecords->where('attendance_date', now()->toDateString())->count();
            }),
        ];

        // Group hosts by program for better organization
        $hostsByProgram = $hosts->groupBy('program.title');

        return view('hosts.index', compact('hosts', 'analytics', 'hostsByProgram'));
    }

    /**
     * Show the form for creating a new host.
     */
    public function create()
    {
        $programs = Program::where('company_id', auth()->user()->company_id)->get();
        return view('hosts.create', compact('programs'));
    }

    /**
     * Store a newly created host.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,id',
            'description' => 'nullable|string|max:1000',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|numeric|min:10|max:10000',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'check_in_start_time' => 'nullable|date_format:H:i',
            'check_in_end_time' => 'nullable|date_format:H:i|after:check_in_start_time',
            'check_out_start_time' => 'nullable|date_format:H:i',
            'check_out_end_time' => 'nullable|date_format:H:i|after:check_out_start_time',
            'max_daily_check_ins' => 'required|integer|min:1|max:10',
            'allow_multiple_check_ins' => 'boolean',
            'require_supervisor_approval' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $host = Host::create([
            'company_id' => auth()->user()->company_id,
            'program_id' => $request->program_id,
            'name' => $request->name,
            'description' => $request->description,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
            'contact_person' => $request->contact_person,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'check_in_start_time' => $request->check_in_start_time,
            'check_in_end_time' => $request->check_in_end_time,
            'check_out_start_time' => $request->check_out_start_time,
            'check_out_end_time' => $request->check_out_end_time,
            'max_daily_check_ins' => $request->max_daily_check_ins,
            'allow_multiple_check_ins' => $request->boolean('allow_multiple_check_ins'),
            'require_supervisor_approval' => $request->boolean('require_supervisor_approval'),
        ]);

        return redirect()->route('hosts.show', $host)
            ->with('success', 'Host location created successfully.');
    }

    /**
     * Display the specified host.
     */
    public function show(Host $host)
    {
        // Ensure user can access this host
        $user = auth()->user();
        $userCompany = $user->company;
        
        if ($user->hasRole(['admin', 'hr_manager', 'company_admin'])) {
            $allowedCompanyIds = $userCompany->getCompanyGroup()->pluck('id');
        } else {
            $allowedCompanyIds = collect([$userCompany->id]);
        }

        if (!$allowedCompanyIds->contains($host->company_id)) {
            abort(403, 'Access denied to this host location.');
        }

        $host->load(['program', 'company', 'attendanceRecords.user']);

        // Calculate analytics for this host
        $analytics = [
            'total_check_ins' => $host->attendanceRecords->count(),
            'check_ins_today' => $host->attendanceRecords->where('attendance_date', now()->toDateString())->count(),
            'check_ins_this_week' => $host->attendanceRecords->where('attendance_date', '>=', now()->startOfWeek())->count(),
            'check_ins_this_month' => $host->attendanceRecords->where('attendance_date', '>=', now()->startOfMonth())->count(),
            'unique_users' => $host->attendanceRecords->unique('user_id')->count(),
            'avg_daily_check_ins' => $host->attendanceRecords->count() > 0 
                ? round($host->attendanceRecords->count() / max(1, $host->attendanceRecords->groupBy('attendance_date')->count()), 1)
                : 0,
        ];

        // Get recent activity
        $recentActivity = $host->attendanceRecords()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('hosts.show', compact('host', 'analytics', 'recentActivity'));
    }

    /**
     * Show the form for editing the specified host.
     */
    public function edit(Host $host)
    {
        $programs = Program::where('company_id', auth()->user()->company_id)->get();
        return view('hosts.edit', compact('host', 'programs'));
    }

    /**
     * Update the specified host.
     */
    public function update(Request $request, Host $host)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,id',
            'description' => 'nullable|string|max:1000',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|numeric|min:10|max:10000',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'check_in_start_time' => 'nullable|date_format:H:i',
            'check_in_end_time' => 'nullable|date_format:H:i|after:check_in_start_time',
            'check_out_start_time' => 'nullable|date_format:H:i',
            'check_out_end_time' => 'nullable|date_format:H:i|after:check_out_start_time',
            'max_daily_check_ins' => 'required|integer|min:1|max:10',
            'allow_multiple_check_ins' => 'boolean',
            'require_supervisor_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $host->update([
            'program_id' => $request->program_id,
            'name' => $request->name,
            'description' => $request->description,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
            'contact_person' => $request->contact_person,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'check_in_start_time' => $request->check_in_start_time,
            'check_in_end_time' => $request->check_in_end_time,
            'check_out_start_time' => $request->check_out_start_time,
            'check_out_end_time' => $request->check_out_end_time,
            'max_daily_check_ins' => $request->max_daily_check_ins,
            'allow_multiple_check_ins' => $request->boolean('allow_multiple_check_ins'),
            'require_supervisor_approval' => $request->boolean('require_supervisor_approval'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('hosts.show', $host)
            ->with('success', 'Host location updated successfully.');
    }

    /**
     * Remove the specified host.
     */
    public function destroy(Host $host)
    {
        $host->delete();
        return redirect()->route('hosts.index')
            ->with('success', 'Host location deleted successfully.');
    }

    /**
     * Generate new QR code for host.
     */
    public function generateQRCode(Host $host)
    {
        $host->generateQRCode();
        $host->save();

        return redirect()->route('hosts.show', $host)
            ->with('success', 'QR code regenerated successfully.');
    }

    /**
     * Download QR code as image.
     */
    public function downloadQRCode(Host $host)
    {
        // This would generate and return a QR code image
        // For now, return the QR code data
        return response()->json([
            'qr_code' => $host->qr_code,
            'qr_data' => $host->qr_code_data_array,
        ]);
    }

    /**
     * Validate QR code for check-in.
     */
    public function validateQRCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $location = null;
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $location = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];
        }

        $result = Host::validateQRCode($request->qr_code, $location);

        return response()->json($result);
    }
}
