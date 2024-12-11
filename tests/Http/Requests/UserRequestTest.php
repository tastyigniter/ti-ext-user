<?php

namespace Igniter\User\Tests\Http\Requests;

use Igniter\User\Http\Requests\UserRequest;

it('has correct attribute labels', function() {
    $attributes = (new UserRequest)->attributes();

    expect($attributes['name'])->toBe(lang('igniter::admin.label_name'))
        ->and($attributes['email'])->toBe(lang('igniter::admin.label_email'))
        ->and($attributes['username'])->toBe(lang('igniter.user::default.staff.label_username'))
        ->and($attributes['password'])->toBe(lang('igniter.user::default.staff.label_password'))
        ->and($attributes['password_confirm'])->toBe(lang('igniter.user::default.staff.label_confirm_password'))
        ->and($attributes['status'])->toBe(lang('igniter::admin.label_status'))
        ->and($attributes['language_id'])->toBe(lang('igniter.user::default.staff.label_language_id'))
        ->and($attributes['user_role_id'])->toBe(lang('igniter.user::default.staff.label_role'))
        ->and($attributes['groups'])->toBe(lang('igniter.user::default.staff.label_group'))
        ->and($attributes['locations'])->toBe(lang('igniter.user::default.staff.label_location'))
        ->and($attributes['groups.*'])->toBe(lang('igniter.user::default.staff.label_group'))
        ->and($attributes['locations.*'])->toBe(lang('igniter.user::default.staff.label_location'));
});

it('has correct validation rules', function() {
    $rules = (new UserRequest)->rules();

    expect($rules['name'])->toBe(['required', 'string', 'between:2,255'])
        ->and($rules['email'])->toContain('required', 'email:filter', 'max:96')
        ->and($rules['email'][3]->__toString())->toBe('unique:admin_users,NULL,NULL,user_id')
        ->and($rules['username'])->toContain('required', 'alpha_dash', 'between:2,32')
        ->and($rules['username'][3]->__toString())->toBe('unique:admin_users,NULL,NULL,user_id')
        ->and($rules['password'])->toBe(['nullable', 'required_if:send_invite,0', 'string', 'between:6,32', 'same:password_confirm'])
        ->and($rules['status'])->toBe(['boolean'])
        ->and($rules['super_user'])->toBe(['boolean'])
        ->and($rules['language_id'])->toBe(['nullable', 'integer'])
        ->and($rules['user_role_id'])->toBe(['sometimes', 'required', 'integer'])
        ->and($rules['groups'])->toBe(['sometimes', 'required', 'array'])
        ->and($rules['locations'])->toBe(['nullable', 'array'])
        ->and($rules['groups.*'])->toBe(['integer'])
        ->and($rules['locations.*'])->toBe(['integer']);
});
