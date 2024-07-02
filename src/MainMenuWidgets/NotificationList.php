<?php

namespace Igniter\User\MainMenuWidgets;

class NotificationList extends \Igniter\Admin\Classes\BaseMainMenuWidget
{
    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('notificationlist/notificationlist');
    }

    public function prepareVars()
    {
        $user = $this->getController()->getUser();
        $this->vars['unreadCount'] = $user->unreadNotifications()->count();
    }

    public function onDropdownOptions()
    {
        $user = $this->getController()->getUser();
        $this->vars['notifications'] = $user->notifications()->get();

        return [
            '#'.$this->getId('options') => $this->makePartial('notificationlist/items'),
        ];
    }

    public function onMarkAsRead()
    {
        $user = $this->getController()->getUser();

        $user->unreadNotifications()->update(['read_at' => now()]);

        // Return a partial if item has a path defined
        return [
            '~#'.$this->getId() => $this->render(),
        ];
    }
}
