<?php

namespace Igniter\User\Tests\Auth;

use Igniter\User\Auth\AuthServiceProvider;
use Igniter\User\Models\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Gate;
use Mockery;

it('merges configuration from auth.php', function() {
    $serviceProvider = new AuthServiceProvider($this->app);
    $serviceProvider->register();

    $config = config('igniter-auth');

    expect($config)->not->toBeNull()
        ->toHaveKey('guards')
        ->toHaveKey('mergeGuards')
        ->toHaveKey('mergeProviders')
        ->and($config['guards']['admin'])->toBe('igniter-admin')
        ->and($config['guards']['web'])->toBe('igniter-customer')
        ->and($config['mergeGuards']['igniter-admin'])->toBe([
            'driver' => 'igniter-admin',
            'provider' => 'igniter-admin',
        ])
        ->and($config['mergeGuards']['igniter-customer'])->toBe([
            'driver' => 'igniter-customer',
            'provider' => 'igniter',
        ])
        ->and($config['mergeProviders']['igniter-admin'])->toBe([
            'driver' => 'igniter',
            'model' => \Igniter\User\Models\User::class,
        ])
        ->and($config['mergeProviders']['igniter'])->toBe([
            'driver' => 'igniter',
            'model' => Customer::class,
        ]);
});

it('publishes configuration when running in console', function() {
    $app = Mockery::mock(Application::class)->makePartial();
    $app->shouldReceive('booted')->once();
    $app->shouldReceive('runningInConsole')->andReturn(true)->once();
    $serviceProvider = new AuthServiceProvider($app);
    $serviceProvider->boot();

    expect(true)->toBeTrue();
});

it('configures auth guards correctly', function() {
    $serviceProvider = new AuthServiceProvider($this->app);
    $serviceProvider->boot();

    $guards = config('auth.guards');
    expect($guards)->toHaveKey('igniter-admin')
        ->and($guards)->toHaveKey('igniter-customer');
});

it('configures auth provider correctly', function() {
    $serviceProvider = new AuthServiceProvider($this->app);
    $serviceProvider->boot();

    $providers = config('auth.providers');
    expect($providers)->toHaveKey('igniter');
});

it('configures gate callback correctly', function() {
    Gate::shouldReceive('after')->with(Mockery::on(function($callback) {
        $adminUser = Mockery::mock(\Igniter\User\Models\User::class);
        $adminUser->shouldReceive('hasAnyPermission')->with('ability')->andReturn(true);

        expect($callback($adminUser, 'ability'))->toBeTrue();

        return true;
    }));

    $serviceProvider = new AuthServiceProvider($this->app);
    $serviceProvider->boot();
});

//it('creates guard with correct configuration', function () {
//    $serviceProvider = new AuthServiceProvider($this->app);
//    $guard = $serviceProvider->createGuard(UserGuard::class, 'igniter - admin', [], Auth::guard());
//
//    expect($guard)->toBeInstanceOf(UserGuard::class);
//});
