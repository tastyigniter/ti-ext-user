<?php

namespace Igniter\User\Tests\Models;

use Igniter\System\Models\Concerns\HasCountry;
use Igniter\System\Models\Country;
use Igniter\User\Models\Address;
use Igniter\User\Models\Concerns\HasCustomer;
use Igniter\User\Models\Customer;
use Mockery;

it('creates or updates address from request', function() {
    $addressData = [
        'customer_id' => 1,
        'address_id' => 1,
        'address_1' => '123 Main St',
        'address_2' => 'Apt 4',
        'city' => 'Anytown',
        'state' => 'CA',
        'postcode' => '12345',
        'country_id' => 1,
    ];

    Address::createOrUpdateFromRequest($addressData);

    expect(Address::where($addressData)->exists())->toBeTrue();
});

it('returns formatted address attribute', function() {
    $expectedAddress = '123 Main St, Apt 4, Anytown 12345, CA, Afghanistan';
    $address = Mockery::mock(Address::class)->makePartial();
    $address->shouldReceive('toArray')->andReturn([
        'address_1' => '123 Main St',
        'address_2' => 'Apt 4',
        'city' => 'Anytown',
        'state' => 'CA',
        'postcode' => '12345',
        'country_id' => 1,
    ]);
    $address->shouldReceive('format_address')->with(Mockery::type('array'), false)->andReturn($expectedAddress);

    $result = $address->getFormattedAddressAttribute(null);

    expect($result)->toBe($expectedAddress);
});

it('applies filters to query builder', function() {
    $query = Address::query()->applyFilters([
        'customer' => 1,
        'sort' => 'address_id desc',
    ]);

    expect($query->toSql())->toContain('where `addresses`.`customer_id` = ?')
        ->and($query->toSql())->toContain('order by `address_id` desc');
});

it('configures address model correctly', function() {
    $address = new Address;

    expect(class_uses_recursive($address))
        ->toContain(HasCountry::class)
        ->toContain(HasCustomer::class)
        ->and($address->getTable())->toBe('addresses')
        ->and($address->getKeyName())->toBe('address_id')
        ->and($address->getFillable())->toBe(['customer_id', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country_id'])
        ->and($address->getCasts()['customer_id'])->toBe('integer')
        ->and($address->getCasts()['country_id'])->toBe('integer')
        ->and($address->getMorphClass())->toBe('addresses')
        ->and($address->relation['belongsTo']['customer'])->toBe(Customer::class)
        ->and($address->relation['belongsTo']['country'])->toBe(Country::class);
});
