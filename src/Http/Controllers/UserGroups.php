<?php

namespace Igniter\User\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;
use Igniter\User\Models\UserGroup;

class UserGroups extends \Igniter\Admin\Classes\AdminController
{
    public $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
        \Igniter\Admin\Http\Actions\FormController::class,
    ];

    public $listConfig = [
        'list' => [
            'model' => \Igniter\User\Models\UserGroup::class,
            'title' => 'lang:igniter.user::default.user_groups.text_title',
            'emptyMessage' => 'lang:igniter.user::default.user_groups.text_empty',
            'defaultSort' => ['user_group_id', 'DESC'],
            'configFile' => 'usergroup',
        ],
    ];

    public $formConfig = [
        'name' => 'lang:igniter.user::default.user_groups.text_form_name',
        'model' => \Igniter\User\Models\UserGroup::class,
        'request' => \Igniter\User\Requests\UserGroupRequest::class,
        'create' => [
            'title' => 'lang:igniter::admin.form.create_title',
            'redirect' => 'user_groups/edit/{user_group_id}',
            'redirectClose' => 'user_groups',
            'redirectNew' => 'user_groups/create',
        ],
        'edit' => [
            'title' => 'lang:igniter::admin.form.edit_title',
            'redirect' => 'user_groups/edit/{user_group_id}',
            'redirectClose' => 'user_groups',
            'redirectNew' => 'user_groups/create',
        ],
        'preview' => [
            'title' => 'lang:igniter::admin.form.preview_title',
            'redirect' => 'user_groups',
        ],
        'delete' => [
            'redirect' => 'user_groups',
        ],
        'configFile' => 'usergroup',
    ];

    protected $requiredPermissions = 'Admin.StaffGroups';

    public static function getSlug()
    {
        return 'user_groups';
    }

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('users', 'system');
    }

    public function formAfterSave()
    {
        UserGroup::syncAutoAssignStatus();
    }
}
