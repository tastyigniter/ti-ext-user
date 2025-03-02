<?php

namespace Igniter\User\Requests;

use Igniter\System\Classes\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'name' => lang('igniter::admin.label_name'),
            'email' => lang('igniter::admin.label_email'),
            'username' => lang('igniter.user::default.staff.label_username'),
            'password' => lang('igniter.user::default.staff.label_password'),
            'password_confirm' => lang('igniter.user::default.staff.label_confirm_password'),
            'status' => lang('igniter::admin.label_status'),
            'language_id' => lang('igniter.user::default.staff.label_language_id'),
            'user_role_id' => lang('igniter.user::default.staff.label_role'),
            'groups' => lang('igniter.user::default.staff.label_group'),
            'locations' => lang('igniter.user::default.staff.label_location'),
            'groups.*' => lang('igniter.user::default.staff.label_group'),
            'locations.*' => lang('igniter.user::default.staff.label_location'),
        ];
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'between:2,255'],
            'email' => ['required', 'max:96', 'email:filter',
                Rule::unique('admin_users')->ignore($this->getRecordId(), 'user_id'),
            ],
            'username' => ['required', 'alpha_dash', 'between:2,32',
                Rule::unique('admin_users')->ignore($this->getRecordId(), 'user_id'),
            ],
            'password' => ['sometimes', 'required_if:send_invite,0', 'string', 'between:6,32', 'same:password_confirm'],
            'status' => ['boolean'],
            'language_id' => ['nullable', 'integer'],
            'user_role_id' => ['sometimes', 'required', 'integer'],
            'groups' => ['sometimes', 'required', 'array'],
            'locations' => ['nullable', 'array'],
            'groups.*' => ['integer'],
            'locations.*' => ['integer'],
        ];
    }
}
