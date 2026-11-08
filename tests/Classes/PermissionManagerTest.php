<?php

declare(strict_types=1);

namespace Igniter\User\Tests\Classes;

use Igniter\System\Classes\ExtensionManager;
use Igniter\User\Classes\PermissionManager;
use Mockery;

it('returns empty list when no permissions are registered', function(): void {
    $extensionManager = Mockery::mock(ExtensionManager::class);
    $extensionManager->shouldReceive('getRegistrationMethodValues')->with('registerPermissions')->andReturn([]);
    app()->instance(ExtensionManager::class, $extensionManager);
    $permissionManager = new PermissionManager;

    $result = $permissionManager->listPermissions();

    expect($result)->toBeEmpty();
});

it('returns registered permissions', function(): void {
    $extensionManager = Mockery::mock(ExtensionManager::class);
    $extensionManager->shouldReceive('getRegistrationMethodValues')->with('registerPermissions')->andReturn([
        'owner' => 'not an array',
    ]);
    app()->instance(ExtensionManager::class, $extensionManager);
    $permissionManager = new PermissionManager;
    $permissionManager->registerPermissions('testOwner', [
        'test.another-code' => [
            'label' => 'Test Another Permission',
            'group' => 'Test Group',
            'priority' => 99,
        ],
        'test.code' => [
            'label' => 'Test Permission',
            'group' => 'Test Group',
            'priority' => 9,
        ],
    ]);

    $result = $permissionManager->listPermissions();

    expect($result)->toBeGreaterThanOrEqual(1)
        ->and($result[0]->code)->toBe('test.code')
        ->and($result[0]->label)->toBe('Test Permission')
        ->and($result[0]->group)->toBe('Test Group');
});

it('returns registered permissions from extensions', function(): void {
    $permissionManager = new PermissionManager;

    $result = $permissionManager->listPermissions();

    expect($result)->not->toBeEmpty();
});

it('returns grouped permissions', function(): void {
    $permissionManager = new PermissionManager;
    $permissionManager->registerPermissions('testOwner', [
        'test.code' => [
            'label' => 'Test Permission',
            'group' => 'Test Group',
        ],
        'test.another-code' => [
            'label' => 'Test Permission',
        ],
    ]);

    $result = $permissionManager->listGroupedPermissions();

    expect($result)->toHaveKey('test group')
        ->and($result['test group'])->toHaveCount(1)
        ->and($result['test group'][0]->code)->toBe('test.code');
});

it('checks permission with starts with wildcard', function(): void {
    $permissionManager = new PermissionManager;
    $permissions = ['test.*' => 1];

    $result = $permissionManager->checkPermission($permissions, ['test.view'], false);

    expect($result)->toBeTrue();
});

it('checks permission with ends with wildcard', function(): void {
    $permissionManager = new PermissionManager;
    $permissions = ['*.view' => 1];

    $result = $permissionManager->checkPermission($permissions, ['test.view'], false);

    expect($result)->toBeTrue();
});

it('checks permission with exact match', function(): void {
    $permissionManager = new PermissionManager;
    $permissions = ['test.view' => 1];

    $result = $permissionManager->checkPermission($permissions, ['test.view'], false);

    expect($result)->toBeTrue();
});

it('returns false when permission is not matched', function(): void {
    $permissionManager = new PermissionManager;
    $permissions = ['test.view' => 1];

    $result = $permissionManager->checkPermission($permissions, ['test.edit'], true);

    expect($result)->toBeFalse();

    $result = $permissionManager->checkPermission($permissions, ['test.edit'], false);

    expect($result)->toBeFalse();
});

