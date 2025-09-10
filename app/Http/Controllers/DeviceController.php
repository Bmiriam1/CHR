<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DeviceController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = Device::with(['user', 'approver', 'blocker'])
            ->whereHas('user', function($q) {
                $q->where('company_id', Auth::user()->company_id);
            });

        if ($request->has('status')) {
            match($request->status) {
                'active' => $query->active(),
                'blocked' => $query->blocked(),
                'needs_approval' => $query->needingApproval(),
                'pending_sync' => $query->pendingSync(),
                default => null,
            };
        }

        if ($request->has('platform')) {
            $query->byPlatform($request->platform);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                  ->orWhere('device_id', 'like', "%{$search}%")
                  ->orWhere('device_model', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $devices = $query->latest('last_seen_at')->paginate(15);

        $companyId = Auth::user()->company_id;
        $stats = [
            'total' => Device::whereHas('user', fn($q) => $q->where('company_id', $companyId))->count(),
            'active' => Device::whereHas('user', fn($q) => $q->where('company_id', $companyId))->active()->count(),
            'blocked' => Device::whereHas('user', fn($q) => $q->where('company_id', $companyId))->blocked()->count(),
            'needs_approval' => Device::whereHas('user', fn($q) => $q->where('company_id', $companyId))->needingApproval()->count(),
        ];

        return view('devices.index', compact('devices', 'stats'));
    }

    public function show(Device $device)
    {
        $this->authorize('view', $device);
        
        $device->load(['user', 'company', 'approver', 'blocker', 'attendanceRecords']);

        $recentAttendance = $device->attendanceRecords()
            ->with(['schedule', 'program'])
            ->latest()
            ->limit(10)
            ->get();

        return view('devices.show', compact('device', 'recentAttendance'));
    }

    public function edit(Device $device)
    {
        $this->authorize('update', $device);
        
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $this->authorize('update', $device);

        $validated = $request->validate([
            'device_name' => 'sometimes|string|max:255',
            'is_trusted' => 'sometimes|boolean',
            'require_location_for_checkin' => 'sometimes|boolean',
            'auto_checkout_enabled' => 'sometimes|boolean',
            'auto_checkout_hours' => 'sometimes|integer|min:1|max:24',
            'push_notifications_enabled' => 'sometimes|boolean',
            'biometric_enabled' => 'sometimes|boolean',
            'registration_notes' => 'sometimes|string|max:1000',
        ]);

        $device->update($validated);

        return redirect()->route('devices.show', $device)
            ->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $this->authorize('delete', $device);
        
        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Device deleted successfully.');
    }

    public function approve(Device $device)
    {
        $this->authorize('manage', $device);

        if ($device->is_active) {
            throw ValidationException::withMessages([
                'device' => 'Device is already approved.'
            ]);
        }

        $device->approve(Auth::user());

        return back()->with('success', 'Device approved successfully.');
    }

    public function block(Request $request, Device $device)
    {
        $this->authorize('manage', $device);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($device->is_blocked) {
            throw ValidationException::withMessages([
                'device' => 'Device is already blocked.'
            ]);
        }

        $device->block(Auth::user(), $validated['reason']);

        return back()->with('success', 'Device blocked successfully.');
    }

    public function unblock(Device $device)
    {
        $this->authorize('manage', $device);

        if (!$device->is_blocked) {
            throw ValidationException::withMessages([
                'device' => 'Device is not blocked.'
            ]);
        }

        $device->unblock();

        return back()->with('success', 'Device unblocked successfully.');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|string|in:mobile,tablet,desktop',
            'platform' => 'required|string|in:ios,android,web,windows,macos,linux',
            'platform_version' => 'nullable|string|max:50',
            'app_version' => 'nullable|string|max:50',
            'device_model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'browser' => 'nullable|string|max:100',
            'browser_version' => 'nullable|string|max:50',
            'expo_push_token' => 'nullable|string|max:255',
            'fcm_token' => 'nullable|string|max:255',
            'apns_token' => 'nullable|string|max:255',
            'supports_qr_scanning' => 'boolean',
            'supports_gps' => 'boolean',
            'supports_camera' => 'boolean',
            'supports_offline_mode' => 'boolean',
            'biometric_enabled' => 'boolean',
            'pin_enabled' => 'boolean',
            'screen_lock_enabled' => 'boolean',
            'device_encrypted' => 'boolean',
            'timezone' => 'nullable|string|max:50',
            'locale' => 'nullable|string|max:10',
            'device_fingerprint' => 'nullable|array',
            'app_permissions' => 'nullable|array',
        ]);

        $user = Auth::user();
        
        $existingDevice = Device::where('user_id', $user->id)
            ->where('device_fingerprint', $validated['device_fingerprint'] ?? [])
            ->first();

        if ($existingDevice) {
            $existingDevice->updateActivity($request->ip(), [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'name' => $request->location_name,
            ]);

            return response()->json([
                'message' => 'Device updated successfully.',
                'device' => $existingDevice,
            ]);
        }

        $validated['user_id'] = $user->id;
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();
        $validated['is_active'] = false; 
        $validated['is_trusted'] = false;
        $validated['push_notifications_enabled'] ??= false;

        if (isset($validated['expo_push_token']) || isset($validated['fcm_token']) || isset($validated['apns_token'])) {
            $validated['push_token_valid'] = true;
        }

        $device = Device::create($validated);

        return response()->json([
            'message' => 'Device registered successfully. Waiting for approval.',
            'device' => $device,
            'requires_approval' => true,
        ], 201);
    }
}
