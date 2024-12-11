<?php

namespace Igniter\User\Tests\Http\Controllers;

use Igniter\User\Facades\AdminAuth;
use Igniter\User\Models\User;

it('logs out user', function() {
    $this->post(route('igniter.admin.logout'))
        ->assertRedirect(route('igniter.admin.login'));
});

it('logs out impersonator', function() {
    $user = User::factory()->create();
    AdminAuth::getSession()->put(AdminAuth::getName().'_impersonate', $user->getKey());

    $this->post(route('igniter.admin.logout'))
        ->assertRedirect(route('igniter.admin.login'));
});
