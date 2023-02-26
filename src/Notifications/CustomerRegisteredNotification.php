<?php

namespace Igniter\User\Notifications;

use Igniter\Admin\Models\User;
use Igniter\System\Classes\Notification;

class CustomerRegisteredNotification extends Notification
{
    public function getRecipients(): array
    {
        return User::isEnabled()
            ->whereIsSuperUser()
            ->get()->all();
    }

    public function getTitle(): string
    {
        return lang('igniter.user::default.login.notify_registered_account_title');
    }

    public function getUrl(): string
    {
        $url = 'customers';
        if ($this->subject)
            $url .= '/edit/'.$this->subject->getKey();

        return admin_url($url);
    }

    public function getMessage(): string
    {
        return sprintf(lang('igniter.user::default.login.notify_registered_account'), $this->subject->full_name);
    }
}
