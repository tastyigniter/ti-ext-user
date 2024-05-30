<?php

namespace Igniter\User\Notifications;

use Igniter\User\Classes\Notification;
use Igniter\User\Models\User;

class CustomerRegisteredNotification extends Notification
{
    public function getRecipients(): array
    {
        return User::query()->isEnabled()
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
        if ($this->subject) {
            $url .= '/edit/'.$this->subject->getKey();
        }

        return admin_url($url);
    }

    public function getMessage(): string
    {
        return sprintf(lang('igniter.user::default.login.notify_registered_account'), $this->subject->full_name);
    }

    public function getIcon(): ?string
    {
        return 'fa-user';
    }

    public function getAlias(): string
    {
        return 'customer-registered';
    }
}
