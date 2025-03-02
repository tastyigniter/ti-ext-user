<?php

namespace Igniter\User\Models;

use Carbon\Carbon;
use Exception;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Reservation\Models\Reservation;
use Igniter\System\Models\Concerns\Switchable;
use Igniter\System\Traits\SendsMailTemplate;
use Igniter\User\Auth\Models\User as AuthUserModel;
use Igniter\User\Models\Concerns\SendsInvite;

/**
 * Customer Model Class
 */
class Customer extends AuthUserModel
{
    use Purgeable;
    use SendsMailTemplate;
    use SendsInvite;
    use HasFactory;
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

        throw new Exception(sprintf(
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
        return $this->addresses()->get()->groupBy(function ($address) {
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

    public function saveAddresses($addresses)
    {
        $customerId = $this->getKey();
        if (!is_numeric($customerId)) {
            return false;
        }

        $idsToKeep = [];
        foreach ($addresses as $address) {
            $customerAddress = $this->addresses()->updateOrCreate(
                array_only($address, ['address_id']),
                array_except($address, ['address_id', 'customer_id'])
            );

            $idsToKeep[] = $customerAddress->getKey();
        }

        $this->addresses()->whereNotIn('address_id', $idsToKeep)->delete();
    }

    /**
     * Update guest orders, address and reservations
     * matching customer email
     *
     * @return bool TRUE on success, or FALSE on failure
     */
    public function saveCustomerGuestOrder()
    {
        $query = false;

        if (is_numeric($this->customer_id) && !empty($this->email)) {
            $customer_id = $this->customer_id;
            $customer_email = $this->email;
            $update = ['customer_id' => $customer_id];

            Order::where('email', $customer_email)->update($update);
            if ($orders = Order::where('email', $customer_email)->get()) {
                foreach ($orders as $row) {
                    if (empty($row['order_id'])) {
                        continue;
                    }

                    if ($row['order_type'] == '1' && !empty($row['address_id'])) {
                        Address::where('address_id', $row['address_id'])->update($update);
                    }
                }
            }

            Reservation::where('email', $customer_email)->update($update);

            $query = true;
        }

        return $query;
    }

    protected function sendInviteGetTemplateCode(): string
    {
        return 'igniter.admin::_mail.invite_customer';
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

        if ($activate) {
            $model->completeActivation($model->getActivationCode());
        }

        // Prevents subsequent saves to this model object
        $model->password = null;

        return $model;
    }
}
