<?php

namespace Igniter\User\Requests;

use Igniter\System\Classes\FormRequest;

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
            '_confirm_password' => lang('igniter.user::default.customers.label_confirm_password'),
        ];
    }

    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'between:1,48'],
            'last_name' => ['required', 'string', 'between:1,48'],
            'email' => ['required', 'email:filter', 'max:96', 'unique:customers,email'],
            'password' => ['nullable', 'required_if:send_invite,0', 'string', 'min:8', 'max:40', 'same:_confirm_password'],
            'telephone' => ['sometimes', 'string'],
            'newsletter' => ['sometimes', 'required', 'boolean'],
            'customer_group_id' => ['required', 'integer'],
            'status' => ['required', 'boolean'],
            'addresses.*.address_1' => ['required', 'string', 'min:3', 'max:255'],
            'addresses.*.address_2' => ['string'],
            'addresses.*.city' => ['required', 'string', 'min:2', 'max:255'],
            'addresses.*.state' => ['string', 'max:255'],
            'addresses.*.postcode' => ['string'],
        ];
    }
}
