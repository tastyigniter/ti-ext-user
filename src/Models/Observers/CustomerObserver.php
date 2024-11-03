<?php

namespace Igniter\User\Models\Observers;

use Igniter\User\Models\Customer;

class CustomerObserver
{
    public function created(Customer $customer)
    {
        $customer->saveCustomerGuestOrder();
    }

    public function saved(Customer $customer)
    {
        $customer->restorePurgedValues();

        if (!$customer->exists) {
            return;
        }

        if ($customer->status && is_null($customer->is_activated)) {
            $customer->completeActivation($customer->getActivationCode());
        }

        if (array_key_exists('addresses', $customer->getAttributes())) {
            $customer->saveAddresses(array_get($customer->getAttributes(), 'addresses', []));
        }
    }

    public function deleting(Customer $customer)
    {
        $customer->addresses()->delete();
    }
}
