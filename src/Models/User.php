<?php

namespace Igniter\User\Models;

use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Local\Models\Concerns\Locationable;
use Igniter\System\Models\Concerns\SendsMailTemplate;
use Igniter\System\Models\Concerns\Switchable;
use Igniter\User\Auth\Models\User as AuthUserModel;
use Igniter\User\Classes\PermissionManager;
use Igniter\User\Classes\UserState;
use Igniter\User\Models\Concerns\SendsInvite;

/**
 * Users Model Class
 *
 * @property int $user_id
 * @property string $name
 * @property string|null $email
 * @property int|null $user_role_id
 * @property int|null $language_id
 * @property bool $status
 * @property int $sale_permission
 * @property string $username
 * @property string $password
 * @property bool|null $super_user
 * @property string|null $reset_code
 * @property \Illuminate\Support\Carbon|null $reset_time
 * @property string|null $activation_code
 * @property string|null $remember_token
 * @property bool|null $is_activated
 * @property \Illuminate\Support\Carbon|null $activated_at
 * @property \Illuminate\Support\Carbon|null $last_login
 * @property string|null $last_seen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $invited_at
 * @property int|null $vendor_id
 * @property-read mixed $avatar_url
 * @property-read mixed $full_name
 * @property-read mixed $staff_email
 * @property-read mixed $staff_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Igniter\User\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Igniter\Api\Models\Token> $tokens
 * @property-read int|null $tokens_count
 * @mixin \Igniter\Flame\Database\Model
 */
class User extends AuthUserModel
{
    use HasFactory;
    use Locationable;
    use Purgeable;
    use SendsInvite;
    use SendsMailTemplate;
    use Switchable;

    const LOCATIONABLE_RELATION = 'locations';

    /**
     * @var string The database table name
     */
    protected $table = 'admin_users';

    protected $primaryKey = 'user_id';

    public $timestamps = true;

    protected $fillable = ['username', 'super_user'];

    protected $appends = ['full_name'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
        'user_role_id' => 'integer',
        'sale_permission' => 'integer',
        'language_id' => 'integer',
        'super_user' => 'boolean',
        'is_activated' => 'boolean',
        'reset_time' => 'datetime',
        'invited_at' => 'datetime',
        'activated_at' => 'datetime',
        'last_login' => 'datetime',
    ];

    public $relation = [
        'hasMany' => [
            'assignable_logs' => [\Igniter\User\Models\AssignableLog::class, 'foreignKey' => 'assignee_id'],
        ],
        'belongsTo' => [
            'role' => [\Igniter\User\Models\UserRole::class, 'foreignKey' => 'user_role_id'],
            'language' => [\Igniter\System\Models\Language::class],
        ],
        'belongsToMany' => [
            'groups' => [\Igniter\User\Models\UserGroup::class, 'table' => 'admin_users_groups'],
        ],
        'morphToMany' => [
            'locations' => [\Igniter\Local\Models\Location::class, 'name' => 'locationable'],
        ],
    ];

    protected $purgeable = ['password_confirm'];

    public function getStaffNameAttribute()
    {
        return $this->name;
    }

    public function getStaffEmailAttribute()
    {
        return $this->email;
    }

    public function getFullNameAttribute($value)
    {
        return $this->name;
    }

    public function getAvatarUrlAttribute()
    {
        return '//www.gravatar.com/avatar/'.md5(strtolower(trim($this->email))).'.png?d=mm';
    }

    public function getSalePermissionAttribute($value)
    {
        return $value ?: 1;
    }

    public static function getDropdownOptions()
    {
        return static::whereIsEnabled()->dropdown('name');
    }

    //
    // Scopes
    //

    public function scopeWhereNotSuperUser($query)
    {
        $query->where('super_user', '!=', 1)->orWhereNull('super_user');
    }

    public function scopeWhereIsSuperUser($query)
    {
        $query->where('super_user', 1);
    }

    //
    // Events
    //

    public function afterLogin()
    {
        app('translator.localization')->setSessionLocale(
            optional($this->language)->code ?? app()->getLocale(),
        );

        $this->query()
            ->whereKey($this->getKey())
            ->update(['last_login' => now()]);
    }

