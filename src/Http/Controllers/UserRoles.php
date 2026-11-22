<?php

declare(strict_types=1);

namespace Igniter\User\Http\Controllers;

use Igniter\Admin\Classes\AdminController;
use Igniter\Admin\Facades\AdminMenu;
use Igniter\Admin\Http\Actions\FormController;
use Igniter\Admin\Http\Actions\ListController;
use Igniter\Flame\Exception\FlashException;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Http\Requests\UserRoleRequest;
use Igniter\User\Models\User;
use Igniter\User\Models\UserRole;

/**
 * @mixin ListController
 * @mixin FormController
 */
class UserRoles extends AdminController
{
    public array $implement = [
        ListController::class,
        FormController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => UserRole::class,
            'title' => 'lang:igniter.user::default.user_roles.text_title',
            'emptyMessage' => 'lang:igniter.user::default.user_roles.text_empty',
            'defaultSort' => ['user_role_id', 'DESC'],
            'configFile' => 'userrole',
            'back' => 'users',
        ],
    ];

    public array $formConfig = [
        'name' => 'lang:igniter.user::default.user_roles.text_form_name',
        'model' => UserRole::class,
        'request' => UserRoleRequest::class,
        'create' => [
            'title' => 'lang:igniter::admin.form.create_title',
            'redirect' => 'user_roles/edit/{user_role_id}',
            'redirectClose' => 'user_roles',
            'redirectNew' => 'user_roles/create',
        ],
        'edit' => [
            'title' => 'lang:igniter::admin.form.edit_title',
            'redirect' => 'user_roles/edit/{user_role_id}',
            'redirectClose' => 'user_roles',
            'redirectNew' => 'user_roles/create',
        ],
        'preview' => [
            'title' => 'lang:igniter::admin.form.preview_title',
            'back' => 'user_roles',
        ],
        'delete' => [
            'redirect' => 'user_roles',
        ],
        'configFile' => 'userrole',
    ];

    protected null|string|array $requiredPermissions = 'Admin.StaffRoles';

    public static function getSlug(): string
    {
        return 'user_roles';
    }

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('users', 'system');
    }

    public function formBeforeSave(UserRole $model): void
    {
        $user = $this->getUser() ?? AdminAuth::getUser();
        throw_unless($user instanceof User, new FlashException(lang('igniter::admin.alert_user_restricted')));

        throw_if(
            !$user->isSuperUser() && $model->exists && (int)$model->getKey() === (int)$user->user_role_id,
            new FlashException(lang('igniter.user::default.user_roles.alert_cannot_edit_own_role')),
        );
    }
}
