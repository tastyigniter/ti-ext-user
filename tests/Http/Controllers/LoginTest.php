<?php

namespace Igniter\User\Tests\Http\Controllers;

use Igniter\User\Facades\AdminAuth;
use Igniter\User\Http\Controllers\Login;
use Igniter\User\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;

beforeEach(function() {
    $this->route = new Route('GET', 'login', ['as' => 'igniter.admin.login']);
    $this->route->parameters = ['slug' => ''];
});

it('loads login page if not logged in', function() {
    $this->route->action = [];
    request()->setRouteResolver(fn() => $this->route);

    $response = (new Login)->index();

    expect($response)->toBeInstanceOf(RedirectResponse::class);
});

it('redirects to dashboard if already logged in', function() {
    AdminAuth::shouldReceive('isLogged')->andReturnTrue();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();
    AdminAuth::shouldReceive('check')->andReturnTrue();
    request()->setRouteResolver(fn() => $this->route);

    $response = (new Login)->index();

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and((new Login)->checkUser())->toBeTrue();
});

it('renders login view if not logged in and on login route', function() {
    request()->setRouteResolver(fn() => $this->route);
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();

    $response = (new Login)->index();

    expect($response)->toBeString();
});

it('renders reset password view', function() {
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();

    $response = (new Login)->reset();

    expect($response)->toBeString();
});

it('redirects reset password to dashboard if already logged in', function() {
    AdminAuth::shouldReceive('isLogged')->andReturnTrue();

    $response = (new Login)->reset();

    expect($response)->toBeInstanceOf(RedirectResponse::class);
});

it('resets password successfully', function() {
    $user = User::factory()->create();
    request()->request->set('email', $user->email);
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();

    $response = (new Login)->onRequestResetPassword();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/login')
        ->and(flash()->messages()->first())->level->toBe('success')
        ->message->toBe(lang('igniter::admin.login.alert_email_sent'));
});

it('fails to reset password with invalid code', function() {
    AdminAuth::shouldReceive('isLogged')->andReturnFalse();
    AdminAuth::shouldReceive('isImpersonator')->andReturnFalse();
    request()->merge(['code' => 'invalid_code']);

    $response = (new Login)->reset();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/login')
        ->and(flash()->messages()->first())->level->toBe('danger')
        ->message->toBe(lang('igniter::admin.login.alert_failed_reset'));
});

it('logs in successfully with valid credentials', function() {
    $data = ['email' => 'test@example.com', 'password' => 'password'];
    request()->request->add($data);
    AdminAuth::shouldReceive('attempt')->with($data, true)->andReturn(true);

    $response = (new Login)->onLogin();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/dashboard');
});

it('logs in successfully with valid credentials and redirects to custom url', function() {
    $data = ['email' => 'test@example.com', 'password' => 'password'];
    request()->request->add($data);
    request()->merge(['redirect' => 'orders']);
    AdminAuth::shouldReceive('attempt')->with($data, true)->andReturn(true);

    $response = (new Login)->onLogin();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/orders');
});

it('fails to log in with invalid credentials', function() {
    $data = ['email' => 'test@example.com', 'password' => 'wrong_password'];
    request()->request->add($data);
    AdminAuth::shouldReceive('attempt')->with($data, true)->andReturnFalse();

    $this->expectException(ValidationException::class);

    (new Login)->onLogin();
});

it('resets password successfully with valid code', function() {
    User::factory()->create([
        'reset_code' => 'valid_code',
        'reset_time' => now()->subMinutes(5),
    ]);
    $data = ['code' => 'valid_code', 'password' => 'new_password', 'password_confirm' => 'new_password'];
    request()->request->add($data);

    $response = (new Login)->onResetPassword();

    expect($response->getTargetUrl())->toBe('http://localhost/admin/login')
        ->and(flash()->messages()->first())->level->toBe('success')
        ->message->toBe(lang('igniter::admin.login.alert_success_reset'));
});

it('resets password fails if code does not match', function() {
    User::factory()->create([
        'reset_code' => 'valid_code',
        'reset_time' => now()->subMinutes(5),
    ]);
    $data = ['code' => 'invalid_code', 'password' => 'new_password', 'password_confirm' => 'new_password'];
    request()->request->add($data);

    $this->expectException(ValidationException::class);

    (new Login)->onResetPassword();
});
