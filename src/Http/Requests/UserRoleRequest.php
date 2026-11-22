<?php

declare(strict_types=1);

namespace Igniter\User\Http\Requests;

use Closure;
use Igniter\System\Classes\FormRequest;
use Igniter\User\Classes\PermissionManager;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Models\User;
use Igniter\User\Models\UserRole;
use Illuminate\Validation\Rule;
use Override;

class UserRoleRequest extends FormRequest
{
    #[Override]
    public function attributes(): array
    {
        return [
            'code' => lang('igniter::admin.label_code'),
            'name' => lang('igniter::admin.label_name'),
            'permissions' => lang('igniter.user::default.user_roles.label_permissions'),
            'permissions.*' => lang('igniter.user::default.user_roles.label_permissions'),
        ];
    }

    public function rules(): array
    {
        return [
            'code' => ['string', 'between:2,32', 'alpha_dash'],
            'name' => ['required', 'string', 'between:2,255',
                Rule::unique('admin_user_roles')->ignore($this->getRecordId(), 'user_role_id'),
            ],
            'permissions' => ['required', 'array', $this->registeredPermissionsRule(...), $this->grantablePermissionsRule(...)],
            'permissions.*' => ['required', 'integer', 'in:-1,0,1'],
        ];
    }

    protected function registeredPermissionsRule(string $attribute, mixed $value, Closure $fail): void
    {
        $unknown = array_diff(array_map(strval(...), array_keys((array)$value)), resolve(PermissionManager::class)->listPermissionCodes());

        if ($unknown !== []) {
            $fail(sprintf(lang('igniter.user::default.user_roles.alert_unknown_permissions'), implode(', ', $unknown)));
        }
    }

    protected function grantablePermissionsRule(string $attribute, mixed $value, Closure $fail): void
    {
        $user = AdminAuth::getUser();
        if (!$user instanceof User || $user->isSuperUser()) {
            return;
        }

        $recordId = $this->getRecordId();
        $before = (array)(is_numeric($recordId) ? UserRole::query()->find($recordId)?->permissions : []);

        $denied = [];
        foreach ((array)$value as $permission => $permitted) {
            $permission = (string)$permission;
            if ((int)$permitted === 1 && (int)($before[$permission] ?? 0) !== 1 && !$user->hasPermission($permission)) {
                $denied[] = $permission;
            }
        }

        if ($denied !== []) {
            $fail(sprintf(lang('igniter.user::default.user_roles.alert_cannot_grant_permissions'), implode(', ', $denied)));
        }
    }
}
