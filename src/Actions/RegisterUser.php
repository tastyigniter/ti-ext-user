<?php

namespace Igniter\User\Actions;

use Igniter\Flame\Exception\ApplicationException;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Igniter\User\Models\CustomerGroup;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

class RegisterUser
{
    public $customer;

    public function handle(array $data = []): Customer
    {
        Event::fire('igniter.user.beforeRegister', [&$data]);

        $customerGroup = CustomerGroup::getDefault();
        $data['customer_group_id'] = $customerGroup?->getKey();

        $requireActivation = $customerGroup?->requiresApproval();
        $autoActivation = !$requireActivation;

        $customer = Auth::register($data, $autoActivation);

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

        $this->customer = $customer;

        $this->notifyRegistered([
            'account_login_link' => page_url($this->loginPage),
        ]);

        Auth::login($customer);
    }

    public function notifyRegistered(array $data)
    {
        $data = array_merge([
            'first_name' => $this->customer->first_name,
            'last_name' => $this->customer->last_name,
            'account_login_link' => null,
        ], $data);

        $settingRegistrationEmail = setting('registration_email');
        is_array($settingRegistrationEmail) || $settingRegistrationEmail = [];

        if (in_array('customer', $settingRegistrationEmail)) {
            Mail::queueTemplate('igniter.user::mail.registration', $data, $this->customer);
        }

        if (in_array('admin', $settingRegistrationEmail)) {
            Mail::queueTemplate('igniter.user::mail.registration_alert', $data, [setting('site_email'), setting('site_name')]);
        }
    }

    public function notifyActivated(array $data)
    {
        $data = array_merge([
            'first_name' => $this->customer->first_name,
            'last_name' => $this->customer->last_name,
            'account_activation_link' => null,
        ], $data);

        Mail::queueTemplate('igniter.user::mail.activation', $data, $this->customer);
    }
}
