<?php

namespace Igniter\User\Http\Requests;

use Igniter\System\Classes\FormRequest;
use Illuminate\Validation\Rule;

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
            'group_name' => ['required', 'string', 'between:2,32',
                Rule::unique('customer_groups')->ignore($this->getRecordId(), 'customer_group_id'),
            ],
            'approval' => ['required', 'boolean'],
            'description' => ['string', 'between:2,512'],
        ];
    }
}
