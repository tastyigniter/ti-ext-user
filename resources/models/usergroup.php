<?php

$config['list']['toolbar'] = [
    'buttons' => [
        'create' => [
            'label' => 'lang:igniter::admin.button_new',
            'class' => 'btn btn-primary',
            'href' => 'user_groups/create',
        ],
    ],
];

$config['list']['bulkActions'] = [
    'delete' => [
        'label' => 'lang:igniter::admin.button_delete',
        'class' => 'btn btn-light text-danger',
        'data-request-confirm' => 'lang:igniter::admin.alert_warning_confirm',
    ],
];

$config['list']['columns'] = [
    'edit' => [
        'type' => 'button',
        'iconCssClass' => 'fa fa-pencil',
        'attributes' => [
            'class' => 'btn btn-edit',
            'href' => 'user_groups/edit/{user_group_id}',
        ],
    ],
    'user_group_name' => [
        'label' => 'lang:igniter::admin.label_name',
        'type' => 'text',
        'searchable' => true,
    ],
    'description' => [
        'label' => 'lang:igniter::admin.label_description',
        'type' => 'text',
        'searchable' => true,
    ],
    'staff_count' => [
        'label' => 'lang:igniter.user::default.user_groups.column_users',
        'type' => 'text',
        'sortable' => false,
    ],
    'user_group_id' => [
        'label' => 'lang:igniter::admin.column_id',
        'invisible' => true,
    ],
    'created_at' => [
        'label' => 'lang:igniter::admin.column_date_added',
        'invisible' => true,
        'type' => 'datetime',
    ],
    'updated_at' => [
        'label' => 'lang:igniter::admin.column_date_updated',
        'invisible' => true,
        'type' => 'datetime',
    ],
];

$config['form']['toolbar'] = [
    'buttons' => [
        'save' => [
            'label' => 'lang:igniter::admin.button_save',
            'context' => ['create', 'edit'],
            'partial' => 'form/toolbar_save_button',
            'class' => 'btn btn-primary',
            'data-request' => 'onSave',
            'data-progress-indicator' => 'igniter::admin.text_saving',
        ],
        'delete' => [
            'label' => 'lang:igniter::admin.button_icon_delete',
            'class' => 'btn btn-danger',
            'data-request' => 'onDelete',
            'data-request-data' => "_method:'DELETE'",
            'data-request-confirm' => 'lang:igniter::admin.alert_warning_confirm',
            'data-progress-indicator' => 'igniter::admin.text_deleting',
            'context' => ['edit'],
        ],
    ],
];

$config['form']['fields'] = [
    'user_group_name' => [
        'label' => 'lang:igniter::admin.label_name',
        'type' => 'text',
    ],
    'description' => [
        'label' => 'lang:igniter::admin.label_description',
        'type' => 'textarea',
    ],
    'auto_assign' => [
        'label' => 'lang:igniter.user::default.user_groups.label_auto_assign',
        'type' => 'switch',
        'comment' => 'lang:igniter.user::default.user_groups.help_auto_assign',
    ],
    'auto_assign_mode' => [
        'label' => 'lang:igniter.user::default.user_groups.label_assignment_mode',
        'type' => 'radiolist',
        'span' => 'left',
        'default' => 1,
        'options' => [
            1 => ['igniter.user::default.user_groups.text_round_robin', 'igniter.user::default.user_groups.help_round_robin'],
            2 => ['igniter.user::default.user_groups.text_load_balanced', 'igniter.user::default.user_groups.help_load_balanced'],
        ],
        'trigger' => [
            'action' => 'show',
            'field' => 'auto_assign',
            'condition' => 'checked',
        ],
    ],
    'auto_assign_limit' => [
        'label' => 'lang:igniter.user::default.user_groups.label_load_balanced_limit',
        'type' => 'number',
        'default' => 20,
        'comment' => 'lang:igniter.user::default.user_groups.help_load_balanced_limit',
        'trigger' => [
            'action' => 'show',
            'field' => 'auto_assign',
            'condition' => 'checked',
        ],
    ],
    'auto_assign_availability' => [
        'label' => 'lang:igniter.user::default.user_groups.label_assignment_availability',
        'type' => 'switch',
        'default' => true,
        'comment' => 'lang:igniter.user::default.user_groups.help_assignment_availability',
        'trigger' => [
            'action' => 'show',
            'field' => 'auto_assign',
            'condition' => 'checked',
        ],
    ],
];

return $config;
