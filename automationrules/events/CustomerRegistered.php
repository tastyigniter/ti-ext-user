<?php

namespace Igniter\User\AutomationRules\Events;

use Igniter\Automation\Classes\BaseEvent;

class CustomerRegistered extends BaseEvent
{
    public function eventDetails()
    {
        return [
            'name' => 'Customer Registered Event',
            'description' => 'When a customer registers',
            'group' => 'customer',
        ];
    }

    public static function makeParamsFromEvent(array $args, $eventName = null)
    {
        return $args;
    }
}