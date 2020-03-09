<?php

namespace Igniter\User\ActivityTypes;

use Admin\Models\Customers_model;
use Igniter\Flame\ActivityLog\Contracts\ActivityInterface;
use Igniter\Flame\ActivityLog\Models\Activity;

class CustomerRegistered implements ActivityInterface
{

    public static function log($customer)
    {
        activity()
            ->logAs(self::getType())
            ->withProperties([
                'customer_id' => $customer->customer_id,
                'full_name' => $customer->full_name,
            ])
            ->performedOn($customer)
            ->causedBy($customer)
            ->log();
    }

    /**
     * {@inheritdoc}
     */
    public function getCauser()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getType()
    {
        return 'customerRegistered';
    }

    public static function getUrl(Activity $activity)
    {
        $url = 'customers';
        if ($activity->subject)
            $url .= '/edit/'.$activity->subject->customer_id;

        return admin_url($url);
    }

    public static function getMessage(Activity $activity)
    {
        return lang('igniter.user::default.login.activity_registered_account');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubjectModel()
    {
        return Customers_model::class;
    }
}