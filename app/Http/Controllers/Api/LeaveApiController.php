<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\LeaveAccrual;
use App\Models\LeaveCarryOver;
use App\Models\Program;
use App\Models\User;
use App\Services\LeaveManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LeaveApiController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveManagementService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Get user's leave balance summary
     */
    public function getBalance(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $year = $request->get('year', Carbon::now()->year);

            $balanceSummary = $this->leaveService->getLeaveBalanceSummary($user, $year);

            return response()->json([
                'success' => true,
                'data' => $balanceSummary,
                'message' => 'Leave balance retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leave balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available leave types for user
     */
    public function getLeaveTypes(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $userRole = $user->getRoleNames()->first() ?? 'learner';

            $leaveTypes = LeaveType::getForRole($userRole);

            // Get current balances for each leave type
            $leaveTypesWithBalances = $leaveTypes->map(function ($leaveType) use ($user) {
                $balance = LeaveAccrual::getCurrentBalance($user->id, $leaveType->id);
                $carryOverDays = LeaveCarryOver::getActiveCarryOverDays($user->id, $leaveType->id);

                return [
                    'id' => $leaveType->id,
                    'code' => $leaveType->code,
                    'name' => $leaveType->name,
                    'description' => $leaveType->description,
                    'annual_entitlement_days' => $leaveType->annual_entitlement_days,
                    'accrual_rate_per_month' => $leaveType->accrual_rate_per_month,
                    'max_carry_over_days' => $leaveType->max_carry_over_days,
                    'min_service_months' => $leaveType->min_service_months,
                    'requires_medical_certificate' => $leaveType->requires_medical_certificate,
                    'medical_cert_required_after_days' => $leaveType->medical_cert_required_after_days,
                    'is_taxable' => $leaveType->is_taxable,
                    'allows_partial_days' => $leaveType->allows_partial_days,
                    'allows_advance_request' => $leaveType->allows_advance_request,
                    'min_notice_days' => $leaveType->min_notice_days,
                    'max_consecutive_days' => $leaveType->max_consecutive_days,
                    'current_balance' => $balance,
                    'carry_over_balance' => $carryOverDays,
                    'is_active' => $leaveType->is_active,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $leaveTypesWithBalances,
                'message' => 'Leave types retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leave types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's programs for leave requests
     */
    public function getUserPrograms(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $programs = $user->programs()
                ->where('programs.status', 'active')
                ->with(['company'])
                ->get()
                ->map(function ($program) {
                    return [
                        'id' => $program->id,
                        'title' => $program->title,
                        'company_name' => $program->company->name,
                        'start_date' => $program->start_date,
                        'end_date' => $program->end_date,
                        'daily_rate' => $program->daily_rate,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $programs,
                'message' => 'User programs retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve programs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit a leave request
     */
    public function submitRequest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'program_id' => 'required|exists:programs,id',
                'leave_type_id' => 'required|exists:leave_types,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'required|string|max:1000',
                'notes' => 'nullable|string|max:1000',
                'is_emergency' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $program = Program::findOrFail($request->program_id);

            // Check if user is enrolled in the program
            if (!$program->participants()->where('user_id', $request->user()->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not enrolled in this program'
                ], 403);
            }

            // Prepare request data
            $requestData = [
                'user_id' => $request->user()->id,
                'program_id' => $request->program_id,
                'company_id' => $program->company_id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'is_emergency' => $request->get('is_emergency', false),
            ];

            // Process leave request through service
            $result = $this->leaveService->processLeaveRequest($requestData);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Leave request validation failed',
                    'errors' => $result['errors'],
                    'warnings' => $result['warnings'] ?? []
                ], 422);
            }

            $leaveRequest = $result['leave_request']->load(['leaveType', 'program', 'company']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $leaveRequest->id,
                    'leave_type' => $leaveRequest->leaveType->name,
                    'start_date' => $leaveRequest->start_date,
                    'end_date' => $leaveRequest->end_date,
                    'duration' => $leaveRequest->duration,
                    'status' => $leaveRequest->status,
                    'reason' => $leaveRequest->reason,
                    'notes' => $leaveRequest->notes,
                    'is_emergency' => $leaveRequest->is_emergency,
                    'requires_medical_certificate' => $leaveRequest->requires_medical_certificate,
                    'submitted_at' => $leaveRequest->submitted_at,
                ],
                'message' => 'Leave request submitted successfully',
                'warnings' => $result['validation']['warnings'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's leave requests
     */
    public function getRequests(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $status = $request->get('status');
            $year = $request->get('year', Carbon::now()->year);
            $limit = $request->get('limit', 20);

            $query = LeaveRequest::where('user_id', $user->id)
                ->with(['leaveType', 'program', 'company', 'approvedBy']);

            if ($status) {
                $query->where('status', $status);
            }

            if ($year) {
                $query->whereYear('start_date', $year);
            }

            $leaveRequests = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'leave_type' => $request->leaveType->name,
                        'leave_type_code' => $request->leaveType->code,
                        'program_title' => $request->program->title,
                        'company_name' => $request->company->name,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'duration' => $request->duration,
                        'status' => $request->status,
                        'reason' => $request->reason,
                        'notes' => $request->notes,
                        'is_emergency' => $request->is_emergency,
                        'requires_medical_certificate' => $request->requires_medical_certificate,
                        'is_paid_leave' => $request->is_paid_leave,
                        'total_leave_pay' => $request->total_leave_pay,
                        'approved_by_name' => $request->approvedBy?->full_name,
                        'approved_at' => $request->approved_at,
                        'rejection_reason' => $request->rejection_reason,
                        'submitted_at' => $request->submitted_at,
                        'created_at' => $request->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $leaveRequests,
                'message' => 'Leave requests retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leave requests: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific leave request details
     */
    public function getRequest(Request $request, LeaveRequest $leaveRequest): JsonResponse
    {
        try {
            // Check access permissions
            if ($leaveRequest->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to leave request'
                ], 403);
            }

            $leaveRequest->load(['leaveType', 'program', 'company', 'approvedBy']);

            $data = [
                'id' => $leaveRequest->id,
                'leave_type' => [
                    'id' => $leaveRequest->leaveType->id,
                    'code' => $leaveRequest->leaveType->code,
                    'name' => $leaveRequest->leaveType->name,
                ],
                'program' => [
                    'id' => $leaveRequest->program->id,
                    'title' => $leaveRequest->program->title,
                    'daily_rate' => $leaveRequest->program->daily_rate,
                ],
                'company' => [
                    'id' => $leaveRequest->company->id,
                    'name' => $leaveRequest->company->name,
                ],
                'start_date' => $leaveRequest->start_date,
                'end_date' => $leaveRequest->end_date,
                'duration' => $leaveRequest->duration,
                'status' => $leaveRequest->status,
                'reason' => $leaveRequest->reason,
                'notes' => $leaveRequest->notes,
                'is_emergency' => $leaveRequest->is_emergency,
                'requires_medical_certificate' => $leaveRequest->requires_medical_certificate,
                'medical_certificate_path' => $leaveRequest->medical_certificate_path,
                'is_paid_leave' => $leaveRequest->is_paid_leave,
                'daily_rate_at_time' => $leaveRequest->daily_rate_at_time,
                'total_leave_pay' => $leaveRequest->total_leave_pay,
                'approved_by' => $leaveRequest->approvedBy ? [
                    'id' => $leaveRequest->approvedBy->id,
                    'name' => $leaveRequest->approvedBy->full_name,
                ] : null,
                'approved_at' => $leaveRequest->approved_at,
                'rejection_reason' => $leaveRequest->rejection_reason,
                'submitted_at' => $leaveRequest->submitted_at,
                'created_at' => $leaveRequest->created_at,
                'updated_at' => $leaveRequest->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Leave request retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a leave request
     */
    public function cancelRequest(Request $request, LeaveRequest $leaveRequest): JsonResponse
    {
        try {
            if ($leaveRequest->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this leave request'
                ], 403);
            }

            if (!in_array($leaveRequest->status, ['pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel this leave request'
                ], 422);
            }

            // If approved, need to restore leave balance
            if ($leaveRequest->status === 'approved' && $leaveRequest->leaveType) {
                $leaveDays = $this->leaveService->calculateLeaveDays(
                    $leaveRequest->start_date,
                    $leaveRequest->end_date,
                    $leaveRequest->leaveType
                );

                // Restore the balance
                LeaveAccrual::createBonusAccrual(
                    $leaveRequest->user_id,
                    $leaveRequest->program_id,
                    $leaveRequest->company_id,
                    $leaveRequest->leave_type_id,
                    $leaveDays,
                    'leave_cancelled',
                    "Leave cancelled: {$leaveRequest->start_date->format('Y-m-d')} to {$leaveRequest->end_date->format('Y-m-d')}"
                );
            }

            $leaveRequest->update([
                'status' => 'cancelled',
                'approved_by' => $request->user()->id,
                'approved_at' => Carbon::now(),
                'rejection_reason' => 'Cancelled by employee',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave request cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get accrual history for user
     */
    public function getAccrualHistory(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $leaveTypeId = $request->get('leave_type_id');
            $limit = $request->get('limit', 50);

            $query = LeaveAccrual::where('user_id', $user->id);

            if ($leaveTypeId) {
                $query->where('leave_type_id', $leaveTypeId);
            }

            $accruals = $query->with('leaveType')
                ->orderBy('accrual_date', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($accrual) {
                    return [
                        'id' => $accrual->id,
                        'leave_type' => $accrual->leaveType->name,
                        'accrual_date' => $accrual->accrual_date,
                        'days_accrued' => $accrual->days_accrued,
                        'running_balance' => $accrual->running_balance,
                        'accrual_reason' => $accrual->accrual_reason,
                        'notes' => $accrual->notes,
                        'created_at' => $accrual->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $accruals,
                'message' => 'Accrual history retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve accrual history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get carry over summary for user
     */
    public function getCarryOverSummary(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $year = $request->get('year');

            $summary = LeaveCarryOver::getCarryOverSummary($user->id, $year);
            $expiringDays = LeaveCarryOver::getExpiringCarryOverDays($user->id, 30);

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'expiring_days' => $expiringDays,
                ],
                'message' => 'Carry over summary retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve carry over summary: ' . $e->getMessage()
            ], 500);
        }
    }
}
