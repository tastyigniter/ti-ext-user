<?php

namespace Igniter\User\Tests\FormWidgets;

use Igniter\Admin\Classes\FormField;
use Igniter\Cart\Http\Controllers\Menus;
use Igniter\Flame\Database\Model;
use Igniter\System\Facades\Assets;
use Igniter\User\FormWidgets\PermissionEditor;
use Mockery;

beforeEach(function() {
    $this->model = Mockery::mock(Model::class)->makePartial();
    $this->formField = new FormField('testField', 'Label');
    $this->permissionEditor = new PermissionEditor(resolve(Menus::class), $this->formField, ['model' => $this->model]);
});

it('initializes with correct config', function() {
    $this->permissionEditor->initialize();

    expect($this->permissionEditor->mode)->toBeNull();
});

it('renders the correct partial', function() {
    $this->permissionEditor->initialize();

    $partial = $this->permissionEditor->render();

    expect($partial)->toBeString();
});

it('prepares variables correctly', function() {
    $this->permissionEditor->prepareVars();

    expect($this->permissionEditor->vars['groupedPermissions'])->toBeArray()
        ->and($this->permissionEditor->vars['checkedPermissions'])->toBe([])
        ->and($this->permissionEditor->vars['field'])->toBe($this->formField);
});

it('loads the correct assets', function() {
    Assets::shouldReceive('addJs')
        ->with(Mockery::on(function($url) {
            return str_contains($url, 'permissioneditor.js');
        }), 'permissioneditor-js')
        ->once();

    $this->permissionEditor->loadAssets();
});
