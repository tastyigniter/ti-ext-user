<?php namespace Igniter\User;

class Extension extends \System\Classes\BaseExtension
{
    public function registerComponents()
    {
        return [
            'Igniter\User\Components\Session' => [
                'code' => 'session',
                'name' => 'lang:igniter.user::default.session.component_title',
                'description' => 'lang:igniter.user::default.session.component_desc',
            ],
            'Igniter\User\Components\Account' => [
                'code' => 'account',
                'name' => 'lang:igniter.user::default.account.component_title',
                'description' => 'lang:igniter.user::default.account.component_desc',
            ],
            'Igniter\User\Components\ResetPassword' => [
                'code' => 'resetPassword',
                'name' => 'lang:igniter.user::default.reset.component_title',
                'description' => 'lang:igniter.user::default.reset.component_desc',
            ],
            'Igniter\User\Components\AddressBook' => [
                'code' => 'accountAddressBook',
                'name' => 'lang:igniter.user::default.addressbook.component_title',
                'description' => 'lang:igniter.user::default.addressbook.component_desc',
            ],
            'Igniter\User\Components\Reviews' => [
                'code' => 'accountReviews',
                'name' => 'lang:igniter.user::default.reviews.component_title',
                'description' => 'lang:igniter.user::default.reviews.component_desc',
            ],
            'Igniter\User\Components\Inbox' => [
                'code' => 'accountInbox',
                'name' => 'lang:igniter.user::default.inbox.component_title',
                'description' => 'lang:igniter.user::default.inbox.component_desc',
            ],
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'igniter.user::mail.password_reset' => 'Password reset email to customer',
            'igniter.user::mail.password_reset_request' => 'Password reset request email to customer',
            'igniter.user::mail.registration' => 'Registration email to customer',
            'igniter.user::mail.registration_alert' => 'Registration email to admin',
        ];
    }
}
