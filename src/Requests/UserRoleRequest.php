<?php

namespace Igniter\User\Requests;

use Igniter\System\Classes\FormRequest;

class UserRoleRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'code' => lang('igniter::admin.label_code'),
            'name' => lang('igniter::admin.label_name'),
            'permissions' => lang('igniter.user::default.user_roles.label_permissions'),
            'permissions.*' => lang('igniter.user::default.user_roles.label_permissions'),
        ];
    }

    public function rules()
    {
        return [
            'code' => ['string', 'between:2,32', 'alpha_dash'],
            'name' => ['required', 'string', 'between:2,255', 'unique:admin_user_roles'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'integer'],
        ];
    }
}
