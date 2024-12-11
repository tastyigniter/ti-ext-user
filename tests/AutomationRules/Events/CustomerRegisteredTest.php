<?php

namespace Igniter\User\Tests\AutomationRules\Events;

use Igniter\User\AutomationRules\Events\CustomerRegistered;

it('returns correct event details', function() {
    $event = new CustomerRegistered;

    $result = $event->eventDetails();

    expect($result)->toBe([
        'name' => 'Customer Registered Event',
        'description' => 'When a customer registers',
        'group' => 'customer',
    ]);
});

it('makes params from event', function() {
    $args = ['customer' => 'John Doe'];
    $result = CustomerRegistered::makeParamsFromEvent($args);

    expect($result)->toBe($args);
});
