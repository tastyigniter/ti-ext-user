<?php

namespace Igniter\User\Models;

use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\Flame\Database\Model;
use Igniter\System\Models\Concerns\Defaultable;

/**
 * CustomerGroup Model Class
 *
 * @property int $customer_group_id
 * @property string $group_name
 * @property string|null $description
 * @property bool $approval
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $customer_count
 * @mixin \Igniter\Flame\Database\Model
 */
class CustomerGroup extends Model
{
    use Defaultable;
    use HasFactory;

    /**
     * @var string The database table name
     */
    protected $table = 'customer_groups';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'customer_group_id';

    protected $casts = [
        'approval' => 'boolean',
    ];

    public $relation = [
        'hasMany' => [
            'customers' => \Igniter\User\Models\Customer::class,
        ],
    ];

    public $timestamps = true;

    public static function getDropdownOptions()
    {
        return static::dropdown('group_name');
    }

    //
    // Accessors & Mutators
    //

    public function getCustomerCountAttribute($value)
    {
        return $this->customers()->count();
    }

    //
    //
    //

    public function requiresApproval()
    {
        return $this->approval == 1;
    }

    public function defaultableName()
    {
        return $this->group_name;
    }
}
