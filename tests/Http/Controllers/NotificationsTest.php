<?php

namespace Igniter\User\Tests\Http\Controllers;

use Igniter\User\Http\Controllers\Notifications;
use Igniter\User\Models\User;
use Illuminate\Http\RedirectResponse;

it('loads notification page', function() {
    actingAsSuperUser()
        ->get(route('igniter.user.notifications'))
        ->assertOk();
});

it('marks notification as read', function() {
    $user = User::factory()->superUser()->create();

    $controller = new Notifications;
    $controller->setUser($user);
    $response = $controller->onMarkAsRead();

    expect($response)->toBeInstanceOf(RedirectResponse::class);
});
