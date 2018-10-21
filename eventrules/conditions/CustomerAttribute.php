<?php namespace Igniter\User\EventRules\Conditions;

use ApplicationException;
use Igniter\EventRules\Classes\BaseModelAttributesCondition;

class CustomerAttribute extends BaseModelAttributesCondition
{
    protected $modelClass = \Admin\Models\Customers_model::class;

    protected $modelAttributes;

    public function conditionDetails()
    {
        return [
            'name' => 'Customer attribute',
            'description' => 'Customer attributes',
        ];
    }

    public function defineModelAttributes()
    {
        return [
            'first_name' => [
                'label' => 'First Name',
            ],
            'last_name' => [
                'label' => 'Last Name',
            ],
            'username' => [
                'label' => 'Username',
            ],
            'email' => [
                'label' => 'Email address',
            ],
        ];
    }

    /**
     * Checks whether the condition is TRUE for specified parameters
     * @param array $params Specifies a list of parameters as an associative array.
     * @return bool
     */
    public function isTrue(&$params)
    {
        if (!$customer = array_get($params, 'customer')) {
            throw new ApplicationException('Error evaluating the customer attribute condition: the customer object is not found in the condition parameters.');
        }

        return parent::evalIsTrue($customer);
    }
}
