<?php

namespace SamPoyigi\Account\Classes;

use ApplicationException;
use Exception;
use Main\Facades\Auth;
use System\Models\Messages_model;

trait AccountComponent
{
//    public $requiredProperties = ['accountConfig'];
//
//    /**
//     * @var array Required controller configuration array keys
//     */
//    public $requiredConfig = [];

    public $customer;

    protected $ordersModel = 'Admin\Models\Orders_model';

    protected $reservationsModel = 'Admin\Models\Reservations_model';

    protected $messagesModel = 'System\Models\Messages_model';

    public function defineProperties()
    {
        return [
            'context' => [
                'label'   => 'Who can access this page',
                'type'    => 'string',
                'default' => 'guest',
            ],
        ];
    }

    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function countInbox()
    {
        return Messages_model::countUnread($this->getCustomer());
    }

    public function getDetails()
    {
        return $this->createModel()->find($this->customer ? $this->customer->getKey() : null);
    }

    public function getAddress()
    {
        if (!$customer = $this->getDetails())
            return null;

        return $customer->address()->first();
    }

    public function findRecord($context, $id = null)
    {
        $model = $this->createModelObject($context);

        $query = $model->newQuery();
        $this->controller->accountExtendFindQuery($query, $context);

        return $query->findOrNew($id);
    }

    public function listFrontEnd($context, $options = [])
    {
        $model = $this->createModelObject($context);

        $properties = $this->getProperties();
        $options = array_merge($properties, $options);

        $query = $model->newQuery();

        if (method_exists($query->getModel(), 'scopeListFrontEnd'))
            return $query->listFrontEnd($options);

        return $query->get();
    }

    public function makeContextComponent($context, $config = [])
    {
        $requiredConfig = $this->getRequiredConfig($context);
        $localConfig = $this->controller->config->validate(
            $this->getConfig('local['.$context.']'), $requiredConfig, TRUE
        );

        $config = array_merge($config, $localConfig);
        if (!isset($config['component']))
            return null;

        $class = $config['component'];

        // Missing Local extension
        if (!class_exists($class)) {
            throw new Exception(sprintf("The Component class name '%s' has not been registered", $class));
        }

        if ($model = $this->createModelFromConfig($localConfig))
            $config['model'] = $model;

        $component = new $class($this->controller, $config);
        $component->alias = $config['alias'];
        $component->name = $config['alias'];

        $this->bindComponentToController($component);

        return $component;
    }

    protected function createModel()
    {
        $class = Auth::getModel();

        if (!class_exists($class))
            throw new ApplicationException(sprintf("Missing customer model in %s", $class));

        return new $class;
    }

    protected function createModelObject($context)
    {
        if (!strlen($class = $this->{"{$context}Model"}))
            throw new Exception(sprintf("Missing %s [modelClass] definition in %s", $context, get_class($this)));

        // $class = $config['modelClass'];
        if (!class_exists($class))
            throw new Exception("{$class} not found in ".get_class($this));

        return new $class;
    }
}