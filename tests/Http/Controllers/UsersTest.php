<?php

namespace Igniter\User\Tests\Http\Controllers;

use Igniter\Flame\Exception\FlashException;
use Igniter\User\Http\Controllers\Users;
use Igniter\User\Models\User;
use Illuminate\Contracts\Auth\Access\Gate;
use Mockery;

it('loads users page', function() {
    actingAsSuperUser()
        ->get(route('igniter.user.users'))
        ->assertOk();
});

it('loads users page with no superadmin', function() {
    $authGate = Mockery::mock(Gate::class);
    $authGate->shouldReceive('inspect')->with('Admin.Staffs')->andReturnSelf();
    $authGate->shouldReceive('allowed')->andReturnTrue();
    app()->instance(Gate::class, $authGate);
    $user = User::factory()->create();

    actingAsSuperUser($user)
        ->get(route('igniter.user.users'))
        ->assertOk();
});

it('loads create user page', function() {
    actingAsSuperUser()
        ->get(route('igniter.user.users', ['slug' => 'create']))
        ->assertOk();
});

it('loads edit user page', function() {
    $user = User::factory()->create();

    actingAsSuperUser()
        ->get(route('igniter.user.users', ['slug' => 'edit/'.$user->getKey()]))
        ->assertOk();
});

it('loads current user account page', function() {
    $user = User::factory()->superUser()->create();

    actingAsSuperUser($user)
        ->get(route('igniter.user.users', ['slug' => 'account']))
        ->assertOk();
});

it('loads user preview page', function() {
    $user = User::factory()->create();

    actingAsSuperUser()
        ->get(route('igniter.user.users', ['slug' => 'preview/'.$user->getKey()]))
        ->assertOk();
});

it('creates user', function() {
    actingAsSuperUser()
        ->post(route('igniter.user.users', ['slug' => 'create']), [
            'User' => [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'email' => 'user@example.com',
                'groups' => [1],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(User::where('email', 'user@example.com')->where('name', 'John Doe')->exists())->toBeTrue();
});

it('updates user', function() {
    $user = User::factory()->create();

    actingAsSuperUser()
        ->post(route('igniter.user.users', ['slug' => 'edit/'.$user->getKey()]), [
            'User' => [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'email' => 'user@example.com',
                'groups' => [1],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(User::where('email', 'user@example.com')->where('name', 'John Doe')->exists())->toBeTrue();
});

it('updates current user and redirects user when email changes', function() {
    $user = User::factory()->create();

    actingAsSuperUser($user)
        ->post(route('igniter.user.users', ['slug' => 'account']), [
            'User' => [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'email' => 'user@example.com',
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(User::where('email', 'user@example.com')->where('name', 'John Doe')->exists())->toBeTrue();
});

it('updates current user and redirects user when users changes', function() {
    $user = User::factory()->superUser()->create();

    actingAsSuperUser($user)
        ->post(route('igniter.user.users', ['slug' => 'account']), [
            'User' => [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'email' => $user->email,
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(User::where('username', 'johndoe')->where('name', 'John Doe')->exists())->toBeTrue();
});

it('deletes user', function() {
    $user = User::factory()->create();

    actingAsSuperUser()
        ->post(route('igniter.user.users', ['slug' => 'edit/'.$user->getKey()]), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(User::find($user->getKey()))->toBeNull();
});

it('bulk deletes users', function() {
    $users = User::factory()->count(5)->create();
    $userIds = $users->pluck('user_id')->all();

    actingAsSuperUser()
        ->post(route('igniter.user.users'), [
            'checked' => $userIds,
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(User::whereIn('user_id', $userIds)->exists())->toBeFalse();
});

it('throws exception when unauthorized to delete user', function() {
    $authGate = Mockery::mock(Gate::class);
    $authGate->shouldReceive('inspect')->with('Admin.DeleteStaffs')->andReturnSelf();
    $authGate->shouldReceive('allowed')->andReturnFalse();
    app()->instance(Gate::class, $authGate);

    $this->expectException(FlashException::class);
    $this->expectExceptionMessage(lang('igniter::admin.alert_user_restricted'));

    (new Users)->index_onDelete();
});

it('impersonates user successfully', function() {
    $authGate = Mockery::mock(Gate::class);
    $authGate->shouldReceive('inspect')->with('Admin.Impersonate')->andReturnSelf();
    $authGate->shouldReceive('allowed')->andReturnTrue();
    app()->instance(Gate::class, $authGate);
    $user = User::factory()->create(['name' => 'John Doe']);
    request()->request->set('recordId', $user->getKey());

    $controller = new Users;
    $controller->setUser(User::factory()->superUser()->create());
    $controller->onImpersonate('edit');

    expect(flash()->messages()->first())
        ->level->toBe('success')
        ->message->toBe(sprintf(lang('igniter.user::default.staff.alert_impersonate_success'), 'John Doe'));
});

it('throws exception when unauthorized to impersonate user', function() {
    $user = User::factory()->create(['name' => 'John Doe']);
    $authGate = Mockery::mock(Gate::class);
    $authGate->shouldReceive('inspect')->with('Admin.Impersonate')->andReturnSelf();
    $authGate->shouldReceive('allowed')->andReturnFalse();
    app()->instance(Gate::class, $authGate);

    $this->expectException(FlashException::class);
    $this->expectExceptionMessage(lang('igniter.user::default.staff.alert_login_restricted'));

    (new Users)->onImpersonate('edit', $user->getKey());
});
