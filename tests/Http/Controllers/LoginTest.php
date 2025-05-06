<?php

declare(strict_types=1);

namespace Igniter\User\Tests\Http\Controllers;

use Igniter\Flame\Exception\FlashException;
use Igniter\User\Auth\UserProvider;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Http\Controllers\Login;
use Igniter\User\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;

beforeEach(function(): void {
    $this->route = new Route('GET', 'login', ['as' => 'igniter.admin.login']);
    $this->route->parameters = ['slug' => ''];
});

it('loads create super admin page if no user exists', function(): void {
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();
    request()->setRouteResolver(fn() => $this->route);

    $response = (new Login)->index();

    expect($response)->toBeString()
        ->and($response)->toContain(lang('igniter.user::default.login.text_create_super_admin'));
});

it('creates super admin account successfully', function(): void {
    request()->request->add([
        'name' => 'Test Admin',
        'email' => 'test@example.com',
        'password' => 'Pa$$w0rd!',
        'password_confirm' => 'Pa$$w0rd!',
    ]);
    AdminAuth::shouldReceive('getProvider')->once()->andReturn($userProvider = mock(UserProvider::class));
    $userProvider->shouldReceive('register')->once();

    $response = (new Login)->onCreateAccount();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/login');
});

it('throws exception if user exists when creating super admin account', function(): void {
    User::factory()->create();

    request()->request->add([
        'name' => 'Test Admin',
        'email' => 'test@example.com',
        'password' => 'Pa$$w0rd!',
        'password_confirm' => 'Pa$$w0rd!',
    ]);

    expect(fn(): RedirectResponse => (new Login)->onCreateAccount())
        ->toThrow(FlashException::class, lang('igniter.user::default.login.alert_super_admin_already_exists'));
});

it('loads login page if not logged in', function(): void {
    $this->route->action = [];
    request()->setRouteResolver(fn() => $this->route);

    $response = (new Login)->index();

    expect($response)->toBeInstanceOf(RedirectResponse::class);
});

it('redirects to dashboard if already logged in', function(): void {
    AdminAuth::shouldReceive('isLogged')->andReturnTrue();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();
    AdminAuth::shouldReceive('check')->andReturnTrue();
    request()->setRouteResolver(fn() => $this->route);

    $response = (new Login)->index();

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and((new Login)->checkUser())->toBeTrue();
});

it('renders login view if not logged in and on login route', function(): void {
    request()->setRouteResolver(fn() => $this->route);
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();

    $response = (new Login)->index();

    expect($response)->toBeString();
});

it('renders reset password view', function(): void {
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();

    $response = (new Login)->reset();

    expect($response)->toBeString();
});

it('redirects reset password to dashboard if already logged in', function(): void {
    AdminAuth::shouldReceive('isLogged')->andReturnTrue();

    $response = (new Login)->reset();

    expect($response)->toBeInstanceOf(RedirectResponse::class);
});

it('resets password successfully', function(): void {
    $user = User::factory()->create();
    request()->request->set('email', $user->email);
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();

    $response = (new Login)->onRequestResetPassword();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/login')
        ->and(flash()->messages()->first())->level->toBe('success')
        ->message->toBe(lang('igniter.user::default.login.alert_email_sent'));
});

it('fails to reset password with invalid code', function(): void {
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();
    request()->merge(['code' => 'invalid_code']);

    $response = (new Login)->reset();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/login')
        ->and(flash()->messages()->first())->level->toBe('danger')
        ->message->toBe(lang('igniter.user::default.login.alert_failed_reset'));
});

it('logs in successfully with valid credentials', function(): void {
    $data = ['email' => 'test@example.com', 'password' => 'password'];
    request()->request->add($data);
    AdminAuth::shouldReceive('check')->andReturnFalse();
    AdminAuth::shouldReceive('attempt')->with($data, true)->andReturnTrue();

    $response = (new Login)->onLogin();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/dashboard');
});

it('logs in successfully with valid credentials and redirects to custom url', function(): void {
    $data = ['email' => 'test@example.com', 'password' => 'password'];
    request()->request->add($data);
    request()->merge(['redirect' => 'orders']);
    AdminAuth::shouldReceive('attempt')->with($data, true)->andReturn(true);

    $response = (new Login)->onLogin();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/orders');
});

it('fails to log in with invalid credentials', function(): void {
    $data = ['email' => 'test@example.com', 'password' => 'wrong_password'];
    request()->request->add($data);
    AdminAuth::shouldReceive('attempt')->with($data, true)->andReturnFalse();

    $this->expectException(ValidationException::class);

    (new Login)->onLogin();
});

it('resets password successfully with valid code', function(): void {
    User::factory()->create([
        'reset_code' => 'valid_code',
        'reset_time' => now()->subMinutes(5),
    ]);
    $data = ['code' => 'valid_code', 'password' => 'new_password', 'password_confirm' => 'new_password'];
    request()->request->add($data);

    $response = (new Login)->onResetPassword();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/login')
        ->and(flash()->messages()->first())->level->toBe('success')
        ->message->toBe(lang('igniter.user::default.login.alert_success_reset'));
});

it('resets password fails if code does not match', function(): void {
    User::factory()->create([
        'reset_code' => 'valid_code',
        'reset_time' => now()->subMinutes(5),
    ]);
    $data = ['code' => 'invalid_code', 'password' => 'new_password', 'password_confirm' => 'new_password'];
    request()->request->add($data);

    $this->expectException(ValidationException::class);

    (new Login)->onResetPassword();
});
