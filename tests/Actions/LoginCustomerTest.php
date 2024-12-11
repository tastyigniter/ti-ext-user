<?php

namespace Igniter\User\Tests\Actions;

use Igniter\Flame\Exception\FlashException;
use Igniter\User\Actions\LoginCustomer;
use Igniter\User\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;

it('fires beforeAuthenticate event with correct parameters', function() {
    Event::fake();
    $credentials = ['email' => 'user@example.com', 'password' => 'password'];
    Auth::shouldReceive('attempt')->andReturn(true);
    Session::shouldReceive('regenerate')->once();

    $loginUser = new LoginCustomer($credentials);
    $loginUser->handle();

    Event::assertDispatched('igniter.user.beforeAuthenticate', function($eventName, $eventPayload) use ($loginUser, $credentials) {
        return $eventPayload[0] === $loginUser && $eventPayload[1] === $credentials;
    });

    Event::assertDispatched('igniter.user.login', function($eventName, $eventPayload) use ($loginUser) {
        return $eventPayload[0] === $loginUser;
    }, true);
});

it('throws FlashException when authentication fails', function() {
    $credentials = ['email' => 'user@example.com', 'password' => 'wrongpassword'];
    Auth::shouldReceive('attempt')->andReturn(false);

    expect(fn() => (new LoginCustomer($credentials))->handle())->toThrow(FlashException::class);
});
