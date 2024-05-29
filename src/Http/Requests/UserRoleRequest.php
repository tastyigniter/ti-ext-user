<?php

namespace Igniter\User\Http\Requests;

use Igniter\System\Classes\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => ['required', 'string', 'between:2,255',
                Rule::unique('admin_user_roles')->ignore($this->getRecordId(), 'user_role_id'),
            ],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'integer'],
        ];
    }
}
