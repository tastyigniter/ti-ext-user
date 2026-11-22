<?php

declare(strict_types=1);

namespace Igniter\User\Tests\FormWidgets;

use Igniter\Admin\Classes\FormField;
use Igniter\Cart\Http\Controllers\Menus;
use Igniter\Flame\Database\Model;
use Igniter\System\Facades\Assets;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\FormWidgets\PermissionEditor;
use Igniter\User\Models\User;
use Igniter\User\Models\UserRole;
use Mockery;

beforeEach(function(): void {
    $this->model = Mockery::mock(Model::class)->makePartial();
    $this->formField = new FormField('testField', 'Label');
    $this->permissionEditor = new PermissionEditor(resolve(Menus::class), $this->formField, ['model' => $this->model]);
});

it('initializes with correct config', function(): void {
    $this->permissionEditor->initialize();

    expect($this->permissionEditor->mode)->toBeNull();
});

it('renders the correct partial', function(): void {
    $this->permissionEditor->initialize();

    $partial = $this->permissionEditor->render();

    expect($partial)->toBeString();
});

it('prepares variables correctly', function(): void {
    $this->permissionEditor->prepareVars();

    expect($this->permissionEditor->vars['groupedPermissions'])->toBeArray()
        ->and($this->permissionEditor->vars['checkedPermissions'])->toBe([])
        ->and($this->permissionEditor->vars['field'])->toBe($this->formField);
});

it('loads the correct assets', function(): void {
    Assets::shouldReceive('addJs')
        ->with(Mockery::on(fn($url): bool => str_contains((string)$url, 'permissioneditor.js')), 'permissioneditor-js')
        ->once();

    $this->permissionEditor->loadAssets();
});

it('only allows granting permissions the user holds', function(): void {
    $role = UserRole::factory()->create(['permissions' => ['Admin.Orders' => 1]]);
    AdminAuth::shouldReceive('getUser')->andReturn(User::factory()->create(['user_role_id' => $role->getKey()]));

    expect($this->permissionEditor->canGrantPermission('Admin.Orders'))->toBeTrue()
        ->and($this->permissionEditor->canGrantPermission('Site.Themes'))->toBeFalse();
});

it('keeps existing grants the user cannot change when rendering', function(): void {
    $role = UserRole::factory()->create(['permissions' => ['Admin.Orders' => 1]]);
    AdminAuth::shouldReceive('getUser')->andReturn(User::factory()->create(['user_role_id' => $role->getKey()]));
    $this->formField->value = ['Site.Themes' => 1];
    $this->formField->arrayName = 'UserRole';
    $this->permissionEditor->initialize();

    $html = $this->permissionEditor->render();

    expect($html)->toContain('type="hidden" name="UserRole[testField][Site.Themes]" value="1"')
        ->and($html)->not->toContain('type="hidden" name="UserRole[testField][Admin.Orders]"');
});
