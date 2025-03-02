<?php

namespace Igniter\User\Models;

use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\Flame\Database\Model;
use Igniter\System\Models\Concerns\HasCountry;
use Igniter\User\Models\Concerns\HasCustomer;

/**
 * Address Model Class
 */
class Address extends Model
{
    use HasCountry;
    use HasCustomer;
    use HasFactory;

    /**
     * @var string The database table name
     */
    protected $table = 'addresses';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'address_id';

    protected $fillable = ['customer_id', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country_id'];

    public $relation = [
        'belongsTo' => [
            'customer' => \Igniter\User\Models\Customer::class,
            'country' => \Igniter\System\Models\Country::class,
        ],
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'country_id' => 'integer',
    ];

    protected array $queryModifierFilters = [
        'customer' => 'applyCustomer',
    ];

    protected array $queryModifierSorts = [
        'address_id asc', 'address_id desc',
        'created_at asc', 'created_at desc',
    ];

    protected $forceDeleting = false;

    public static function createOrUpdateFromRequest($address)
    {
        return self::updateOrCreate(
            array_only($address, ['customer_id', 'address_id']),
            $address
        );
    }

    //
    // Accessors & Mutators
    //

    public function getFormattedAddressAttribute($value)
    {
        return format_address($this->toArray(), false);
    }
}
