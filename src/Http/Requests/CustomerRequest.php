<?php

namespace Igniter\User\Http\Requests;

use Igniter\System\Classes\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'first_name' => lang('igniter.user::default.customers.label_first_name'),
            'last_name' => lang('igniter.user::default.customers.label_last_name'),
            'email' => lang('igniter::admin.label_email'),
            'telephone' => lang('igniter.user::default.customers.label_telephone'),
            'newsletter' => lang('igniter.user::default.customers.label_newsletter'),
            'customer_group_id' => lang('igniter.user::default.customers.label_customer_group'),
            'status' => lang('igniter::admin.label_status'),
            'addresses.*.address_1' => lang('igniter.user::default.customers.label_address_1'),
            'addresses.*.city' => lang('igniter.user::default.customers.label_city'),
            'addresses.*.state' => lang('igniter.user::default.customers.label_state'),
            'addresses.*.postcode' => lang('igniter.user::default.customers.label_postcode'),
            'addresses.*.country_id' => lang('igniter.user::default.customers.label_country'),
            'password' => lang('igniter.user::default.customers.label_password'),
            'confirm_password' => lang('igniter.user::default.customers.label_confirm_password'),
        ];
    }

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'between:1,48'],
            'last_name' => ['required', 'string', 'between:1,48'],
            'email' => ['required', 'email:filter', 'max:96', Rule::unique('customers')->ignore($this->getRecordId(), 'customer_id')],
            'password' => ['nullable', 'required_if:send_invite,0', 'string', 'min:8', 'max:40', 'same:confirm_password'],
            'telephone' => ['nullable', 'string'],
            'newsletter' => ['nullable', 'required', 'boolean'],
            'customer_group_id' => ['required', 'integer'],
            'status' => ['required', 'boolean'],
            'addresses.*.address_id' => ['nullable', 'integer'],
            'addresses.*.address_1' => ['required', 'string', 'min:3', 'max:255'],
            'addresses.*.address_2' => ['nullable', 'string'],
            'addresses.*.city' => ['nullable', 'string', 'min:2', 'max:255'],
            'addresses.*.state' => ['nullable', 'string', 'max:255'],
            'addresses.*.postcode' => ['nullable', 'string'],
        ];
    }
}
