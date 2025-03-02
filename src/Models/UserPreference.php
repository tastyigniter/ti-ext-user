<?php

namespace Igniter\User\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Exception\SystemException;
use Igniter\User\Facades\AdminAuth;

class UserPreference extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'admin_user_preferences';

    protected $casts = [
        'user_id' => 'integer',
        'value' => 'json',
    ];

    /**
     * @var \Igniter\User\Auth\Models\User A user who owns the preferences
     */
    public $userContext;

    protected static $cache = [];

    public static function onUser($user = null)
    {
        $self = new static;
        $self->userContext = $user ?: $self->resolveUser();

        return $self;
    }

    public static function findRecord($item, $user = null)
    {
        return static::applyItemAndUser($item, $user)->first();
    }

    public function resolveUser()
    {
        $user = AdminAuth::getUser();
        if (!$user) {
            throw new SystemException(lang('igniter::admin.alert_user_not_logged'));
        }

        return $user;
    }

    public function get($item, $default = null)
    {
        if (!($user = $this->userContext)) {
            return $default;
        }

        $cacheKey = $this->getCacheKey($item, $user);

        if (array_key_exists($cacheKey, static::$cache)) {
            return static::$cache[$cacheKey];
        }

        $record = static::findRecord($item, $user);
        if (!$record) {
            return static::$cache[$cacheKey] = $default;
        }

        return static::$cache[$cacheKey] = $record->value;
    }

    public function set($item, $value)
    {
        if (!$user = $this->userContext) {
            return false;
        }

        $record = static::findRecord($item, $user);
        if (!$record) {
            $record = new static;
            $record->item = $item;
            $record->user_id = $user->user_id;
        }

        $record->value = $value;
        $record->save();

        $cacheKey = $this->getCacheKey($item, $user);
        static::$cache[$cacheKey] = $value;

        return true;
    }

    public function reset($item)
    {
        if (!$user = $this->userContext) {
            return false;
        }

        $record = static::findRecord($item, $user);
        if (!$record) {
            return false;
        }

        $record->delete();

        $cacheKey = $this->getCacheKey($item, $user);
        unset(static::$cache[$cacheKey]);

        return true;
    }

    public function scopeApplyItemAndUser($query, $item, $user = null)
    {
        $query = $query->where('item', $item);

        if ($user) {
            $query = $query->where('user_id', $user->user_id);
        }

        return $query;
    }

    /**
     * Builds a cache key for the preferences record.
     * @return string
     */
    protected function getCacheKey($item, $user)
    {
        return $user->user_id.'-'.$item;
    }
}
