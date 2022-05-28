<?php

namespace Igniter\User\ActivityTypes;

use Igniter\Flame\ActivityLog\Contracts\ActivityInterface;
use Igniter\Flame\ActivityLog\Models\Activity;
use Igniter\Flame\Auth\Models\User;
use Igniter\Main\Models\Customer;

class CustomerRegistered implements ActivityInterface
{
    public $type;

    public $subject;

    public $causer;

    public function __construct(string $type, Customer $subject, User $causer = null)
    {
        $this->type = $type;
        $this->subject = $subject;
        $this->causer = $causer;
    }

    public static function log($customer)
    {
        $recipients = User::isEnabled()
            ->whereIsSuperUser()
            ->get()->map(function ($staff) {
                return $staff->user;
            })->all();

        activity()->pushLog(new static('customerRegistered', $customer), $recipients);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getCauser()
    {
        return $this->causer ?? $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return [
            'customer_id' => $this->subject->getKey(),
            'full_name' => $this->subject->full_name,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubjectModel()
    {
        return Customer::class;
    }

    public static function getTitle(Activity $activity)
    {
        return lang('igniter.user::default.login.activity_registered_account_title');
    }

    public static function getUrl(Activity $activity)
    {
        $url = 'customers';
        if ($activity->subject)
            $url .= '/edit/'.$activity->subject->getKey();

        return admin_url($url);
    }

    public static function getMessage(Activity $activity)
    {
        return lang('igniter.user::default.login.activity_registered_account');
    }
}
