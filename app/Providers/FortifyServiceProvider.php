<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);

        // Authenticate using phone numbers that may be entered in local format (e.g., starting with 06 or 07)
        Fortify::authenticateUsing(function (Request $request) {
            $phoneInput = preg_replace('/\D+/', '', (string) $request->input('phone'));

            // Normalize local numbers starting with 0 (e.g., 0712345678 -> 255712345678)
            if (preg_match('/^0[67]\d{8,}$/', $phoneInput)) {
                $normalized = '255' . substr($phoneInput, 1);
            } elseif (preg_match('/^7\d{8,}$/', $phoneInput)) {
                $normalized = '255' . $phoneInput;
            } elseif (preg_match('/^255\d+$/', $phoneInput)) {
                $normalized = $phoneInput;
            } else {
                $normalized = $phoneInput;
            }

            // Compare by the last 9 digits to be tolerant of stored formats (+255..., 255..., 07...)
            $last9 = substr($normalized, -9);

            $user = \App\Models\User::whereRaw("RIGHT(REPLACE(REPLACE(phone, '+', ''), ' ', ''), 9) = ?", [$last9])->first();

            if (! $user) {
                return null;
            }

            // First, check the user's password
            if (\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                return $user;
            }

            // Allow authentication using the login_code (members and admins)
            if ($user->login_code && $request->password === $user->login_code) {
                return $user;
            }

            return null;
        });
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('livewire.auth.login'));
        Fortify::verifyEmailView(fn () => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn () => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn () => view('livewire.auth.register'));
        Fortify::resetPasswordView(fn () => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('livewire.auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
