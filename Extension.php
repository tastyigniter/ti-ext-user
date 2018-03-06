<?php namespace SamPoyigi\Account;

class Extension extends \System\Classes\BaseExtension
{
    public function registerComponents()
    {
        return [
            'SamPoyigi\Account\Components\Account'      => [
                'code'        => 'account',
                'name'        => 'lang:sampoyigi.account::default.account.component_title',
                'description' => 'lang:sampoyigi.account::default.account.component_desc',
            ],
            'SamPoyigi\Account\Components\ResetPassword'      => [
                'code'        => 'resetPassword',
                'name'        => 'lang:sampoyigi.account::default.reset.component_title',
                'description' => 'lang:sampoyigi.account::default.reset.component_desc',
            ],
            'SamPoyigi\Account\Components\AddressBook'  => [
                'code'        => 'accountAddressBook',
                'name'        => 'lang:sampoyigi.account::default.addressbook.component_title',
                'description' => 'lang:sampoyigi.account::default.addressbook.component_desc',
            ],
            'SamPoyigi\Account\Components\Settings'     => [
                'code'        => 'accountSettings',
                'name'        => 'lang:sampoyigi.account::default.settings.component_title',
                'description' => 'lang:sampoyigi.account::default.settings.component_desc',
            ],
            'SamPoyigi\Account\Components\Orders'       => [
                'code'        => 'accountOrders',
                'name'        => 'lang:sampoyigi.account::default.orders.component_title',
                'description' => 'lang:sampoyigi.account::default.orders.component_desc',
            ],
            'SamPoyigi\Account\Components\Reservations' => [
                'code'        => 'accountReservations',
                'name'        => 'lang:sampoyigi.account::default.reservations.component_title',
                'description' => 'lang:sampoyigi.account::default.reservations.component_desc',
            ],
            'SamPoyigi\Account\Components\Reviews'      => [
                'code'        => 'accountReviews',
                'name'        => 'lang:sampoyigi.account::default.reviews.component_title',
                'description' => 'lang:sampoyigi.account::default.reviews.component_desc',
            ],
            'SamPoyigi\Account\Components\Inbox'        => [
                'code'        => 'accountInbox',
                'name'        => 'lang:sampoyigi.account::default.inbox.component_title',
                'description' => 'lang:sampoyigi.account::default.inbox.component_desc',
            ],
        ];
    }
}
