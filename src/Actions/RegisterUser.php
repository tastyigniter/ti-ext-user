<?php

namespace Igniter\User\Actions;

use Igniter\Flame\Exception\ApplicationException;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Igniter\User\Models\CustomerGroup;
use Illuminate\Support\Facades\Event;

class RegisterUser
{
    public Customer $customer;

    public function handle(array $data = []): Customer
    {
        Event::fire('igniter.user.beforeRegister', [&$data]);

        $customerGroup = CustomerGroup::getDefault();
        $data['customer_group_id'] = $customerGroup?->getKey();

        $requireActivation = $customerGroup?->requiresApproval();
        $autoActivation = !$requireActivation;

        $customer = Auth::getProvider()->register($data, $autoActivation);

        Event::fire('igniter.user.register', [$customer, $data]);

        if ($autoActivation) {
            Auth::login($customer);
        }

        return $this->customer = $customer;
    }

    public function activate(string $code)
    {
        throw_unless($customer = Customer::whereActivationCode($code)->first(),
            new ApplicationException(lang('igniter.user::default.reset.alert_activation_failed')));

        throw_unless($customer->completeActivation($code),
            new ApplicationException(lang('igniter.user::default.reset.alert_activation_failed')));

        Auth::login($customer);

        return $this->customer = $customer;
    }
}
