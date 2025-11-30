<x-layouts.auth>


    <div class="w-full max-w-sm sm:max-w-md mx-auto bg-white rounded-lg shadow dark:border mt-4 p-4 dark:bg-gray-800 dark:border-gray-700">
        



        <!-- Header -->
        <x-auth-header 
            :title="__('Log in to your account')" 
            :description="__('Enter your email and password below to log in')" 
        />

        <!-- Session Status -->
        <x-auth-session-status 
            class="text-center text-green-600 mb-4" 
            :status="session('status')" 
        />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6 w-full">
            @csrf

            <!-- Email Address -->
            <flux:input 
                class="w-full text-sm sm:text-base md:text-lg bg-transparent border-none rounded focus:outline-none focus:ring-2 focus:ring-cyan-500 hover:ring-cyan-400 transition-all duration-200 px-3 py-2 sm:py-3 placeholder-gray-500 dark:placeholder-gray-400"
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative w-full">
                <flux:input
                    class="w-full text-sm sm:text-base md:text-lg bg-transparent border-none rounded focus:outline-none focus:ring-2 focus:ring-cyan-500 hover:ring-cyan-400 transition-all duration-200 px-3 py-2 sm:py-3 placeholder-gray-500 dark:placeholder-gray-400"
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link 
                        class="absolute top-2 sm:top-3 right-0 text-xs sm:text-sm md:text-base text-cyan-600 hover:text-cyan-400 transition-colors duration-200" 
                        :href="route('password.request')" 
                        wire:navigate
                    >
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox 
                class="text-gray-700 dark:text-gray-300 text-sm sm:text-base" 
                name="remember" 
                :label="__('Remember me')" 
                :checked="old('remember')" 
            />

            <!-- Submit Button -->
            <flux:button 
                variant="primary" 
                type="submit" 
                class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-semibold text-sm sm:text-base md:text-lg py-3 sm:py-4 rounded-lg shadow-md transition-colors duration-300"
                data-test="login-button"
            >
                {{ __('Log in') }}
            </flux:button>
        </form>

        <!-- Registration Link -->
        @if (Route::has('register'))
            <div class="text-center text-xs sm:text-sm md:text-base text-gray-600 dark:text-gray-400 mt-4">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link 
                    :href="route('register')" 
                    class="text-cyan-500 hover:text-cyan-400 font-medium transition-colors duration-200" 
                    wire:navigate
                >
                    {{ __('Sign up') }}
                </flux:link>
            </div>
        @endif
    </div>


    
</x-layouts.auth>
