<?php

namespace Igniter\User\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;
use Igniter\User\Models\CustomerGroup;

class CustomerGroups extends \Igniter\Admin\Classes\AdminController
{
    public $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
        \Igniter\Admin\Http\Actions\FormController::class,
    ];

    public $listConfig = [
        'list' => [
            'model' => \Igniter\User\Models\CustomerGroup::class,
            'title' => 'lang:igniter.user::default.customer_groups.text_title',
            'emptyMessage' => 'lang:igniter.user::default.customer_groups.text_empty',
            'defaultSort' => ['customer_group_id', 'DESC'],
            'configFile' => 'customergroup',
            'back' => 'customers',
        ],
    ];

    public $formConfig = [
        'name' => 'lang:igniter.user::default.customer_groups.text_form_name',
        'model' => \Igniter\User\Models\CustomerGroup::class,
        'request' => \Igniter\User\Requests\CustomerGroupRequest::class,
        'create' => [
            'title' => 'lang:admin::lang.form.create_title',
            'redirect' => 'customer_groups/edit/{customer_group_id}',
            'redirectClose' => 'customer_groups',
            'redirectNew' => 'customer_groups/create',
        ],
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'customer_groups/edit/{customer_group_id}',
            'redirectClose' => 'customer_groups',
            'redirectNew' => 'customer_groups/create',
        ],
        'preview' => [
            'title' => 'lang:admin::lang.form.preview_title',
            'back' => 'customer_groups',
        ],
        'delete' => [
            'redirect' => 'customer_groups',
        ],
        'configFile' => 'customergroup',
    ];

    protected $requiredPermissions = 'Admin.CustomerGroups';

    public static function getSlug()
    {
        return 'customer_groups';
    }

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('customers', 'users');
    }

    public function index_onSetDefault()
    {
        if (CustomerGroup::updateDefault(post('default'))) {
            flash()->success(sprintf(lang('admin::lang.alert_success'), lang('igniter.user::default.customer_groups.alert_set_default')));
        }

        return $this->refreshList('list');
    }

    public function listOverrideColumnValue($record, $column, $alias = null)
    {
        if ($column->type != 'button') {
            return null;
        }

        if ($column->columnName != 'default') {
            return null;
        }

        $attributes = $column->attributes;
        $column->iconCssClass = 'fa fa-star-o';
        if ($record->isDefault()) {
            $column->iconCssClass = 'fa fa-star';
        }

        return $attributes;
    }
}
