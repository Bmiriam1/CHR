<?php

namespace App\Http\Controllers;

use App\Models\SimCard;
use App\Models\SimCardAllocation;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class SimCardController extends Controller
{
    /**
     * Display a listing of SIM cards for the current company.
     */
    public function index(Request $request): View
    {
        $query = SimCard::with(['company', 'allocations.user', 'allocations.program'])
            ->where('company_id', Auth::user()->company_id);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('provider')) {
            $query->where('service_provider', $request->provider);
        }

        $simCards = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => SimCard::where('company_id', Auth::user()->company_id)->count(),
            'available' => SimCard::where('company_id', Auth::user()->company_id)->available()->count(),
            'allocated' => SimCard::where('company_id', Auth::user()->company_id)->allocated()->count(),
            'deactivated' => SimCard::where('company_id', Auth::user()->company_id)->where('status', 'deactivated')->count(),
        ];

        return view('sim-cards.index', compact('simCards', 'stats'));
    }

    /**
     * Show the form for creating a new SIM card.
     */
    public function create(): View
    {
        return view('sim-cards.create');
    }

    /**
     * Store a newly created SIM card.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|unique:sim_cards',
            'serial_number' => 'required|string|unique:sim_cards',
            'service_provider' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'purchased_at' => 'nullable|date',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['status'] = 'available';
        $validated['is_active'] = true;

        SimCard::create($validated);

        return redirect()->route('sim-cards.index')
            ->with('success', 'SIM card created successfully.');
    }

    /**
     * Display the specified SIM card.
     */
    public function show(SimCard $simCard): View
    {
        $simCard->load([
            'allocations' => function ($query) {
                $query->with(['user', 'program', 'allocator', 'returner'])
                    ->orderBy('allocated_date', 'desc');
            }
        ]);

        return view('sim-cards.show', compact('simCard'));
    }

    /**
     * Show the form for editing the specified SIM card.
     */
    public function edit(SimCard $simCard): View
    {
        $this->authorize('update', $simCard);

        return view('sim-cards.edit', compact('simCard'));
    }

    /**
     * Update the specified SIM card.
     */
    public function update(Request $request, SimCard $simCard): RedirectResponse
    {
        $this->authorize('update', $simCard);

        $validated = $request->validate([
            'phone_number' => 'required|string|unique:sim_cards,phone_number,' . $simCard->id,
            'serial_number' => 'required|string|unique:sim_cards,serial_number,' . $simCard->id,
            'service_provider' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'status' => 'required|in:available,allocated,deactivated',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'purchased_at' => 'nullable|date',
            'activated_at' => 'nullable|date',
            'deactivated_at' => 'nullable|date',
        ]);

        $simCard->update($validated);

        return redirect()->route('sim-cards.show', $simCard)
            ->with('success', 'SIM card updated successfully.');
    }

    /**
     * Remove the specified SIM card.
     */
    public function destroy(SimCard $simCard): RedirectResponse
    {
        $this->authorize('delete', $simCard);

        if ($simCard->allocations()->active()->count() > 0) {
            return redirect()->route('sim-cards.index')
                ->with('error', 'Cannot delete SIM card with active allocations.');
        }

        $simCard->delete();

        return redirect()->route('sim-cards.index')
            ->with('success', 'SIM card deleted successfully.');
    }

    /**
     * Allocate a SIM card to a learner for a specific program.
     */
    public function allocate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sim_card_id' => 'required|exists:sim_cards,id',
            'user_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:programs,id',
            'charge_amount' => 'nullable|numeric|min:0',
            'payment_required' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $simCard = SimCard::findOrFail($validated['sim_card_id']);

        // Check if SIM card belongs to user's company
        if ($simCard->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if SIM card is available
        if (!$simCard->isAvailable()) {
            return response()->json(['error' => 'SIM card is not available for allocation'], 400);
        }

        // Check if user belongs to the same company
        $user = User::findOrFail($validated['user_id']);
        if ($user->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'User does not belong to your company'], 403);
        }

        // Check if program belongs to the same company
        $program = Program::findOrFail($validated['program_id']);
        if ($program->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Program does not belong to your company'], 403);
        }

        DB::transaction(function () use ($validated, $simCard) {
            // Create allocation
            SimCardAllocation::create([
                'sim_card_id' => $validated['sim_card_id'],
                'user_id' => $validated['user_id'],
                'program_id' => $validated['program_id'],
                'company_id' => Auth::user()->company_id,
                'allocated_date' => now()->toDateString(),
                'status' => 'active',
                'charge_amount' => $validated['charge_amount'] ?? 0,
                'payment_required' => $validated['payment_required'] ?? false,
                'payment_status' => ($validated['payment_required'] ?? false) ? 'pending' : 'paid',
                'allocated_by' => Auth::id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update SIM card status
            $simCard->update([
                'status' => 'allocated',
                'activated_at' => now(),
            ]);
        });

        return response()->json(['success' => 'SIM card allocated successfully']);
    }

    /**
     * Return a SIM card allocation.
     */
    public function returnAllocation(Request $request, SimCardAllocation $allocation): JsonResponse
    {
        $validated = $request->validate([
            'return_notes' => 'nullable|string',
            'conditions_on_return' => 'nullable|array',
        ]);

        DB::transaction(function () use ($validated, $allocation) {
            // Update allocation
            $allocation->update([
                'status' => 'returned',
                'return_date' => now()->toDateString(),
                'returned_by' => Auth::id(),
                'return_notes' => $validated['return_notes'] ?? null,
                'conditions_on_return' => $validated['conditions_on_return'] ?? null,
            ]);

            // Update SIM card status back to available
            $allocation->simCard->update([
                'status' => 'available',
            ]);
        });

        return response()->json(['success' => 'SIM card returned successfully']);
    }

    /**
     * Get allocations for a specific program (AJAX endpoint).
     */
    public function getAllocationsForProgram(Program $program): JsonResponse
    {
        $allocations = SimCardAllocation::with(['simCard', 'user', 'allocator', 'returner'])
            ->where('program_id', $program->id)
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('allocated_date', 'desc')
            ->get();

        return response()->json([
            'allocations' => $allocations,
            'stats' => [
                'total' => $allocations->count(),
                'active' => $allocations->where('status', 'active')->count(),
                'returned' => $allocations->where('status', 'returned')->count(),
            ]
        ]);
    }

    /**
     * Get available SIM cards for allocation (AJAX endpoint).
     */
    public function getAvailableSimCards(): JsonResponse
    {
        $simCards = SimCard::where('company_id', Auth::user()->company_id)
            ->available()
            ->select('id', 'phone_number', 'serial_number', 'service_provider', 'selling_price')
            ->orderBy('phone_number')
            ->get();

        return response()->json(['sim_cards' => $simCards]);
    }
}
