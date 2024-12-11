<?php

namespace Igniter\User\Tests\Actions;

use Igniter\Flame\Exception\ApplicationException;
use Igniter\Flame\Exception\SystemException;
use Igniter\User\Actions\RegisterCustomer;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Illuminate\Support\Facades\Event;
use Mockery;

it('registers customer and fires events correctly', function() {
    Event::fake();
    $data = ['email' => 'user@example.com', 'password' => 'password', 'customer_group_id' => 1];
    Auth::shouldReceive('getProvider->register')->with($data, true)->andReturn(Mockery::mock(Customer::class));
    Auth::shouldReceive('login')->once();

    $result = (new RegisterCustomer)->handle($data);

    expect($result)->toBeInstanceOf(Customer::class);

    Event::assertDispatched('igniter.user.beforeRegister', function($eventName, $eventPayload) use ($data) {
        return $eventPayload[0] === $data;
    });
    Event::assertDispatched('igniter.user.register', function($eventName, $eventPayload) use ($result, $data) {
        return $eventPayload[0] === $result && $eventPayload[1] === $data;
    });
});

it('throws exception when activation code is invalid', function() {
    expect(fn() => (new RegisterCustomer)->activate('invalid_code'))->toThrow(
        ApplicationException::class, lang('igniter.user::default.reset.alert_activation_failed'),
    );
});

it('throws exception when activation fails', function() {
    Customer::factory()->create([
        'status' => false,
        'is_activated' => true,
        'activation_code' => 'valid_code',
    ]);

    expect(fn() => (new RegisterCustomer)->activate('valid_code'))->toThrow(
        SystemException::class, 'User is already active!',
    );
});

it('activates customer and logs in successfully', function() {
    $customer = Customer::factory()->create([
        'status' => false,
        'is_activated' => false,
        'activation_code' => 'valid_code',
    ]);
    Auth::shouldReceive('login')->once();

    $result = (new RegisterCustomer)->activate('valid_code');

    expect($result->getKey())->toBe($customer->getKey());
});