it('does not treat permissions sharing a prefix as equivalent', function(): void {
    $permissionManager = new PermissionManager;

    expect($permissionManager->checkPermission(['Admin.ImpersonateCustomers' => 1], ['Admin.Impersonate'], false))->toBeFalse()
        ->and($permissionManager->checkPermission(['Admin.ImpersonateCustomers' => 1], ['Admin.Impersonate'], true))->toBeFalse()
        ->and($permissionManager->checkPermission(['Admin.Impersonate' => 1], ['Admin.ImpersonateCustomers'], false))->toBeFalse()
        ->and($permissionManager->checkPermission(['Admin.Impersonate' => 1], ['Admin.ImpersonateCustomers'], true))->toBeFalse();
});

it('does not treat permissions sharing a suffix as equivalent', function(): void {
    $permissionManager = new PermissionManager;

    expect($permissionManager->checkPermission(['Admin.DeleteOrders' => 1], ['Orders'], false))->toBeFalse()
        ->and($permissionManager->checkPermission(['Orders' => 1], ['Admin.DeleteOrders'], false))->toBeFalse();
});

it('does not treat a parent namespace as granting its children without a wildcard', function(): void {
    $permissionManager = new PermissionManager;

    expect($permissionManager->checkPermission(['Igniter.Api' => 1], ['Igniter.Api.Manage'], false))->toBeFalse()
        ->and($permissionManager->checkPermission(['Igniter.Api.Manage' => 1], ['Igniter.Api'], false))->toBeFalse();
});

it('matches a required wildcard permission against any granted permission in the namespace', function(): void {
    $permissionManager = new PermissionManager;

    expect($permissionManager->checkPermission(['Igniter.Pages.Manage' => 1], ['Igniter.Pages.*'], false))->toBeTrue()
        ->and($permissionManager->checkPermission(['Igniter.PagesOther.Manage' => 1], ['Igniter.Pages.*'], false))->toBeFalse();
});

it('does not match a single asterisk as a grant-all wildcard', function(): void {
    $permissionManager = new PermissionManager;

    expect($permissionManager->checkPermission(['*' => 1], ['Admin.Orders'], false))->toBeFalse()
        ->and($permissionManager->checkPermission(['Admin.Orders' => 1], ['*'], false))->toBeFalse();
});

it('ignores permissions that are not granted', function(): void {
    $permissionManager = new PermissionManager;

    expect($permissionManager->checkPermission(['Admin.Orders' => 0], ['Admin.Orders'], false))->toBeFalse()
        ->and($permissionManager->checkPermission(['Admin.*' => -1], ['Admin.Orders'], false))->toBeFalse();
});

it('checks all permissions when check all is enabled', function(): void {
    $permissionManager = new PermissionManager;
    $permissions = ['Admin.Orders' => 1, 'Admin.Staffs' => 1];

    expect($permissionManager->checkPermission($permissions, ['Admin.Orders', 'Admin.Staffs'], true))->toBeTrue()
        ->and($permissionManager->checkPermission($permissions, ['Admin.Orders', 'Admin.Impersonate'], true))->toBeFalse()
        ->and($permissionManager->checkPermission($permissions, ['Admin.Impersonate', 'Admin.Staffs'], false))->toBeTrue();
});

it('executes registered callback functions', function(): void {
    $extensionManager = Mockery::mock(ExtensionManager::class);
    $extensionManager->shouldReceive('getRegistrationMethodValues')->with('registerPermissions')->andReturn([]);
    app()->instance(ExtensionManager::class, $extensionManager);
    $permissionManager = new PermissionManager;
    $callback = function($manager): void {
        $manager->registerPermissions('testOwner', [
            'test.code' => [
                'description' => 'Test Permission',
                'group' => 'Test Group',
            ],
        ]);
    };

    $permissionManager->registerCallback($callback);
    $permissionManager->listPermissions();

    $permissions = $permissionManager->listPermissions();
    expect($permissions)->toHaveCount(1)
        ->and($permissions[0]->code)->toBe('test.code')
        ->and($permissions[0]->label)->toBe('Test Permission')
        ->and($permissions[0]->group)->toBe('Test Group');
});
