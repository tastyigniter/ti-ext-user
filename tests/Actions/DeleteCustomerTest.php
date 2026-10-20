<?php

declare(strict_types=1);

use Igniter\Flame\Exception\ApplicationException;
use Igniter\User\Actions\DeleteCustomer;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

it('deletes the signed in customer and their addresses', function(): void {
    Event::fake(['igniter.user.beforeDeleteAccount', 'igniter.user.deleteAccount']);
    $customer = Customer::factory()->create(['password' => 'secret-password']);
    Auth::login($customer);

    (new DeleteCustomer)->handle($customer, 'secret-password');

    expect(Customer::find($customer->getKey()))->toBeNull()
        ->and(Auth::check())->toBeFalse()
        ->and(flash()->messages()->first())
        ->level->toBe('success')
        ->message->toBe(lang('igniter.user::default.alert_delete_account_success'));

    Event::assertDispatched('igniter.user.beforeDeleteAccount', fn($eventName, $eventPayload): bool => $eventPayload[0]->is($customer));
    Event::assertDispatched('igniter.user.deleteAccount');
});

it('rejects the wrong password', function(): void {
    $customer = Customer::factory()->create(['password' => 'secret-password']);

    expect(fn() => (new DeleteCustomer)->handle($customer, 'wrong-password'))
        ->toThrow(ValidationException::class, lang('igniter.user::default.alert_delete_account_password'));

    expect(Customer::find($customer->getKey()))->not->toBeNull();
});

it('refuses while an admin is impersonating the customer', function(): void {
    $customer = Customer::factory()->create(['password' => 'secret-password']);
    Auth::shouldReceive('isImpersonator')->andReturn(true);

    expect(fn() => (new DeleteCustomer)->handle($customer, 'secret-password'))
        ->toThrow(ApplicationException::class, lang('igniter.user::default.alert_delete_account_impersonating'));
});
