<?php

declare(strict_types=1);

namespace Igniter\User\Tests\Http\Controllers;

use Igniter\User\Models\User;
use Igniter\User\Models\UserRole;
use Illuminate\Testing\TestResponse;

it('loads user roles page', function(): void {
    actingAsSuperUser()
        ->get(route('igniter.user.user_roles'))
        ->assertOk();
});

it('loads create user role page', function(): void {
    actingAsSuperUser()
        ->get(route('igniter.user.user_roles', ['slug' => 'create']))
        ->assertOk();
});

it('loads edit user role page', function(): void {
    $userRole = UserRole::factory()->create();

    actingAsSuperUser()
        ->get(route('igniter.user.user_roles', ['slug' => 'edit/'.$userRole->getKey()]))
        ->assertOk();
});

it('loads user role preview page', function(): void {
    $userRole = UserRole::factory()->create();

    actingAsSuperUser()
        ->get(route('igniter.user.user_roles', ['slug' => 'preview/'.$userRole->getKey()]))
        ->assertOk();
});

it('creates user role', function(): void {
    actingAsSuperUser()
        ->post(route('igniter.user.user_roles', ['slug' => 'create']), [
            'UserRole' => [
                'name' => 'Created User Role',
                'permissions' => [
                    'Admin.Dashboard' => 1,
                    'Admin.Staffs' => 1,
                ],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(UserRole::where('name', 'Created User Role')->exists())->toBeTrue();
});

it('updates user role', function(): void {
    $userRole = UserRole::factory()->create();

    actingAsSuperUser()
        ->post(route('igniter.user.user_roles', ['slug' => 'edit/'.$userRole->getKey()]), [
            'UserRole' => [
                'name' => 'Updated User Role',
                'permissions' => [
                    'Admin.Dashboard' => 1,
                    'Admin.Staffs' => 1,
                ],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(UserRole::where('name', 'Updated User Role')->exists())->toBeTrue();
});

it('deletes user role', function(): void {
    $userRole = UserRole::factory()->create();

    actingAsSuperUser()
        ->post(route('igniter.user.user_roles', ['slug' => 'edit/'.$userRole->getKey()]), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(UserRole::find($userRole->getKey()))->toBeNull();
});

it('bulk deletes user roles', function(): void {
    $userRole = UserRole::factory()->count(5)->create();
    $userRoleIds = $userRole->pluck('user_role_id')->all();

    actingAsSuperUser()
        ->post(route('igniter.user.user_roles'), [
            'checked' => $userRoleIds,
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(UserRole::whereIn('user_role_id',
        $userRoleIds,
    )->exists())->toBeFalse();
});

function actingAsRoleManager(array $permissions = [])
{
    $role = UserRole::factory()->create([
        'permissions' => array_merge(['Admin.StaffRoles' => 1], $permissions),
    ]);

    return actingAsSuperUser(User::factory()->create(['user_role_id' => $role->getKey()]));
}

function saveUserRole($test, ?UserRole $role, array $permissions): TestResponse
{
    return $test->post(route('igniter.user.user_roles', ['slug' => $role instanceof UserRole ? 'edit/'.$role->getKey() : 'create']), [
        'UserRole' => [
            'name' => $role?->name ?? 'New Role',
            'permissions' => $permissions,
        ],
    ], [
        'X-Requested-With' => 'XMLHttpRequest',
        'X-IGNITER-REQUEST-HANDLER' => 'onSave',
    ]);
}

it('rejects unknown and wildcard permissions', function(string $permission): void {
    saveUserRole(actingAsSuperUser(), null, ['Admin.Staffs' => 1, $permission => 1]);

    expect(UserRole::where('name', 'New Role')->exists())->toBeFalse();
})->with(['Admin.Unknown', 'Admin.*', '*']);

it('lets a super user grant any permission', function(): void {
    saveUserRole(actingAsSuperUser(), null, ['Site.Themes' => 1]);

    expect(UserRole::where('name', 'New Role')->first()?->permissions)->toBe(['Site.Themes' => 1]);
});

it('does not let staff grant a permission they do not hold', function(): void {
    $role = UserRole::factory()->create(['permissions' => ['Admin.Orders' => 1]]);

    saveUserRole(actingAsRoleManager(['Admin.Orders' => 1]), $role, ['Admin.Orders' => 1, 'Site.Themes' => 1]);

    expect($role->fresh()->permissions)->toBe(['Admin.Orders' => 1]);
});

it('does not let staff create a role with a permission they do not hold', function(): void {
    saveUserRole(actingAsRoleManager(), null, ['Site.Themes' => 1]);

    expect(UserRole::where('name', 'New Role')->exists())->toBeFalse();
});

it('lets staff grant permissions they hold', function(): void {
    $role = UserRole::factory()->create(['permissions' => ['Admin.Orders' => 1]]);

    saveUserRole(actingAsRoleManager(['Admin.Orders' => 1, 'Admin.Menus' => 1]), $role, ['Admin.Orders' => 1, 'Admin.Menus' => 1]);

    expect($role->fresh()->permissions)->toBe(['Admin.Orders' => 1, 'Admin.Menus' => 1]);
});

it('lets staff keep or remove a permission they do not hold', function(): void {
    $role = UserRole::factory()->create(['permissions' => ['Site.Themes' => 1, 'Admin.Orders' => 1]]);
    $test = actingAsRoleManager();

    saveUserRole($test, $role, ['Site.Themes' => 1, 'Admin.Orders' => -1]);
    expect($role->fresh()->permissions)->toBe(['Site.Themes' => 1, 'Admin.Orders' => -1]);

    saveUserRole($test, $role, ['Admin.Orders' => -1]);
    expect($role->fresh()->permissions)->toBe(['Admin.Orders' => -1]);
});

it('does not let staff change their own role', function(): void {
    $role = UserRole::factory()->create(['permissions' => ['Admin.StaffRoles' => 1, 'Admin.Orders' => 1]]);
    $test = actingAsSuperUser(User::factory()->create(['user_role_id' => $role->getKey()]));

    saveUserRole($test, $role, ['Admin.StaffRoles' => 1]);

    expect($role->fresh()->permissions)->toBe(['Admin.StaffRoles' => 1, 'Admin.Orders' => 1]);
});