    public function extendUserQuery($query)
    {
        $query
            ->with(['role', 'groups', 'locations'])
            ->isEnabled();
    }

    public function isSuperUser()
    {
        return $this->super_user == 1;
    }

    //
    // Permissions
    //

    public function hasAnyPermission($permissions)
    {
        return $this->hasPermission($permissions, false);
    }

    public function hasPermission($permissions, $checkAll = true)
    {
        // Bail out if the user is a superuser
        if ($this->isSuperUser()) {
            return true;
        }

        $staffPermissions = $this->getPermissions();

        if (is_string($permissions) && str_contains($permissions, ',')) {
            $permissions = explode(',', $permissions);
        }

        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        if (resolve(PermissionManager::class)->checkPermission(
            $staffPermissions, $permissions, $checkAll)
        ) {
            return true;
        }

        return false;
    }

    public function getPermissions()
    {
        $role = $this->role;

        $permissions = [];
        if ($role && is_array($role->permissions)) {
            $permissions = $role->permissions;
        }

        return $permissions;
    }

    //
    // Location
    //

    public function mailGetRecipients($type)
    {
        return match ($type) {
            'staff' => [
                [$this->email, $this->full_name],
            ],
            'admin' => [
                [setting('site_email'), setting('site_name')],
            ],
            default => [],
        };
    }

    public function mailGetData()
    {
        $model = $this->fresh();

        return array_merge($model->toArray(), [
            'staff' => $model,
            'staff_name' => $model->name,
            'staff_email' => $model->email,
            'username' => $model->username,
        ]);
    }

    //
    // Assignment
    //

    public function canAssignTo()
    {
        return !UserState::forUser($this->user)->isAway();
    }

    public function hasGlobalAssignableScope()
    {
        return $this->sale_permission === 1;
    }

    public function hasGroupAssignableScope()
    {
        return $this->sale_permission === 2;
    }

    public function hasRestrictedAssignableScope()
    {
        return $this->sale_permission === 3;
    }

    //
    // Helpers
    //

    protected function mailSendInvite(array $vars = [])
    {
        $this->mailSend('igniter.user::mail.invite', 'staff', $vars);
    }

    public function mailSendResetPasswordRequest(array $vars = [])
    {
        $vars = array_merge([
            'reset_link' => null,
        ], $vars);

        $this->mailSend('igniter.user::mail.admin_password_reset_request', 'staff', $vars);
    }

    public function mailSendResetPassword(array $vars = [])
    {
        $vars = array_merge([
            'login_link' => null,
        ], $vars);

        $this->mailSend('igniter.user::mail.admin_password_reset', 'staff', $vars);
    }

    /**
     * Return the dates of all staff
     * @return array
     */
    public function getUserDates()
    {
        return $this->pluckDates('created_at');
    }

    public function getLocale()
    {
        return optional($this->language)->code;
    }

    /**
     * Create a new or update existing user locations
     *
     * @param array $locations
     *
     * @return bool
     */
    public function addLocations($locations = [])
    {
        return $this->locations()->sync($locations);
    }

    /**
     * Create a new or update existing user groups
     *
     * @param array $groups
     *
     * @return bool
     */
    public function addGroups($groups = [])
    {
        return $this->groups()->sync($groups);
    }

    public function register(array $attributes, $activate = false)
    {
        $this->name = array_get($attributes, 'name');
        $this->email = array_get($attributes, 'email');
        $this->username = array_get($attributes, 'username');
        $this->password = array_get($attributes, 'password');
        $this->language_id = array_get($attributes, 'language_id');
        $this->user_role_id = array_get($attributes, 'user_role_id');
        $this->super_user = array_get($attributes, 'super_user', false);
        $this->status = array_get($attributes, 'status', true);
        $this->is_activated = $activate ? false : null; // Set to false to prevent auto activation in observer
        $this->save();

        if ($activate) {
            $this->completeActivation($this->getActivationCode());
        }

        // Prevents subsequent saves to this model object
        $this->password = null;

        if (array_key_exists('groups', $attributes)) {
            $this->groups()->attach($attributes['groups']);
        }

        if (array_key_exists('locations', $attributes)) {
            $this->locations()->attach($attributes['locations']);
        }

        return $this->reload();
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'admin.users.'.$this->getKey();
    }
}
