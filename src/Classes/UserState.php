<?php

namespace Igniter\User\Classes;

use Igniter\User\Facades\AdminAuth;
use Igniter\User\Models\UserPreference;

/**
 * Admin User State
 */
class UserState
{
    public const USER_PREFERENCE_KEY = 'admin_users_state';

    public const ONLINE_STATUS = 1;

    public const BACK_SOON_STATUS = 2;

    public const AWAY_STATUS = 3;

    public const CUSTOM_STATUS = 4;

    protected $user;

    protected $defaultStateConfig = [
        'status' => 1,
        'awayMessage' => null,
        'updatedAt' => null,
        'clearAfterMinutes' => 0,
    ];

    protected $stateConfigCache;

    public static function forUser($user = null)
    {
        $instance = new static;
        $instance->user = $user ?: AdminAuth::getUser();

        return $instance;
    }

    public function isAway()
    {
        return $this->getStatus() !== static::ONLINE_STATUS;
    }

    public function isOnline()
    {
        return $this->getStatus() === static::ONLINE_STATUS;
    }

    public function isIdle()
    {
        return $this->getStatus() === static::BACK_SOON_STATUS;
    }

    public function getStatus()
    {
        return (int)$this->getConfig('status');
    }

    public function getStatusName()
    {
        $status = $this->getStatus();
        if ($status === static::CUSTOM_STATUS) {
            return $this->getMessage();
        }

        return array_get(static::getStatusDropdownOptions(), $status);
    }

    public function getMessage()
    {
        return $this->getConfig('awayMessage');
    }

    public function getClearAfterMinutes()
    {
        return (int)$this->getConfig('clearAfterMinutes', 0);
    }

    public function getUpdatedAt()
    {
        return $this->getConfig('updatedAt');
    }

    public function getClearAfterAt()
    {
        if ($this->getStatus() !== static::CUSTOM_STATUS) {
            return null;
        }

        return make_carbon($this->getConfig('updatedAt'))
            ->addMinutes($this->getClearAfterMinutes());
    }

    public static function getStatusDropdownOptions()
    {
        return [
            static::ONLINE_STATUS => 'igniter.user::default.staff_status.text_online',
            static::BACK_SOON_STATUS => 'igniter.user::default.staff_status.text_back_soon',
            static::AWAY_STATUS => 'igniter.user::default.staff_status.text_away',
            static::CUSTOM_STATUS => 'igniter.user::default.staff_status.text_custom_status',
        ];
    }

    public static function getClearAfterMinutesDropdownOptions()
    {
        return [
            1440 => 'igniter.user::default.staff_status.text_clear_tomorrow',
            240 => 'igniter.user::default.staff_status.text_clear_hours',
            30 => 'igniter.user::default.staff_status.text_clear_minutes',
            0 => 'igniter.user::default.staff_status.text_dont_clear',
        ];
    }

    //
    //
    //

    public function updateState(string $status, string $message, int $clearAfterMinutes = 30)
    {
        UserPreference::onUser($this->user)->set(self::USER_PREFERENCE_KEY, array_merge($this->defaultStateConfig, [
            'status' => $status,
            'updatedAt' => now(),
            'awayMessage' => e($message),
            'clearAfterMinutes' => $clearAfterMinutes,
        ]));

        $this->stateConfigCache = null;
    }

    public function getConfig($key = null, $default = null)
    {
        if (is_null($this->stateConfigCache)) {
            $this->stateConfigCache = $this->loadConfigFromPreference();
        }

        $result = array_merge($this->defaultStateConfig, $this->stateConfigCache);
        if (is_null($key)) {
            return $result;
        }

        return array_get($result, $key, $default);
    }

    protected function loadConfigFromPreference()
    {
        if (!$this->user) {
            return [];
        }

        return UserPreference::onUser($this->user)->get(self::USER_PREFERENCE_KEY, []);
    }
}
