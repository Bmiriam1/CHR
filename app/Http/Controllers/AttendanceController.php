<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Company;
use App\Models\Program;
use App\Models\User;
use App\Models\Device;
use App\Notifications\ProofApprovedNotification;
use App\Notifications\ProofRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request)
    {
        $query = AttendanceRecord::with(['user', 'program', 'company'])
            ->where('company_id', auth()->user()->company_id);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('attendance_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('attendance_date', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by program
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by proof status
        if ($request->filled('proof_status')) {
            $query->where('proof_status', $request->proof_status);
        }

        $attendanceRecords = $query->orderBy('attendance_date', 'desc')
            ->paginate(20);

        return view('attendance.index', compact('attendanceRecords'));
    }

    /**
     * Show the form for creating a new attendance record.
     */
    public function create()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->where('is_learner', true)
            ->get();

        $programs = Program::where('company_id', auth()->user()->company_id)
            ->get();

        return view('attendance.create', compact('users', 'programs'));
    }

    /**
     * Store a newly created attendance record.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:programs,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,late,absent_unauthorized,absent_authorized,excused,on_leave,sick,half_day',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'exception_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if attendance record already exists for this user/date/program
        $existingRecord = AttendanceRecord::where('company_id', auth()->user()->company_id)
            ->where('user_id', $request->user_id)
            ->where('program_id', $request->program_id)
            ->where('attendance_date', $request->attendance_date)
            ->first();

        if ($existingRecord) {
            return redirect()->back()
                ->withErrors(['attendance_date' => 'Attendance record already exists for this user on this date.'])
                ->withInput();
        }

        $attendanceRecord = AttendanceRecord::create([
            'company_id' => auth()->user()->company_id,
            'user_id' => $request->user_id,
            'program_id' => $request->program_id,
            'attendance_date' => $request->attendance_date,
            'status' => $request->status,
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
            'notes' => $request->notes,
            'exception_reason' => $request->exception_reason,
            'is_payable' => in_array($request->status, ['present', 'late', 'half_day', 'absent_authorized']),
        ]);

        return redirect()->route('attendance.show', $attendanceRecord)
            ->with('success', 'Attendance record created successfully.');
    }

    /**
     * Display the specified attendance record.
     */
    public function show(AttendanceRecord $attendance)
    {
        $attendance->load(['user', 'program', 'company', 'validator', 'approver', 'proofApprover']);

        return view('attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified attendance record.
     */
    public function edit(AttendanceRecord $attendance)
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->where('is_learner', true)
            ->get();

        $programs = Program::where('company_id', auth()->user()->company_id)
            ->get();

        return view('attendance.edit', compact('attendance', 'users', 'programs'));
    }

    /**
     * Update the specified attendance record.
     */
    public function update(Request $request, AttendanceRecord $attendance)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:programs,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,late,absent_unauthorized,absent_authorized,excused,on_leave,sick,half_day',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'exception_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $attendance->update([
            'user_id' => $request->user_id,
            'program_id' => $request->program_id,
            'attendance_date' => $request->attendance_date,
            'status' => $request->status,
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
            'notes' => $request->notes,
            'exception_reason' => $request->exception_reason,
            'is_payable' => in_array($request->status, ['present', 'late', 'half_day', 'absent_authorized']),
        ]);

        return redirect()->route('attendance.show', $attendance)
            ->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified attendance record.
     */
    public function destroy(AttendanceRecord $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    /**
     * Mark attendance for multiple learners at once.
     */
    public function bulkMark(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attendance_date' => 'required|date',
            'program_id' => 'required|exists:programs,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:present,late,absent_unauthorized,absent_authorized,excused,on_leave,sick,half_day',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $created = 0;
        $updated = 0;

        foreach ($request->user_ids as $userId) {
            $attendanceRecord = AttendanceRecord::updateOrCreate(
                [
                    'company_id' => auth()->user()->company_id,
                    'user_id' => $userId,
                    'program_id' => $request->program_id,
                    'attendance_date' => $request->attendance_date,
                ],
                [
                    'status' => $request->status,
                    'notes' => $request->notes,
                    'is_payable' => in_array($request->status, ['present', 'late', 'half_day', 'absent_authorized']),
                ]
            );

            if ($attendanceRecord->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        return response()->json([
            'message' => "Bulk attendance marked successfully. Created: {$created}, Updated: {$updated}",
            'created' => $created,
            'updated' => $updated,
        ]);
    }

    /**
     * QR Code check-in for learners.
     */
    public function qrCheckIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'program_id' => 'required|exists:programs,id',
            'device_id' => 'nullable|exists:devices,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $today = now()->toDateString();

        // Check if attendance record already exists for today
        $attendanceRecord = AttendanceRecord::where('company_id', $user->company_id)
            ->where('user_id', $user->id)
            ->where('program_id', $request->program_id)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendanceRecord) {
            $attendanceRecord = AttendanceRecord::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'program_id' => $request->program_id,
                'attendance_date' => $today,
                'status' => 'present',
                'is_payable' => true,
            ]);
        }

        // Update with QR check-in
        $attendanceRecord->markPresentWithQR($request->qr_code);

        return response()->json([
            'message' => 'Check-in successful',
            'attendance_record' => $attendanceRecord->fresh(),
        ]);
    }

    /**
     * Upload proof document for unauthorized absence.
     */
    public function uploadProof(Request $request, AttendanceRecord $attendance)
    {
        $validator = Validator::make($request->all(), [
            'proof_document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // 10MB max
            'document_type' => 'required|in:medical_certificate,emergency_document,other',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($attendance->status !== 'absent_unauthorized') {
            return response()->json(['error' => 'Proof can only be uploaded for unauthorized absences.'], 400);
        }

        // Store the file
        $file = $request->file('proof_document');
        $path = $file->store('attendance-proofs', 'private');

        $attendance->uploadProof($path, $request->document_type, $request->notes);

        return response()->json([
            'message' => 'Proof document uploaded successfully',
            'attendance_record' => $attendance->fresh(),
        ]);
    }

    /**
     * Approve proof document.
     */
    public function approveProof(Request $request, AttendanceRecord $attendance)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($attendance->proof_status !== 'pending') {
            return response()->json(['error' => 'Only pending proof documents can be approved.'], 400);
        }

        $attendance->approveProof(auth()->user(), $request->notes);

        // Send notification to learner
        $attendance->learner->notify(new ProofApprovedNotification($attendance));

        return response()->json([
            'message' => 'Proof document approved successfully',
            'attendance_record' => $attendance->fresh(),
        ]);
    }

    /**
     * Reject proof document.
     */
    public function rejectProof(Request $request, AttendanceRecord $attendance)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($attendance->proof_status !== 'pending') {
            return response()->json(['error' => 'Only pending proof documents can be rejected.'], 400);
        }

        $attendance->rejectProof(auth()->user(), $request->notes);

        // Send notification to learner
        $attendance->learner->notify(new ProofRejectedNotification($attendance, $request->notes));

        return response()->json([
            'message' => 'Proof document rejected',
            'attendance_record' => $attendance->fresh(),
        ]);
    }

    /**
     * Get attendance records requiring proof approval.
     */
    public function pendingProof()
    {
        $attendanceRecords = AttendanceRecord::with(['user', 'program'])
            ->where('company_id', auth()->user()->company_id)
            ->withPendingProof()
            ->orderBy('attendance_date', 'desc')
            ->paginate(20);

        return view('attendance.pending-proof', compact('attendanceRecords'));
    }

    /**
     * Get attendance summary for a date range.
     */
    public function summary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'program_id' => 'nullable|exists:programs,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = AttendanceRecord::where('company_id', auth()->user()->company_id)
            ->whereBetween('attendance_date', [$request->start_date, $request->end_date]);

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $summary = $query->selectRaw('
                status,
                COUNT(*) as count,
                SUM(CASE WHEN is_payable = 1 THEN 1 ELSE 0 END) as payable_count,
                SUM(calculated_pay) as total_pay
            ')
            ->groupBy('status')
            ->get();

        $totalRecords = $query->count();
        $totalPayable = $query->where('is_payable', true)->count();
        $totalPay = $query->where('is_payable', true)->sum('calculated_pay');

        return response()->json([
            'summary' => $summary,
            'totals' => [
                'total_records' => $totalRecords,
                'total_payable' => $totalPayable,
                'total_pay' => $totalPay,
            ],
        ]);
    }

    /**
     * Download proof document.
     */
    public function downloadProof(AttendanceRecord $attendance)
    {
        if (!$attendance->proof_document_path) {
            abort(404, 'Proof document not found');
        }

        if (!Storage::disk('private')->exists($attendance->proof_document_path)) {
            abort(404, 'Proof document file not found');
        }

        return Storage::disk('private')->download(
            $attendance->proof_document_path,
            'proof_' . $attendance->id . '_' . $attendance->attendance_date . '.' . pathinfo($attendance->proof_document_path, PATHINFO_EXTENSION)
        );
    }
}
