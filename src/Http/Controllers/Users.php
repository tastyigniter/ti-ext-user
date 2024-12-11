<?php

namespace Igniter\User\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;
use Igniter\Flame\Exception\FlashException;
use Igniter\User\Facades\AdminAuth;

class Users extends \Igniter\Admin\Classes\AdminController
{
    public array $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
        \Igniter\Admin\Http\Actions\FormController::class,
        \Igniter\Local\Http\Actions\LocationAwareController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => \Igniter\User\Models\User::class,
            'title' => 'lang:igniter.user::default.staff.text_title',
            'emptyMessage' => 'lang:igniter.user::default.staff.text_empty',
            'defaultSort' => ['user_id', 'DESC'],
            'configFile' => 'user',
        ],
    ];

    public array $formConfig = [
        'name' => 'lang:igniter.user::default.staff.text_form_name',
        'model' => \Igniter\User\Models\User::class,
        'request' => \Igniter\User\Http\Requests\UserRequest::class,
        'create' => [
            'title' => 'lang:igniter::admin.form.create_title',
            'redirect' => 'users/edit/{user_id}',
            'redirectClose' => 'users',
            'redirectNew' => 'users/create',
        ],
        'edit' => [
            'title' => 'lang:igniter::admin.form.edit_title',
            'redirect' => 'users/edit/{user_id}',
            'redirectClose' => 'users',
            'redirectNew' => 'users/create',
        ],
        'preview' => [
            'title' => 'lang:igniter::admin.form.preview_title',
            'back' => 'users',
        ],
        'delete' => [
            'redirect' => 'users',
        ],
        'configFile' => 'user',
    ];

    public array $locationConfig = [
        'addAbsenceConstraint' => false,
    ];

    protected null|string|array $requiredPermissions = 'Admin.Staffs';

    public static function getSlug()
    {
        return 'users';
    }

    public function __construct()
    {
        parent::__construct();

        if ($this->action == 'account') {
            $this->requiredPermissions = null;
        }

        AdminMenu::setContext('users', 'system');
    }

    public function account()
    {
        $this->asExtension('LocationAwareController')?->setConfig(['applyScopeOnFormQuery' => false]);

        $this->asExtension('FormController')->edit('account', $this->getUser()->getKey());

        return $this->makeView('edit');
    }

    public function account_onSave()
    {
        $this->asExtension('LocationAwareController')?->setConfig(['applyScopeOnFormQuery' => false]);

        $result = $this->asExtension('FormController')->edit_onSave('account', $this->currentUser->user_id);

        $usernameChanged = $this->currentUser->username != post('User[username]');
        $passwordChanged = strlen(post('User[password]'));
        $languageChanged = $this->currentUser->language != post('User[language_id]');
        $emailChanged = $this->currentUser->email != post('User[email]');
        if ($emailChanged || $passwordChanged) {
            AdminAuth::logout();

            return redirect('/logout');
        }

        if ($usernameChanged || $languageChanged) {
            $this->currentUser->reload()->reloadRelations();
            AdminAuth::login($this->currentUser, true);
        }

        return $result;
    }

    public function index_onDelete()
    {
        throw_unless($this->authorize('Admin.DeleteStaffs'),
            new FlashException(lang('igniter::admin.alert_user_restricted')),
        );

        return $this->asExtension(\Igniter\Admin\Http\Actions\ListController::class)->index_onDelete();
    }

    public function edit_onDelete($context, $recordId)
    {
        throw_unless($this->authorize('Admin.DeleteStaffs'),
            new FlashException(lang('igniter::admin.alert_user_restricted')),
        );

        return $this->asExtension(\Igniter\Admin\Http\Actions\FormController::class)->edit_onDelete($context, $recordId);
    }

    public function onImpersonate($context, $recordId = null)
    {
        throw_unless($this->authorize('Admin.Impersonate'),
            new FlashException(lang('igniter.user::default.staff.alert_login_restricted')),
        );

        $id = post('recordId', $recordId);
        if ($user = $this->formFindModelObject((int)$id)) {
            AdminAuth::stopImpersonate();
            AdminAuth::impersonate($user);
            flash()->success(sprintf(lang('igniter.user::default.staff.alert_impersonate_success'), $user->name));
        }

        return $this->redirect('dashboard');
    }

    public function listExtendQuery($query)
    {
        if (!AdminAuth::isSuperUser()) {
            $query->whereNotSuperUser();
        }
    }

    public function formExtendQuery($query)
    {
        if (!AdminAuth::isSuperUser()) {
            $query->whereNotSuperUser();
        }
    }

    public function formExtendFields($form)
    {
        if (!AdminAuth::isSuperUser()) {
            $form->removeField('user_role_id');
            $form->removeField('status');
            $form->removeField('super_user');
        }
    }
}
