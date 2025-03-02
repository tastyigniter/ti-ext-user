<?php

namespace Igniter\User\Requests;

use Igniter\System\Classes\FormRequest;

class CustomerGroupRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'group_name' => lang('igniter::admin.label_name'),
            'approval' => lang('igniter.user::default.customer_groups.label_approval'),
            'description' => lang('igniter::admin.label_description'),
        ];
    }

    public function rules()
    {
        return [
            'group_name' => ['required', 'string', 'between:2,32'],
            'approval' => ['required', 'boolean'],
            'description' => ['string', 'between:2,512'],
        ];
    }
}
