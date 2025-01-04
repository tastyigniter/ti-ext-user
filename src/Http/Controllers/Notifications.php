<?php

namespace Igniter\User\Http\Controllers;

/**
 * @mixin \Igniter\Admin\Http\Actions\ListController
 */
class Notifications extends \Igniter\Admin\Classes\AdminController
{
    public array $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => \Igniter\User\Models\Notification::class,
            'title' => 'lang:igniter.user::default.notifications.text_title',
            'emptyMessage' => 'lang:igniter.user::default.notifications.text_empty',
            'defaultSort' => ['updated_at', 'DESC'],
            'configFile' => 'notification',
        ],
    ];

    protected null|string|array $requiredPermissions = 'Admin.Notifications';

    public static function getSlug()
    {
        return 'notifications';
    }

    public function onMarkAsRead()
    {
        $this->currentUser->unreadNotifications()->update(['read_at' => now()]);

        return $this->redirectBack();
    }

    public function listExtendQuery($query)
    {
        $query->whereNotifiable($this->currentUser);
    }
}
