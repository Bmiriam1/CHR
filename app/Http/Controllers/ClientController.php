<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Program;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of clients.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Check if user has a company
        if (!$user->company_id) {
            $primaryClients = collect();
            return view('clients.index', compact('primaryClients'))->with('error', 'You must be assigned to a company to view clients. Please contact your administrator.');
        }

        // Get primary clients (parent clients) for the company
        $primaryClients = Client::where('company_id', $user->company_id)
            ->parents()
            ->active()
            ->with(['subClients' => function ($query) {
                $query->active()->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('clients.index', compact('primaryClients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(Request $request): View
    {
        $user = auth()->user();

        // Check if user has a company
        if (!$user->company_id) {
            return redirect()->route('clients.index')
                ->with('error', 'You must be assigned to a company to create clients. Please contact your administrator.');
        }

        $parentClientId = $request->get('parent_client_id');
        $parentClient = $parentClientId ? Client::find($parentClientId) : null;

        // Get all parent clients for the dropdown
        $parentClients = Client::where('company_id', $user->company_id)
            ->parents()
            ->active()
            ->orderBy('name')
            ->get();

        return view('clients.create', compact('parentClient', 'parentClients'));
    }

    /**
     * Store a newly created client.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Check if user has a company
        if (!$user->company_id) {
            return redirect()->back()
                ->withErrors(['company' => 'You must be assigned to a company to create clients. Please contact your administrator.'])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:clients,code',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'parent_client_id' => 'nullable|exists:clients,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $client = Client::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'South Africa',
            'parent_client_id' => $request->parent_client_id,
            'company_id' => $user->company_id,
            'created_by' => $user->id,
        ]);

        $message = $client->isSubClient()
            ? 'Sub-client created successfully!'
            : 'Client created successfully!';

        return redirect()->route('clients.show', $client)
            ->with('success', $message);
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client): View
    {
        $this->authorize('view', $client);

        // Load relationships
        $client->load([
            'subClients' => function ($query) {
                $query->active()->orderBy('name');
            },
            'programs' => function ($query) {
                $query->orderBy('title');
            },
            'parentClient',
            'creator',
            'updater'
        ]);

        // Get all programs for this client and its sub-clients
        $allPrograms = $client->programs;
        foreach ($client->subClients as $subClient) {
            $allPrograms = $allPrograms->merge($subClient->programs);
        }

        return view('clients.show', compact('client', 'allPrograms'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client): View
    {
        $this->authorize('update', $client);

        // Get all parent clients for the dropdown (excluding current client and its sub-clients)
        $parentClients = Client::where('company_id', auth()->user()->company_id)
            ->parents()
            ->active()
            ->where('id', '!=', $client->id)
            ->whereNotIn('id', $client->getAllSubClients()->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('clients.edit', compact('client', 'parentClients'));
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $this->authorize('update', $client);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:clients,code,' . $client->id,
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'parent_client_id' => 'nullable|exists:clients,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $client->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'country' => $request->country ?? 'South Africa',
            'parent_client_id' => $request->parent_client_id,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client updated successfully!');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        // Check if client has programs
        if ($client->programs()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete client with existing programs. Please move or delete the programs first.');
        }

        // Check if client has sub-clients
        if ($client->subClients()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete client with sub-clients. Please move or delete the sub-clients first.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully!');
    }

    /**
     * Show programs for a specific client.
     */
    public function programs(Client $client): View
    {
        $this->authorize('view', $client);

        $programs = $client->programs()
            ->with(['programType', 'schedules'])
            ->orderBy('title')
            ->paginate(20);

        return view('clients.programs', compact('client', 'programs'));
    }
}
