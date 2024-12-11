<?php

use Igniter\User\Actions\LogoutCustomer;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;

it('logs out customer and invalidates session when not impersonating', function() {
    Event::fake();
    $customer = Mockery::mock(Customer::class);
    Auth::shouldReceive('getUser')->andReturn($customer);
    Auth::shouldReceive('isImpersonator')->andReturn(false);
    Auth::shouldReceive('logout')->once();
    Session::shouldReceive('invalidate')->once();
    Session::shouldReceive('regenerateToken')->once();

    (new LogoutCustomer)->handle();

    expect(flash()->messages()->first())
        ->level->toBe('success')
        ->message->toBe(lang('igniter.user::default.alert_logout_success'));

    Event::assertDispatched('igniter.user.logout', function($eventName, $eventPayload) use ($customer) {
        return $eventPayload[0] === $customer;
    });
});

it('stops impersonation when customer is impersonating', function() {
    Event::fake();
    Auth::shouldReceive('getUser')->andReturn(null);
    Auth::shouldReceive('isImpersonator')->andReturn(true);
    Auth::shouldReceive('stopImpersonate')->once();
    Session::shouldReceive('invalidate')->never();
    Session::shouldReceive('regenerateToken')->never();

    (new LogoutCustomer)->handle();

    expect(flash()->messages()->first())
        ->level->toBe('success')
        ->message->toBe(lang('igniter.user::default.alert_logout_success'));

    Event::assertNotDispatched('igniter.user.logout');
});

it('does not dispatch logout event when customer is null', function() {
    Event::fake();
    Auth::shouldReceive('getUser')->andReturn(null);
    Auth::shouldReceive('isImpersonator')->andReturn(false);
    Auth::shouldReceive('logout')->once();
    Session::shouldReceive('invalidate')->once();
    Session::shouldReceive('regenerateToken')->once();

    (new LogoutCustomer)->handle();

    expect(flash()->messages()->first())
        ->level->toBe('success')
        ->message->toBe(lang('igniter.user::default.alert_logout_success'));

    Event::assertNotDispatched('igniter.user.logout');
});
