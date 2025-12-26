<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->withoutTwoFactor()->create(['phone' => '255712345678']);

    $response = $this->post(route('login.store'), [
        'phone' => '0712345678',
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('admins can authenticate using login_code', function () {
    $admin = User::factory()->withoutTwoFactor()->create([
        'phone' => '255712345679',
        'role' => 'admin',
        'login_code' => 'ADMINCODE123',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->post(route('login.store'), [
        'phone' => '0712345679',
        'password' => 'ADMINCODE123',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});


test('members can authenticate using login_code', function () {
    $member = User::factory()->withoutTwoFactor()->create([
        'phone' => '255712345680',
        'role' => 'member',
        'login_code' => 'MEMBER1234',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->post(route('login.store'), [
        'phone' => '0712345680',
        'password' => 'MEMBER1234',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'phone' => '0712345678',
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('phone');

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest();
});