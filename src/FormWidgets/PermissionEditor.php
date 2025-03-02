<?php

namespace Igniter\User\FormWidgets;

use Igniter\Admin\Classes\BaseFormWidget;
use Igniter\User\Classes\PermissionManager;

/**
 * User group permission editor
 * This widget is used by the system internally on the Users / User Groups pages.
 */
class PermissionEditor extends BaseFormWidget
{
    public function initialize()
    {
        $this->fillFromConfig([
            'mode',
        ]);
    }

    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('permissioneditor/permissioneditor');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['groupedPermissions'] = $this->listPermissions();
        $this->vars['checkedPermissions'] = (array)$this->formField->value;
        $this->vars['field'] = $this->formField;
        $this->vars['tabs'] = $this->tabs;
        $this->vars['actionCssClasses'] = $this->actionCssClasses;
    }

    public function loadAssets()
    {
        $this->addJs('permissioneditor.js', 'permissioneditor-js');
    }

    /**
     * @return array
     */
    protected function listPermissions()
    {
        return resolve(PermissionManager::class)->listGroupedPermissions();
    }
}
