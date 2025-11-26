<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Livewire\Groups\CreateGroup;
use App\Livewire\RegisterThroughInvite;
use App\Livewire\Groups\ViewMembers;
use App\Livewire\Groups\GroupsIndex;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');
Route::get('/invite/{code}', RegisterThroughInvite::class)->name('invite.register');
    
Route::get('/groups/{group}/members', ViewMembers::class)->name('groups.members');

Route::get('/groups', GroupsIndex::class)->name('groups.index');

Route::middleware(['auth'])->group(function() {
    Route::get('/groups/create', CreateGroup::class)->name('groups.create');
});

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
