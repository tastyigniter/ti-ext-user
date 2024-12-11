<?php

namespace Igniter\User\Models;

use Igniter\Flame\Database\Casts\Serialize;
use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\Flame\Database\Model;
use InvalidArgumentException;

class UserRole extends Model
{
    use HasFactory;

    /**
     * @var string The database table name
     */
    protected $table = 'admin_user_roles';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'user_role_id';

    public $timestamps = true;

    public $relation = [
        'hasMany' => [
            'users' => [\Igniter\User\Models\User::class, 'foreignKey' => 'user_role_id', 'otherKey' => 'user_role_id'],
        ],
    ];

    protected $casts = [
        'permissions' => Serialize::class,
    ];

    public static function getDropdownOptions()
    {
        return static::dropdown('name');
    }

    public static function listDropdownOptions()
    {
        return self::select('user_role_id', 'name', 'description')
            ->get()
            ->keyBy('user_role_id')
            ->map(function($model) {
                return [$model->name, $model->description];
            });
    }

    public function getStaffCountAttribute($value)
    {
        return $this->users->count();
    }

    public function setPermissionsAttribute($permissions)
    {
        foreach ($permissions ?? [] as $permission => $value) {
            if (!in_array((int)$value, [-1, 0, 1])) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid value "%s" for permission "%s" given.', $value, $permission,
                ));
            }

            if ($value === 0) {
                unset($permissions[$permission]);
            }
        }

        $this->attributes['permissions'] = !empty($permissions) ? serialize($permissions) : '';
    }
}
