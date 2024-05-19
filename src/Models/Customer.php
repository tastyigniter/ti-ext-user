<?php

namespace Igniter\User\Models;

use Carbon\Carbon;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Flame\Exception\SystemException;
use Igniter\Reservation\Models\Reservation;
use Igniter\System\Models\Concerns\Switchable;
use Igniter\System\Models\Country;
use Igniter\System\Traits\SendsMailTemplate;
use Igniter\User\Auth\Models\User as AuthUserModel;
use Igniter\User\Models\Concerns\SendsInvite;
use Illuminate\Support\Facades\Mail;

/**
 * Customer Model Class
 */
class Customer extends AuthUserModel
{
    use HasFactory;
    use Purgeable;
    use SendsInvite;
    use SendsMailTemplate;
    use Switchable;

    /**
     * @var string The database table name
     */
    protected $table = 'customers';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'customer_id';

    protected $guarded = ['reset_code', 'activation_code', 'remember_token'];

    protected $hidden = ['password'];

    public $timestamps = true;

    public $relation = [
        'hasMany' => [
            'addresses' => [\Igniter\User\Models\Address::class, 'delete' => true],
            'orders' => [\Igniter\Cart\Models\Order::class],
            'reservations' => [\Igniter\Reservation\Models\Reservation::class],
        ],
        'belongsTo' => [
            'group' => [\Igniter\User\Models\CustomerGroup::class, 'foreignKey' => 'customer_group_id'],
            'address' => \Igniter\User\Models\Address::class,
        ],
    ];

    protected $purgeable = ['addresses'];

    public $appends = ['full_name'];

    protected $casts = [
        'customer_id' => 'integer',
        'address_id' => 'integer',
        'customer_group_id' => 'integer',
        'newsletter' => 'boolean',
        'is_activated' => 'boolean',
        'last_login' => 'datetime',
        'invited_at' => 'datetime',
        'activated_at' => 'datetime',
        'reset_time' => 'datetime',
    ];

    public static function getDropdownOptions()
    {
        return static::whereIsEnabled()->selectRaw('customer_id, concat(first_name, " ", last_name) as name')->dropdown('name');
    }

    //
    // Accessors & Mutators
    //

    public function getFullNameAttribute($value)
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getEmailAttribute($value)
    {
        return strtolower($value);
    }

    //
    // Events
    //

    public function beforeLogin()
    {
        if (!$this->group || !$this->group->requiresApproval()) {
            return;
        }

        if ($this->is_activated && $this->isEnabled()) {
            return;
        }

        throw new SystemException(sprintf(
            lang('igniter.user::default.customers.alert_customer_not_active'), $this->email
        ));
    }

    public function extendUserQuery($query)
    {
        $query->isEnabled();
    }

    //
    // Helpers
    //

    public function enabled()
    {
        return $this->isEnabled();
    }

    public function getCustomerName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function listAddresses()
    {
        return $this->addresses()->get()->groupBy(function($address) {
            return $address->getKey();
        });
    }

    /**
     * Return all customer registration dates
     *
     * @return array
     */
    public function getCustomerDates()
    {
        return $this->pluckDates('created_at');
    }

    /**
     * Reset a customer password,
     * new password is sent to registered email
     *
     * @return string Reset code
     */
    public function resetPassword()
    {
        if (!$this->enabled()) {
            return false;
        }

        $this->reset_code = $resetCode = $this->generateResetCode();
        $this->reset_time = Carbon::now();
        $this->save();

        return $resetCode;
    }

    public function sendResetPasswordMail(array $vars = [])
    {
        Mail::queueTemplate('igniter.user::mail.password_reset_request', array_merge([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'reset_code' => $this->reset_code,
        ], $vars), $this);
    }

    public function saveAddresses($addresses)
    {
        $customerId = $this->getKey();
        if (!is_numeric($customerId)) {
            return false;
        }

        $idsToKeep = [];
        foreach ($addresses as $address) {
            if (!array_key_exists('country_id', $address)) {
                $address['country_id'] = Country::getDefaultKey();
            }

            $customerAddress = $this->addresses()->updateOrCreate(
                array_only($address, ['address_id']),
                array_except($address, ['address_id', 'customer_id'])
            );

            $idsToKeep[] = $customerAddress->getKey();
        }

        $this->addresses()->whereNotIn('address_id', $idsToKeep)->delete();
    }

    public function saveDefaultAddress(string|int $addressId)
    {
        throw_unless($this?->addresses()->find($addressId),
            new ApplicationException('Address not found or does not belong to the customer')
        );

        $this->address_id = $addressId;
        $this->save();

        return $this;
    }

    public function deleteCustomerAddress(string|int $addressId)
    {
        throw_unless($address = $this?->addresses()->find($addressId),
            new ApplicationException('Address not found or does not belong to the customer')
        );

        $address->delete();

        return $address;
    }

    /**
     * Update guest orders, address and reservations
     * matching customer email
     *
     * @return bool TRUE on success, or FALSE on failure
     */
    public function saveCustomerGuestOrder()
    {
        $update = ['customer_id' => $this->customer_id];

        Reservation::where('email', $this->email)
            ->whereNull('customer_id')
            ->orWhere('customer_id', 0)
            ->update($update);

        Order::where('email', $this->email)
            ->whereNull('customer_id')
            ->orWhere('customer_id', 0)
            ->update($update);

        Address::whereIn('address_id', Order::where('email', $this->email)
            ->whereNotNull('address_id')
            ->pluck('address_id')->all()
        )->update($update);

        return true;
    }

    protected function sendInviteGetTemplateCode(): string
    {
        return 'igniter.user::mail.invite_customer';
    }

    public function mailGetRecipients($type)
    {
        return [
            [$this->email, $this->full_name],
        ];
    }

    public function mailGetData()
    {
        $model = $this->fresh();

        return array_merge($model->toArray(), [
            'customer' => $model,
            'full_name' => $model->full_name,
            'email' => $model->email,
        ]);
    }

    public function register(array $attributes, $activate = false)
    {
        $model = new static;
        $model->fill($attributes);
        $model->save();

        if ($activate && !$model->is_activated) {
            $model->completeActivation($model->getActivationCode());
        }

        // Prevents subsequent saves to this model object
        $model->password = null;

        return $model;
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'main.users.'.$this->getKey();
    }
}
