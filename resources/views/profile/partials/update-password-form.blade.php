<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Current Password') }}</label>
            <input id="current_password" name="current_password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500" autocomplete="current-password" />
            @if($errors->updatePassword->has('current_password'))
                <p class="mt-2 text-red-600 text-sm">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('New Password') }}</label>
            <input id="password" name="password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500" autocomplete="new-password" />
            @if($errors->updatePassword->has('password'))
                <p class="mt-2 text-red-600 text-sm">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500" autocomplete="new-password" />
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="mt-2 text-red-600 text-sm">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <button
    type="submit"
    style="background-color: blue; color: white; padding: 4px 16px; border-radius: 4px; display: inline-flex;"
>
    {{ __('Save') }}
</button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-gray-600 dark:text-gray-400"> {{ __('Saved.') }} </p>
            @endif
        </div>
    </form>
</section>
