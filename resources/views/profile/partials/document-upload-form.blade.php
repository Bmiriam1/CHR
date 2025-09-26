<section class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <header class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('Document Management') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Upload and manage your qualification certificates, CV, and verification documents') }}
                </p>
            </div>
        </div>
    </header>

    <!-- Success/Error Messages -->
    @if (session('status') === 'document-uploaded')
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ __('Document Uploaded Successfully!') }}
                    </h3>
                    <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                        {{ __('Your :document_type has been saved and is pending verification.', ['document_type' => session('document_type', 'document')]) }}
                    </p>
                </div>
            </div>
        </div>
        
    
    @endif

    @if (session('status') === 'document-deleted')
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ __('Document Deleted Successfully!') }}
                    </h3>
                    <p class="text-xs text-red-700 dark:text-red-300 mt-1">
                        {{ __('The :document_type has been permanently removed from our system.', ['document_type' => session('document_type', 'document')]) }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Document Upload Form -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            {{ __('Upload New Document') }}
        </h3>
        
        <form method="post" action="{{ route('profile.upload-document') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="document_type" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('Document Type') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="document_type" name="document_type" required
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 transition duration-200">
                        <option value="">{{ __('Select Document Type') }}</option>
                        <option value="qualification">{{ __('Qualification/Certificate') }}</option>
                        <option value="cv">{{ __('Curriculum Vitae (CV)') }}</option>
                        <option value="banking_statement">{{ __('Banking Statement') }}</option>
                        <option value="id_document">{{ __('ID Document') }}</option>
                        <option value="proof_of_residence">{{ __('Proof of Residence') }}</option>
                    </select>
                    @if($errors->documentUpload->has('document_type'))
                        <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $errors->documentUpload->first('document_type') }}</span>
                        </p>
                    @endif
                </div>
                
                <div class="space-y-2">
                    <label for="document" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('Upload Document') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input id="document" name="document" type="file" required
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                            class="block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900 dark:file:text-green-300">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('PDF, JPG, PNG, DOC, DOCX (Max: 5MB)') }}
                        </p>
                    </div>
                    @if($errors->documentUpload->has('document'))
                        <p class="text-sm text-red-600 dark:text-red-400 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $errors->documentUpload->first('document') }}</span>
                        </p>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center justify-end pt-4">
                <button type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 !text-white font-medium rounded-lg shadow-sm border border-green-600 transition duration-200 transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    {{ __('Upload Document') }}
                </button>
            </div>
        </form>
        <script>
        // Auto-hide flash message after 5 seconds
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500); // Wait for fade-out transition
            }
        }, 5000);
    </script>
    </div>
    

    <!-- Document Status Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Document Status') }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Document Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Upload Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @php
                        $documentTypes = [
                            'qualification' => 'Qualification/Certificate',
                            'cv' => 'Curriculum Vitae (CV)',
                            'banking_statement' => 'Banking Statement',
                            'id_document' => 'ID Document',
                            'proof_of_residence' => 'Proof of Residence'
                        ];
                        
                        $documentFieldMapping = [
                            'qualification' => 'qualification_document',
                            'cv' => 'cv_document',
                            'banking_statement' => 'banking_statement',      
                            'id_document' => 'id_document',                  
                            'proof_of_residence' => 'proof_of_residence'  
                        ];
                    @endphp
                    
                    @foreach($documentTypes as $type => $label)
                        @php
                            $documentField = $documentFieldMapping[$type];
                            $verifiedField = $type . '_verified';
                        @endphp
                        
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $label }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->{$documentField})
                                    @if($user->{$verifiedField})
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Pending Review
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Not Uploaded
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($user->{$documentField})
                                    {{ basename($user->{$documentField}) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @if($user->{$documentField})
                                    {{ $user->updated_at->format('Y-m-d H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($user->{$documentField})
                                    <form method="post" action="{{ route('profile.delete-document') }}" class="inline">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="document_type" value="{{ $type }}">
                                        <button type="submit" 
                                            onclick="return confirm('Are you sure you want to delete this document?')"
                                            class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-800/40 text-red-700 dark:text-red-400 text-xs font-medium rounded border border-red-200 dark:border-red-800 transition duration-200">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-xs">No action available</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
// Add visual feedback for file selection
document.addEventListener('DOMContentLoaded', function() {
    const documentInput = document.getElementById('document');
    if (documentInput) {
        documentInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Remove any existing file info
                const existingInfo = e.target.parentNode.querySelector('.file-info');
                if (existingInfo) {
                    existingInfo.remove();
                }
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'file-info mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800';
                fileInfo.innerHTML = `
                    <div class="flex items-center text-sm text-blue-700 dark:text-blue-300">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                    </div>
                `;
                
                e.target.parentNode.appendChild(fileInfo);
            }
        });
    }
});
</script>