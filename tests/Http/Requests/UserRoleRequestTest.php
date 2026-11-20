<?php

declare(strict_types=1);

namespace Igniter\User\Tests\Http\Requests;

use Closure;
use Igniter\User\Http\Requests\UserRoleRequest;
use Illuminate\Support\Facades\Validator;

it('returns correct attribute labels for user role', function(): void {
    $attributes = (new UserRoleRequest)->attributes();

    expect($attributes['code'])->toBe(lang('igniter::admin.label_code'))
        ->and($attributes['name'])->toBe(lang('igniter::admin.label_name'))
        ->and($attributes['permissions'])->toBe(lang('igniter.user::default.user_roles.label_permissions'))
        ->and($attributes['permissions.*'])->toBe(lang('igniter.user::default.user_roles.label_permissions'));
});

it('validates rules correctly for user role', function(): void {
    $rules = (new UserRoleRequest)->rules();

    expect($rules['code'])->toBe(['string', 'between:2,32', 'alpha_dash'])
        ->and($rules['name'])->toContain('required', 'string', 'between:2,255')
        ->and($rules['name'][3]->__toString())->toBe('unique:admin_user_roles,NULL,NULL,user_role_id')
        ->and($rules['permissions'])->toContain('required', 'array')
        ->and($rules['permissions'][2])->toBeInstanceOf(Closure::class)
        ->and($rules['permissions'][3])->toBeInstanceOf(Closure::class)
        ->and($rules['permissions.*'])->toBe(['required', 'integer', 'in:-1,0,1']);
});

it('accepts only registered permission codes', function(array $permissions, bool $passes): void {
    $rules = (new UserRoleRequest)->rules();

    $validator = Validator::make(['permissions' => $permissions], ['permissions' => $rules['permissions']]);

    expect($validator->passes())->toBe($passes);
})->with([
    'registered' => [['Admin.Staffs' => 1, 'Admin.Orders' => -1], true],
    'unknown' => [['Admin.Unknown' => 1], false],
    'wildcard' => [['Admin.*' => 1], false],
    'global wildcard' => [['*' => 1], false],
]);
