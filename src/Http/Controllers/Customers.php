<?php

namespace Igniter\User\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;
use Igniter\Admin\Facades\Template;
use Igniter\Flame\Exception\FlashException;
use Igniter\User\Facades\Auth;

use function flash;
use function lang;
use function post;

class Customers extends \Igniter\Admin\Classes\AdminController
{
    public array $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
        \Igniter\Admin\Http\Actions\FormController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => \Igniter\User\Models\Customer::class,
            'title' => 'lang:igniter.user::default.customers.text_title',
            'emptyMessage' => 'lang:igniter.user::default.customers.text_empty',
            'defaultSort' => ['customer_id', 'DESC'],
            'configFile' => 'customer',
        ],
    ];

    public array $formConfig = [
        'name' => 'lang:igniter.user::default.customers.text_form_name',
        'model' => \Igniter\User\Models\Customer::class,
        'request' => \Igniter\User\Http\Requests\CustomerRequest::class,
        'create' => [
            'title' => 'lang:igniter::admin.form.create_title',
            'redirect' => 'customers/edit/{customer_id}',
            'redirectClose' => 'customers',
            'redirectNew' => 'customers/create',
        ],
        'edit' => [
            'title' => 'lang:igniter::admin.form.edit_title',
            'redirect' => 'customers/edit/{customer_id}',
            'redirectClose' => 'customers',
            'redirectNew' => 'customers/create',
        ],
        'preview' => [
            'title' => 'lang:igniter::admin.form.preview_title',
            'back' => 'customers',
        ],
        'delete' => [
            'redirect' => 'customers',
        ],
        'configFile' => 'customer',
    ];

    protected null|string|array $requiredPermissions = 'Admin.Customers';

    public static function getSlug()
    {
        return 'customers';
    }

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('customers');
    }

    public function index_onDelete()
    {
        throw_unless($this->authorize('Admin.DeleteCustomers'),
            new FlashException(lang('igniter::admin.alert_user_restricted')),
        );

        return $this->asExtension(\Igniter\Admin\Http\Actions\ListController::class)->index_onDelete();
    }

    public function edit_onDelete($context, $recordId)
    {
        throw_unless($this->authorize('Admin.DeleteCustomers'),
            new FlashException(lang('igniter::admin.alert_user_restricted')),
        );

        return $this->asExtension(\Igniter\Admin\Http\Actions\FormController::class)->edit_onDelete($context, $recordId);
    }

    public function onImpersonate($context, $recordId = null)
    {
        throw_unless($this->authorize('Admin.ImpersonateCustomers'),
            new FlashException(lang('igniter.user::default.customers.alert_login_restricted')),
        );

        $id = post('recordId', $recordId);
        if ($customer = $this->formFindModelObject((int)$id)) {
            Auth::stopImpersonate();
            Auth::impersonate($customer);
            flash()->success(sprintf(lang('igniter.user::default.customers.alert_impersonate_success'), $customer->full_name));
        }
    }

    public function edit_onActivate($context, $recordId = null)
    {
        if ($customer = $this->formFindModelObject((int)$recordId)) {
            $customer->completeActivation($customer->getActivationCode());
            flash()->success(lang('igniter.user::default.customers.alert_activation_success'));
        }

        return $this->redirectBack();
    }

    public function formExtendModel($model)
    {
        if ($model->exists && !$model->is_activated) {
            Template::setButton(lang('igniter.user::default.customers.button_activate'), [
                'class' => 'btn btn-success pull-right',
                'data-request' => 'onActivate',
            ]);
        }
    }
}
