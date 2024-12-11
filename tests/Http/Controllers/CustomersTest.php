<?php

namespace Igniter\User\Tests\Http\Controllers;

use Igniter\Flame\Exception\FlashException;
use Igniter\User\Http\Controllers\Customers;
use Igniter\User\Models\Customer;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\RedirectResponse;
use Mockery;

it('loads customers page', function() {
    actingAsSuperUser()
        ->get(route('igniter.user.customers'))
        ->assertOk();
});

it('loads create customer page', function() {
    actingAsSuperUser()
        ->get(route('igniter.user.customers', ['slug' => 'create']))
        ->assertOk();
});

it('loads edit customer page', function() {
    $customer = Customer::factory()->create();

    actingAsSuperUser()
        ->get(route('igniter.user.customers', ['slug' => 'edit/'.$customer->getKey()]))
        ->assertOk();
});

it('loads customer preview page', function() {
    $customer = Customer::factory()->create();

    actingAsSuperUser()
        ->get(route('igniter.user.customers', ['slug' => 'preview/'.$customer->getKey()]))
        ->assertOk();
});

it('creates customer', function() {
    actingAsSuperUser()
        ->post(route('igniter.user.customers', ['slug' => 'create']), [
            'Customer' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'user@example.com',
                'customer_group_id' => 1,
                'newsletter' => 1,
                'status' => 1,
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Customer::where('email', 'user@example.com')->where('first_name', 'John')->exists())->toBeTrue();
});

it('updates customer', function() {
    $customer = Customer::factory()->create(['is_activated' => false]);

    actingAsSuperUser()
        ->post(route('igniter.user.customers', ['slug' => 'edit/'.$customer->getKey()]), [
            'Customer' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'user@example.com',
                'customer_group_id' => 1,
                'newsletter' => 1,
                'status' => 1,
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Customer::where('email', 'user@example.com')->where('first_name', 'John')->exists())->toBeTrue();
});

it('deletes customer', function() {
    $customer = Customer::factory()->create();

    actingAsSuperUser()
        ->post(route('igniter.user.customers', ['slug' => 'edit/'.$customer->getKey()]), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(Customer::find($customer->getKey()))->toBeNull();
});

it('bulk deletes customers', function() {
    $customer = Customer::factory()->count(5)->create();
    $customerIds = $customer->pluck('customer_id')->all();

    actingAsSuperUser()
        ->post(route('igniter.user.customers'), [
            'checked' => $customerIds,
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(Customer::whereIn('customer_id', $customerIds)->exists())->toBeFalse();
});

it('throws exception when unauthorized to delete customer', function() {
    $authGate = Mockery::mock(Gate::class);
    $authGate->shouldReceive('inspect')->with('Admin.DeleteCustomers')->andReturnSelf();
    $authGate->shouldReceive('allowed')->andReturnFalse();
    app()->instance(Gate::class, $authGate);

    $this->expectException(FlashException::class);
    $this->expectExceptionMessage(lang('igniter::admin.alert_user_restricted'));

    (new Customers)->index_onDelete();
});

it('impersonates customer successfully', function() {
    $customer = Customer::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $authGate = Mockery::mock(Gate::class);
    $authGate->shouldReceive('inspect')->with('Admin.ImpersonateCustomers')->andReturnSelf();
    $authGate->shouldReceive('allowed')->andReturnTrue();
    app()->instance(Gate::class, $authGate);

    (new Customers)->onImpersonate('edit', $customer->getKey());

    expect(flash()->messages()->first())
        ->level->toBe('success')
        ->message->toBe(sprintf(lang('igniter.user::default.customers.alert_impersonate_success'), 'John Doe'));
});

it('throws exception when unauthorized to impersonate customer', function() {
    $customer = Customer::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $authGate = Mockery::mock(Gate::class);
    $authGate->shouldReceive('inspect')->with('Admin.ImpersonateCustomers')->andReturnSelf();
    $authGate->shouldReceive('allowed')->andReturnFalse();
    app()->instance(Gate::class, $authGate);

    $this->expectException(FlashException::class);
    $this->expectExceptionMessage(lang('igniter.user::default.customers.alert_login_restricted'));

    (new Customers)->onImpersonate('edit', $customer->getKey());
});

it('activates customer successfully', function() {
    $customer = Customer::factory()->create(['is_activated' => false]);

    $response = (new Customers)->edit_onActivate('context', $customer->getKey());

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and(flash()->messages()->first())
        ->level->toBe('success')
        ->message->toBe(lang('igniter.user::default.customers.alert_activation_success'));
});
