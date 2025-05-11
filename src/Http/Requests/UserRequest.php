<?php

declare(strict_types=1);

namespace Igniter\User\Http\Requests;

use Igniter\System\Classes\FormRequest;
use Igniter\User\Facades\AdminAuth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Override;

/**
 * @property null|string $send_invite
 */
class UserRequest extends FormRequest
{
    #[Override]
    public function attributes(): array
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

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'between:2,255'],
            'email' => ['required', 'max:96', 'email:filter',
                Rule::unique('admin_users')->ignore($this->getRecordId(), 'user_id'),
            ],
            'username' => ['required', 'alpha_dash', 'between:2,32',
                Rule::unique('admin_users')->ignore($this->getRecordId(), 'user_id'),
            ],
            'send_invite' => ['nullable', 'boolean'],
            'password' => ['nullable', 'required_if:send_invite,0', 'string', Password::min(8)->numbers()->symbols()->letters()->mixedCase(), 'same:password_confirm'],
            'status' => ['boolean'],
            'super_user' => ['boolean'],
            'language_id' => ['nullable', 'integer'],
            'user_role_id' => ['sometimes', 'required', 'integer'],
            'groups' => ['sometimes', 'required', 'array'],
            'locations' => ['nullable', 'array'],
            'groups.*' => ['integer'],
            'locations.*' => ['integer'],
        ];

        if ($this->send_invite) {
            unset($rules['password']);
        }

        return $rules;
    }

    #[Override]
    protected function getRecordId(): int|string|null
    {
        $slugName = ($slug = $this->route('slug'))
            ? str_after($slug, '/') : null;

        return $slugName == 'account' ? AdminAuth::id() : $slugName;
    }
}
