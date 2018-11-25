<?php

namespace Igniter\User\EventRules\Events;

use Igniter\EventRules\Classes\BaseEvent;

class CustomerRegistered extends BaseEvent
{
    public function eventDetails()
    {
        return [
            'name' => 'Customer Registered Event',
            'description' => 'A customer is registered',
            'group' => 'customer'
        ];
    }

    public static function makeParamsFromEvent(array $args, $eventName = null)
    {
        return $args;
    }
}