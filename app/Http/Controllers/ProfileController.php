<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Get user documents - only include documents that actually exist
        $documents = collect([
            'qualification' => $user->qualification_document,
            'cv' => $user->cv_document,
            'banking_statement' => $user->banking_statement,
            'id_document' => $user->id_document,
            'proof_of_residence' => $user->proof_of_residence,
        ])->filter();

        return view('profile.edit', [
            'user' => $user,
            'documents' => $documents,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
 * Upload learner documents - FIXED VERSION
 */
public function uploadDocument(Request $request): RedirectResponse
{
    $validator = Validator::make($request->all(), [
        'document_type' => 'required|in:qualification,cv,banking_statement,id_document,proof_of_residence',
        'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
    ]);

    if ($validator->fails()) {
        return Redirect::route('profile.edit')->withErrors($validator, 'documentUpload');
    }

    $user = $request->user();
    $documentType = $request->input('document_type');
    
    // Map document types to their actual database field names
    $documentFieldMapping = [
        'qualification' => 'qualification_document',
        'cv' => 'cv_document',
        'banking_statement' => 'banking_statement',      // NOT banking_statement_document
        'id_document' => 'id_document',                  // NOT id_document_document
        'proof_of_residence' => 'proof_of_residence'    // NOT proof_of_residence_document
    ];
    
    $documentField = $documentFieldMapping[$documentType];
    
    Log::info('Document Upload Started', [
        'user_id' => $user->id,
        'document_type' => $documentType,
        'document_field' => $documentField,
        'original_filename' => $request->file('document')->getClientOriginalName()
    ]);

    try {
        // Delete old document if exists
        $oldDocument = $user->{$documentField};
        if ($oldDocument && Storage::disk('private')->exists($oldDocument)) {
            Storage::disk('private')->delete($oldDocument);
            Log::info('Old document deleted', ['path' => $oldDocument]);
        }

        // Store new document
        $file = $request->file('document');
        $filename = $user->id . '_' . $documentType . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('learner_documents', $filename, 'private');

        Log::info('New document stored', [
            'path' => $path,
            'filename' => $filename,
            'file_exists' => Storage::disk('private')->exists($path)
        ]);

        // Update user record - use the mapped field name
        $updateData = [
            $documentField => $path,  // Use correct field name from mapping
            $documentType . '_verified' => false,
        ];

        $user->update($updateData);

        Log::info('Database updated', [
            'update_data' => $updateData,
            'user_document_value' => $user->fresh()->{$documentField}
        ]);

        return Redirect::route('profile.edit')
            ->with('status', 'document-uploaded')
            ->with('document_type', $documentType);

    } catch (\Exception $e) {
        Log::error('Document upload failed', [
            'error' => $e->getMessage(),
            'user_id' => $user->id,
            'document_type' => $documentType
        ]);

        return Redirect::route('profile.edit')
            ->withErrors(['document' => 'Failed to upload document. Please try again.'], 'documentUpload');
    }
}

/**
 * Delete a specific document - FIXED VERSION
 */
public function deleteDocument(Request $request): RedirectResponse
{
    $validator = Validator::make($request->all(), [
        'document_type' => 'required|in:qualification,cv,banking_statement,id_document,proof_of_residence',
    ]);

    if ($validator->fails()) {
        return Redirect::route('profile.edit')->withErrors($validator);
    }

    $user = $request->user();
    $documentType = $request->input('document_type');
    
    // Use the same field mapping
    $documentFieldMapping = [
        'qualification' => 'qualification_document',
        'cv' => 'cv_document',
        'banking_statement' => 'banking_statement',
        'id_document' => 'id_document',
        'proof_of_residence' => 'proof_of_residence'
    ];
    
    $documentField = $documentFieldMapping[$documentType];
    
    try {
        // Delete file from storage
        $document = $user->{$documentField};
        if ($document && Storage::disk('private')->exists($document)) {
            Storage::disk('private')->delete($document);
            Log::info('Document deleted from storage', ['path' => $document]);
        }

        // Update user record
        $user->update([
            $documentField => null,
            $documentType . '_verified' => false,
        ]);

        Log::info('Document deleted from database', [
            'user_id' => $user->id,
            'document_type' => $documentType,
            'document_field' => $documentField
        ]);

        return Redirect::route('profile.edit')
            ->with('status', 'document-deleted')
            ->with('document_type', $documentType);

    } catch (\Exception $e) {
        Log::error('Document deletion failed', [
            'error' => $e->getMessage(),
            'user_id' => $user->id,
            'document_type' => $documentType
        ]);

        return Redirect::route('profile.edit')
            ->withErrors(['document' => 'Failed to delete document. Please try again.']);
    }
}

    /**
     * Initiate banking verification through API
     */
    public function initiateBankVerification(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'bank_account_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:13',
        ]);

        if ($validator->fails()) {
            return Redirect::route('profile.edit')->withErrors($validator, 'bankingVerification');
        }

        $user = $request->user();
        
        try {
            $bankingApiResponse = $this->callBankingVerificationAPI([
                'account_number' => $request->input('bank_account_number'),
                'id_number' => $request->input('id_number'),
                'user_id' => $user->id,
            ]);

            // Update user with verification status
            $user->update([
                'bank_account_number' => $request->input('bank_account_number'),
                'banking_verification_status' => $bankingApiResponse['status'],
                'banking_verification_reference' => $bankingApiResponse['reference'] ?? null,
                'banking_verified_at' => $bankingApiResponse['status'] === 'verified' ? now() : null,
                'account_holder_name' => $bankingApiResponse['account_holder_name'] ?? null,
                'bank_name' => $bankingApiResponse['bank_name'] ?? null,
            ]);

            $message = match($bankingApiResponse['status']) {
                'verified' => 'Bank account verified successfully!',
                'pending' => 'Bank verification initiated. You will be notified once complete.',
                'failed' => 'Bank verification failed. Please check your details and try again.',
                default => 'Bank verification request submitted.'
            };

            return Redirect::route('profile.edit')->with('status', 'banking-verification-initiated')->with('message', $message);

        } catch (\Exception $e) {
            Log::error('Banking API Error: ' . $e->getMessage());
            return Redirect::route('profile.edit')->withErrors(['banking_api' => 'Banking verification service is temporarily unavailable. Please try again later.'], 'bankingVerification');
        }
    }

    /**
     * Handle banking API webhook callback
     */
    public function bankingWebhookCallback(Request $request)
    {
        try {
            // Validate webhook signature
            $signature = $request->header('X-Banking-Signature');
            if (!$this->validateWebhookSignature($signature, $request->getContent())) {
                Log::warning('Invalid webhook signature received');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            $data = $request->json()->all();
            
            // Find user by reference
            $user = User::where('banking_verification_reference', $data['reference'])->first();
            
            if (!$user) {
                Log::warning('Webhook received for unknown reference', ['reference' => $data['reference']]);
                return response()->json(['error' => 'User not found'], 404);
            }

            // Update verification status
            $user->update([
                'banking_verification_status' => $data['status'],
                'banking_verified_at' => $data['status'] === 'verified' ? now() : null,
                'bank_name' => $data['bank_name'] ?? $user->bank_name,
                'account_holder_name' => $data['account_holder_name'] ?? $user->account_holder_name,
            ]);

            Log::info('Banking verification webhook processed', [
                'user_id' => $user->id,
                'status' => $data['status'],
                'reference' => $data['reference']
            ]);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Call external banking verification API
     */
    private function callBankingVerificationAPI(array $data): array
    {
        $apiUrl = config('services.banking_api.url');
        $apiKey = config('services.banking_api.key');

        // If no API configured, return pending status for development
        if (empty($apiUrl) || empty($apiKey)) {
            Log::info('Banking API not configured, returning pending status');
            return [
                'status' => 'pending',
                'reference' => 'DEV_REF_' . time(),
                'message' => 'Verification initiated (development mode)'
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(30)->post($apiUrl . '/verify-account', [
            'account_number' => $data['account_number'],
            'id_number' => $data['id_number'],
            'callback_url' => route('banking.webhook'),
            'user_reference' => $data['user_id'],
        ]);

        if ($response->failed()) {
            Log::error('Banking API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            throw new \Exception('Banking API request failed: ' . $response->body());
        }

        $responseData = $response->json();
        
        Log::info('Banking API response received', $responseData);

        return $responseData;
    }

    /**
     * Validate webhook signature
     */
    private function validateWebhookSignature(?string $signature, string $payload): bool
    {
        if (!$signature) {
            return false;
        }

        $webhookSecret = config('services.banking_api.webhook_secret');
        if (!$webhookSecret) {
            Log::warning('Banking webhook secret not configured');
            return true; // Allow in development
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $webhookSecret);
        return hash_equals($signature, $expectedSignature);
    }

    /**
     * Retry failed bank verification
     */
    public function retryBankVerification(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->banking_verification_status !== 'failed') {
            return Redirect::route('profile.edit')->withErrors(['banking_retry' => 'No failed verification to retry.']);
        }

        return $this->initiateBankVerification($request);
    }

    /**
     * Update additional learner information
     */
    public function updateLearnerInfo(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date|before:today',
            'physical_address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'education_level' => 'nullable|in:matric,diploma,degree,postgraduate,other',
        ]);

        if ($validator->fails()) {
            return Redirect::route('profile.edit')->withErrors($validator, 'learnerInfo');
        }

        $user = $request->user();
        $user->update($validator->validated());

        return Redirect::route('profile.edit')->with('status', 'learner-info-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            // Delete all user documents before deleting account
            $documentTypes = ['qualification', 'cv', 'banking_statement', 'id_document', 'proof_of_residence'];
            foreach ($documentTypes as $type) {
                $document = $user->{$type . '_document'};
                if ($document && Storage::disk('private')->exists($document)) {
                    Storage::disk('private')->delete($document);
                }
            }

            Auth::logout();
            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/')->with('status', 'account-deleted');

        } catch (\Exception $e) {
            Log::error('Account deletion failed: ' . $e->getMessage());
            return Redirect::route('profile.edit')->withErrors(['account_deletion' => 'Failed to delete account. Please try again.']);
        }
    }
}