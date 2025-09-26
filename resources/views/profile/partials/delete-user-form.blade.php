<section class="space-y-6 relative">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <!-- Delete Account Button -->
<button
    type="button"
    style="background-color: red; color: white; padding: 4px 16px; border-radius: 4px; display: inline-flex;"
    onclick="document.getElementById('delete-modal').classList.remove('hidden')"
>
    {{ __('Delete Account') }}
</button>

    <!-- Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <form method="post" action="{{ route('profile.destroy') }}" class="bg-white dark:bg-gray-800 p-6 rounded shadow w-96">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
            </p>

            <div class="mt-4">
                <label for="password" class="sr-only">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('Password') }}"
                />
                @if($errors->userDeletion->has('password'))
                    <p class="mt-2 text-red-600 text-sm">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button
                    type="button"
                    onclick="document.getElementById('delete-modal').classList.add('hidden')"
                    class="inline-flex items-center px-4 py-2 rounded-md bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-400 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500"
                >
                    {{ __('Cancel') }}
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500"
                >
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </div>
</section>
